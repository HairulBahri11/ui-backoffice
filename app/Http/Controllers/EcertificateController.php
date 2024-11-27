<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Price;
use App\Models\Students;
use App\Models\TestItems;
use App\Models\Tests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EcertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $where = '';
            $having = '';
            $level = Price::get();
            if ($request->level) {
                $adult = Price::findOrFail($request->level);
                if (Auth::guard('teacher')->check() == true) {
                    $where = 'AND id_teacher = ' . Auth::guard('teacher')->user()->id;
                }
                if ($request->level && Auth::guard('staff')->check() == true) {
                    $where = $where . ' AND priceid = ' . $request->level;
                }
                if ($request->level && Auth::guard('teacher')->check() == true) {
                    $where = $where . ' AND priceid = ' . $request->level . ' AND id_teacher =' . Auth::guard('teacher')->user()->id;
                }
                if (strpos($adult->program, "Adult") !== false || strpos($adult->program, "Conversation") !== false) {
                    $having = 'HAVING COUNT(DISTINCT CASE WHEN ss.test_id = 2 THEN ss.student_id END) > 0';
                    $where .= ' AND ss.test_id = 2';
                } else if (strpos($adult->program, "Advance") !== false) {
                    $having = 'HAVING COUNT(DISTINCT CASE WHEN ss.test_id = 1 THEN ss.student_id END) > 0';
                    $where .= ' AND ss.test_id = 1';
                } else {
                    $having = 'HAVING COUNT(DISTINCT CASE WHEN ss.test_id = 3 THEN ss.student_id END) > 0';
                    $where .= ' AND ss.test_id = 3';
                }

                $class = DB::select("SELECT priceid, day1, day2, course_time, student.status as status_student, id_teacher, price.level, price.program, day_1.day AS day_one, day_2.day AS day_two, teacher.name AS teacher_name, student.id_teacher AS teacher_id, student.day1 AS d1, student.day2 AS d2 FROM student JOIN price ON student.priceid = price.id JOIN student_scores AS ss ON ss.student_id = student.id JOIN day day_1 ON student.day1 = day_1.id JOIN day day_2 ON student.day2 = day_2.id JOIN teacher ON student.id_teacher = teacher.id WHERE day1 IS NOT NULL AND day2 IS NOT NULL AND course_time IS NOT NULL AND id_teacher IS NOT NULL AND ss.price_id = price.id AND student.status = 'ACTIVE'
                -- AND (student.is_certificate = 0 OR student.is_certificate IS NULL)
                 $where GROUP BY priceid, day1, day2, course_time, id_teacher, price.level, price.program, day_one, day_two, teacher_name, teacher_id, d1, d2 $having ORDER BY priceid ASC, day1, course_time;");
            } else {
                $class = [];
            }
            return view('e-certificate.index', compact('class', 'level'));
        } catch (\Throwable $th) {
            throw $th;
        }
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
        if ($request->type != 'show') {
            $request->validate([
                'status.*' => 'required',
            ]);
        }
        DB::beginTransaction();
        try {
            if ($request->type == 'show') {
                if ($request->status != true) {
                    $followUp = FollowUp::where('student_id', $request->student_id)->first();
                    // change status student
                    Students::where('id', $followUp->student_id)->update([
                        'is_follow_up' => '0',
                        'priceid' => $followUp->old_price_id,
                        'day1' => $followUp->old_day_1,
                        'day2' => $followUp->old_day_2,
                        'id_teacher' => $followUp->old_teacher_id,
                        'course_time' => $followUp->course_time,
                        "is_failed_promoted" => '1',
                    ]);
                    // Delete Follow Up
                    $followUp->delete();
                } else if ($request->status == true) {
                    $followUp = FollowUp::where('student_id', $request->student_id)->first();
                    // change status student
                    Students::where('id', $followUp->student_id)->update([
                        'is_follow_up' => '0',
                        'date_certificate' => $request->date_certificate,
                        'is_certificate' => true,
                    ]);
                    // Delete Follow Up
                    $followUp->delete();
                } else {
                }
            } else {
                foreach ($request->student_id as $key => $value) {
                    $students = Students::find($value);
                    if ($request->status[$key] == '0') {
                        // Failed
                        Students::where('id', $value)->update([
                            'is_failed_promoted' => '1',
                        ]);
                    } else if ($request->status[$key] == '1') {
                        // Passed
                        Students::where('id', $value)->update([
                            'is_follow_up' => '0',
                            'date_certificate' => $request->date_certificate,
                            'is_certificate' => true,
                        ]);
                    } else {
                        // Follow Up
                        $followUp = new FollowUp();
                        $followUp->student_id = $value;
                        $followUp->old_price_id = $students->priceid;
                        $followUp->old_day_1 = $students->day1;
                        $followUp->old_day_2 = $students->day2;
                        $followUp->old_teacher_id = $students->id_teacher;
                        $followUp->course_time = $students->course_time;
                        $followUp->save();

                        $students->is_follow_up = '1';
                        $students->save();
                    }
                }
            }

            DB::commit();
            return redirect('/e-certificate')->with('message', 'Berhasil mengupdate');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($request->type == 'show') {
            // $students = Students::with('score')->where('id', $id)->where('priceid', $request->price_id)->where('day1', $request->day1)->where('day2', $request->day2)->where('id_teacher', $request->teacher)->where('course_time', $request->time)
            //     // ->where('is_certificate', false)
            //     // ->where(function ($query) {
            //     //     $query->orWhere('is_follow_up', true);
            //     // })
            //     ->get();
            $students = FollowUp::with('class', 'teacher', 'student')->where('student_id', $id)->get();
            $class = Price::find($request->price_id);
            // return
            //     DB::table('student_scores')
            //     ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
            //     ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
            //     ->where('student_id', 2762)
            //     ->where('price_id', $class->id)
            //     ->where('test_id', 1)
            //     ->where('student_score_details.test_item_id', 1)
            //     ->get();
            $test = Tests::get();
            if ($request->price_id == 1 || $request->price_id == 2 || $request->price_id == 3 || $request->price_id == 4 || $request->price_id == 5 || $request->price_id == 6) {
                $testItem = TestItems::where('id', '!=', 5)->where('id', '!=', 6)->get();
            } else {
                $testItem = TestItems::get();
            }
            return view('e-certificate.show', compact('students', 'class', 'testItem', 'test'));
        } else {
            $students = Students::with('score')->where('priceid', $id)->where('day1', $request->day1)->where('day2', $request->day2)->where('id_teacher', $request->teacher)->where('course_time', $request->time)
                // ->where('is_certificate', false)
                // ->where(function ($query) {
                //     $query->orWhere('is_follow_up', true);
                // })
                ->get();
            $class = Price::find($id);
            $test = Tests::get();
            if ($id == 1 || $id == 2 || $id == 3 || $id == 4 || $id == 5 || $id == 6) {
                $testItem = TestItems::where('id', '!=', 5)->where('id', '!=', 6)->get();
            } else {
                $testItem = TestItems::get();
            }
            return view('e-certificate.form', compact('students', 'class', 'testItem', 'test'));
        }
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
        DB::beginTransaction();
        try {
            if ($request->type == 'done') {
                $student = Students::find($id);
                $student->is_certificate = true;
                $student->date_certificate = $request->date_certificate;
                $student->save();
                DB::commit();
            }
            return redirect('/e-certificate')->with('message', 'Berhasil mengupdate');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
