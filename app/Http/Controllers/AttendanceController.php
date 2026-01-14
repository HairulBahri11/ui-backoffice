<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\AttendanceDetailPoint;
use App\Models\Mutasi;
use App\Models\OrderReview;
use App\Models\PointCategories;
use App\Models\PointHistory;
use App\Models\Price;
use App\Models\Students;
use App\Models\StudentScore;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $class = Price::all();
        // if (Auth::gurad) {
        //     # code...
        // }
        $where = '';
        $teachers = Teacher::get();
        $level = Price::get();
        if (Auth::guard('teacher')->check() == true) {
            $where = 'AND id_teacher = ' . Auth::guard('teacher')->user()->id;
        }

        if (Auth::guard('staff')->check() == true && Auth::guard('staff')->user()->id != 7) {
            $where = $where . ' AND id_staff = ' . Auth::guard('staff')->user()->id;
        }
        if ($request->teacher) {
            $where = $where . ' AND id_teacher = ' . $request->teacher;
        }
        if ($request->branch) {
            $where = $where . ' AND branch_id = ' . $request->branch;
        }
        if ($request->level && Auth::guard('staff')->check() == true) {
            $where = $where . ' AND priceid = ' . $request->level;
        }
        if ($request->level && Auth::guard('teacher')->check() == true) {
            $where = $where . ' AND priceid = ' . $request->level . ' AND id_teacher =' . Auth::guard('teacher')->user()->id;
        }
        if ($request->day && Auth::guard('teacher')->check() == true) {
            $where = $where . ' AND (day1 = ' . $request->day . ' OR day2 = ' . $request->day . ') AND id_teacher =' . Auth::guard('teacher')->user()->id;
        }
        $class = DB::select("SELECT DISTINCT priceid,day1,day2,course_time,id_teacher,price.level,price.program,day_1.day day_one,day_2.day day_two,teacher.name teacher_name, is_class_new, branch.location from student
        join price on student.priceid = price.id
        join day day_1 on student.day1 = day_1.id
        join day day_2 on student.day2 = day_2.id
        join branch on student.branch_id = branch.id
        -- join attendance_details ad on student.id = ad.student_id
        join teacher on student.id_teacher = teacher.id  WHERE day1 is NOT null AND day2 is NOT null AND course_time is NOT null AND id_teacher is NOT null AND student.status = 'ACTIVE' $where ORDER BY priceid ASC, day1,course_time;");

        $private = [];
        $general = [];
        $semiPrivate = [];
        foreach ($class as $key => $value) {
            if ($value->level == 'Private') {
                if ($value->program == 'Private') {
                    array_push($private, $value);
                } else {
                    array_push($semiPrivate, $value);
                }
            } else {
                array_push($general, $value);
            }
        }
        // dd($general);
        $day = DB::table('day',)->get();

        $isNew = Attendance::where('is_class_new', '1')->get();
        // // dd($checkAbsent->toArray());
        // $already_absent = [];
        // foreach ($checkAbsent as $key => $value) {
        //     $check = AttendanceDetail::where('attendance_id', $value->id)->where('is_absent', '1')->get();
        //     array_push($already_absent, $check);
        // };
        // dd($already_absent);


        return view('attendance.index', compact('private', 'general', 'day', 'teachers', 'level', 'semiPrivate', 'isNew'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($priceId, Request $request)
    {
        $reqDay1 = $request->day1;
        $reqDay2 = $request->day2;
        $reqTime = $request->time;
        $reqTeacher = $request->teacher;
        // $reqNew = $request->new;
        // $reqAmpm = $request->ampm;
        $student = "";
        $whereRaw = "";
        $whereStudent = '';
        $day = DB::table('day')->get();
        $cek = Attendance::where('price_id', $priceId)
            ->where('date', date('Y-m-d'))
            ->where('day1', $reqDay1)
            ->where('day2', $reqDay2)
            ->where('course_time', $reqTime)
            ->where('teacher_id', $reqTeacher)
            // ->where('is_class_new', $reqNew)
            ->orderBy('id', 'DESC')
            ->first();
        // $agenda = [];


        $tgl_mutasi = '';
        $mutasi_teacher = '';
        if ($cek != null) {
            $mutasi_teacher = $cek->mutasi_teacher;
            $tgl_mutasi = $cek->tgl_mutasi;
        }




        $class = Price::where('id', $priceId)->first();

        $title = $class->level == 'Private' ? 'Private Class ' . $class->program : 'Regular';
        if ($cek) {
            $detail = AttendanceDetail::where('attendance_id', $cek->id)->get();
            foreach ($detail as $key => $id) {
                // multiple
                $points = [];
                $attPoint = AttendanceDetailPoint::where('attendance_detail_id', $id->id)
                    ->select('point_category_id')
                    ->get();

                foreach ($attPoint as $idp) {
                    array_push($points, intval($idp->point_category_id));
                }
                $id['category'] = $points;

                // manual
                // $points = [];
                // $catPoints = [];
                // $attPoint = AttendanceDetailPoint::where('attendance_detail_id', $id->id)
                //     ->get();

                // foreach ($attPoint as $idp) {
                //     array_push($points, $idp->point);
                //     array_push($catPoints, $idp->point_category);
                // }
                // $id->categoryPoint = $points != null ? $points[0] : '';
                // $id->category = $catPoints != null ? $catPoints[0] : '';
            }
            $data = (object)[
                'type' => 'update',
                'id' => $class->id,
                'attendanceId' => $cek->id,
                'comment' => $cek->activity,
                'textBook' => $cek->text_book,
                'excerciseBook' => $cek->excercise_book,
                'students' => $detail,
                'is_presence' => $cek->is_presence,
                'id_test' => $cek->id_test,
                'date_review' => $cek->date_review,
                'date_test' => $cek->date_test,
            ];
            // return $data;
        } else {
            // $agenda = [];
            $data = (object)[
                'type' => 'create',
                'id' => $class->id,
                'attendanceId' => 0,
                'comment' => '',
                'textBook' => '',
                'excerciseBook' => '',
                'students' => [],
                'is_presence' => '',
                'id_test' => '',
                'date_review' => '',
                'date_test' => '',
            ];
        }


        $student = Students::where('status', 'ACTIVE')->where('priceid', $class->id)
            ->where("day1", $reqDay1)
            ->where("day2", $reqDay2)
            ->where('course_time', $reqTime)
            // ->where('is_class_new', $request->new)
            ->where('is_follow_up', '!=', '1');
        if ($request->student) {
            $student = $student->where('id', $request->student);
        }
        if (Auth::guard('teacher')->check() == true) {
            $student = $student->where('id_teacher', Auth::guard('teacher')->user()->id);
        } else {
            $student = $student->where('id_teacher', $reqTeacher);
        }

        $student = $student->get();


        foreach ($student as $keyS => $valueS) {
            $or = $keyS + 1 != count($student) ? ' or ' : '';
            $whereStudent .= 'student_id = ' . $valueS->id . $or;
        }
        $whereRaw = '(' . $whereStudent . ')';
        $pointCategories = PointCategories::where('id', '!=', 5)->orderBy('point', 'ASC')->get();
        $attendance = Attendance::with('detail')->where('price_id', $priceId)
            ->where('day1', $reqDay1)
            ->where('day2', $reqDay2)
            ->where('course_time', $reqTime)
            ->where('teacher_id', $reqTeacher)
            // ->where('is_class_new', $request->new)
            ->orderBy('id', 'DESC');
        if ($request->student) {
            $attendance = $attendance->whereHas('detail', function ($q) use ($request) {
                $q->where('student_id', $request->student);
            });
        } else {
            $attendance = $attendance->whereHas('detail', function ($q) use ($whereRaw) {
                $q->whereRaw($whereRaw);
            });
        }
        $all_attendence = $attendance->get();
        $attendance = $attendance->paginate(3);

        if (count($student) != 0) {

            return view('attendance.form', compact(
                'attendance',
                'all_attendence',
                'title',
                'data',
                'student',
                'pointCategories',
                'day',
                'priceId',
                'reqDay1',
                'reqDay2',
                'reqTeacher',
                'reqTime',
                'mutasi_teacher',
                'tgl_mutasi'
            ));
        } else {
            $inStudent = Students::where('status', 'INACTIVE')->where('priceid', $class->id)
                ->where("day1", $reqDay1)
                ->where("day2", $reqDay2)
                ->where('course_time', $reqTime);
            if (Auth::guard('teacher')->check() == true) {
                $inStudent = $inStudent->where('id_teacher', Auth::guard('teacher')->user()->id);
            } else {
                $inStudent = $inStudent->where('id_teacher', $reqTeacher);
            }
            $inStudent = $inStudent->update([
                'day1' => null,
                'day2' => null,
                'course_time' => null,
                'id_teacher' => null,
                'id_staff' => null,
            ]);

            return redirect('/attendance/class')->with('error', 'There is no student');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->cekAllAbsen == true) {
                $idTest = "";
                if ($request->id_test != null) {
                    $count = count($request->id_test);
                    for ($i = 0; $i < $count; $i++) {
                        $file = $request->id_test[$i];
                        $comma = $i + 1 != $count ? ',' : '';
                        $idTest .= $file . $comma;
                    }
                }
                $countStudent = 0;
                $pointCategories = PointCategories::all();
                $createAttendance = [
                    'price_id' => $request->priceId,
                    'day1' => (int)$request->day1,
                    'day2' => (int)$request->day2,
                    'course_time' => $request->time,
                    'date' => date('Y-m-d'),
                    'teacher_id' => $request->teacher,
                    'activity' => $request->comment,
                    'text_book' => $request->textBook,
                    'excercise_book' => $request->excerciseBook,
                    'is_presence' => true,
                    'id_test' => $idTest,
                    'date_review' => $request->date_review,
                    'date_test' => $request->date_test,
                    'is_class_new' => $request->is_new,
                ];
                $attendance = Attendance::create($createAttendance);
                for ($i = 0; $i < count($request->totalPoint); $i++) {
                    if (count($request->isAbsent[$i + 1]) > 1) {
                        $countStudent += 1;
                    }
                    // $detail = AttendanceDetail::create([
                    //     'attendance_id' => $attendance->id,
                    //     'student_id' => $request->studentId[$i],
                    //     'is_absent' => count($request->isAbsent[$i + 1]) > 1 ? '1' : '0',
                    //     'total_point' => $request->totalPoint[$i],
                    //     'is_permission' => count($request->isPermission[$i + 1]) > 1 ? true : false,
                    //     'is_alpha' => $alphaTrue,
                    // ]);
                    $detail = new AttendanceDetail();
                    $detail->attendance_id = $attendance->id;
                    $detail->student_id = $request->studentId[$i];
                    if (count($request->isAbsent[$i + 1]) > 1) {
                        $detail->is_absent = '1';
                        $detail->is_permission = false;
                    } else {
                        $detail->is_absent = '0';
                        $detail->is_permission = true;
                    }
                    $detail->total_point = $request->totalPoint[$i];
                    if (count($request->isPermission[$i + 1]) > 1) {
                        $detail->is_permission = true;
                    }
                    if (count($request->isAlpha[$i + 1]) > 1) {
                        $detail->is_alpha = true;
                        $detail->is_permission = false;
                    } else {
                        $detail->is_alpha = false;
                        $detail->is_permission = true;
                    }
                    $detail->save();

                    $student = Students::where('id', $request->studentId[$i])->first();
                    Students::where('id', $request->studentId[$i])->update([
                        'total_point' => $student->total_point + $request->totalPoint[$i],
                    ]);
                    if (count($request->isAbsent[$i + 1]) != 1) {
                        PointHistory::create([
                            'student_id' => $request->studentId[$i],
                            'date' => date('Y-m-d'),
                            'total_point' => $request->isAbsentPoint[$i + 1],
                            'type' => 'in',
                            'keterangan' => 'Present',
                            'balance_in_advanced' => $student->total_point,
                        ]);
                    }
                    if ($request->birthdaypoint[$i + 1][0] != 0) {
                        AttendanceDetailPoint::create([
                            'attendance_detail_id' => $detail->id,
                            'point_category_id' => 5,
                            'point' => 30,
                        ]);
                        PointHistory::create([
                            'student_id' => $request->studentId[$i],
                            'date' => date('Y-m-d'),
                            'total_point' => 30,
                            'type' => 'in',
                            'keterangan' => 'Extra Birthday',
                            'balance_in_advanced' => $student->total_point,
                        ]);
                        Students::where('id', $request->studentId[$i])->update([
                            'total_point' => $student->total_point + 30,
                        ]);
                    }

                    // Multiple
                    if ($request->categories) {
                        if (array_key_exists($i + 1, $request->categories)) {
                            for ($x = 0; $x < count($request->categories[$i + 1]); $x++) {
                                $pos = 0;
                                foreach ($pointCategories as $key => $value) {
                                    if ($request->categories[$i + 1][$x] == $value['id']) {
                                        $pos = $key;
                                    }
                                }
                                AttendanceDetailPoint::create([
                                    'attendance_detail_id' => $detail->id,
                                    'point_category_id' => $request->categories[$i + 1][$x],
                                    'point' => $pointCategories[$pos]->point,
                                ]);
                                if ($request->totalPoint[$i] > 0) {
                                    $totalPointCategory = $pointCategories[$pos]->point;
                                    PointHistory::create([
                                        'student_id' => $request->studentId[$i],
                                        'date' => date('Y-m-d'),
                                        'total_point' => $pointCategories[$pos]->point,
                                        'type' => 'in',
                                        'keterangan' => $pointCategories[$pos]->name,
                                        'balance_in_advanced' => $student->total_point,
                                    ]);
                                }
                                // return ([
                                //     'attendance_detail_id' => $detail->id,
                                //     'point_category_id' => $request->categories[$i + 1][$x-1],
                                //     'point' => $pointCategories[$pos]->point,
                                // ]);
                            }
                        }
                    }

                    // Manual
                    // if ($request->category) {
                    //     if (array_key_exists($i + 1, $request->category)) {
                    //         for ($x = 0; $x < count($request->category[$i + 1]); $x++) {
                    //             if ($request->category[$i + 1][$x] != null && $request->point_category[$i + 1][$x] != null) {
                    //                 $attendanceDetailPoint = new AttendanceDetailPoint;
                    //                 $attendanceDetailPoint->attendance_detail_id = $detail->id;
                    //                 $attendanceDetailPoint->point_category = $request->category[$i + 1][$x];
                    //                 $attendanceDetailPoint->point = $request->point_category[$i + 1][$x];
                    //                 $attendanceDetailPoint->save();
                    //                 if ($request->totalPoint[$i] > 0) {
                    //                     PointHistory::create([
                    //                         'student_id' => $request->studentId[$i],
                    //                         'date' => date('Y-m-d'),
                    //                         'total_point' =>  $request->point_category[$i + 1][$x],
                    //                         'type' => 'in',
                    //                         'keterangan' =>  $request->category[$i + 1][$x],
                    //                     ]);
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }
                }
                $or_rev = null;
                $or_test = null;
                $class = Price::find($request->priceId);
                $day1 = DB::table('day')->where('id', (int)$request->day1)->first();
                $day2 = DB::table('day')->where('id', (int)$request->day2)->first();
                if ($request->date_review) {
                    foreach ($request->id_test as $keyReview => $valueReview) {
                        $or_rev = OrderReview::create(array(
                            'id_attendance' => $attendance->id,
                            'test_id' => $valueReview,
                            'id_teacher' => $request->teacher,
                            'class' => $class->program . ' ' . substr($day1->day, 0, 3) . ' ' . substr($day2->day, 0, 3) . ' On ' . $request->time,
                            'review_test' => 'Review ' . $valueReview,
                            'due_date' => $request->date_review,
                            // 'qty' => $countStudent,
                            'qty' => count($request->studentId),
                            'type' => 'review',
                        ));
                    }
                }
                if ($request->date_test) {
                    foreach ($request->id_test as $keyTest => $valueTest) {
                        $or_test = OrderReview::create(array(
                            'id_attendance' => $attendance->id,
                            'test_id' => $valueTest,
                            'id_teacher' => $request->teacher,
                            'class' => $class->program . ' ' . substr($day1->day, 0, 3) . ' ' . substr($day2->day, 0, 3) . ' On ' . $request->time,
                            'review_test' => 'Test ' . $valueTest,
                            'due_date' => $request->date_test,
                            'qty' => count($request->studentId),
                            // 'qty' => $countStudent,
                            'type' => 'test',
                        ));
                    }
                }
                // DB::commit();
                // Check if either review or test was created and save them
                if ($or_rev != null || $or_test != null) {
                    $or_rev->save();
                    $or_test->save();

                    if ($or_rev && $or_test) {
                        $message =
                            'Success! Both Review and Test have been recorded!';
                    } elseif ($or_test) {
                        $message = ' Test Recorded! Test ID: ' . $or_test->test_id . ' | Due Date: ' . $or_test->due_date;
                    } elseif ($or_rev) {
                        $message = ' Review Recorded! Review ID: ' . $or_rev->test_id . ' | Due Date: ' . $or_rev->due_date;
                    } else {
                        $message = 'Oops! Neither Test nor Review could be recorded.';
                    }
                    DB::commit();
                } else {
                    DB::commit();
                    $message = 'Attendance and Agenda have been Recorded';
                }
                return redirect('/attendance/class')->with('message', "Student`s Schedule Updated!");
            } else {
                if ($request->date_review || $request->date_test) {
                    $class = Price::find($request->priceId);
                    $day1 = DB::table('day')->where('id', (int)$request->day1)->first();
                    $day2 = DB::table('day')->where('id', (int)$request->day2)->first();
                    if ($request->date_review) {
                        foreach ($request->id_test as $keyReview => $valueReview) {
                            OrderReview::create(array(
                                // 'id_attendance' => $attendance->id,
                                'test_id' => $valueReview,
                                'id_teacher' => $request->teacher,
                                'class' => $class->program . ' ' . substr($day1->day, 0, 3) . ' ' . substr($day2->day, 0, 3) . ' On ' . $request->time,
                                'review_test' => 'Review ' . $valueReview,
                                'due_date' => $request->date_review,
                                // 'qty' => $countStudent,
                                'qty' => count($request->studentId),
                                'type' => 'review',
                            ));
                        }
                    }
                    if ($request->date_test) {
                        foreach ($request->id_test as $keyTest => $valueTest) {
                            OrderReview::create(array(
                                // 'id_attendance' => $attendance->id,
                                'test_id' => $valueTest,
                                'id_teacher' => $request->teacher,
                                'class' => $class->program . ' ' . substr($day1->day, 0, 3) . ' ' . substr($day2->day, 0, 3) . ' On ' . $request->time,
                                'review_test' => 'Test ' . $valueTest,
                                'due_date' => $request->date_test,
                                'qty' => count($request->studentId),
                                // 'qty' => $countStudent,
                                'type' => 'test',
                            ));
                        }
                    }
                    DB::commit();
                    return redirect('/attendance/class')->with('message', "Student`s Schedule Updated!");
                } else {
                    DB::rollback();
                    return redirect()->back()->with('status', 'Schedule failed to update');
                }
            }
        } catch (\Throwable $th) {
            // ddd($th);
            DB::rollback();
            return $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $reqDay1 = $request->day1;
        $reqDay2 = $request->day2;
        $reqTime = $request->time;
        $reqNew = $request->new;
        $reqTeacher = $request->teacher;
        $priceId = $request->class;
        $student = "";
        $whereRaw = "";
        $whereStudent = '';
        $day = DB::table('day')->get();
        $cek = Attendance::where('id', $id)
            ->orderBy('id', 'DESC')
            ->first();
        // $agenda = [];

        $tgl_mutasi = '';
        $mutasi_teacher = '';
        if ($cek != null) {
            $mutasi_teacher = $cek->mutasi_teacher;
            $tgl_mutasi = $cek->tgl_mutasi;
        }

        $class = Price::where('id', $priceId)->first();
        $title = $class->level == 'Private' ? 'Private Class ' . $class->program : 'Regular';
        if ($cek) {
            $detail = AttendanceDetail::where('attendance_id', $cek->id)->get();
            foreach ($detail as $key => $idDetail) {
                // multiple
                $points = [];
                $attPoint = AttendanceDetailPoint::where('attendance_detail_id', $idDetail->id)
                    ->select('point_category_id')
                    ->get();

                foreach ($attPoint as $idp) {
                    array_push($points, intval($idp->point_category_id));
                }
                $idDetail['category'] = $points;

                // manual
                // $points = [];
                // $catPoints = [];
                // $attPoint = AttendanceDetailPoint::where('attendance_detail_id', $id->id)
                //     ->get();

                // foreach ($attPoint as $idp) {
                //     array_push($points, $idp->point);
                //     array_push($catPoints, $idp->point_category);
                // }
                // $id->categoryPoint = $points != null ? $points[0] : '';
                // $id->category = $catPoints != null ? $catPoints[0] : '';
            }
            $data = (object)[
                'type' => 'update',
                'id' => $class->id,
                'attendanceId' => $cek->id,
                'comment' => $cek->activity,
                'textBook' => $cek->text_book,
                'excerciseBook' => $cek->excercise_book,
                'students' => $detail,
                'is_presence' => $cek->is_presence,
                'id_test' => $cek->id_test,
                'date_review' => $cek->date_review,
                'date_test' => $cek->date_test,
            ];
        } else {
            // $agenda = [];
            $data = (object)[
                'type' => 'create',
                'id' => $class->id,
                'attendanceId' => 0,
                'comment' => '',
                'textBook' => '',
                'excerciseBook' => '',
                'students' => [],
                'is_presence' => '',
                'id_test' => '',
                'date_review' => '',
                'date_test' => '',
            ];
        }


        $student = Students::where('status', 'ACTIVE')->where('priceid', $class->id)
            ->where("day1", $reqDay1)
            ->where("day2", $reqDay2)
            ->where('course_time', $reqTime)
            ->where('is_class_new', $reqNew);
        if (Auth::guard('teacher')->check() == true) {
            $student = $student->where('id_teacher', Auth::guard('teacher')->user()->id);
        } else {
            $student = $student->where('id_teacher', $reqTeacher);
        }

        $student = $student->get();
        foreach ($student as $keyS => $valueS) {
            $or = $keyS + 1 != count($student) ? ' or ' : '';
            $whereStudent .= 'student_id = ' . $valueS->id . $or;
        }
        $whereRaw = '(' . $whereStudent . ')';

        $pointCategories = PointCategories::where('id', '!=', 5)->orderBy('point', 'ASC')->get();
        $attendance = Attendance::where('id', $id)
            ->where('is_class_new', $request->new)
            ->orderBy('id', 'DESC');
        if ($request->student) {
            $attendance = $attendance->whereHas('detail', function ($q) use ($request) {
                $q->where('student_id', $request->student);
            });
        } else {
            $attendance = $attendance->whereHas('detail', function ($q) use ($whereRaw) {
                $q->whereRaw($whereRaw);
            });
        }
        $attendance = $attendance->get();

        return view('attendance.form', compact(
            'attendance',
            'title',
            'data',
            'student',
            'pointCategories',
            'day',
            'priceId',
            'reqDay1',
            'reqDay2',
            'reqTeacher',
            'reqTime',
            'mutasi_teacher',
            'tgl_mutasi'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $attendance)
    {
        DB::beginTransaction();
        try {
            // if ($request->cekAllAbsen == true) {
            $countStudent = 0;
            $pointCategories = PointCategories::all();
            Attendance::where('id', $request->attendanceId)->update([
                'price_id' => $request->priceId,
                'teacher_id' => $request->teacher,
                'activity' => $request->comment,
                'text_book' => $request->textBook,
                'excercise_book' => $request->excerciseBook,
                'id_test' => $request->id_test,
                'date_review' => $request->date_review,
                'date_test' => $request->date_test,
            ]);
            for ($i = 0; $i < count($request->totalPoint); $i++) {
                $dataDetail = AttendanceDetail::where('attendance_id', $request->attendanceId)
                    ->where('student_id', $request->studentId[$i]);
                if ($dataDetail->count() == 0) {
                    //insert
                    if (count($request->isAbsent[$i + 1]) > 1) {
                        $countStudent += 1;
                    }
                    // $insert = AttendanceDetail::create([
                    //     'attendance_id' => $request->attendanceId,
                    //     'student_id' => $request->studentId[$i],
                    //     'is_absent' => count($request->isAbsent[$i + 1]) > 1 ? '1' : '0',
                    //     'total_point' => $request->totalPoint[$i],
                    //     'is_permission' => count($request->isPermission[$i + 1]) > 1 ? true : false,
                    //     'is_alpha' => count($request->isAlpha[$i + 1]) > 1 ? true : false,
                    // ]);
                    $insert = new AttendanceDetail();
                    $insert->attendance_id = $request->attendanceId;
                    $insert->student_id = $request->studentId[$i];
                    if (count($request->isAbsent[$i + 1]) > 1) {
                        $insert->is_absent = '1';
                        $insert->is_permission = false;
                    } else {
                        $insert->is_absent = '0';
                        $insert->is_permission = true;
                    }
                    $insert->total_point = $request->totalPoint[$i];
                    if (count($request->isPermission[$i + 1]) > 1) {
                        $insert->is_permission = true;
                    } else {
                        $insert->is_permission = false;
                    }
                    if (count($request->isAlpha[$i + 1]) > 1) {
                        $insert->is_alpha = true;
                        $insert->is_permission = false;
                    } else {
                        $insert->is_alpha = false;
                        $insert->is_permission = true;
                    }
                    $insert->save();

                    $detailTotalPoint = 0;
                    $attendanceDetailId = $insert->id;
                } else {
                    if (count($request->isAbsent[$i + 1]) > 1) {
                        $countStudent += 1;
                    }
                    $dataDetail = $dataDetail->first();
                    $attendanceDetailId = $dataDetail->id;
                    $detailTotalPoint = $dataDetail->total_point;
                    $arrUpdate = [
                        'total_point' => $request->totalPoint[$i],
                    ];
                    if (count($request->isAbsent[$i + 1]) > 1) {
                        $arrUpdate['is_absent'] = '1';
                        $arrUpdate['is_permission'] = false;
                    } else {
                        $arrUpdate['is_absent'] = '0';
                        $arrUpdate['is_permission'] = true;
                    }
                    if (count($request->isPermission[$i + 1]) > 1) {
                        $arrUpdate['is_permission'] = true;
                    }
                    if (count($request->isAlpha[$i + 1]) > 1) {
                        $arrUpdate['is_alpha'] = true;
                        $arrUpdate['is_permission'] = false;
                    } else {
                        $arrUpdate['is_alpha'] = false;
                        $arrUpdate['is_permission'] = true;
                    }
                    AttendanceDetail::where('attendance_id', $request->attendanceId)
                        ->where('student_id', $request->studentId[$i])
                        ->update($arrUpdate);
                }
                $student = Students::where('id', $request->studentId[$i])->first();
                $tmpPoint = $student->total_point - $detailTotalPoint;
                Students::where('id', $request->studentId[$i])->update([
                    'total_point' => $tmpPoint + $request->totalPoint[$i],
                ]);
                if (count($request->isAbsent[$i + 1]) > 1) {
                    PointHistory::create([
                        'student_id' => $request->studentId[$i],
                        'date' => date('Y-m-d'),
                        'total_point' => $request->isAbsentPoint[$i + 1],
                        'type' => 'in',
                        'keterangan' => 'Present',
                    ]);
                }

                // Multiple
                if ($request->categories) {
                    if (array_key_exists($i + 1, $request->categories)) {
                        for ($x = 0; $x < count($request->categories[$i + 1]); $x++) {
                            $pos = 0;
                            foreach ($pointCategories as $key => $value) {
                                if ($request->categories[$i + 1][$x] == $value['id']) {
                                    $pos = $key;
                                }
                            }
                            $avl = AttendanceDetailPoint::where('attendance_detail_id', $attendanceDetailId)
                                ->get();
                            $tmpDetail = [];

                            foreach ($avl as $key => $value) {
                                if (in_array($value->point_category_id, $request->categories[$i + 1]) == 0) {
                                    AttendanceDetailPoint::where('id', $value->id)->delete();
                                }
                                array_push($tmpDetail, $value->point_category_id);
                            }
                            if (in_array($request->categories[$i + 1][$x], $tmpDetail) == false) {
                                AttendanceDetailPoint::create([
                                    'attendance_detail_id' => $attendanceDetailId,
                                    'point_category_id' => $request->categories[$i + 1][$x],
                                    'point' => $pointCategories[$pos]->point,
                                ]);
                            }
                        }
                    }
                }

                // Manual
                // if ($request->category) {
                //     if (array_key_exists($i + 1, $request->category)) {
                //         for ($x = 0; $x < count($request->category[$i + 1]); $x++) {
                //             if ($request->category[$i + 1][$x] != null && $request->point_category[$i + 1][$x] != null) {
                //                 $avl =  AttendanceDetailPoint::where('attendance_detail_id', $dataDetail->id)
                //                     ->get();
                //                 foreach ($avl as $key => $value) {
                //                     AttendanceDetailPoint::where('id', $value->id)->delete();
                //                 }
                //                 $attendanceDetailPoint = new AttendanceDetailPoint;
                //                 $attendanceDetailPoint->attendance_detail_id = $dataDetail->id;
                //                 $attendanceDetailPoint->point_category = $request->category[$i + 1][$x];
                //                 $attendanceDetailPoint->point = $request->point_category[$i + 1][$x];
                //                 $attendanceDetailPoint->save();
                //             }
                //         }
                //     }
                // }
            }
            $or_rev = null;
            $or_test = null;

            $class = Price::find($request->priceId);
            $day1 = DB::table('day')->where('id', (int)$request->day1)->first();
            $day2 = DB::table('day')->where('id', (int)$request->day2)->first();

            $dataBefore =  OrderReview::where('id_attendance', $request->attendanceId)->get();
            if (count($dataBefore) > 0) {
                $dataBefore->each->delete();
            }
            if ($request->date_review) {
                // OrderReview::where('id_attendance', $request->attendanceId)->delete();
                foreach ($request->id_test as $keyReview => $valueReview) {
                    $or_rev = OrderReview::create(array(
                        'id_attendance' => $request->attendanceId,
                        'test_id' => $valueReview,
                        'id_teacher' => $request->teacher,
                        'class' => $class->program . ' ' . substr($day1->day, 0, 3) . ' ' . substr($day2->day, 0, 3) . ' On ' . $request->time,
                        'review_test' => 'Review ' . $valueReview,
                        'due_date' => $request->date_review,
                        // 'qty' => $countStudent,
                        'qty' => count($request->studentId),
                        'type' => 'review',
                    ));
                }
            }
            if ($request->date_test) {
                // OrderReview::where('id_attendance', $request->attendanceId)->delete();
                foreach ($request->id_test as $keyTest => $valueTest) {
                    $or_test =  OrderReview::create(array(
                        'id_attendance' => $request->attendanceId,
                        'test_id' => $valueTest,
                        'id_teacher' => $request->teacher,
                        'class' => $class->program . ' ' . substr($day1->day, 0, 3) . ' ' . substr($day2->day, 0, 3) . ' On ' . $request->time,
                        'review_test' => 'Test ' . $valueTest,
                        'due_date' => $request->date_test,
                        'qty' => count($request->studentId),
                        // 'qty' => $countStudent,
                        'type' => 'test',
                    ));
                }
            }
            // } else {
            //     Attendance::where('id', $request->attendanceId)->delete();
            //     AttendanceDetail::where('attendance_id', $request->attendanceId)->delete();
            // }
            // DB::commit();

            // Check if either review or test was created and save them
            if ($or_rev != null || $or_test != null) {
                $or_rev->save();
                $or_test->save();

                if ($or_rev && $or_test) {
                    $message =
                        'Update! Both Review and Test have been recorded!';
                } elseif ($or_test) {
                    $message = ' Test Recorded! Test ID: ' . $or_test->test_id . ' | Due Date: ' . $or_test->due_date;
                } elseif ($or_rev) {
                    $message = ' Review Recorded! Review ID: ' . $or_rev->test_id . ' | Due Date: ' . $or_rev->due_date;
                } else {
                    $message = 'Oops! Neither Test nor Review could be recorded.';
                }
                DB::commit();
            } else {
                DB::commit();
                $message = 'Attendance and Agenda have been Recorded';
            }
            return redirect('/attendance/class')->with('message', $message);
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    public function reminder(Request $request)
    {
        $arrAbsent = [];
        $arrAbsentFilter = [];

        $students = Students::where('status', 'ACTIVE')->get();
        //   $students = Students::limit(500)->where('status', 'ACTIVE')->get();
        $class = Price::get();
        $teachers = Teacher::get();

        /*$attendance = DB::table('student s')
            ->join('price p', 's.priceid=p.id')
            ->join('teacher t', 's.id_teacher=t.id', 'left')
            ->join('attendance_details ad', 's.id=ad.student_id')
            ->selectRaw('s.name, t.name AS teacher, p.program, ad.comment_teacher, ad.comment_staff, COUNT(s.id) AS absent')
            ->where('ad.is_absent', 1)
            ->where('ad.is_done', 0)
            ->where('s.status', 'ACTIVE');

        if($request->level!='')
            $attendance = $attendance->where('s.priceid', $request->level);


        if($request->teacher!='')
            $attendance = $attendance->where('s.id_teacher', $request->teacher)

        $attendance = $attendance
            ->groupBy('s.id')
            ->havingRaw('absent >= 2');*/

        $data = [];

        //echo count($students);

        if ($request->level == '' && $request->teacher == '' && Auth::guard('staff')->user() == true) {
            $data = [];
        } else {
            foreach ($students as $s) {
                $attendance = DB::table('student as s')
                    ->join('price as p', 's.priceid', 'p.id')
                    ->join('teacher as t', 's.id_teacher', 't.id')
                    ->join('attendance_details as ad', 's.id', 'ad.student_id')
                    ->join('attendances as a', 'ad.attendance_id', 'a.id')
                    ->selectRaw('s.id AS student_id, s.name, t.name AS teacher, p.program, ad.comment_teacher, ad.comment_staff, ad.is_absent, ad.is_permission, ad.is_done, ad.is_deleted, ad.id, a.date')
                    ->where('s.id', $s->id)
                    ->where('s.status', 'ACTIVE');


                if (Auth::guard('teacher')->user() != null)
                    $attendance = $attendance->where('s.id_teacher', Auth::guard('teacher')->user()->id);


                if ($request->level != '')
                    $attendance = $attendance->where('s.priceid', $request->level);

                if ($request->teacher != '')
                    $attendance = $attendance->where('s.id_teacher', $request->teacher);


                $date = new \DateTime(date('Y-m-d'));
                $date->modify('-30 day');


                $attendance = $attendance
                    ->where('a.date', '>=', $date->format('Y-m-d'))
                    ->orderBy('a.date', 'desc')->get();
                //->limit(7)->get();

                $temp = null;


                $process = true;
                $index = 0;
                while ($process && $index < count($attendance)) {
                    //jika hadir atau sudah dihapus maka temp kembali ke null
                    //if($attendance[$index]->is_done=='1' || $attendance[$index]->is_absent=='1'){
                    if ($attendance[$index]->is_done == '1' || $attendance[$index]->is_permission == '1' || $attendance[$index]->is_absent == '1' || $attendance[$index]->is_deleted == '1') {
                        $temp = null;
                    } else if ($attendance[$index]->is_absent == '0' && $attendance[$index]->is_deleted == '0') {
                        //jika absen dan belum dihapus
                        if ($temp == null) {
                            $attendance[$index]->absent_date = $attendance[$index]->date;
                            $attendance[$index]->absent_id = $attendance[$index]->id;

                            $temp = $attendance[$index];
                        } else {
                            //jika sebelumnya sudah ada data maka masukkan ke dalam array

                            $temp->absent_date .= ', ' . $attendance[$index]->date;
                            $temp->absent_id .= '-' . $attendance[$index]->id;
                            array_push($data, $temp);
                            $temp = null;

                            $process = false;
                        }
                    }

                    $index++;
                }

                // $data = [];

                /*foreach($attendance as $r){
                //kalau sudah done maka tidak usah diuji lagi

                if($r->is_done){
                    $temp=null;
                }
                else if($r->is_absent){
                    if($temp!=null){
                        $temp->


                        array_push($array, $temp)
                    }
                    else{
                        $r->
                    }
                }


            }*/
            }
        }



        //foreach($data as $r){
        //    echo $r->student_id . '-' . $r->name . ' ' . $r->absent_date . '<br>';
        //}


        /*foreach ($students as $key => $value) {
            $ttlApha = 0;
            $attendance = AttendanceDetail::join('student as st', 'st.id', 'attendance_details.student_id')
                ->join('price as p', 'p.id', 'st.priceid')
                ->join('attendances as a', 'a.id', 'attendance_details.attendance_id')
                ->join('teacher as t', 't.id', 'a.teacher_id')
                ->select('attendance_details.*', 'st.name', 'p.program', 't.name as teacher', 'a.price_id', 'a.teacher_id', 'st.id_staff', 'a.date')
                ->where('attendance_details.student_id', $value->id)
                ->groupBy('a.date');
            if (Auth::guard('teacher')->user() != null) {
                $attendance = $attendance->where('teacher_id', Auth::guard('teacher')->user()->id);
            }
            if (Auth::guard('staff')->user() != null && Auth::guard('staff')->user()->id != 7 && Auth::guard('staff')->user()->id != 1) {
                $attendance = $attendance->where('id_staff', Auth::guard('staff')->user()->id);
            }

            $attendance = $attendance->orderBy('attendance_details.id', 'desc')->limit(2)->get();
            $countA = count($attendance);
            if ($countA != 0) {
                foreach ($attendance as $keya => $valuea) {
                    if ($valuea->is_absent == '0') {
                        if ($valuea->is_done == false) {
                            $ttlApha++;
                        }
                    }
                }
            }
            if ($ttlApha >= 2) {
                array_push($arrAbsent, $attendance);
            }
        }*/


        /*foreach ($arrAbsent as $k => $v) {
            if ($request->level != '' && $request->teacher == '') {
                if ($v[0]->price_id == $request->level) {
                    array_push($arrAbsentFilter, $v);
                }
            }
            if ($request->level == '' && $request->teacher != '') {
                if ($v[0]->teacher_id == $request->teacher) {
                    array_push($arrAbsentFilter, $v);
                }
            }
            if ($request->level != '' && $request->teacher != '') {
                if ($v[0]->teacher_id == $request->teacher && $v[0]->price_id == $request->level) {
                    array_push($arrAbsentFilter, $v);
                }
            }
        }

        if ($request->level != '' && $request->teacher == '') {
            $data = $arrAbsentFilter;
        } else if ($request->level == '' && $request->teacher != '') {
            $data = $arrAbsentFilter;
        } else if ($request->level != '' && $request->teacher != '') {
            $data = $arrAbsentFilter;
        } else {
            $data = $arrAbsent;
        }*/

        // $page = !empty($request->page) ? (int) $request->page : 1;
        // $total = count($data); //total items in array
        // $limit = 10; //per page
        // $totalPages = ceil($total / $limit); //calculate total pages
        // $page = max($page, 1); //get 1 page when $request->page <= 0
        // $page = min($page, $totalPages); //get last page when $request->page > $totalPages
        // $offset = ($page - 1) * $limit;
        // if ($offset < 0) $offset = 0;
        // $data = array_slice($data, $offset, $limit);
        // return view('attendance.reminder', compact('data', 'totalPages'));

        return view('attendance.reminder', compact('data', 'class', 'teachers'));
    }

    public function reminderDone(Request $request)
    {
        try {
            $student = $request->student;
            //AttendanceDetail::where('student_id', $student)->limit(2)->orderBy('id', 'desc')->update([
            //    "is_done" => true,
            //]);

            $arr = explode('-', $student);
            foreach ($arr as $r) {
                AttendanceDetail::where('id', $r)->update([
                    'is_done' => '1',
                ]);
            }

            return redirect()->back()->with('message', 'Berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function reminderAbsen(Request $request)
    {
        try {
            $student = $request->student;

            /*AttendanceDetail::where('student_id', $student)->limit(2)->orderBy('id', 'desc')->update([
                "is_absent" => '1'
            ]);*/

            $arr = explode('-', $student);
            foreach ($arr as $r) {
                AttendanceDetail::where('id', $r)->update([
                    'is_deleted' => '1'
                ]);
            }


            return redirect()->back()->with('message', 'Berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function mutasi(Request $request)
    {
        $studentId = $request->student;
        $students = Students::where('status', 'ACTIVE')->get();
        $price = Price::get();
        $data = [];
        // $class = Students::join('price', 'price.id', 'student.priceid')
        //     ->select('price.program')
        //     ->where('student.id', $studentId)->first();
        // $query = [];
        // $query = AttendanceDetail::join('attendances as atd', 'atd.id', 'attendance_details.attendance_id')
        //     ->join('student as st', 'st.id', 'attendance_details.student_id')
        //     ->join('price as pr', 'pr.id', 'atd.price_id')
        //     ->select('st.name', 'pr.program', 'atd.id as attendance_id1', 'attendance_details.*', 'atd.date', 'st.id as student_id1', 'pr.id as price_id')
        //     ->where('attendance_details.student_id', $studentId);
        // if ($request->class) {
        //     $query = $query->where('atd.price_id',  $request->class);
        // }
        // $data = $query->orderBy('atd.date', 'DESC')->groupBy('pr.program')->paginate(10);
        if ($request->student && $request->class) {
            $student = Students::find($studentId);
            $data = Mutasi::with('level', 'score')->where('student_id', $student->id)->paginate(10);
        }
        return view('attendance.mutasi', compact('data', 'students', 'price'));
    }

    public function storeMutasi(Request $request)
    {
        try {
            $student = Students::find($request->student);
            $score = StudentScore::where('student_id', $request->student)->where('price_id', $student->priceid)->orderBy('id', 'desc')->first();
            $mutasi = new Mutasi;
            $mutasi->student_id = $request->student;
            $mutasi->price_id = $student->priceid;
            $mutasi->user_name = Auth::guard('staff')->user()->name;
            $mutasi->from = 'Backoffice';
            $mutasi->created_at = Carbon::now()->addHours(7);
            $mutasi->updated_at = Carbon::now()->addHours(7);
            if ($score != null) {
                $mutasi->score_id = $score->id;
            }
            $student->priceid = $request->level;
            $student->save();
            $mutasi->save();
            return redirect('mutasi?student=' . $student->id . '&class=' . $student->priceid)->with('message', 'Berhasil dimutasi');
        } catch (\Exception $e) {
            return redirect()->back()->with('status', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('status', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        }
        return $score != null ? 'asd' : 'ccd';
    }

    public function getClass(Request $request)
    {
        $level = DB::table('price');
        if ($request->level == 'priv') {
            $level = $level->where('level', 'Private');
        } else {
            $level = $level->where('level', '!=', 'Private');
        }
        return $level->get();
    }

    public function updateClass(Request $request)
    {
        try {
            $reqDay1 = $request->update_day1;
            $reqDay2 = $request->update_day2;
            $reqTime = $request->update_time;
            $priceId = $request->update_class;
            $teacherOld = $request->old_teacher;
            $student = DB::table('student')->where('priceid', $priceId)->where("day1", $reqDay1)
                ->where("day2", $reqDay2)
                ->where('course_time', $reqTime)->where('is_class_new', false)->where('id_teacher', $teacherOld)->get();
            if ($request->type == 'edit') {
                DB::table('student')->where('priceid', $priceId)->where("day1", $reqDay1)
                    ->where("day2", $reqDay2)
                    ->where('course_time', $reqTime)->where('id_teacher', $teacherOld)->update([
                        "day1" => $request->update_day_one,
                        "day2" => $request->update_day_two,
                        "course_time" => $request->update_course_time,
                        "priceid" => $request->update_level,
                        "id_teacher" => $request->update_teacher,
                    ]);
            } else {
                foreach ($student as $key => $value) {
                    if ($value->priceid != $request->update_level) {
                        $score = StudentScore::where('student_id', $request->student)->where('price_id', $value->priceid)->orderBy('id', 'desc')->first();
                        $mutasi = new Mutasi;
                        $mutasi->student_id = $value->id;
                        $mutasi->price_id = $priceId;
                        $mutasi->user_name = Auth::guard('staff')->user()->name;
                        $mutasi->from = 'Backoffice';
                        $mutasi->created_at = Carbon::now()->addHours(7);
                        $mutasi->updated_at = Carbon::now()->addHours(7);
                        if ($score != null) {
                            $mutasi->score_id = $score->id;
                        }
                        $mutasi->save();
                    }
                }
                // New Class
                DB::table('student')->where('priceid', $priceId)->where('is_class_new', false)->where("day1", $reqDay1)
                    ->where("day2", $reqDay2)
                    ->where('course_time', $reqTime)->where('id_teacher', $teacherOld)->where('is_failed_promoted', '0')->update([
                        "day1" => $request->update_day_one,
                        "day2" => $request->update_day_two,
                        "course_time" => $request->update_course_time,
                        "priceid" => $request->update_level,
                        "id_teacher" => $request->update_teacher,
                        "is_class_new" => true,
                        "is_certificate" => null,
                        "date_certificate" => null,
                        "is_failed_promoted" => '0',
                        "is_follow_up" => '0',
                    ]);

                // Failed Promoted From ecertificate
                DB::table('student')->where('priceid', $priceId)->where('is_class_new', false)->where("day1", $reqDay1)
                    ->where("day2", $reqDay2)
                    ->where('course_time', $reqTime)->where('id_teacher', $teacherOld)->where('is_failed_promoted', '1')->update([
                        "is_certificate" => null,
                        "date_certificate" => null,
                        "is_failed_promoted" => '0',
                        "is_follow_up" => '0',
                    ]);


                // Old Class
                DB::table('student')->where('priceid', $priceId)->where("day1", $reqDay1)
                    ->where("day2", $reqDay2)
                    ->where('course_time', $reqTime)->where('id_teacher', $request->old_teacher)->where('is_class_new', true)->update([
                        "is_class_new" => false,
                        "is_failed_promoted" => '0',
                        "is_follow_up" => '0',
                    ]);
            }

            return redirect()->back()->with('message', 'Berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function addComment($id, Request $request)
    {
        try {
            /*$model = AttendanceDetail::find($id);
            if ($request->type == 'teacher') {
                $model->comment_teacher = $request->comment;
            } else {
                $model->comment_staff = $request->comment;
            }
            $model->save();*/

            $arr = explode('-', $id);

            foreach ($arr as $r) {
                $model = AttendanceDetail::find($r);
                if ($request->type == 'teacher') {
                    $model->comment_teacher = $request->comment;
                } else {
                    $model->comment_staff = $request->comment;
                }
                $model->save();
            }


            return redirect()->back()->with('message', 'Berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }


    function ajaxStudent(Request $request)
    {
        $student = Students::where('status', 'ACTIVE')->where('priceid', $request->class)
            ->where("day1", $request->day1)
            ->where("day2", $request->day2)
            ->where('course_time', $request->time)
            ->where('id_teacher', $request->teacher)->get();
        return $student;
    }

    function mutasi_class(Request $request)
    // {
    //     // Get necessary data from the request
    //     $priceId = $request->input('priceid');
    //     $courseTime = $request->input('course_time');
    //     $day1 = $request->input('day1');
    //     $day2 = $request->input('day2');
    //     $oldTeacherId = $request->input('id_teacher');
    //     $newTeacherId = $request->input('transfer_teacher');

    //     // dd($priceId, $courseTime, $day1, $day2, $oldTeacherId, $newTeacherId);

    //     try {
    //         DB::transaction(function () use ($priceId, $courseTime, $day1, $day2, $oldTeacherId, $newTeacherId) {
    //              // Update attendances table
    //             DB::table('attendances')
    //                 ->where('price_id', $priceId)
    //                 ->where('course_time', $courseTime)
    //                 ->where('day1', $day1)
    //                 ->where('day2', $day2)
    //                 ->where('teacher_id', $oldTeacherId)
    //                 ->update([
    //                     'teacher_id' => $newTeacherId,
    //                     'mutasi_teacher' => $oldTeacherId,
    //                     'tgl_mutasi' => now(),
    //                 ]);

    //             // Update student table
    //             DB::table('student')
    //                 ->where('priceid', $priceId)
    //                 ->where('id_teacher', $oldTeacherId)
    //                 ->where('course_time', $courseTime)
    //                 ->where('day1', $day1)
    //                 ->where('day2', $day2)
    //                 ->update(['id_teacher' => $newTeacherId]);

    //             $class = DB::table('price')->where('id', $priceId)->first();
    //             $day1 = DB::table('day')->where('id', $day1)->first();
    //             $day2 = DB::table('day')->where('id', $day2)->first();
    //             $className = $class->program;

    //             // modify
    //             // Convert full day names to their abbreviations
    //             $day1_nya = substr($day1->day, 0, 3); // Fri
    //             $day2_nya = substr($day2->day, 0, 3); // Mon

    //             $modify_class =
    //                 $className .
    //                 ' ' .
    //                 $day1_nya .
    //                 ' ' .
    //                 $day2_nya .
    //                 ' On ' .
    //                 $courseTime;
    //             // Update order_reviews table
    //             DB::table('order_reviews')
    //                 ->where('id_teacher', $oldTeacherId)
    //                 ->where('class', $modify_class)
    //                 ->update(['id_teacher' => $newTeacherId]);
    //         });
    //         // Return success message
    //         return redirect()->back()->with('message', 'Success transfer class');
    //     } catch (\Exception $e) {
    //         // Handle error and return failure message
    //         return redirect()->back()->with('message', 'Failed transfer class : ' . $e->getMessage());
    //     }
    // }

    {

        // Get necessary data from the request
        $priceId = $request->input('priceid');
        $courseTime = $request->input('course_time');
        $day1 = $request->input('day1');
        $day2 = $request->input('day2');
        $oldTeacherId = $request->input('id_teacher');
        $newTeacherId = $request->input('transfer_teacher');
        $newStaffId = $request->input('transfer_staff');
        $studentId = $request->input('studentid');

        $day1_old = $request->input('day1_old');
        $day2_old = $request->input('day2_old');
        $courseTime_old = $request->input('course_time_old');

        // dd($request->all());


        // dd($priceId, $courseTime, $day1, $day2, $oldTeacherId, $newTeacherId);

        try {
            DB::transaction(function () use (
                $priceId,
                $courseTime,
                $day1,
                $day2,
                $oldTeacherId,
                $newTeacherId,
                $newStaffId,
                $studentId,
                $day1_old,
                $day2_old,
                $courseTime_old
            ) {
                // Update attendances table
                if ($priceId == 39) {
                    $cek_studentid = DB::table('attendances')
                        ->join('attendance_details', 'attendances.id', '=', 'attendance_details.attendance_id')
                        ->where('attendance_details.student_id', $studentId)
                        ->where('price_id', $priceId)
                        ->where('course_time', $courseTime_old)
                        ->where('day1', $day1_old)
                        ->where('day2', $day2_old)
                        ->first();
                    if ($cek_studentid != null) {
                        // cek apakah old teacher id sama dengan new teacher id
                        if ($cek_studentid->teacher_id == $newTeacherId) {
                            $attendances  =  DB::table('attendances')
                                ->join('attendance_details', 'attendances.id', '=', 'attendance_details.attendance_id')
                                ->where('attendance_details.student_id', $studentId)
                                ->where('price_id', $priceId)
                                ->where('course_time', $courseTime_old)
                                ->where('day1', $day1_old)
                                ->where('day2', $day2_old)
                                ->update([
                                    'teacher_id' => $newTeacherId,
                                    'day1' => $day1,
                                    'day2' => $day2,
                                    'course_time' => $courseTime,
                                    // 'mutasi_teacher' => $oldTeacherId,
                                    'day1_mutasi' => $day1_old,
                                    'day2_mutasi' => $day2_old,
                                    'course_time_mutasi' => $courseTime_old,
                                    'tgl_mutasi' => now(),
                                ]);
                        } else {
                            $attendances  =  DB::table('attendances')
                                ->join('attendance_details', 'attendances.id', '=', 'attendance_details.attendance_id')
                                ->where('attendance_details.student_id', $studentId)
                                ->where('price_id', $priceId)
                                ->where('course_time', $courseTime_old)
                                ->where('day1', $day1_old)
                                ->where('day2', $day2_old)
                                ->update([
                                    'teacher_id' => $newTeacherId,
                                    'day1' => $day1,
                                    'day2' => $day2,
                                    'course_time' => $courseTime,
                                    'mutasi_teacher' => $oldTeacherId,
                                    'day1_mutasi' => $day1_old,
                                    'day2_mutasi' => $day2_old,
                                    'course_time_mutasi' => $courseTime_old,
                                    'tgl_mutasi' => now(),
                                ]);
                        }
                    }
                } else {
                    $attendance = DB::table('attendances')
                        ->where('price_id', $priceId)
                        ->where('course_time', $courseTime_old)
                        ->where('day1', $day1_old)
                        ->where('day2', $day2_old)
                        ->where('teacher_id', $oldTeacherId)
                        ->get();

                    if ($attendance->count() >= 0) {
                        // cek apakah old teacher id sama dengan new teacher id
                        // if ($attendance[0]->teacher_id != $newTeacherId && $day1 != $day1_old && $day2 != $day2_old && $courseTime != $courseTime_old) {
                        if ($attendance[0]->teacher_id != $newTeacherId) {


                            DB::table('attendances')
                                ->where('price_id', $priceId)
                                ->where('course_time', $courseTime_old)
                                ->where('day1', $day1_old)
                                ->where('day2', $day2_old)
                                ->where('teacher_id', $oldTeacherId)
                                ->update([
                                    'teacher_id' => $newTeacherId,
                                    'mutasi_teacher' => $oldTeacherId,
                                    'day1' => $day1,
                                    'day2' => $day2,
                                    'course_time' => $courseTime,
                                    'day1_mutasi' => $day1_old,
                                    'day2_mutasi' => $day2_old,
                                    'course_time_mutasi' => $courseTime_old,
                                    'tgl_mutasi' => now(),
                                ]);
                        } else {
                            DB::table('attendances')
                                ->where('price_id', $priceId)
                                ->where('course_time', $courseTime_old)
                                ->where('day1', $day1_old)
                                ->where('day2', $day2_old)
                                ->where('teacher_id', $oldTeacherId)
                                ->update([
                                    'teacher_id' => $newTeacherId,
                                    // 'mutasi_teacher' => $oldTeacherId,
                                    'day1' => $day1,
                                    'day2' => $day2,
                                    'course_time' => $courseTime,
                                    'day1_mutasi' => $day1_old,
                                    'day2_mutasi' => $day2_old,
                                    'course_time_mutasi' => $courseTime_old,
                                    'tgl_mutasi' => now(),
                                ]);
                        }
                    }
                }



                // jika student_id ada maka klasnya private dan hanya 1 siswa
                if ($studentId != null) {

                    DB::table('student')
                        ->where('id', $studentId)
                        ->where('id_teacher', $oldTeacherId)
                        ->where('course_time', $courseTime_old)
                        ->where('day1', $day1_old)
                        ->where('day2', $day2_old)
                        ->update([
                            'id_teacher' => $newTeacherId,
                            'id_staff' => $newStaffId,
                            'day1' => $day1,
                            'day2' => $day2,
                            'course_time' => $courseTime,
                        ]);
                } else {
                    DB::table('student')
                        ->where('priceid', $priceId)
                        ->where('id_teacher', $oldTeacherId)
                        ->where('course_time', $courseTime_old)
                        ->where('day1', $day1_old)
                        ->where('day2', $day2_old)
                        ->update([
                            'id_teacher' => $newTeacherId,
                            'id_staff' => $newStaffId,
                            'day1' => $day1,
                            'day2' => $day2,
                            'course_time' => $courseTime,
                        ]);
                }

                $class = DB::table('price')->where('id', $priceId)->first();
                $day1 = DB::table('day')->where('id', $day1)->first();
                $day2 = DB::table('day')->where('id', $day2)->first();
                $day1_old = DB::table('day')->where('id', $day1_old)->first();
                $day2_old = DB::table('day')->where('id', $day2_old)->first();
                $className = $class->program;

                // modify
                // Convert full day names to their abbreviations
                $day1_nya = substr($day1->day, 0, 3); // Fri
                $day2_nya = substr($day2->day, 0, 3); // Mon
                $day1_old_nya = substr($day1_old->day, 0, 3); // Fri
                $day2_old_nya = substr($day2_old->day, 0, 3); // Mon

                $modify_class_new =
                    $className .
                    ' ' .
                    $day1_nya .
                    ' ' .
                    $day2_nya .
                    ' On ' .
                    $courseTime;
                $modify_class_old =
                    $className .
                    ' ' .
                    $day1_old_nya .
                    ' ' .
                    $day2_old_nya .
                    ' On ' .
                    $courseTime_old;


                if ($modify_class_new != $modify_class_old) {
                    // Update order_reviews table
                    DB::table('order_reviews')
                        ->where('id_teacher', $oldTeacherId)
                        ->where('class', $modify_class_old)
                        ->update([
                            'id_teacher' => $newTeacherId,
                            'class' => $modify_class_new
                        ]);
                } else {
                    // Update order_reviews table
                    DB::table('order_reviews')
                        ->where('id_teacher', $oldTeacherId)
                        ->where('class', $modify_class_new)
                        ->update(['id_teacher' => $newTeacherId]);
                }
            });
            // Return success message
            return redirect()->back()->with('message', 'Success transfer class');
        } catch (\Exception $e) {


            // Handle error and return failure message
            return redirect()->back()->with('message', 'Failed transfer class : ' . $e->getMessage());
        }
    }


    public function updateStar(Request $request)
    {
        try {
            // dd($request->all());
            $updatedRows = Attendance::where('price_id', $request->priceid)
                ->where('teacher_id', $request->id_teacher)
                ->where('day1', $request->day1)
                ->where('day2', $request->day2)
                ->where('course_time', $request->course_time)
                ->get();

                // dd($updatedRows);

            foreach ($updatedRows as $row) {
                $row->star = $request->selected_star;
                $row->save();
            }

            $updatedCount = $updatedRows->count();

            if ($updatedCount > 0) {
                return redirect()->back()->with('message', 'Data Star Updated Successfully.');
            }

            return redirect()->back()->with('message', 'Tidak ada data yang cocok untuk di-update.');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan umum : ' . $e->getMessage());
        }
        
    }

    public function setAssistant(Request $request)
    {
        try {
            $updatedRows = Attendance::where('price_id', $request->old_priceid)
                ->where('teacher_id', $request->old_teacher_id)
                ->where('day1', $request->old_day1)
                ->where('day2', $request->old_day2)
                ->where('course_time', $request->old_course_time)
                ->get();

            foreach ($updatedRows as $row) {
                $row->assist_id = $request->assistant_teacher_id;
                $row->assist_day1 = $request->assist_day1 == "true" ? True : False;
                $row->assist_day2 = $request->assist_day2 == "true" ? True : False;
                $row->save();
            }

            $updatedCount = $updatedRows->count();

            if ($updatedCount > 0) {
                return redirect()->back()->with('message', 'Data Assistant Updated Successfully.');
            }

            return redirect()->back()->with('message', 'Tidak ada data yang cocok untuk di-update.');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan umum : ' . $e->getMessage());
        }
    }

    public function removeAssistant(Request $request)
    {

        try {
            $updatedRows = Attendance::where('price_id', $request->priceid)
                ->where('teacher_id', $request->teacher_id)
                ->where('day1', $request->day1)
                ->where('day2', $request->day2)
                ->where('course_time', $request->course_time)
                ->get();

            foreach ($updatedRows as $row) {
                $row->assist_id = null;
                $row->assist_day1 = false;
                $row->assist_day2 = false;
                $row->save();
            }

            $updatedCount = $updatedRows->count();

            if ($updatedCount > 0) {
                return redirect()->back()->with('message', 'Data Assistant Removed Successfully.');
            }

            return redirect()->back()->with('message', 'Tidak ada data yang cocok untuk di-update.');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan umum : ' . $e->getMessage());
        }
    }

    public function removeStar(Request $request)
    {
        try {
            $updatedRows = Attendance::where('price_id', $request->priceid)
                ->where('teacher_id', $request->teacher_id)
                ->where('day1', $request->day1)
                ->where('day2', $request->day2)
                ->where('course_time', $request->course_time)
                ->get();

            foreach ($updatedRows as $row) {
                $row->star = null;
                $row->save();
            }

            $updatedCount = $updatedRows->count();

            if ($updatedCount > 0) {
                return redirect()->back()->with('message', 'Data Star Removed Successfully.');
            }

            return redirect()->back()->with('message', 'Tidak ada data yang cocok untuk di-update.');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan umum : ' . $e->getMessage());
        }
    }
}
