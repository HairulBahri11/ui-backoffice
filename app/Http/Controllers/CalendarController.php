<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
   public function index(Request $request)
    {
        $teacherId = null;
        
        if (Auth::guard('teacher')->check()) {
            $teacherId = Auth::guard('teacher')->user()->id;
        } elseif (Auth::guard('staff')->check()) {
            $teacherId = $request->input('teacher_id');
            if (!$teacherId) {
                $teacherId = 4; // Fallback default for staff viewing
            }
        }

        if (!$teacherId) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $startDateParam = $request->input('start_date');
        
        try {
            $startOfWeekDate = $startDateParam 
                ? Carbon::parse($startDateParam)->startOfWeek(Carbon::MONDAY) 
                : Carbon::now()->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $startOfWeekDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        
        $startOfWeekDateString = $startOfWeekDate->format('Y-m-d');

        // Sub-query: Get student count per session
        $subQuery = DB::table('student')
            ->select('id_teacher', 'day1', 'day2', 'course_time', 'priceid', 
                     DB::raw('COUNT(*) as total_entri_duplikat'))
            ->where('id_teacher', $teacherId)
            ->where('status', 'active')
            ->whereNotNull('course_time')
            ->where(function ($query) {
                $query->whereNotNull('day1')
                      ->orWhereNotNull('day2');
            })
            ->groupBy('id_teacher', 'day1', 'day2', 'course_time', 'priceid');

        // Main query: Join with related tables
        $schedule = DB::table(DB::raw('(' . $subQuery->toSql() . ') as sub'))
            ->mergeBindings($subQuery)
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
                'sub.total_entri_duplikat'
            )
            ->get();
            
        return view('calendar.index', [
            'data' => $schedule,
            'startOfWeekDate' => $startOfWeekDateString,
            'currentTeacherId' => $teacherId,
        ]);
    }
}
