<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookCollectionController extends Controller
{
    public function index()
    {
        $data = DB::table('paydetail')
            ->select(
                'paydetail.studentid',
                'paydetail.price_id',
                'student.name as student_name',
                'price.program',
                'day_one.day as day_one_name',
                'day_two.day as day_two_name',
                'teacher.name as teacher_name',
                'student.course_time as course_time',
                'paydetail.monthpay',
                DB::raw("GROUP_CONCAT(paydetail.category SEPARATOR ', ') as combined_categories"),
                DB::raw("GROUP_CONCAT(paydetail.id) as combined_ids")
            )
            ->join('student', 'student.id', '=', 'paydetail.studentid')
            ->join('price', 'price.id', '=', 'paydetail.price_id')
            // GANTI KE LEFT JOIN agar siswa tanpa jadwal/guru tetap muncul
            ->leftJoin('day as day_one', 'day_one.id', '=', 'student.day1')
            ->leftJoin('day as day_two', 'day_two.id', '=', 'student.day2')
            ->leftJoin('teacher', 'teacher.id', '=', 'student.id_teacher')

            ->whereIn('paydetail.category', ['BOOK', 'BOOKLET'])
            ->where('paydetail.price_id', '!=', 0)

            // Sesuaikan dengan isi database (0 atau NULL)
            ->where(function ($query) {
                $query->where('paydetail.is_taken', 0)
                    ->orWhereNull('paydetail.is_taken');
            })

            ->groupBy(
                'paydetail.studentid',
                'paydetail.price_id',
                'paydetail.monthpay',
                'student.name',
                'price.program',
                'day_one.day',
                'day_two.day',
                'teacher.name',
                'student.course_time'
            )
            ->orderBy('paydetail.id', 'DESC')
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

        return redirect()->back()->with('status', 'Book/Booklet marked as taken successfully!');
    }
}
