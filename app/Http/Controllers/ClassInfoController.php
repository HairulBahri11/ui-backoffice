<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClassInfoController extends Controller
{
    public function index(Request $request)
    {
        $class = $request->class;
        $teacher = $request->teacher;

        $teachers = DB::table('teacher')->select('id', 'name')->get();
        $classes = DB::table('price')->select('id', 'program')->get();

        $grouped_data = collect();

        if (Auth::guard('staff')->check()) {

            if ($request->filled('class') || $request->filled('teacher')) {

    // 1. Ambil ID absensi terakhir untuk setiap grup (Teacher + Class + Time)
    // Ini adalah kunci agar query tidak menjalankan ribuan subquery di SELECT
    $latestAttendanceIds = DB::table('attendances')
        ->select(DB::raw('MAX(id) as last_id'))
        ->groupBy('teacher_id', 'price_id', 'course_time');

    // 2. Ambil detail data dari ID-ID terakhir tersebut
    $latestAttendanceDetail = DB::table('attendances as a')
        ->joinSub($latestAttendanceIds, 'latest', function($join) {
            $join->on('a.id', '=', 'latest.last_id');
        })
        ->select('a.teacher_id', 'a.price_id', 'a.course_time', 'a.date', 'a.activity', 'a.text_book', 'a.excercise_book');

    // 3. Query Utama Siswa
    $all_students = DB::table('student as s')
        ->select(
            's.id',
            's.name',
            's.priceid',
            's.course_time',
            's.id_teacher',
            'price.program',
            'day_one.day as day1_name',
            'day_two.day as day2_name',
            't.name as teacher_name',
            'att.date as last_class_date',
            'att.activity as last_activity',
            'att.text_book as last_text_book',
            'att.excercise_book as last_exercise_book',
            // Query First Attendance tetap di sini karena unik per-siswa
            DB::raw('(SELECT MIN(a.date) 
                      FROM attendances a 
                      JOIN attendance_details ad ON ad.attendance_id = a.id 
                      WHERE ad.student_id = s.id 
                      AND a.price_id = s.priceid 
                      AND a.teacher_id = s.id_teacher 
                      AND a.course_time = s.course_time
                    ) as first_attendance')
        )
        ->join('price', 'price.id', '=', 's.priceid')
        ->join('teacher as t', 't.id', '=', 's.id_teacher')
        ->join('day as day_one', 'day_one.id', '=', 's.day1')
        ->join('day as day_two', 'day_two.id', '=', 's.day2')
        // Gantikan subquery SELECT dengan LEFT JOIN ke tabel detail absensi terakhir
        ->leftJoinSub($latestAttendanceDetail, 'att', function($join) {
            $join->on('s.id_teacher', '=', 'att.teacher_id')
                 ->on('s.priceid', '=', 'att.price_id')
                 ->on('s.course_time', '=', 'att.course_time');
        })
        ->where('s.status', 'ACTIVE')
        ->when($class, fn($q) => $q->where('s.priceid', $class))
        ->when($teacher, fn($q) => $q->where('s.id_teacher', $teacher))
        ->orderBy('s.priceid', 'asc')
        ->get();

    // 4. Proses Grouping (Tetap sama, namun data input sekarang jauh lebih cepat ditarik)
    $grouped_data = $all_students->groupBy(function ($item) {
        return $item->priceid . '|' . $item->course_time . '|' . $item->day1_name . '|' . $item->day2_name . '|' . $item->id_teacher;
    })->map(function ($group) {
        $first = $group->first();
        return (object) [
            'priceid'            => $first->priceid,
            'program'            => $first->program,
            'course_time'        => $first->course_time,
            'day1'               => $first->day1_name,
            'day2'               => $first->day2_name,
            'teacher_name'       => $first->teacher_name,
            'id_teacher'         => $first->id_teacher,
            'total_student'      => $group->count(),
            'last_activity'      => $first->last_activity,
            'last_text_book'     => $first->last_text_book,
            'last_exercise_book' => $first->last_exercise_book,
            'last_class'         => $first->last_class_date,
            'students'           => $group->map(fn($std) => (object) [
                'id'               => $std->id,
                'name'             => $std->name,
                'first_attendance' => $std->first_attendance
            ])->values()->all()
        ];
    })->values();
}
        }

        // 3. PAGINATION
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 6;
        $currentItems = $grouped_data->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $student_class = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $grouped_data->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('class_info.index', compact('student_class', 'teachers', 'classes'));
    }
}
