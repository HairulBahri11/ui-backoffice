<?php

namespace App\Http\Controllers;

use App\Models\BookTaken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookCollectionController extends Controller
{
    public function index()
    {
        // 1. Subquery Sertifikat: Ambil ID sertifikat terakhir dari setiap siswa
        $latestCertificate = DB::table('history-certificate')
            ->select('student_id', DB::raw('MAX(id) as max_cert_id'))
            ->groupBy('student_id');

        // 2. Subquery Paydetail + Payment: Ringkas data pembayaran buku/booklet mulai dari bulan sekarang
        $startOfCurrentMonth = \Carbon\Carbon::now()->startOfMonth()->toDateString();

        $bookPayments = DB::table('paydetail')
            ->join('payment', 'payment.id', '=', 'paydetail.paymentid')
            ->select(
                'paydetail.studentid',
                'paydetail.price_id',
                'paydetail.monthpay',
                DB::raw("GROUP_CONCAT(paydetail.category SEPARATOR ', ') as combined_categories"),
                DB::raw("GROUP_CONCAT(paydetail.id) as combined_ids"),
                // Hitung item yang belum diambil (is_taken = 0 atau null)
                DB::raw("SUM(CASE WHEN paydetail.is_taken = 0 OR paydetail.is_taken IS NULL THEN 1 ELSE 0 END) as total_ready"),
                // Total record pembayaran buku siswa untuk paket harga ini
                DB::raw("COUNT(paydetail.id) as total_records")
            )
            ->whereIn('paydetail.category', ['BOOK', 'BOOKLET'])
            ->where('payment.paydate', '>=', $startOfCurrentMonth)
            ->groupBy('paydetail.studentid', 'paydetail.price_id', 'paydetail.monthpay');

        // 3. Subquery Book Taken: Ambil tanggal pengambilan buku/booklet terakhir per siswa & paket harga
        $latestBookTaken = DB::table('book_taken')
            ->select('student_id', 'price_id', DB::raw('MAX(taken_at) as taken_at'))
            ->groupBy('student_id', 'price_id');

        // 4. Main Query
        $data = DB::table('student')
            ->select(
                'student.id as studentid',
                'student.name as student_name',
                'student.course_time as course_time',
                'student.is_failed_promoted',
                'student.is_book_taken',
                'price.id as price_id',
                'price.program',
                'day_one.id as day_1_id',
                'day_one.day as day_one_name',
                'day_two.id as day_2_id',
                'day_two.day as day_two_name',
                'teacher.id as teacher_id',
                'teacher.name as teacher_name',
                'hc.date_certificate as history_date',
                'hc.status as certificate_status', // Status dinamis passed/failed
                'bp.monthpay',
                'bp.combined_categories',
                'bp.combined_ids',
                'bp.total_ready',
                'bt.taken_at as book_taken_at',

                // LOGIKA STATUS: READY TO TAKE, TAKEN, atau UNPAID
                DB::raw("
                CASE
                    WHEN bp.total_ready > 0 THEN 'READY TO TAKE'
                    WHEN bp.total_records > 0 AND bp.total_ready = 0 THEN 'TAKEN'
                    ELSE 'UNPAID'
                END as payment_status
            ")
            )
            // LEFT JOIN ke subquery sertifikat
            ->leftJoinSub($latestCertificate, 'latest_cert', function ($join) {
                $join->on('latest_cert.student_id', '=', 'student.id');
            })
            ->leftJoin('history-certificate as hc', 'hc.id', '=', 'latest_cert.max_cert_id')

            // Hubungkan data master pendukung siswa
            ->join('price', 'price.id', '=', 'student.priceid')
            ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
            ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
            ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

            // Hubungkan ke Subquery Paydetail
            ->leftJoinSub($bookPayments, 'bp', function ($join) {
                $join->on('bp.studentid', '=', 'student.id')
                    ->on('bp.price_id', '=', 'student.priceid');
            })

            // Hubungkan ke Subquery Book Taken (tanggal pengambilan terakhir)
            ->leftJoinSub($latestBookTaken, 'bt', function ($join) {
                $join->on('bt.student_id', '=', 'student.id')
                    ->on('bt.price_id', '=', 'student.priceid');
            })

            // Filter kriteria status sertifikat (menangani dynamic passed/failed & siswa baru),
            // atau siswa yang punya pembayaran buku/booklet bulan ini (baik masih READY TO TAKE maupun sudah TAKEN)
            ->where(function ($query) {
                $query->where('hc.status', '=', 'failed') // Sertifikat terakhirnya gagal
                    ->orWhere(function ($sub) {
                        $sub->where('hc.status', '=', 'passed') // Jika lulus, tanggalnya harus cocok
                            ->whereColumn('hc.date_certificate', 'student.date_certificate');
                    })
                    ->orWhere('bp.total_records', '>', 0);
            })

            ->where('student.status', 'ACTIVE')
            // kecuali student.priceid 39 dan 40 karena itu paket khusus yang tidak termasuk buku/buklet
            ->whereNotIn('student.priceid', [39, 40])

            ->groupBy(
                'student.id',
                'student.name',
                'student.course_time',
                'student.is_failed_promoted',
                'student.is_book_taken',
                'student.date_certificate',
                'price.id',
                'price.program',
                'day_one.id',
                'day_one.day',
                'day_two.id',
                'day_two.day',
                'teacher.id',
                'teacher.name',
                'hc.date_certificate',
                'hc.status', // Wajib masuk group by karena di-select
                'bp.studentid',
                'bp.monthpay',
                'bp.combined_categories',
                'bp.combined_ids',
                'bp.total_ready',
                'bp.total_records',
                'bt.taken_at'
            )
            ->orderBy('payment_status', 'DESC')
            ->get();

        return view('book-collection.index', compact('data'));
    }

    public function markAsTaken(Request $request)
    {
        // Kita tangkap string ID yang digabung (misal: "12,13") lalu pecah jadi array
        $ids = explode(',', $request->item_ids);

        DB::table('paydetail')
            ->whereIn('id', $ids)
            ->update([
                'is_taken' => 1
            ]);

        DB::table('student')
            ->where('id', $request->studentid)
            ->update([
                'is_book_taken' => 1
            ]);

        // Simpan riwayat pengambilan buku/booklet
        BookTaken::create([
            'student_id' => $request->studentid,
            'teacher_id' => $request->teacher_id ?: 0,
            'price_id' => $request->price_id ?: 0,
            'day_1' => $request->day_1 ?: 0,
            'day_2' => $request->day_2 ?: 0,
            'course_time' => $request->course_time ?: '-',
            'book_taken' => $request->book_taken ?: 'N/A',
            'taken_at' => now(),
        ]);

        return redirect()->back()->with('status', 'Book/Booklet marked as taken successfully!');
    }
}
