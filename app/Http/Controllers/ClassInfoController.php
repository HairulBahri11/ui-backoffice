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

    if(Auth::guard('staff')->check()){

    if ($request->filled('class') || $request->filled('teacher')) {
        
        $all_students = DB::table('student as s')
            ->select(
                's.id', 's.name', 's.priceid', 's.course_time', 's.id_teacher',
                'price.program', 
                'day_one.day as day1_name', 
                'day_two.day as day2_name',
                't.name as teacher_name',
               DB::raw('(SELECT MIN(a.date) 
          FROM attendances a 
          JOIN attendance_details ad ON ad.attendance_id = a.id 
          WHERE ad.student_id = s.id 
          AND a.price_id = s.priceid 
          AND a.teacher_id = s.id_teacher 
          AND a.course_time = s.course_time
        ) as first_attendance'),
                DB::raw('(SELECT a.activity FROM attendances a 
                          WHERE a.price_id = s.priceid 
                          AND a.course_time = s.course_time 
                          AND a.teacher_id = s.id_teacher 
                          ORDER BY a.date DESC LIMIT 1) as last_activity'),
                DB::raw('(SELECT MAX(a.date) FROM attendances a 
                          WHERE a.price_id = s.priceid 
                          AND a.course_time = s.course_time 
                          AND a.teacher_id = s.id_teacher) as last_class_date')
            )
            ->join('price', 'price.id', '=', 's.priceid')
            ->join('teacher as t', 't.id', '=', 's.id_teacher')
            ->join('day as day_one', 'day_one.id', '=', 's.day1')
            ->join('day as day_two', 'day_two.id', '=', 's.day2')
            ->where('s.status', 'ACTIVE')
            ->when($class, fn($q) => $q->where('s.priceid', $class))
            ->when($teacher, fn($q) => $q->where('s.id_teacher', $teacher))
            ->orderBy('s.priceid', 'asc')
            ->get();

        // 2. Proses Grouping (Gunakan alias hasil join agar grouping akurat)
        $grouped_data = $all_students->groupBy(function($item) {
            return $item->priceid . '|' . $item->course_time . '|' . $item->day1_name . '|' . $item->day2_name . '|' . $item->id_teacher;
        })->map(function ($group) {
            $first = $group->first();
            return (object) [
                'priceid'       => $first->priceid,
                'program'       => $first->program,
                'course_time'   => $first->course_time,
                'day1'          => $first->day1_name, // Menggunakan Nama Hari
                'day2'          => $first->day2_name, // Menggunakan Nama Hari
                'teacher_name'  => $first->teacher_name,
                'id_teacher'    => $first->id_teacher,
                'total_student' => $group->count(),
                'last_activity' => $first->last_activity,
                'last_class'    => $first->last_class_date,
                'students'      => $group->map(fn($std) => (object) [
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
