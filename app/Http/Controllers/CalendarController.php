<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{

//ini paling bener
// public function index(Request $request)
// {
//     $teacherId = null;
    
//     // --- 1. OTENTIKASI DAN PENENTUAN ID GURU ---
//     if (Auth::guard('teacher')->check()) {
//         $teacherId = Auth::guard('teacher')->user()->id;
//     } elseif (Auth::guard('staff')->check()) {
//         $teacherId = $request->input('teacher_id');
//         if (!$teacherId) {
//             $teacherId = 4; // Fallback default for staff viewing
//         }
//     }

//     if (!$teacherId) {
//         return redirect('/')->with('error', 'Akses ditolak.');
//     }

//     // --- 2. PENENTUAN TANGGAL AWAL MINGGU ---
//     $startDateParam = $request->input('start_date');
    
//     try {
//         $startOfWeekDate = $startDateParam 
//             ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
//             : Carbon::now()->startOfWeek(Carbon::MONDAY);
//     } catch (\Exception $e) {
//         $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//     }
    
//     $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//     // 3. QUERY JADWAL UTAMA (Main Teacher Schedule) (TIDAK DIUBAH)
//     $mainScheduleSubQuery = DB::table('student')
//         ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
//                 DB::raw('COUNT(*) as total_students'),
//                 DB::raw('"main" as role'))
//         ->where('id_teacher', $teacherId)
//         ->where('status', 'active')
//         ->whereNotNull('course_time')
//         ->where(function ($query) {
//             $query->whereNotNull('day1')
//                   ->orWhereNotNull('day2');
//         })
//         ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//     $mainSchedule = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
//         ->mergeBindings($mainScheduleSubQuery)
//         ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//         ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//         ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//         ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//         ->select(
//             't.name as teacher_name',
//             'p.program as class',
//             'd1.day as day1_name',
//             'd2.day as day2_name',
//             'sub.id_teacher',
//             'sub.course_time',
//             'sub.total_students',
//             'sub.role',
//             'sub.priceid', 
//             'sub.day1 as day1_id', 
//             'sub.day2 as day2_id' 
//         )
//         ->get();

//     // --- 4. QUERY JADWAL ASSIST (Assistant Teacher Schedule) - PERBAIKAN PENGHITUNGAN SISWA ---
    
//     // Basis Query Assist (Gabungkan attendances dan attendance_detail)
//     $baseAssistQuery = DB::table('attendances as a')
//         ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
//         ->where('a.assist_id', $teacherId)
//         ->whereNotNull('a.assist_id')
//         ->whereNotNull('a.course_time');

//     // Sub-Query untuk Assist di Day 1 (dimana assist_day1 = 1)
//     $assistDay1Query = (clone $baseAssistQuery)
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day1 as day_id'), 
//             DB::raw('NULL as day2_id'), 
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             // MENGHITUNG SISWA UNIK DARI DETAIL
//             DB::raw('COUNT(DISTINCT ad.student_id) as total_students'), 
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_day1', 1) 
//         ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');


//     // Sub-Query untuk Assist di Day 2 (dimana assist_day2 = 1)
//     $assistDay2Query = (clone $baseAssistQuery)
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day2 as day_id'), 
//             DB::raw('NULL as day2_id'), 
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             // MENGHITUNG SISWA UNIK DARI DETAIL
//             DB::raw('COUNT(DISTINCT ad.student_id) as total_students'), 
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_day2', 1) 
//         ->groupBy('a.assist_id', 'a.day2', 'a.course_time', 'a.price_id');

//     // Gabungkan hasil Day 1 dan Day 2
//     $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);


//     // Dapatkan data hasil Union dan Grouping
//     $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
//         ->mergeBindings($assistScheduleUnion)
//         ->select(
//             'sub.id_teacher',
//             'sub.day_id', 
//             'sub.course_time',
//             'sub.priceid',
//             'sub.role',
//             // Gunakan MAX untuk mengambil hitungan siswa yang sudah benar dari subquery
//             DB::raw('MAX(sub.total_students) as total_students_aggregated') 
//         )
//         // Grouping berdasarkan kriteria jadwal untuk menggabungkan hasil union yang sama
//         ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
//         ->get();


//     // 5. Gabungkan dan Lengkapi Detail Jadwal Assist (Logika Main Teacher tetap dipertahankan)
//     $mergedSchedule = $mainSchedule;

//     foreach ($assistScheduleData as $assistItem) {
        
//         // Cari detail kelas dan nama hari
//         $details = DB::table('price as p')
//             ->where('p.id', $assistItem->priceid)
//             ->join('day as d', 'd.id', '=', DB::raw($assistItem->day_id))
//             ->join('teacher as t_assist', 't_assist.id', '=', DB::raw($assistItem->id_teacher))
//             ->select(
//                 'p.program as class',
//                 'd.day as day_name',
//                 't_assist.name as assist_teacher_name'
//             )
//             ->first();

//         if ($details) {
            
//             // LANGKAH 1: Cari ID Siswa yang benar-benar di-assist dari attendance_detail
//             $actualStudentAssisted = DB::table('attendances as a')
//                 ->where('a.assist_id', $assistItem->id_teacher) 
//                 ->where('a.price_id', $assistItem->priceid)
//                 ->where('a.course_time', $assistItem->course_time)
//                 ->where(function($query) use ($assistItem) {
//                     $query->where('a.day1', $assistItem->day_id)
//                           ->orWhere('a.day2', $assistItem->day_id);
//                 })
//                 ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
//                 ->select('ad.student_id') 
//                 ->first(); 
            
//             $mainTeacherName = null;

//             if ($actualStudentAssisted) {
                
//                 // LANGKAH 2: Cari Guru Utama berdasarkan ID Siswa
//                 $mainTeacherId = DB::table('student')
//                     ->where('id', $actualStudentAssisted->student_id) 
//                     ->value('id_teacher');
                
//                 if ($mainTeacherId) {
//                     $mainTeacherName = DB::table('teacher as t_main')
//                         ->where('id', $mainTeacherId)
//                         ->value('name'); 
//                 }
//             }
                
//             $assistItem->class = $details->class;
//             $assistItem->day1_name = $details->day_name; 
//             $assistItem->day2_name = null; 
//             // Menggunakan hasil agregasi baru
//             $assistItem->total_students = $assistItem->total_students_aggregated; 

//             if ($mainTeacherName) {
//                 $assistItem->teacher_name = 'Assist: ' . $mainTeacherName;
//             } else {
//                 $assistItem->teacher_name = 'Assist Class (Guru Utama Tidak Ditemukan)';
//             }

//             // Tambahkan sebagai objek Laravel Collection Item
//             $mergedSchedule->push((object)[
//                 'teacher_name'      => $assistItem->teacher_name,
//                 'class'             => $assistItem->class,
//                 'day1_name'         => $assistItem->day1_name,
//                 'day2_name'         => $assistItem->day2_name,
//                 'id_teacher'        => $assistItem->id_teacher,
//                 'course_time'       => $assistItem->course_time,
//                 'total_students'    => $assistItem->total_students,
//                 'role'              => $assistItem->role,
//                 'priceid'           => $assistItem->priceid, 
//                 'day1_id'           => $assistItem->day_id,
//                 'day2_id'           => null,
//             ]);
//         }
//     }

//     // Sortir hasil gabungan berdasarkan waktu kelas (opsional)
//     $mergedSchedule = $mergedSchedule->sortBy('course_time');

//     return view('calendar.index', [
//         'data' => $mergedSchedule,
//         'startOfWeekDate' => $startOfWeekDateString,
//         'currentTeacherId' => $teacherId,
//     ]);
// }

// public function index(Request $request)
// {
//     $teacherId = null;
    
//     // --- 1. OTENTIKASI DAN PENENTUAN ID GURU ---
//     if (Auth::guard('teacher')->check()) {
//         $teacherId = Auth::guard('teacher')->user()->id;
//     } elseif (Auth::guard('staff')->check()) {
//         $teacherId = $request->input('teacher_id') ?? 4;
//     }

//     if (!$teacherId) {
//         return redirect('/')->with('error', 'Akses ditolak.');
//     }

//     // --- 2. PENENTUAN TANGGAL AWAL MINGGU ---
//     $startDateParam = $request->input('start_date');
//     try {
//         $startOfWeekDate = $startDateParam 
//             ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
//             : Carbon::now()->startOfWeek(Carbon::MONDAY);
//     } catch (\Exception $e) {
//         $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//     }
//     $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//     // --- 3. QUERY JADWAL UTAMA (Main Teacher) ---
//     $mainScheduleSubQuery = DB::table('student')
//         ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
//                 DB::raw('COUNT(*) as total_students'),
//                 DB::raw('"main" as role'))
//         ->where('id_teacher', $teacherId)
//         ->where('status', 'active')
//         ->whereNotNull('course_time')
//         ->where(function ($query) {
//             $query->whereNotNull('day1')->orWhereNotNull('day2');
//         })
//         ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//     $mainScheduleData = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
//         ->mergeBindings($mainScheduleSubQuery)
//         ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//         ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//         ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//         ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//         ->select(
//             't.name as teacher_name',
//             'p.program as class',
//             'd1.day as day1_name',
//             'd2.day as day2_name',
//             'sub.id_teacher',
//             'sub.course_time',
//             'sub.total_students',
//             'sub.role',
//             'sub.priceid', 
//             'sub.day1 as day1_id', 
//             'sub.day2 as day2_id' 
//         )
//         ->get();

//     // Menambahkan student_list (ARRAY) untuk Jadwal Utama
//     foreach ($mainScheduleData as $item) {
//         $item->student_list = DB::table('student')
//             ->where('id_teacher', $item->id_teacher)
//             ->where('day1', $item->day1_id)
//             ->where('day2', $item->day2_id)
//             ->where('course_time', $item->course_time)
//             ->where('priceid', $item->priceid)
//             ->where('status', 'active')
//             ->pluck('name')
//             ->toArray();
//     }

//     $mergedSchedule = $mainScheduleData;

//     // --- 4. QUERY JADWAL ASSIST ---
//     $baseAssistQuery = DB::table('attendances as a')
//         ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
//         ->where('a.assist_id', $teacherId)
//         ->whereNotNull('a.assist_id')
//         ->whereNotNull('a.course_time');

//     $assistDay1Query = (clone $baseAssistQuery)
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day1 as day_id'), 
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             DB::raw('COUNT(DISTINCT ad.student_id) as total_students'), 
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_day1', 1) 
//         ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');

//     $assistDay2Query = (clone $baseAssistQuery)
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day2 as day_id'), 
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             DB::raw('COUNT(DISTINCT ad.student_id) as total_students'), 
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_day2', 1) 
//         ->groupBy('a.assist_id', 'a.day2', 'a.course_time', 'a.price_id');

//     $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);

//     $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
//         ->mergeBindings($assistScheduleUnion)
//         ->select(
//             'sub.id_teacher',
//             'sub.day_id', 
//             'sub.course_time',
//             'sub.priceid',
//             'sub.role',
//             DB::raw('MAX(sub.total_students) as total_students_aggregated') 
//         )
//         ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
//         ->get();

//     // --- 5. GABUNGKAN DAN LENGKAPI DETAIL (Termasuk List Student ARRAY) ---
//     foreach ($assistScheduleData as $assistItem) {
//         $details = DB::table('price as p')
//             ->where('p.id', $assistItem->priceid)
//             ->join('day as d', 'd.id', '=', DB::raw($assistItem->day_id))
//             ->select('p.program as class', 'd.day as day_name')
//             ->first();

//         if ($details) {
//             // Ambil Nama Siswa dan ID Teacher Utama
//             $studentsInAssist = DB::table('attendances as a')
//                 ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
//                 ->join('student as s', 'ad.student_id', '=', 's.id')
//                 ->where('a.assist_id', $assistItem->id_teacher) 
//                 ->where('a.price_id', $assistItem->priceid)
//                 ->where('a.course_time', $assistItem->course_time)
//                 ->where(function($query) use ($assistItem) {
//                     $query->where('a.day1', $assistItem->day_id)
//                           ->orWhere('a.day2', $assistItem->day_id);
//                 })
//                 ->select('s.name', 's.id_teacher as main_teacher_id')
//                 ->get();

//             $studentArray = $studentsInAssist->pluck('name')->toArray();
            
//             // Cari Nama Guru Utama (Main Teacher)
//             $mainTeacherName = null;
//             if ($studentsInAssist->isNotEmpty()) {
//                 $mainTeacherName = DB::table('teacher')
//                     ->where('id', $studentsInAssist->first()->main_teacher_id)
//                     ->value('name');
//             }

//             $mergedSchedule->push((object)[
//                 'teacher_name'   => $mainTeacherName ? 'Assist: ' . $mainTeacherName : 'Assist Class',
//                 'class'          => $details->class,
//                 'day1_name'      => $details->day_name,
//                 'day2_name'      => null,
//                 'id_teacher'     => $assistItem->id_teacher,
//                 'course_time'    => $assistItem->course_time,
//                 'total_students' => count($studentArray),
//                 'student_list'   => $studentArray, // DATA ARRAY
//                 'role'           => $assistItem->role,
//                 'priceid'        => $assistItem->priceid, 
//                 'day1_id'        => $assistItem->day_id,
//                 'day2_id'        => null,
//             ]);
//         }
//     }

//     $mergedSchedule = $mergedSchedule->sortBy('course_time');

//     dd($mergedSchedule);

//     return view('calendar.index', [
//         'data' => $mergedSchedule,
//         'startOfWeekDate' => $startOfWeekDateString,
//         'currentTeacherId' => $teacherId,
//     ]);
// }

public function index(Request $request)
{
    $teacherId = null;
    $teachers = []; // Inisialisasi variabel guru

    // Ambil semua daftar guru untuk dropdown (Hanya jika yang login adalah Staff)
    if (Auth::guard('staff')->check()) {
        $teachers = DB::table('teacher')->select('id', 'name')->orderBy('name', 'asc')->get();
    }

    // --- 1. OTENTIKASI ---
    if (Auth::guard('teacher')->check()) {
        $teacherId = Auth::guard('teacher')->user()->id;
    } elseif (Auth::guard('staff')->check()) {
        $teacherId = $request->input('teacher_id');
    }

    if (!$teacherId && Auth::guard('teacher')->check()) {
         return redirect('/')->with('error', 'Akses ditolak.');
    }

    // --- 2. PENENTUAN TANGGAL ---
    $startDateParam = $request->input('start_date');
    try {
        $startOfWeekDate = $startDateParam 
            ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
    } catch (\Exception $e) {
        $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
    }
    $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

    // --- 3. QUERY JADWAL UTAMA (Main Teacher) ---
    $mainScheduleSubQuery = DB::table('student')
        ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
                DB::raw('COUNT(*) as total_students'),
                DB::raw('"main" as role'))
        ->where('id_teacher', $teacherId)
        ->where('status', 'active')
        ->whereNotNull('course_time')
        ->where(function ($query) {
            $query->whereNotNull('day1')->orWhereNotNull('day2');
        })
        ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

    $mainScheduleData = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
        ->mergeBindings($mainScheduleSubQuery)
        ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
        ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
        ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
        ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
        ->select(
            't.name as teacher_name', 'p.program as class', 'd1.day as day1_name', 'd2.day as day2_name',
            'sub.id_teacher', 'sub.course_time', 'sub.total_students', 'sub.role', 'sub.priceid', 
            'sub.day1 as day1_id', 'sub.day2 as day2_id' 
        )
        ->get();

    // Student list untuk Main Teacher (Siswa Aktif)
    foreach ($mainScheduleData as $item) {
        $item->student_list = DB::table('student')
            ->where('id_teacher', $item->id_teacher)
            ->where('day1', $item->day1_id)
            ->where('day2', $item->day2_id)
            ->where('course_time', $item->course_time)
            ->where('priceid', $item->priceid)
            ->where('status', 'active')
            ->pluck('name')
            ->toArray();
    }

    $mergedSchedule = $mainScheduleData;

    // --- 4. QUERY JADWAL ASSIST ---
    $baseAssistQuery = DB::table('attendances as a')
        ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
        ->where('a.assist_id', $teacherId)
        ->whereNotNull('a.assist_id')
        ->whereNotNull('a.course_time');

    $assistDay1Query = (clone $baseAssistQuery)
        ->select(DB::raw('a.assist_id as id_teacher'), DB::raw('a.day1 as day_id'), 'a.course_time', DB::raw('a.price_id as priceid'), DB::raw('"assist" as role'))
        ->where('a.assist_day1', 1) 
        ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');

    $assistDay2Query = (clone $baseAssistQuery)
        ->select(DB::raw('a.assist_id as id_teacher'), DB::raw('a.day2 as day_id'), 'a.course_time', DB::raw('a.price_id as priceid'), DB::raw('"assist" as role'))
        ->where('a.assist_day2', 1) 
        ->groupBy('a.assist_id', 'a.day2', 'a.course_time', 'a.price_id');

    $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);

    $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
        ->mergeBindings($assistScheduleUnion)
        ->select('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
        ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
        ->get();

    // --- 5. LENGKAPI DETAIL ASSIST ---
    foreach ($assistScheduleData as $assistItem) {
        $details = DB::table('price as p')
            ->where('p.id', $assistItem->priceid)
            ->join('day as d', 'd.id', '=', DB::raw($assistItem->day_id))
            ->select('p.program as class', 'd.day as day_name')
            ->first();

        if ($details) {
            // PERBAIKAN: Gunakan DISTINCT agar nama siswa tidak double dari record absensi lama
            $studentsInAssist = DB::table('attendances as a')
                ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
                ->join('student as s', 'ad.student_id', '=', 's.id')
                ->where('a.assist_id', $assistItem->id_teacher) 
                ->where('a.price_id', $assistItem->priceid)
                ->where('a.course_time', $assistItem->course_time)
                ->where(function($query) use ($assistItem) {
                    $query->where('a.day1', $assistItem->day_id)
                          ->orWhere('a.day2', $assistItem->day_id);
                })
                ->select('s.name', 's.id_teacher as main_teacher_id')
                ->distinct() // <--- Kunci agar tidak 251 siswa
                ->get();

            $studentArray = $studentsInAssist->pluck('name')->toArray();
            
            $mainTeacherName = null;
            if ($studentsInAssist->isNotEmpty()) {
                // Ambil guru utama dari siswa pertama di daftar tersebut
                $mainTeacherName = DB::table('teacher')
                    ->where('id', $studentsInAssist->first()->main_teacher_id)
                    ->value('name');
            }

            $mergedSchedule->push((object)[
                'teacher_name'   => $mainTeacherName ? 'Assist: ' . $mainTeacherName : 'Assist Class',
                'class'          => $details->class,
                'day1_name'      => $details->day_name,
                'day2_name'      => null,
                'id_teacher'     => $assistItem->id_teacher,
                'course_time'    => $assistItem->course_time,
                'total_students' => count($studentArray), // Total berdasarkan nama unik
                'student_list'   => $studentArray,
                'role'           => $assistItem->role,
                'priceid'        => $assistItem->priceid, 
                'day1_id'        => $assistItem->day_id,
                'day2_id'        => null,
            ]);
        }
    }

    $mergedSchedule = $mergedSchedule->sortBy('course_time');

    return view('calendar.index', [
        'data' => $mergedSchedule ?? [],
        'teachers' => $teachers,
        'startOfWeekDate' => $startOfWeekDateString,
        'currentTeacherId' => $teacherId,
    ]);
}



}