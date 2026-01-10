<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
//    public function index(Request $request)
//     {
//         $teacherId = null;
        
//         if (Auth::guard('teacher')->check()) {
//             $teacherId = Auth::guard('teacher')->user()->id;
//         } elseif (Auth::guard('staff')->check()) {
//             $teacherId = $request->input('teacher_id');
//             if (!$teacherId) {
//                 $teacherId = 4; // Fallback default for staff viewing
//             }
//         }

//         if (!$teacherId) {
//             return redirect('/')->with('error', 'Akses ditolak.');
//         }

//         $startDateParam = $request->input('start_date');
        
//         try {
//             $startOfWeekDate = $startDateParam 
//                 ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
//                 : Carbon::now()->startOfWeek(Carbon::MONDAY);
//         } catch (\Exception $e) {
//             $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//         }
        
//         $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//         // Sub-query: Get student count per session
//         $subQuery = DB::table('student')
//             ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
//                      DB::raw('COUNT(*) as total_entri_duplikat'))
//             ->where('id_teacher', $teacherId)
//             ->where('status', 'active')
//             ->whereNotNull('course_time')
//             ->where(function ($query) {
//                 $query->whereNotNull('day1')
//                       ->orWhereNotNull('day2');
//             })
//             ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//         // Main query: Join with related tables
//         $schedule = DB::table(DB::raw('(' . $subQuery->toSql() . ') as sub'))
//             ->mergeBindings($subQuery)
//             ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//             ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//             ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//             ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//             ->select(
//                 't.name as teacher_name',
//                 'p.program as class',
//                 'd1.day as day1_name',
//                 'd2.day as day2_name',
//                 'sub.id_teacher',
//                 'sub.course_time',
//                 'sub.total_entri_duplikat'
//             )
//             ->get();
            
//         return view('calendar.index', [
//             'data' => $schedule,
//             'startOfWeekDate' => $startOfWeekDateString,
//             'currentTeacherId' => $teacherId,
//         ]);
//     }

    // public function index(Request $request)
    // {
    //     $teacherId = null;
        
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

    //     $startDateParam = $request->input('start_date');
        
    //     try {
    //         $startOfWeekDate = $startDateParam 
    //             ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
    //             : Carbon::now()->startOfWeek(Carbon::MONDAY);
    //     } catch (\Exception $e) {
    //         $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
    //     }
        
    //     $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

    //     // 1. QUERY JADWAL UTAMA (Main Teacher Schedule)
    //     $mainScheduleSubQuery = DB::table('student')
    //         ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
    //                  DB::raw('COUNT(*) as total_students'),
    //                  DB::raw('"main" as role'))
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

    //     // 2. QUERY JADWAL ASSIST (Assistant Teacher Schedule)
    //     $assistSchedule = DB::table('attendances as a')
    //         ->select(
    //             'a.assist_id as id_teacher', 
    //             'a.day1 as day1_id',
    //             'a.day2 as day2_id',
    //             'a.course_time',
    //             'a.price_id as priceid',
    //             DB::raw('1 as total_students'), 
    //             DB::raw('"assist" as role')
    //         )
    //         ->where('a.assist_id', $teacherId) 
    //         ->whereNotNull('a.assist_id') 
    //         ->where(function ($query) {
    //             // Asumsi 'assist_day1' dan 'assist_day2' adalah kolom integer/boolean (1 atau 0)
    //             $query->where('a.assist_day1', 1) 
    //                   ->orWhere('a.assist_day2', 1);
    //         })
    //         ->groupBy('a.assist_id', 'a.day1', 'a.day2', 'a.course_time', 'a.price_id')
    //         ->get();

    //     // 3. Gabungkan dan Lengkapi Detail Jadwal Assist
    //     $mergedSchedule = $mainSchedule;

    //     foreach ($assistSchedule as $assistItem) {
            
    //         // Mencari detail kelas dengan memperbaiki syntax JOIN
    //         $classDetails = DB::table('price as p')
    //             ->where('p.id', $assistItem->priceid)
                
    //             // Perbaikan Join D1: Memastikan nilai diperlakukan sebagai literal integer
    //             ->join('day as d1', function($join) use ($assistItem) {
    //                 $join->on('d1.id', '=', DB::raw($assistItem->day1_id)); 
    //             })
                
    //             // Perbaikan Join D2: Memastikan nilai diperlakukan sebagai literal integer
    //             ->join('day as d2', function($join) use ($assistItem) {
    //                 $join->on('d2.id', '=', DB::raw($assistItem->day2_id)); 
    //             })
                
    //             // Perbaikan Join Teacher: Mengambil nama guru yang sedang login (sebagai assist)
    //             ->join('teacher as t', function($join) use ($assistItem) {
    //                 $join->on('t.id', '=', DB::raw($assistItem->id_teacher)); 
    //             })
                
    //             ->select(
    //                 'p.program as class',
    //                 'd1.day as day1_name',
    //                 'd2.day as day2_name',
    //                 't.name as teacher_name'
    //             )
    //             ->first();

    //         if ($classDetails) {
    //             // Lengkapi detail kelas
    //             $assistItem->class = $classDetails->class;
    //             $assistItem->day1_name = $classDetails->day1_name;
    //             $assistItem->day2_name = $classDetails->day2_name;

    //             // Cari guru utama kelas tersebut (berdasarkan kriteria kelas)
    //             $mainTeacher = DB::table('student')
    //                 ->where('priceid', $assistItem->priceid)
    //                 ->where('day1', $assistItem->day1_id)
    //                 ->where('day2', $assistItem->day2_id)
    //                 ->where('course_time', $assistItem->course_time)
    //                 ->join('teacher as t', 't.id', '=', 'student.id_teacher')
    //                 ->select('t.name as main_teacher_name')
    //                 ->first();
                    
    //             // Ubah teacher_name untuk assist agar lebih informatif
    //             if ($mainTeacher) {
    //                  $assistItem->teacher_name = 'Assist: ' . $mainTeacher->main_teacher_name;
    //             } else {
    //                  $assistItem->teacher_name = 'Assist Class';
    //             }

    //             $mergedSchedule->push($assistItem);
    //         }
    //     }

    //     // Sortir hasil gabungan (opsional, berdasarkan waktu kelas)
    //     $mergedSchedule = $mergedSchedule->sortBy('course_time');

    //     return view('calendar.index', [
    //         'data' => $mergedSchedule,
    //         'startOfWeekDate' => $startOfWeekDateString,
    //         'currentTeacherId' => $teacherId,
    //     ]);
    // }

// betooled version
//     public function index(Request $request)
// {
//     $teacherId = null;
    
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

//     $startDateParam = $request->input('start_date');
    
//     try {
//         $startOfWeekDate = $startDateParam 
//             ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
//             : Carbon::now()->startOfWeek(Carbon::MONDAY);
//     } catch (\Exception $e) {
//         $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//     }
    
//     $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//     // 1. QUERY JADWAL UTAMA (Main Teacher Schedule)
//     $mainScheduleSubQuery = DB::table('student')
//         ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
//                     DB::raw('COUNT(*) as total_students'),
//                     DB::raw('"main" as role'))
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

//     // 2. QUERY JADWAL ASSIST (Assistant Teacher Schedule) - Diperbaiki
    
//     // Sub-Query untuk Assist di Day 1 (dimana assist_day1 = 1)
//     $assistDay1Query = DB::table('attendances as a')
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day1 as day_id'), // Ambil day1_id sebagai hari jadwal
//             DB::raw('NULL as day2_id'), // Set day2_id menjadi NULL
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             DB::raw('COUNT(*) as total_students'), // Hitung jumlah siswa di jam/hari itu
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_id', $teacherId)
//         ->where('a.assist_day1', 1) // HANYA ambil data assist di Day 1
//         ->whereNotNull('a.assist_id')
//         ->whereNotNull('a.course_time')
//         ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');


//     // Sub-Query untuk Assist di Day 2 (dimana assist_day2 = 1)
//     $assistDay2Query = DB::table('attendances as a')
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day2 as day_id'), // Ambil day2_id sebagai hari jadwal
//             DB::raw('NULL as day2_id'), // Set day2_id menjadi NULL
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             DB::raw('COUNT(*) as total_students'), // Hitung jumlah siswa di jam/hari itu
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_id', $teacherId)
//         ->where('a.assist_day2', 1) // HANYA ambil data assist di Day 2
//         ->whereNotNull('a.assist_id')
//         ->whereNotNull('a.course_time')
//         ->groupBy('a.assist_id', 'a.day2', 'a.course_time', 'a.price_id');

//     // Gabungkan hasil Day 1 dan Day 2
//     $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);


//     // Dapatkan data hasil Union dan Grouping
//     $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
//         ->mergeBindings($assistScheduleUnion)
//         ->select(
//             'sub.id_teacher',
//             'sub.day_id', // Ini adalah ID hari yang valid (dari day1 atau day2)
//             'sub.course_time',
//             'sub.priceid',
//             'sub.role',
//             // Hitung total_students lagi setelah union (opsional, tapi lebih aman)
//             DB::raw('SUM(sub.total_students) as total_students_aggregated') 
//         )
//         ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
//         ->get();


//     // 3. Gabungkan dan Lengkapi Detail Jadwal Assist - Diperbaiki
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
//             // Cari guru utama kelas tersebut (berdasarkan kriteria kelas)
//             $mainTeacher = DB::table('student')
//                 ->where('priceid', $assistItem->priceid)
//                 ->where('course_time', $assistItem->course_time)
//                 // Harus cocok di day1 ATAU day2
//                 ->where(function($query) use ($assistItem) {
//                     $query->where('day1', $assistItem->day_id)
//                           ->orWhere('day2', $assistItem->day_id);
//                 })
//                 ->join('teacher as t_main', 't_main.id', '=', 'student.id_teacher')
//                 ->select('t_main.name as main_teacher_name')
//                 ->first();
                
//             $assistItem->class = $details->class;
//             $assistItem->day1_name = $details->day_name; // Hari yang valid sebagai hari jadwal
//             $assistItem->day2_name = null; // Karena ini adalah jadwal satu hari (hasil union)
//             $assistItem->total_students = $assistItem->total_students_aggregated;

//             if ($mainTeacher) {
//                 $assistItem->teacher_name = 'Assist: ' . $mainTeacher->main_teacher_name;
//             } else {
//                 $assistItem->teacher_name = 'Assist Class';
//             }

//             // Tambahkan sebagai objek Laravel Collection Item
//             $mergedSchedule->push((object)[
//                 'teacher_name'      => $assistItem->teacher_name,
//                 'class'             => $assistItem->class,
//                 'day1_name'         => $assistItem->day1_name,
//                 'day2_name'         => $assistItem->day2_name, // Null
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

//     // Sortir hasil gabungan berdasarkan waktu kelas dan hari (opsional)
//     // Sorting by day name will be handled in the Blade file
//     $mergedSchedule = $mergedSchedule->sortBy('course_time');

//     return view('calendar.index', [
//         'data' => $mergedSchedule,
//         'startOfWeekDate' => $startOfWeekDateString,
//         'currentTeacherId' => $teacherId,
//     ]);
// }

// public function index(Request $request)
//     {
//         $teacherId = null;
        
//         if (Auth::guard('teacher')->check()) {
//             $teacherId = Auth::guard('teacher')->user()->id;
//         } elseif (Auth::guard('staff')->check()) {
//             $teacherId = $request->input('teacher_id');
//             if (!$teacherId) {
//                 // Catatan: Fallback default 4 mungkin harus dihapus
//                 // jika Staff harus selalu memilih guru.
//                 $teacherId = 4; // Fallback default for staff viewing
//             }
//         }

//         if (!$teacherId) {
//             return redirect('/')->with('error', 'Akses ditolak.');
//         }

//         $startDateParam = $request->input('start_date');
        
//         try {
//             $startOfWeekDate = $startDateParam 
//                 ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
//                 : Carbon::now()->startOfWeek(Carbon::MONDAY);
//         } catch (\Exception $e) {
//             $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//         }
        
//         $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//         // --- 1. QUERY JADWAL UTAMA (Main Teacher Schedule) ---
        
//         // Sub-Query Main: student data
//         $mainScheduleSubQuery = DB::table('student')
//             ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
//                         DB::raw('COUNT(*) as total_students'),
//                         DB::raw('"main" as role'))
//             ->where('id_teacher', $teacherId)
//             ->where('status', 'active')
//             ->whereNotNull('course_time')
//             ->where(function ($query) {
//                 $query->whereNotNull('day1')
//                       ->orWhereNotNull('day2');
//             })
//             ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//         // Final Main Query
//         $mainSchedule = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
//             ->mergeBindings($mainScheduleSubQuery)
//             ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//             ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//             ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//             ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//             ->select(
//                 't.name as teacher_name',
//                 'p.program as class',
//                 'd1.day as day1_name',
//                 'd2.day as day2_name',
//                 'sub.id_teacher',
//                 'sub.course_time',
//                 'sub.total_students',
//                 'sub.role',
//                 'sub.priceid', 
//                 'sub.day1 as day1_id', 
//                 'sub.day2 as day2_id' 
//             )
//             ->get();

//         // --- 2. QUERY JADWAL ASSIST (Assistant Teacher Schedule) ---
        
//         // Sub-Query untuk Assist di Day 1 (dimana assist_day1 = 1)
//         $assistDay1Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.assist_id as id_teacher'), 
//                 DB::raw('a.day1 as day_id'), 
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('1 as single_student_count'), // Hitungan per baris
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.assist_id', $teacherId)
//             ->where('a.assist_day1', 1) 
//             ->whereNotNull('a.assist_id')
//             ->whereNotNull('a.course_time');


//         // Sub-Query untuk Assist di Day 2 (dimana assist_day2 = 1)
//         $assistDay2Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.assist_id as id_teacher'), 
//                 DB::raw('a.day2 as day_id'), 
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('1 as single_student_count'), // Hitungan per baris
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.assist_id', $teacherId)
//             ->where('a.assist_day2', 1) 
//             ->whereNotNull('a.assist_id')
//             ->whereNotNull('a.course_time');

//         // Gabungkan hasil Day 1 dan Day 2
//         $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);

//         // Dapatkan data hasil Union dan Grouping
//         $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
//             ->mergeBindings($assistScheduleUnion)
//             ->select(
//                 'sub.id_teacher',
//                 'sub.day_id', 
//                 'sub.course_time',
//                 'sub.priceid',
//                 'sub.role',
//                 DB::raw('SUM(sub.single_student_count) as total_students_aggregated') 
//             )
//             ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
//             ->get();


//         // --- 3. Lengkapi Detail Jadwal Assist (Optimasi N+1) ---

//         // 3a. Kumpulkan semua kombinasi PriceID dan DayID yang unik dari jadwal assist
//         $uniqueAssistKeys = $assistScheduleData->map(function ($item) {
//             return [
//                 'priceid' => $item->priceid, 
//                 'day_id' => $item->day_id, 
//                 'course_time' => $item->course_time
//             ];
//         })->unique()->values()->toArray();

//         $mainTeacherDetails = collect();

//         if (!empty($uniqueAssistKeys)) {
//             // 3b. Cari semua Guru Utama untuk semua kelas yang di-assist dalam satu query (Bulk Query)
//             $mainTeacherQuery = DB::table('student')
//                 ->join('teacher as t_main', 't_main.id', '=', 'student.id_teacher')
//                 ->where('student.status', 'active')
//                 ->where(function($query) use ($uniqueAssistKeys) {
//                     // Membuat klausa OR/AND yang kompleks
//                     foreach ($uniqueAssistKeys as $key) {
//                         $query->orWhere(function($q) use ($key) {
//                             $q->where('priceid', $key['priceid'])
//                               ->where('course_time', $key['course_time'])
//                               ->where(function($qq) use ($key) {
//                                   $qq->where('day1', $key['day_id'])
//                                      ->orWhere('day2', $key['day_id']);
//                               });
//                         });
//                     }
//                 })
//                 ->select(
//                     't_main.name as main_teacher_name',
//                     'student.priceid',
//                     'student.course_time',
//                     'student.day1',
//                     'student.day2'
//                 )
//                 ->get();

//             // 3c. Kelompokkan hasilnya untuk akses cepat
//             $mainTeacherDetails = $mainTeacherQuery->mapWithKeys(function ($item) {
//                 // Key harus unik untuk PriceID + Time + DayID
//                 // Kita akan membuat dua key untuk Day1 dan Day2
//                 $baseKey = $item->priceid . '_' . $item->course_time;
//                 $keys = [];
//                 if ($item->day1) {
//                     $keys[] = $baseKey . '_' . $item->day1;
//                 }
//                 if ($item->day2 && $item->day2 != $item->day1) {
//                     $keys[] = $baseKey . '_' . $item->day2;
//                 }
                
//                 $result = [];
//                 foreach ($keys as $key) {
//                     $result[$key] = $item->main_teacher_name;
//                 }
//                 return $result;

//             });
//         }
        
//         // 3d. Gabungkan dan Lengkapi Detail Jadwal Assist ke mergedSchedule
//         $mergedSchedule = $mainSchedule;

//         foreach ($assistScheduleData as $assistItem) {
            
//             // Cari detail kelas dan nama hari
//             $details = DB::table('price as p')
//                 ->where('p.id', $assistItem->priceid)
//                 ->join('day as d', 'd.id', '=', DB::raw($assistItem->day_id))
//                 ->select(
//                     'p.program as class',
//                     'd.day as day_name'
//                 )
//                 ->first();

//             if ($details) {
                
//                 // Cari nama guru utama dari collection yang sudah di-cache
//                 $cacheKey = $assistItem->priceid . '_' . $assistItem->course_time . '_' . $assistItem->day_id;
//                 $mainTeacherName = $mainTeacherDetails->get($cacheKey);
                
//                 $assistItem->class = $details->class;
//                 $assistItem->day1_name = $details->day_name; 
//                 $assistItem->day2_name = null; 
//                 $assistItem->total_students = $assistItem->total_students_aggregated;

//                 if ($mainTeacherName) {
//                     $teacherDisplay = 'Assist: ' . $mainTeacherName;
//                 } else {
//                     $teacherDisplay = 'Assist Class';
//                 }

//                 // Tambahkan sebagai objek Laravel Collection Item
//                 $mergedSchedule->push((object)[
//                     'teacher_name'      => $teacherDisplay,
//                     'class'             => $assistItem->class,
//                     'day1_name'         => $assistItem->day1_name,
//                     'day2_name'         => $assistItem->day2_name, // Null
//                     'id_teacher'        => $assistItem->id_teacher,
//                     'course_time'       => $assistItem->course_time,
//                     'total_students'    => $assistItem->total_students,
//                     'role'              => $assistItem->role,
//                     'priceid'           => $assistItem->priceid, 
//                     'day1_id'           => $assistItem->day_id,
//                     'day2_id'           => null,
//                 ]);
//             }
//         }

//         // Sortir hasil gabungan berdasarkan waktu kelas 
//         $mergedSchedule = $mergedSchedule->sortBy('course_time');

//         return view('calendar.index', [
//             'data' => $mergedSchedule,
//             'startOfWeekDate' => $startOfWeekDateString,
//             'currentTeacherId' => $teacherId,
//         ]);
//     }


// public function index(Request $request)
//     {
//         $teacherId = null;
        
//         // Penentuan Teacher ID
//         if (Auth::guard('teacher')->check()) {
//             $teacherId = Auth::guard('teacher')->user()->id;
//         } elseif (Auth::guard('staff')->check()) {
//             $teacherId = $request->input('teacher_id');
//             if (!$teacherId) {
//                 $teacherId = 4; // Fallback default for staff viewing
//             }
//         }

//         if (!$teacherId) {
//             return redirect('/')->with('error', 'Akses ditolak.');
//         }

//         $startDateParam = $request->input('start_date');
        
//         try {
//             $startOfWeekDate = $startDateParam 
//                 ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
//                 : Carbon::now()->startOfWeek(Carbon::MONDAY);
//         } catch (\Exception $e) {
//             $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//         }
        
//         $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//         // --- 1. QUERY JADWAL UTAMA (Main Teacher Schedule) ---
        
//         $mainScheduleSubQuery = DB::table('student')
//             ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
//                         DB::raw('COUNT(*) as total_students'),
//                         DB::raw('"main" as role'))
//             ->where('id_teacher', $teacherId)
//             ->where('status', 'active')
//             ->whereNotNull('course_time')
//             ->where(function ($query) {
//                 $query->whereNotNull('day1')
//                     ->orWhereNotNull('day2');
//             })
//             ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//         $mainSchedule = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
//             ->mergeBindings($mainScheduleSubQuery)
//             ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//             ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//             ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//             ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//             ->select(
//                 't.name as teacher_name',
//                 'p.program as class',
//                 'd1.day as day1_name',
//                 'd2.day as day2_name',
//                 'sub.id_teacher',
//                 'sub.course_time',
//                 'sub.total_students',
//                 'sub.role',
//                 'sub.priceid', 
//                 'sub.day1 as day1_id', 
//                 'sub.day2 as day2_id' 
//             )
//             ->get();


//         // --- 2. QUERY JADWAL ASSIST (Assistant Teacher Schedule) ---
        
//         $assistDay1Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.assist_id as id_teacher'), 
//                 DB::raw('a.day1 as day_id'), 
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('1 as single_student_count'),
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.assist_id', $teacherId)
//             ->where('a.assist_day1', 1) 
//             ->whereNotNull('a.assist_id')
//             ->whereNotNull('a.course_time');

//         $assistDay2Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.assist_id as id_teacher'), 
//                 DB::raw('a.day2 as day_id'), 
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('1 as single_student_count'),
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.assist_id', $teacherId)
//             ->where('a.assist_day2', 1) 
//             ->whereNotNull('a.assist_id')
//             ->whereNotNull('a.course_time');

//         $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);

//         $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
//             ->mergeBindings($assistScheduleUnion)
//             ->select(
//                 'sub.id_teacher',
//                 'sub.day_id', 
//                 'sub.course_time',
//                 'sub.priceid',
//                 'sub.role',
//                 DB::raw('SUM(sub.single_student_count) as total_students_aggregated') 
//             )
//             ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
//             ->get();


//         // --- 3. LENGKAPI DETAIL JADWAL ASSIST (Optimasi Program & Day Name) ---
        
//         $uniqueAssistKeys = $assistScheduleData->map(function ($item) {
//             return [
//                 'priceid' => $item->priceid, 
//                 'day_id' => $item->day_id, 
//                 'course_time' => $item->course_time
//             ];
//         })->unique()->values()->toArray();

//         $priceIds = $assistScheduleData->pluck('priceid')->unique()->toArray();
//         $dayIds = $assistScheduleData->pluck('day_id')->unique()->toArray();

//         $programDetails = DB::table('price')->whereIn('id', $priceIds)->pluck('program', 'id');
//         $dayNames = DB::table('day')->whereIn('id', $dayIds)->pluck('day', 'id');
        
//         $mainTeacherDetails = collect();

//         if (!empty($uniqueAssistKeys)) {
//             // Cari semua Guru Utama (Main Teacher) untuk kelas yang di-assist
//             $mainTeacherQuery = DB::table('student')
//                 ->join('teacher as t_main', 't_main.id', '=', 'student.id_teacher')
//                 ->where('student.status', 'active')
//                 ->where(function($query) use ($uniqueAssistKeys) {
//                     foreach ($uniqueAssistKeys as $key) {
//                         $query->orWhere(function($q) use ($key) {
//                             $q->where('priceid', $key['priceid'])
//                               ->where('course_time', $key['course_time'])
//                               ->where(function($qq) use ($key) {
//                                   $qq->where('day1', $key['day_id'])
//                                      ->orWhere('day2', $key['day_id']);
//                               });
//                         });
//                     }
//                 })
//                 ->select(
//                     't_main.name as main_teacher_name',
//                     'student.priceid',
//                     'student.course_time',
//                     'student.day1',
//                     'student.day2'
//                 )
//                 ->get();

//             $mainTeacherDetails = $mainTeacherQuery->mapWithKeys(function ($item) {
//                 $baseKey = $item->priceid . '_' . $item->course_time;
//                 $keys = [];
//                 if ($item->day1) {
//                     $keys[] = $baseKey . '_' . $item->day1;
//                 }
//                 if ($item->day2 && $item->day2 != $item->day1) {
//                     $keys[] = $baseKey . '_' . $item->day2;
//                 }
                
//                 $result = [];
//                 foreach ($keys as $key) {
//                     $result[$key] = $item->main_teacher_name;
//                 }
//                 return $result;
//             });
//         }
        
//         // Gabungkan dan Lengkapi Detail Jadwal Assist
//         $assistScheduleComplete = collect();

//         foreach ($assistScheduleData as $assistItem) {
            
//             $detailsClass = $programDetails->get($assistItem->priceid);
//             $detailsDayName = $dayNames->get($assistItem->day_id);

//             if ($detailsClass && $detailsDayName) {
                
//                 $cacheKey = $assistItem->priceid . '_' . $assistItem->course_time . '_' . $assistItem->day_id;
//                 $mainTeacherName = $mainTeacherDetails->get($cacheKey);
                
//                 $teacherDisplay = $mainTeacherName ? ('Assist: ' . $mainTeacherName) : 'Assist Class';

//                 $assistScheduleComplete->push((object)[
//                     'teacher_name'      => $teacherDisplay,
//                     'class'             => $detailsClass,
//                     'day1_name'         => $detailsDayName,
//                     'day2_name'         => null, 
//                     'id_teacher'        => $assistItem->id_teacher,
//                     'course_time'       => $assistItem->course_time,
//                     'total_students'    => $assistItem->total_students_aggregated,
//                     'role'              => $assistItem->role,
//                     'priceid'           => $assistItem->priceid, 
//                     'day1_id'           => $assistItem->day_id,
//                     'day2_id'           => null,
//                     'assistant_name'    => null,
//                 ]);
//             }
//         }


//         // --- 4. CARI GURU ASSIST UNTUK JADWAL UTAMA (Main Schedule) - BULK QUERY ---
        
//         $mainScheduleKeys = collect();
//         foreach ($mainSchedule as $item) {
//             $keyBase = $item->priceid . '_' . $item->course_time;
//             if ($item->day1_id) {
//                 $mainScheduleKeys->push([
//                     'priceid' => $item->priceid,
//                     'course_time' => $item->course_time,
//                     'day_id' => $item->day1_id,
//                     'key' => $keyBase . '_' . $item->day1_id
//                 ]);
//             }
//             if ($item->day2_id && $item->day2_id != $item->day1_id) {
//                 $mainScheduleKeys->push([
//                     'priceid' => $item->priceid,
//                     'course_time' => $item->course_time,
//                     'day_id' => $item->day2_id,
//                     'key' => $keyBase . '_' . $item->day2_id
//                 ]);
//             }
//         }
//         $uniqueMainScheduleKeys = $mainScheduleKeys->unique('key')->values();

//         $mainScheduleAssistants = collect();
//         if ($uniqueMainScheduleKeys->isNotEmpty()) {
            
//             $assistantQuery = DB::table('attendances as a')
//                 ->join('teacher as t_assist', 'a.assist_id', '=', 't_assist.id')
//                 ->select('t_assist.name', 'a.price_id', 'a.course_time', 'a.day1', 'a.day2');

//             $assistantQuery->where(function($query) use ($uniqueMainScheduleKeys) {
//                 foreach ($uniqueMainScheduleKeys as $key) {
//                     $query->orWhere(function($q) use ($key) {
//                         $q->where('a.price_id', $key['priceid'])
//                           ->where('a.course_time', $key['course_time'])
//                           ->where(function($qq) use ($key) {
//                               $qq->whereRaw('(a.day1 = ? AND a.assist_day1 = 1)', [$key['day_id']])
//                                  ->orWhereRaw('(a.day2 = ? AND a.assist_day2 = 1)', [$key['day_id']]);
//                           });
//                     });
//                 }
//             });

//             $mainScheduleAssistants = $assistantQuery->get()->mapWithKeys(function($item) {
//                 $keyBase = $item->price_id . '_' . $item->course_time;
//                 $result = [];
                
//                 $keyDay1 = $keyBase . '_' . $item->day1;
//                 $result[$keyDay1] = $item->name;
                
//                 if ($item->day2 && $item->day2 != $item->day1) {
//                     $keyDay2 = $keyBase . '_' . $item->day2;
//                     $result[$keyDay2] = $item->name;
//                 }
                
//                 return $result;
//             })->unique();
//         }

//         $mainScheduleModified = $mainSchedule->map(function ($mainItem) use ($mainScheduleAssistants) {
//             $mainItem->assistant_name = null;
            
//             // Cek Day 1
//             if ($mainItem->day1_id) {
//                 $keyDay1 = $mainItem->priceid . '_' . $mainItem->course_time . '_' . $mainItem->day1_id;
//                 $mainItem->assistant_name = $mainScheduleAssistants->get($keyDay1);
//             }
            
//             // Cek Day 2 jika Day 1 tidak punya assistant
//             if (!$mainItem->assistant_name && $mainItem->day2_id) {
//                 $keyDay2 = $mainItem->priceid . '_' . $mainItem->course_time . '_' . $mainItem->day2_id;
//                 $mainItem->assistant_name = $mainScheduleAssistants->get($keyDay2);
//             }
            
//             return $mainItem;
//         });


//         // --- 5. GABUNGKAN SEMUA JADWAL ---
        
//         $mergedSchedule = $mainScheduleModified->concat($assistScheduleComplete);

//         // Sortir hasil gabungan berdasarkan waktu kelas 
//         $mergedSchedule = $mergedSchedule->sortBy('course_time');

//         return view('calendar.index', [
//             'data' => $mergedSchedule,
//             'startOfWeekDate' => $startOfWeekDateString,
//             'currentTeacherId' => $teacherId,
//         ]);
//     }

// }



// public function index(Request $request)
//     {
//         $teacherId = null;

//         // --- Autentikasi dan Penentuan Teacher ID ---
//         if (Auth::guard('teacher')->check()) {
//             $teacherId = Auth::guard('teacher')->user()->id;
//         } elseif (Auth::guard('staff')->check()) {
//             $teacherId = $request->input('teacher_id');
//             if (!$teacherId) {
//                 $teacherId = 4; // Fallback default for staff viewing
//             }
//         }

//         if (!$teacherId) {
//             return redirect('/')->with('error', 'Akses ditolak.');
//         }

//         // --- Penentuan Tanggal Awal Pekan ---
//         $startDateParam = $request->input('start_date');

//         try {
//             $startOfWeekDate = $startDateParam
//                 ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY)
//                 : Carbon::now()->startOfWeek(Carbon::MONDAY);
//         } catch (\Exception $e) {
//             $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//         }

//         $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//         // 1. QUERY JADWAL UTAMA (Main Teacher Schedule)
//         $mainScheduleSubQuery = DB::table('student')
//             ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid',
//                         DB::raw('COUNT(*) as total_students'),
//                         DB::raw('"main" as role'))
//             ->where('id_teacher', $teacherId)
//             ->where('status', 'active')
//             ->whereNotNull('course_time')
//             ->where(function ($query) {
//                 $query->whereNotNull('day1')
//                       ->orWhereNotNull('day2');
//             })
//             ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//         $mainSchedule = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
//             ->mergeBindings($mainScheduleSubQuery)
//             ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//             ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//             ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//             ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//             ->select(
//                 't.name as teacher_name',
//                 'p.program as class',
//                 'd1.day as day1_name',
//                 'd2.day as day2_name',
//                 'sub.id_teacher',
//                 'sub.course_time',
//                 'sub.total_students',
//                 'sub.role',
//                 'sub.priceid',
//                 'sub.day1 as day1_id',
//                 'sub.day2 as day2_id'
//             )
//             ->get();


//         // 2. QUERY JADWAL ASSIST (Assistant Teacher Schedule) - Mendapatkan kombinasi jadwal unik

//         // Sesi Assist di Day 1
//         $assistDay1Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.teacher_id as id_teacher'), // Menggunakan a.teacher_id
//                 DB::raw('a.day1 as day_id'),
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.teacher_id', $teacherId) // Menggunakan a.teacher_id
//             ->where('a.assist_day1', 1)
//             ->whereNotNull('a.teacher_id') // Menggunakan a.teacher_id
//             ->whereNotNull('a.course_time')
//             ->groupBy('a.teacher_id', 'a.day1', 'a.course_time', 'a.price_id');


//         // Sesi Assist di Day 2
//         $assistDay2Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.teacher_id as id_teacher'), // Menggunakan a.teacher_id
//                 DB::raw('a.day2 as day_id'),
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.teacher_id', $teacherId) // Menggunakan a.teacher_id
//             ->where('a.assist_day2', 1)
//             ->whereNotNull('a.teacher_id') // Menggunakan a.teacher_id
//             ->whereNotNull('a.course_time')
//             ->groupBy('a.teacher_id', 'a.day2', 'a.course_time', 'a.price_id');

//         // Gabungkan hasil Day 1 dan Day 2. Menggunakan UNION untuk mendapatkan kombinasi jadwal unik.
//         $assistScheduleUnion = $assistDay1Query->union($assistDay2Query);

//         // Dapatkan data jadwal assist unik
//         $assistScheduleUnique = $assistScheduleUnion->get();


//         // 3. Gabungkan dan Lengkapi Detail Jadwal Assist
        
//         // Ambil data detail tambahan di luar loop untuk efisiensi
//         $priceDetails = DB::table('price')->pluck('program', 'id');
//         $dayDetails = DB::table('day')->pluck('day', 'id');

//         $mergedSchedule = $mainSchedule;

//         foreach ($assistScheduleUnique as $assistItem) {
            
//             $dayId = $assistItem->day_id;
//             $priceId = $assistItem->priceid;
//             $courseTime = $assistItem->course_time;
            
//             // A. HITUNG TOTAL SISWA AKTIF DARI TABEL STUDENT (Logika yang Benar)
//             $totalStudents = DB::table('student')
//                 ->where('status', 'active')
//                 ->where('priceid', $priceId)
//                 ->where('course_time', $courseTime)
//                 // Jadwal siswa (day1 ATAU day2) harus cocok dengan hari assist
//                 ->where(function($query) use ($dayId) {
//                     $query->where('day1', $dayId)
//                           ->orWhere('day2', $dayId);
//                 })
//                 ->count(); // Menggunakan count() untuk mendapatkan jumlah siswa

//             // B. Cari guru utama kelas tersebut
//             $mainTeacher = DB::table('student')
//                 ->where('priceid', $priceId)
//                 ->where('course_time', $courseTime)
//                 ->where(function($query) use ($dayId) {
//                     $query->where('day1', $dayId)
//                           ->orWhere('day2', $dayId);
//                 })
//                 // Opsional: Guru utama tidak sama dengan guru assist
//                 // ->where('student.id_teacher', '!=', $assistItem->id_teacher) 
//                 ->join('teacher as t_main', 't_main.id', '=', 'student.id_teacher')
//                 ->select('t_main.name as main_teacher_name')
//                 ->first();
                
//             $teacherName = 'Assist Class';
//             if ($mainTeacher) {
//                 $teacherName = 'Assist: ' . $mainTeacher->main_teacher_name;
//             }

//             // C. Lengkapi detail
//             $className = $priceDetails[$priceId] ?? 'Unknown Class';
//             $dayName = $dayDetails[$dayId] ?? 'Unknown Day';


//             // D. Tambahkan sebagai objek Laravel Collection Item
//             $mergedSchedule->push((object)[
//                 'teacher_name'      => $teacherName,
//                 'class'             => $className,
//                 'day1_name'         => $dayName, 
//                 'day2_name'         => null, 
//                 'id_teacher'        => $assistItem->id_teacher,
//                 'course_time'       => $courseTime,
//                 'total_students'    => $totalStudents, // Hasil COUNT dari student
//                 'role'              => $assistItem->role,
//                 'priceid'           => $priceId, 
//                 'day1_id'           => $dayId,
//                 'day2_id'           => null,
//             ]);
//         }

//         // Sortir hasil gabungan berdasarkan waktu kelas (opsional)
//         $mergedSchedule = $mergedSchedule->sortBy('course_time');

//         return view('calendar.index', [
//             'data' => $mergedSchedule,
//             'startOfWeekDate' => $startOfWeekDateString,
//             'currentTeacherId' => $teacherId,
//         ]);
//     }


// public function index(Request $request)
//     {
//         $teacherId = null;

//         // --- Autentikasi dan Penentuan Teacher ID ---
//         if (Auth::guard('teacher')->check()) {
//             $teacherId = Auth::guard('teacher')->user()->id;
//         } elseif (Auth::guard('staff')->check()) {
//             $teacherId = $request->input('teacher_id');
//             if (!$teacherId) {
//                 $teacherId = 4; // Fallback default for staff viewing
//             }
//         }

//         if (!$teacherId) {
//             return redirect('/')->with('error', 'Akses ditolak.');
//         }

//         // --- Penentuan Tanggal Awal Pekan ---
//         $startDateParam = $request->input('start_date');

//         try {
//             $startOfWeekDate = $startDateParam
//                 ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY)
//                 : Carbon::now()->startOfWeek(Carbon::MONDAY);
//         } catch (\Exception $e) {
//             $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
//         }

//         $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

//         // 1. QUERY JADWAL UTAMA (Main Teacher Schedule)
//         $mainScheduleSubQuery = DB::table('student')
//             ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid',
//                         DB::raw('COUNT(*) as total_students'),
//                         DB::raw('"main" as role'))
//             ->where('id_teacher', $teacherId)
//             ->where('status', 'active')
//             ->whereNotNull('course_time')
//             ->where(function ($query) {
//                 $query->whereNotNull('day1')
//                       ->orWhereNotNull('day2');
//             })
//             ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

//         $mainSchedule = DB::table(DB::raw('(' . $mainScheduleSubQuery->toSql() . ') as sub'))
//             ->mergeBindings($mainScheduleSubQuery)
//             ->leftJoin('teacher as t', 'sub.id_teacher', '=', 't.id')
//             ->leftJoin('price as p', 'sub.priceid', '=', 'p.id')
//             ->leftJoin('day as d1', 'sub.day1', '=', 'd1.id')
//             ->leftJoin('day as d2', 'sub.day2', '=', 'd2.id')
//             ->select(
//                 't.name as teacher_name',
//                 'p.program as class',
//                 'p.id as price_id',
//                 'd1.day as day1_name',
//                 'd2.day as day2_name',
//                 'sub.id_teacher',
//                 'sub.course_time',
//                 'sub.total_students',
//                 'sub.role',
//                 'sub.priceid',
//                 'sub.day1 as day1_id',
//                 'sub.day2 as day2_id'
//             )
//             ->get();


//         // 2. QUERY JADWAL ASSIST (Assistant Teacher Schedule) - Menggunakan assist_id

//         // Sesi Assist di Day 1
//         $assistDay1Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.assist_id as id_teacher'), // Menggunakan a.assist_id
//                 DB::raw('a.day1 as day_id'),
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('"assist" as role'),

//             )
//             ->where('a.assist_id', $teacherId) // Menggunakan a.assist_id
//             ->where('a.assist_day1', 1)
//             ->whereNotNull('a.assist_id') // Menggunakan a.assist_id
//             ->whereNotNull('a.course_time')
//             ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');


//         // Sesi Assist di Day 2
//         $assistDay2Query = DB::table('attendances as a')
//             ->select(
//                 DB::raw('a.assist_id as id_teacher'), // Menggunakan a.assist_id
//                 DB::raw('a.day2 as day_id'),
//                 'a.course_time',
//                 DB::raw('a.price_id as priceid'),
//                 DB::raw('"assist" as role')
//             )
//             ->where('a.assist_id', $teacherId) // Menggunakan a.assist_id
//             ->where('a.assist_day2', 1)
//             ->whereNotNull('a.assist_id') // Menggunakan a.assist_id
//             ->whereNotNull('a.course_time')
//             ->groupBy('a.assist_id', 'a.day2', 'a.course_time', 'a.price_id');

//         // Gabungkan hasil Day 1 dan Day 2. Menggunakan UNION untuk mendapatkan kombinasi jadwal unik.
//         $assistScheduleUnion = $assistDay1Query->union($assistDay2Query);

//         // Dapatkan data jadwal assist unik
//         $assistScheduleUnique = $assistScheduleUnion->get();


//         // 3. Gabungkan dan Lengkapi Detail Jadwal Assist
        
//         // Ambil data detail tambahan di luar loop untuk efisiensi
//         $priceDetails = DB::table('price')->pluck('program', 'id');
//         $dayDetails = DB::table('day')->pluck('day', 'id');

//         $mergedSchedule = $mainSchedule;

//         foreach ($assistScheduleUnique as $assistItem) {
            
//             $dayId = $assistItem->day_id;
//             $priceId = $assistItem->priceid;
//             $courseTime = $assistItem->course_time;
            
//             // A. HITUNG TOTAL SISWA AKTIF DARI TABEL STUDENT (Logika yang Benar: Mencocokkan Jadwal)
//             $totalStudents = DB::table('student')
//                 ->where('status', 'active')
//                 ->where('priceid', $priceId)
//                 ->where('course_time', $courseTime)
//                 // Jadwal siswa (day1 ATAU day2) harus cocok dengan hari assist
//                 ->where(function($query) use ($dayId) {
//                     $query->where('day1', $dayId)
//                           ->orWhere('day2', $dayId);
//                 })
//                 ->count(); 

//             // B. Cari guru utama kelas tersebut
//             $mainTeacher = DB::table('student')
//                 ->where('priceid', $priceId)
//                 ->where('course_time', $courseTime)
//                 ->where(function($query) use ($dayId) {
//                     $query->where('day1', $dayId)
//                           ->orWhere('day2', $dayId);
//                 })
//                 // Opsional: Guru utama tidak sama dengan guru assist (id_teacher != assist_id)
//                 // ->where('student.id_teacher', '!=', $assistItem->id_teacher) 
//                 ->join('teacher as t_main', 't_main.id', '=', 'student.id_teacher')
//                 ->select('t_main.name as main_teacher_name', 't_main.id as main_teacher_id','student.day1 as main_day1','student.day2 as main_day2')
//                 ->first();
                
//             $teacherName = 'Assist Class';
//             if ($mainTeacher) {
//                 $teacherName = 'Assist: ' . $mainTeacher->main_teacher_name;
//             }

//             // C. Lengkapi detail
//             $className = $priceDetails[$priceId] ?? 'Unknown Class';
//             $dayName = $dayDetails[$dayId] ?? 'Unknown Day';


//             // D. Tambahkan sebagai objek Laravel Collection Item
//             $mergedSchedule->push((object)[
//                 'teacher_name'      => $teacherName,
//                 'class'             => $className,
//                 'day1_name'         => $dayName, 
//                 'day2_name'         => null, 
//                 'id_teacher'        => $assistItem->id_teacher,
//                 'course_time'       => $courseTime,
//                 'total_students'    => $totalStudents, 
//                 'role'              => $assistItem->role,
//                 'priceid'           => $priceId, 
//                 'day1_id'           => $dayId,
//                 'day2_id'           => null,
//                 'main_teacher_id'   => $mainTeacher ? $mainTeacher->main_teacher_id : null,
//                 // 'assist_id'        => $assistItem->id,
//             ]);
//         }

//         // Sortir hasil gabungan berdasarkan waktu kelas (opsional)
//         $mergedSchedule = $mergedSchedule->sortBy('course_time');

//         return view('calendar.index', [
//             'data' => $mergedSchedule,
//             'startOfWeekDate' => $startOfWeekDateString,
//             'currentTeacherId' => $teacherId,
//         ]);
//     }

// }

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

//     // 3. QUERY JADWAL UTAMA (Main Teacher Schedule)
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

//     // 4. QUERY JADWAL ASSIST (Assistant Teacher Schedule) 
    
//     // Sub-Query untuk Assist di Day 1 (dimana assist_day1 = 1)
//     $assistDay1Query = DB::table('attendances as a')
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day1 as day_id'), 
//             DB::raw('NULL as day2_id'), 
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             // Kita tidak bisa COUNT(*) di sini karena ini menghitung baris di attendances, bukan siswa unik per jadwal assist
//             // Namun, karena kita akan mencari siswa di detail, kita biarkan COUNT(*) untuk estimasi jumlah slot.
//             DB::raw('COUNT(*) as total_students'), 
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_id', $teacherId)
//         ->where('a.assist_day1', 1) 
//         ->whereNotNull('a.assist_id')
//         ->whereNotNull('a.course_time')
//         ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');

//     // Sub-Query untuk Assist di Day 2 (dimana assist_day2 = 1)
//     $assistDay2Query = DB::table('attendances as a')
//         ->select(
//             DB::raw('a.assist_id as id_teacher'), 
//             DB::raw('a.day2 as day_id'), 
//             DB::raw('NULL as day2_id'), 
//             'a.course_time',
//             DB::raw('a.price_id as priceid'),
//             DB::raw('COUNT(*) as total_students'), 
//             DB::raw('"assist" as role')
//         )
//         ->where('a.assist_id', $teacherId)
//         ->where('a.assist_day2', 1) 
//         ->whereNotNull('a.assist_id')
//         ->whereNotNull('a.course_time')
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
//             DB::raw('SUM(sub.total_students) as total_students_aggregated') 
//         )
//         ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
//         ->get();


//     // 5. Gabungkan dan Lengkapi Detail Jadwal Assist (PERBAIKAN LOGIKA PENCARIAN GURU UTAMA)
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
            
//             // ** LANGKAH 1 PERBAIKAN: Cari student_id dari attendance_detail **
//             // Kita harus join dengan attendances untuk mendapatkan kriteria waktu/hari
//             $actualStudentAssisted = DB::table('attendances as a')
//                 ->where('a.assist_id', $assistItem->id_teacher) 
//                 ->where('a.price_id', $assistItem->priceid)
//                 ->where('a.course_time', $assistItem->course_time)
//                 ->where(function($query) use ($assistItem) {
//                     $query->where('a.day1', $assistItem->day_id)
//                           ->orWhere('a.day2', $assistItem->day_id);
//                 })
//                 // Join ke attendance_detail untuk mendapatkan student_id
//                 ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
//                 ->select('ad.student_id') 
//                 ->first(); // Ambil ID salah satu siswa yang di-assist
            
//             $mainTeacherName = null;

//             if ($actualStudentAssisted) {
                
//                 // ** LANGKAH 2 PERBAIKAN: Cari Guru Utama berdasarkan ID Siswa **
//                 $mainTeacherId = DB::table('student')
//                     ->where('id', $actualStudentAssisted->student_id) // Kunci: ID Siswa
//                     // Kita asumsikan baris siswa ini masih aktif atau memiliki Guru Utama yang valid
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
//             $assistItem->total_students = $assistItem->total_students_aggregated;

//             if ($mainTeacherName) {
//                 $assistItem->teacher_name = 'Assist: ' . $mainTeacherName;
//             } else {
//                 // Fallback jika tidak ditemukan Guru Utama di tabel student (misalnya kelasnya sudah tidak aktif)
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
// }

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

public function index(Request $request)
{
    $teacherId = null;
    
    // --- 1. OTENTIKASI DAN PENENTUAN ID GURU ---
    if (Auth::guard('teacher')->check()) {
        $teacherId = Auth::guard('teacher')->user()->id;
    } elseif (Auth::guard('staff')->check()) {
        $teacherId = $request->input('teacher_id') ?? 4;
    }

    if (!$teacherId) {
        return redirect('/')->with('error', 'Akses ditolak.');
    }

    // --- 2. PENENTUAN TANGGAL AWAL MINGGU ---
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
            't.name as teacher_name',
            'p.program as class',
            'd1.day as day1_name',
            'd2.day as day2_name',
            'sub.id_teacher',
            'sub.course_time',
            'sub.total_students',
            'sub.role',
            'sub.priceid', 
            'sub.day1 as day1_id', 
            'sub.day2 as day2_id' 
        )
        ->get();

    // Menambahkan student_list (ARRAY) untuk Jadwal Utama
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
        ->select(
            DB::raw('a.assist_id as id_teacher'), 
            DB::raw('a.day1 as day_id'), 
            'a.course_time',
            DB::raw('a.price_id as priceid'),
            DB::raw('COUNT(DISTINCT ad.student_id) as total_students'), 
            DB::raw('"assist" as role')
        )
        ->where('a.assist_day1', 1) 
        ->groupBy('a.assist_id', 'a.day1', 'a.course_time', 'a.price_id');

    $assistDay2Query = (clone $baseAssistQuery)
        ->select(
            DB::raw('a.assist_id as id_teacher'), 
            DB::raw('a.day2 as day_id'), 
            'a.course_time',
            DB::raw('a.price_id as priceid'),
            DB::raw('COUNT(DISTINCT ad.student_id) as total_students'), 
            DB::raw('"assist" as role')
        )
        ->where('a.assist_day2', 1) 
        ->groupBy('a.assist_id', 'a.day2', 'a.course_time', 'a.price_id');

    $assistScheduleUnion = $assistDay1Query->unionAll($assistDay2Query);

    $assistScheduleData = DB::table(DB::raw('(' . $assistScheduleUnion->toSql() . ') as sub'))
        ->mergeBindings($assistScheduleUnion)
        ->select(
            'sub.id_teacher',
            'sub.day_id', 
            'sub.course_time',
            'sub.priceid',
            'sub.role',
            DB::raw('MAX(sub.total_students) as total_students_aggregated') 
        )
        ->groupBy('sub.id_teacher', 'sub.day_id', 'sub.course_time', 'sub.priceid', 'sub.role')
        ->get();

    // --- 5. GABUNGKAN DAN LENGKAPI DETAIL (Termasuk List Student ARRAY) ---
    foreach ($assistScheduleData as $assistItem) {
        $details = DB::table('price as p')
            ->where('p.id', $assistItem->priceid)
            ->join('day as d', 'd.id', '=', DB::raw($assistItem->day_id))
            ->select('p.program as class', 'd.day as day_name')
            ->first();

        if ($details) {
            // Ambil Nama Siswa dan ID Teacher Utama
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
                ->get();

            $studentArray = $studentsInAssist->pluck('name')->toArray();
            
            // Cari Nama Guru Utama (Main Teacher)
            $mainTeacherName = null;
            if ($studentsInAssist->isNotEmpty()) {
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
                'total_students' => count($studentArray),
                'student_list'   => $studentArray, // DATA ARRAY
                'role'           => $assistItem->role,
                'priceid'        => $assistItem->priceid, 
                'day1_id'        => $assistItem->day_id,
                'day2_id'        => null,
            ]);
        }
    }

    $mergedSchedule = $mergedSchedule->sortBy('course_time');

    return view('calendar.index', [
        'data' => $mergedSchedule,
        'startOfWeekDate' => $startOfWeekDateString,
        'currentTeacherId' => $teacherId,
    ]);
}

}