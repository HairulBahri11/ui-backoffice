<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookCollectionController extends Controller
{
    // public function index()
    // {
    //     $data = DB::table('paydetail')
    //         ->select(
    //             'paydetail.studentid',
    //             'paydetail.price_id',
    //             'student.name as student_name',
    //             'price.program',
    //             'day_one.day as day_one_name',
    //             'day_two.day as day_two_name',
    //             'teacher.name as teacher_name',
    //             'student.course_time as course_time',
    //             'paydetail.monthpay',
    //             DB::raw("GROUP_CONCAT(paydetail.category SEPARATOR ', ') as combined_categories"),
    //             DB::raw("GROUP_CONCAT(paydetail.id) as combined_ids")
    //         )
    //         ->join('student', 'student.id', '=', 'paydetail.studentid')
    //         ->join('price', 'price.id', '=', 'paydetail.price_id')
    //         // GANTI KE LEFT JOIN agar siswa tanpa jadwal/guru tetap muncul
    //         ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
    //         ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
    //         ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

    //         ->whereIn('paydetail.category', ['BOOK', 'BOOKLET'])
    //         ->where('paydetail.price_id', '!=', 0)

    //         // Sesuaikan dengan isi database (0 atau NULL)
    //         ->where(function ($query) {
    //             $query->where('paydetail.is_taken', 0)
    //                 ->orWhereNull('paydetail.is_taken');
    //         })

    //         ->groupBy(
    //             'paydetail.studentid',
    //             'paydetail.price_id',
    //             'paydetail.monthpay',
    //             'student.name',
    //             'price.program',
    //             'day_one.day',
    //             'day_two.day',
    //             'teacher.name',
    //             'student.course_time'
    //         )
    //         ->orderBy('paydetail.id', 'DESC')
    //         ->get();

    //     return view('book-collection.index', compact('data'));
    // }


    // // new index, ini akan menampilkan data student yang sertification atau yang belum mengambil buku/buklet
    // public function index()
    // {
    //     // 1. Ambil subquery untuk sertifikat terakhir agar tidak merusak GROUP BY utama
    //     $latestCertificate = DB::table('history-certificate')
    //         ->select('student_id', 'date_certificate as history_date')
    //         ->whereIn('id', function ($query) {
    //             $query->select(DB::raw('MAX(id)'))
    //                 ->from('history-certificate')
    //                 ->groupBy('student_id');
    //         });

    //     // 2. Main Query
    //     $data = DB::table('paydetail')
    //         ->select(
    //             'paydetail.studentid',
    //             'paydetail.price_id',
    //             'student.name as student_name',
    //             'price.program',
    //             'day_one.day as day_one_name',
    //             'day_two.day as day_two_name',
    //             'teacher.name as teacher_name',
    //             'student.course_time as course_time',
    //             'paydetail.monthpay',
    //             'cert.history_date', // Mengambil data sertifikat terakhir siswa
    //             DB::raw("GROUP_CONCAT(paydetail.category SEPARATOR ', ') as combined_categories"),
    //             DB::raw("GROUP_CONCAT(paydetail.id) as combined_ids")
    //         )
    //         ->join('student', 'student.id', '=', 'paydetail.studentid')
    //         ->join('price', 'price.id', '=', 'paydetail.price_id')

    //         // Left join ke subquery sertifikat
    //         ->leftJoinSub($latestCertificate, 'cert', function ($join) {
    //             $join->on('cert.student_id', '=', 'student.id');
    //         })

    //         ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
    //         ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
    //         ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

    //         // Filter kategori pembayaran buku/booklet
    //         ->whereIn('paydetail.category', ['BOOK', 'BOOKLET'])
    //         ->where('paydetail.price_id', '!=', 0)

    //         // COCOKKAN PRICE_ID (Sesuai logika checkPaymentBookOrBooklet Anda di CI3)
    //         ->whereColumn('paydetail.price_id', 'student.priceid')

    //         // Filter: Belum diambil (is_taken 0 atau NULL)
    //         ->where(function ($query) {
    //             $query->where('paydetail.is_taken', 0)
    //                 ->orWhereNull('paydetail.is_taken');
    //         })

    //         ->groupBy(
    //             'paydetail.studentid',
    //             'paydetail.price_id',
    //             'paydetail.monthpay',
    //             'student.name',
    //             'price.program',
    //             'day_one.day',
    //             'day_two.day',
    //             'teacher.name',
    //             'student.course_time',
    //             'cert.history_date' // Wajib dimasukkan ke group by karena ada di select
    //         )
    //         ->orderBy('paydetail.id', 'DESC')
    //         ->get();

    //     return view('book-collection.index', compact('data'));
    // }


    // public function index()
    // {
    //     // 1. Ambil subquery untuk sertifikat terakhir dari setiap siswa
    //     $latestCertificate = DB::table('history-certificate')
    //         ->select('student_id', DB::raw('MAX(id) as max_cert_id')) // Alias di sini adalah max_cert_id
    //         ->groupBy('student_id');

    //     // 2. Main Query: Berpatokan pada Student yang memiliki Sertifikat
    //     $data = DB::table('student')
    //         ->select(
    //             'student.id as studentid',
    //             'student.name as student_name',
    //             'student.course_time as course_time',
    //             'price.id as price_id',
    //             'price.program',
    //             'day_one.day as day_one_name',
    //             'day_two.day as day_two_name',
    //             'teacher.name as teacher_name',
    //             'hc.date_certificate as history_date',
    //             'pd.monthpay',
    //             DB::raw("GROUP_CONCAT(pd.category SEPARATOR ', ') as combined_categories"),
    //             DB::raw("GROUP_CONCAT(pd.id) as combined_ids"),

    //             // Logika Status: Jika data paydetail (pd.id) ditemukan, berarti sudah bayar (READY TO TAKE)
    //             DB::raw("
    //             CASE 
    //                 WHEN COUNT(pd.id) > 0 THEN 'READY TO TAKE'
    //                 ELSE 'UNPAID'
    //             END as payment_status
    //         ")
    //         )
    //         // INNER JOIN ke subquery sertifikat agar HANYA menampilkan siswa yang sertifikasi
    //         ->joinSub($latestCertificate, 'latest_cert', function ($join) {
    //             $join->on('latest_cert.student_id', '=', 'student.id');
    //         })
    //         // PERBAIKAN DI SINI: Sesuaikan 'max_id' menjadi 'max_cert_id'
    //         ->join('history-certificate as hc', 'hc.id', '=', 'latest_cert.max_cert_id')

    //         // Hubungkan data pelengkap siswa
    //         ->join('price', 'price.id', '=', 'student.priceid')
    //         ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
    //         ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
    //         ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

    //         // LEFT JOIN ke paydetail (Supaya yang UNPAID tetap muncul di daftar sertifikasi)
    //         ->leftJoin('paydetail as pd', function ($join) {
    //             $join->on('pd.studentid', '=', 'student.id')
    //                 ->on('pd.price_id', '=', 'student.priceid')
    //                 ->whereIn('pd.category', ['BOOK', 'BOOKLET'])
    //                 ->where('pd.price_id', '!=', 0)
    //                 ->where(function ($query) {
    //                     $query->where('pd.is_taken', 0)
    //                         ->orWhereNull('pd.is_taken');
    //                 });
    //         })

    //         // Kelompokkan data siswa
    //         ->groupBy(
    //             'student.id',
    //             'student.name',
    //             'student.course_time',
    //             'price.id',
    //             'price.program',
    //             'day_one.day',
    //             'day_two.day',
    //             'teacher.name',
    //             'hc.date_certificate',
    //             'pd.monthpay'
    //         )
    //         ->orderBy('hc.id', 'DESC') // Urutkan berdasarkan sertifikat terbaru
    //         ->get();

    //     return view('book-collection.index', compact('data'));
    // }

    // public function index()
    // {
    //     // 1. Ambil subquery untuk mendapatkan ID sertifikat terakhir dari setiap siswa
    //     $latestCertificate = DB::table('history-certificate')
    //         ->select('student_id', DB::raw('MAX(id) as max_cert_id'))
    //         ->groupBy('student_id');

    //     // 2. Main Query: Berpatokan pada Student dengan Filter Ketat
    //     $data = DB::table('student')
    //         ->select(
    //             'student.id as studentid',
    //             'student.name as student_name',
    //             'student.course_time as course_time',
    //             'student.is_failed_promoted',
    //             'price.id as price_id',
    //             'price.program',
    //             'day_one.day as day_one_name',
    //             'day_two.day as day_two_name',
    //             'teacher.name as teacher_name',
    //             'hc.date_certificate as history_date',
    //             'pd.monthpay',
    //             DB::raw("GROUP_CONCAT(pd.category SEPARATOR ', ') as combined_categories"),
    //             DB::raw("GROUP_CONCAT(pd.id) as combined_ids"),

    //             // LOGIKA BAYAR: Menentukan status READY TO TAKE atau UNPAID
    //             DB::raw("
    //             CASE 
    //                 -- Jika ditemukan data pembayaran dengan priceid yang sama dan belum diambil (is_taken = 0)
    //                 WHEN COUNT(pd.id) > 0 AND SUM(CASE WHEN pd.is_taken = 0 THEN 1 ELSE 0 END) > 0 THEN 'READY TO TAKE'
    //                 -- Jika ditemukan data pembayaran tapi semuanya sudah diambil (is_taken = 1)
    //                 WHEN COUNT(pd.id) > 0 AND SUM(CASE WHEN pd.is_taken = 1 THEN 1 ELSE 0 END) = COUNT(pd.id) THEN 'TAKEN'
    //                 -- Jika tidak ada data pembayaran buku/booklet yang cocok dengan priceid saat ini
    //                 ELSE 'UNPAID'
    //             END as payment_status
    //         ")
    //         )
    //         // LEFT JOIN ke subquery sertifikat agar data student.is_failed_promoted yang tidak punya sertifikat tetap aman
    //         ->leftJoinSub($latestCertificate, 'latest_cert', function ($join) {
    //             $join->on('latest_cert.student_id', '=', 'student.id');
    //         })
    //         ->leftJoin('history-certificate as hc', 'hc.id', '=', 'latest_cert.max_cert_id')

    //         // Hubungkan data master pendukung siswa (Wajib INNER JOIN agar data valid)
    //         ->join('price', 'price.id', '=', 'student.priceid')
    //         ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
    //         ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
    //         ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

    //         // LEFT JOIN ke paydetail: Hanya ikat yang kategori BOOK/BOOKLET dan price_id cocok dengan priceid student saat ini
    //         ->leftJoin('paydetail as pd', function ($join) {
    //             $join->on('pd.studentid', '=', 'student.id')
    //                 ->on('pd.price_id', '=', 'student.priceid')
    //                 ->whereIn('pd.category', ['BOOK', 'BOOKLET']);
    //         })

    //         // PENGAMAN UTAMA: Filter khusus mengisolasi KEDUA logika sertifikasi saja
    //         // PERBAIKAN LOGIKA UTAMA: Filter pencocokan tanggal sertifikasi
    //         ->where(function ($query) {
    //             $query->where('student.is_failed_promoted', '1') // Kondisi 1: Gagal promosi
    //                 // Kondisi 2: Tanggal di history HARUS SAMA dengan tanggal di tabel student
    //                 ->orWhere(function ($sub) {
    //                     $sub->whereNotNull('latest_cert.max_cert_id')
    //                         ->whereColumn('hc.date_certificate', 'student.date_certificate');
    //                     // *Catatan: Sesuaikan 'student.date_certificate' dengan nama kolom asli di tabel student milikmu
    //                 });
    //         })

    //         ->where(function ($query) {
    //             $query->where('pd.is_taken', 0)
    //                 ->orWhereNull('pd.is_taken');
    //         })

    //         // Grouping data berdasarkan entitas siswa dan sertifikatnya
    //         ->groupBy(
    //             'student.id',
    //             'student.name',
    //             'student.course_time',
    //             'student.is_failed_promoted',
    //             'price.id',
    //             'price.program',
    //             'day_one.day',
    //             'day_two.day',
    //             'teacher.name',
    //             'hc.date_certificate',
    //             'pd.monthpay'
    //         )

    //         // Singkirkan siswa yang bukunya sudah berstatus 'TAKEN' (Lunas & Sudah diambil)
    //         ->having('payment_status', '!=', 'TAKEN')

    //         ->orderBy('student.id', 'DESC')
    //         ->get();


    //     return view('book-collection.index', compact('data'));
    // }

    // public function index()
    // {
    //     // 1. Ambil subquery untuk mendapatkan ID sertifikat terakhir dari setiap siswa
    //     $latestCertificate = DB::table('history-certificate')
    //         ->select('student_id', DB::raw('MAX(id) as max_cert_id'))
    //         ->groupBy('student_id');

    //     // 2. Main Query: Filter Ketat Berdasarkan Sinkronisasi Tanggal Sertifikat
    //     $data = DB::table('student')
    //         ->select(
    //             'student.id as studentid',
    //             'student.name as student_name',
    //             'student.course_time as course_time',
    //             'student.is_failed_promoted',
    //             'price.id as price_id',
    //             'price.program',
    //             'day_one.day as day_one_name',
    //             'day_two.day as day_two_name',
    //             'teacher.name as teacher_name',
    //             'hc.date_certificate as history_date',
    //             'pd.monthpay',
    //             DB::raw("GROUP_CONCAT(pd.category SEPARATOR ', ') as combined_categories"),
    //             DB::raw("GROUP_CONCAT(pd.id) as combined_ids"),

    //             // LOGIKA BAYAR: Menentukan status READY TO TAKE atau UNPAID
    //             DB::raw("
    //             CASE 
    //                 -- Jika ditemukan data pembayaran dengan priceid yang sama dan belum diambil (is_taken = 0)
    //                 WHEN COUNT(pd.id) > 0 AND SUM(CASE WHEN pd.is_taken = 0 THEN 1 ELSE 0 END) > 0 THEN 'READY TO TAKE'
    //                 -- Jika ditemukan data pembayaran tapi semuanya sudah diambil (is_taken = 1)
    //                 WHEN COUNT(pd.id) > 0 AND SUM(CASE WHEN pd.is_taken = 1 THEN 1 ELSE 0 END) = COUNT(pd.id) THEN 'TAKEN'
    //                 -- Jika tidak ada data pembayaran buku/booklet yang cocok dengan priceid saat ini
    //                 ELSE 'UNPAID'
    //             END as payment_status
    //         ")
    //         )
    //         // LEFT JOIN ke subquery sertifikat
    //         ->leftJoinSub($latestCertificate, 'latest_cert', function ($join) {
    //             $join->on('latest_cert.student_id', '=', 'student.id');
    //         })
    //         ->leftJoin('history-certificate as hc', 'hc.id', '=', 'latest_cert.max_cert_id')

    //         // Hubungkan data master pendukung siswa
    //         ->join('price', 'price.id', '=', 'student.priceid')
    //         ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
    //         ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
    //         ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

    //         // LEFT JOIN ke paydetail berdasarkan kecocokan price_id saat ini
    //         ->leftJoin('paydetail as pd', function ($join) {
    //             $join->on('pd.studentid', '=', 'student.id')
    //                 ->on('pd.price_id', '=', 'student.priceid')
    //                 ->whereIn('pd.category', ['BOOK', 'BOOKLET']);
    //         })

    //         // PERBAIKAN LOGIKA UTAMA: Filter pencocokan tanggal sertifikasi
    //         ->where(function ($query) {
    //             $query->where('student.is_failed_promoted', '1') // Kondisi 1: Gagal promosi
    //                 // Kondisi 2: Tanggal di history HARUS SAMA dengan tanggal di tabel student
    //                 ->orWhere(function ($sub) {
    //                     $sub->whereNotNull('latest_cert.max_cert_id')
    //                         ->whereColumn('hc.date_certificate', 'student.date_certificate');
    //                     // *Catatan: Sesuaikan 'student.date_certificate' dengan nama kolom asli di tabel student milikmu
    //                 });
    //         })

    //         ->where('student.status', 'ACTIVE')->where('student.priceid', '!=', 0) // Pastikan priceid valid

    //         // Grouping data
    //         ->groupBy(
    //             'student.id',
    //             'student.name',
    //             'student.course_time',
    //             'student.is_failed_promoted',
    //             'student.date_certificate', // Ikut dimasukkan ke grup jika ada aturan strict
    //             'price.id',
    //             'price.program',
    //             'day_one.day',
    //             'day_two.day',
    //             'teacher.name',
    //             'hc.date_certificate',
    //             'pd.monthpay'
    //         )

    //         // Singkirkan siswa yang bukunya sudah diambil (TAKEN)
    //         ->having('payment_status', '!=', 'TAKEN')

    //         ->orderBy('student.id', 'DESC')
    //         ->get();


    //     dd($data);

    //     return view('book-collection.index', compact('data'));
    // }

    public function index()
    {
        // 1. Subquery Sertifikat: Ambil ID sertifikat terakhir dari setiap siswa
        $latestCertificate = DB::table('history-certificate')
            ->select('student_id', DB::raw('MAX(id) as max_cert_id'))
            ->groupBy('student_id');

        // 2. Subquery Paydetail: Ringkas data pembayaran buku/booklet
        $bookPayments = DB::table('paydetail')
            ->select(
                'studentid',
                'price_id',
                'monthpay',
                DB::raw("GROUP_CONCAT(category SEPARATOR ', ') as combined_categories"),
                DB::raw("GROUP_CONCAT(id) as combined_ids"),
                // Hitung item yang belum diambil (is_taken = 0 atau null)
                DB::raw("SUM(CASE WHEN is_taken = 0 OR is_taken IS NULL THEN 1 ELSE 0 END) as total_ready"),
                // Total record pembayaran buku siswa untuk paket harga ini
                DB::raw("COUNT(id) as total_records")
            )
            ->whereIn('category', ['BOOK', 'BOOKLET'])
            ->groupBy('studentid', 'price_id', 'monthpay');

        // 3. Main Query
        $data = DB::table('student')
            ->select(
                'student.id as studentid',
                'student.name as student_name',
                'student.course_time as course_time',
                'student.is_failed_promoted',
                'student.is_book_taken',
                'price.id as price_id',
                'price.program',
                'day_one.day as day_one_name',
                'day_two.day as day_two_name',
                'teacher.name as teacher_name',
                'hc.date_certificate as history_date',
                'bp.monthpay',
                'bp.combined_categories',
                'bp.combined_ids',
                'bp.total_ready',

                // LOGIKA STATUS: READY TO TAKE atau UNPAID
                DB::raw("
                CASE 
                    -- Jika ada data pembayaran dan ada item yang belum diambil
                    WHEN bp.total_ready > 0 THEN 'READY TO TAKE'
                    -- Jika ada data pembayaran tapi semua item sudah diambil
                    WHEN bp.total_records > 0 AND bp.total_ready = 0 THEN 'TAKEN'
                    -- Jika tidak ada data pembayaran sama sekali
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

            // Filter ketat pencocokan kriteria sertifikasi siswa
            ->where(function ($query) {
                $query->where('student.is_failed_promoted', '1')
                    ->orWhere(function ($sub) {
                        $sub->whereNotNull('latest_cert.max_cert_id')
                            ->whereColumn('hc.date_certificate', 'student.date_certificate');
                    });
            })

            // PERBAIKAN DI SINI: Jika bp.studentid NULL artinya UNPAID (lolos), jika ada pembayaran harus yang total_ready > 0
            ->where(function ($query) {
                $query->whereNull('bp.studentid')
                    ->orWhere('bp.total_ready', '>', 0);
            })

            ->where('student.status', 'ACTIVE')
            // kecuali student.priceid 39 dan 40 karena itu paket khusus yang tidak termasuk buku/buklet
            ->whereNotIn('student.priceid', [39, 40])

            // Group By Utama
            ->groupBy(
                'student.id',
                'student.name',
                'student.course_time',
                'student.is_failed_promoted',
                'student.is_book_taken',
                'student.date_certificate',
                'price.id',
                'price.program',
                'day_one.day',
                'day_two.day',
                'teacher.name',
                'hc.date_certificate',
                'bp.studentid', // Ikut dimasukkan karena dipakai di where clause atas
                'bp.monthpay',
                'bp.combined_categories',
                'bp.combined_ids',
                'bp.total_ready',
                'bp.total_records',
                // taken or not taken'

            )
            ->orderBy('payment_status', 'DESC') // Urutkan berdasarkan tanggal sertifikasi terbaru.', 'DESC')
            ->get();

        // dd($data);

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

        return redirect()->back()->with('status', 'Book/Booklet marked as taken successfully!');
    }
}
