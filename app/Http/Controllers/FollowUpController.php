<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Price;
use App\Models\Students;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $class = Price::get();
        $teacher = Teacher::get();
        $day = DB::table('day')->get();
        $students = FollowUp::with('class', 'teacher', 'student')->select('follow_up.*', 'd1.day as day1', 'd2.day as day2')
            ->join('day as d1', 'd1.id', 'follow_up.old_day_1')
            ->join('day as d2', 'd2.id', 'follow_up.old_day_2')
             ->join('student', 'student.id', 'follow_up.student_id')
            ->where('student.status', 'ACTIVE')
            ->orderBy('follow_up.id', 'DESC');

        if (Auth::guard('teacher')->check() == true) {
            $students = $students->where('old_teacher_id', Auth::guard('teacher')->user()->id)
                /* ->whereNull('is_level_up') */;
        } else {
            // $students = $students->whereNotNull('is_level_up');
        }
        $students = $students->get();
        return view('follow-up.index', compact('students', 'class', 'teacher', 'day'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Insert data in follow up table
            foreach ($request->id as $key => $value) {
                $cekFollowUp = FollowUp::where('student_id', $value)->first();
                if (!$cekFollowUp) {
                    $followUp = new FollowUp();
                    $followUp->student_id = $value;
                    $followUp->old_price_id = $request->old_class;
                    $followUp->old_day_1 = $request->old_day1;
                    $followUp->old_day_2 = $request->old_day2;
                    $followUp->old_teacher_id = $request->old_teacher;
                    $followUp->course_time = $request->old_time;
                    $followUp->save();

                    // change status student
                    Students::where('id', $value)->update([
                        'is_follow_up' => '1',
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('status', 'Success bulk update');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed bulk update ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed bulk update ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->promoted != 'true') {
                $followUp = FollowUp::where('id', $id)->first();
                // change status student
                Students::where('id', $followUp->student_id)->update([
                    'is_follow_up' => '0',
                    'priceid' => $followUp->old_price_id,
                    'day1' => $followUp->old_day_1,
                    'day2' => $followUp->old_day_2,
                    'id_teacher' => $followUp->old_teacher_id,
                    'course_time' => $followUp->course_time,
                ]);
                // Delete Follow Up
                $followUp->delete();
            } else {
                // if ($request->new_class == '') {
                //     # code...
                // }
                $followUp = FollowUp::where('id', $id)->first();
                // change status student
                Students::where('id', $followUp->student_id)->update([
                    'is_follow_up' => '0',
                    'priceid' => $request->new_class,
                    'day1' => $request->new_day1,
                    'day2' => $request->new_day2,
                    'id_teacher' => $request->new_teacher,
                    'course_time' => $request->new_course_time,
                ]);
                // Delete Follow Up
                $followUp->delete();
            }

            DB::commit();
            return redirect('/follow-up')->with('status', 'Success update data');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/follow-up')->with('error', 'Failed update data ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return redirect('/follow-up')->with('error', 'Failed update database ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->student_id as $key => $value) {
                $followUp = FollowUp::where('student_id', $value)->first();
                $followUp->is_level_up = $request->is_passed[$value];
                $followUp->save();
                // FollowUp::where('id', $value)->delete();
                // // change status student
                // Students::where('id', $value)->update([
                //     'is_follow_up' => '0',
                // ]);
            }
            DB::commit();
            return redirect('/follow-up')->with('status', 'Success bulk update');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/follow-up')->with('error', 'Failed bulk update ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return redirect('/follow-up')->with('error', 'Failed bulk update ' . $e->getMessage());
        }
    }
}
