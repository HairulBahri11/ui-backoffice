<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\AttendanceDetailPoint;
use App\Models\PointCategories;
use App\Models\PointHistory;
use App\Models\PointHistoryCategory;
use App\Models\Price;
use App\Models\ReedemItems;
use App\Models\ReedemPoint;
use App\Models\Students;
use App\Models\Teacher;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReedemPointController extends Controller
{
    public function create()
    {
        try {
            $class = Price::all();
            // $students = Students::join('price as p', 'p.id', 'student.priceid')
            //     ->select('student.name', 'student.id', 'student.total_point')
            //     ->get();
            $students = Students::where('status', 'ACTIVE')->get();
            $item = ReedemItems::all();
            $title = 'Reedem Point';
            return view('reedemPoint.form', compact('students', 'title', 'item', 'class'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function store(Request $request)
    {
        try {
            if ($request->point >= $request->total_point) {
                $student = $request->student == null && $request->id_student != null ? $request->id_student : $request->student;
                $cek = ReedemPoint::where('student_id', $student)->where('date', date('Y-m-d'))->first();
                if (!$cek) {
                    $findPointStudent = Students::find($student);
                    ReedemPoint::create([
                        'point' => intval($request->total_point),
                        'student_id' => $student,
                        'date' => date('Y-m-d'),
                    ]);
                    PointHistory::create([
                        'student_id' => $student,
                        'date' => date('Y-m-d'),
                        'total_point' => intval($request->total_point),
                        'type' => 'redeem',
                        'keterangan' => 'Reedem Point',
                        'balance_in_advanced' => $findPointStudent->total_point,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $newPoint = intval($request->point) - intval($request->total_point);
                    Students::where('id', $student)
                        ->update([
                            'total_point' =>  $newPoint,
                        ]);
                    return redirect('/reedemPoint')->with('status', 'Success Reedem Point');
                    // for ($i = 0; $i < count($request->item); $i++) {
                    //     $items = ReedemItems::where('id', $request->item[$i])->first();
                    //     $subTotal = intval($items->point) * intval($request->qty[$i]);
                    //     $tmpTotal += $subTotal;
                    // }
                    // if ($request->point < $tmpTotal) {
                    //     return back()->with('error', 'Point tidak cukup');
                    // } else {
                    //     for ($i = 0; $i < count($request->item); $i++) {
                    //         $items = ReedemItems::where('id', $request->item[$i])->first();
                    //         $subTotal = intval($items->point) * intval($request->qty[$i]);
                    //         ReedemPoint::create([
                    //             'item_id' => $request->item[$i],
                    //             'point' => intval($items->point),
                    //             'student_id' => $student,
                    //             'qty' => $request->qty[$i],
                    //         ]);
                    //         PointHistory::create([
                    //             'student_id' => $student,
                    //             'date' => date('Y-m-d'),
                    //             'total_point' => intval($items->point),
                    //             'type' => 'redeem',
                    //             'keterangan' => $items->item,
                    //         ]);
                    //         // PointHistoryCategory::create([
                    //         //     'point_history_id' => $history->id,
                    //         //     'point_category_id' =>  $request->item[$i],
                    //         // ]);
                    //     }
                    //     $newPoint = intval($request->point) - $subTotal;
                    //     Students::where('id', $student)
                    //         ->update([
                    //             'total_point' =>  $newPoint,
                    //         ]);
                    //     return redirect('/reedemPoint')->with('status', 'Success Reedem Point');
                    // }
                } else {
                    return redirect('/reedemPoint')->with('error', 'Reedem Point Failed!');
                }
            } else {
                return redirect('/reedemPoint')->with('error', 'Reedem Point Failed');
            }
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed Reedem Point');
            //throw $th;
        }
    }

    public function getStudent(Request $request)
    {
        try {
            // $data =
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function saldoAwal(Request $request)
    {
        $where = '';
        $teachers = Teacher::get();
        $level = Price::get();
        // if (Auth::guard('teacher')->check() == true) {
        //     $where = 'AND id_teacher = ' . Auth::guard('teacher')->user()->id;
        // }
        if (Auth::guard('staff')->check() == true && Auth::guard('staff')->user()->id != 7) {
            $where = $where . ' AND id_staff = ' . Auth::guard('staff')->user()->id;
        }
        if ($request->teacher) {
            $where = $where . ' AND id_teacher = ' . $request->teacher;
        }
        if ($request->level && Auth::guard('staff')->check() == true) {
            $where = $where . ' AND priceid = ' . $request->level;
        }
        if ($request->level && Auth::guard('teacher')->check() == true) {
            $where = $where . ' AND priceid = ' . $request->level; /* . ' AND id_teacher =' . Auth::guard('teacher')->user()->id; */
        }
        if ($request->day && Auth::guard('teacher')->check() == true) {
            $where = $where . ' AND day1 = ' . $request->day; /* . ' AND id_teacher =' . Auth::guard('teacher')->user()->id; */
        }
        $class = DB::select("SELECT DISTINCT priceid,day1,day2,course_time,id_teacher,price.level,price.program,day_1.day day_one,day_2.day day_two,teacher.name teacher_name from student join price on student.priceid = price.id join day day_1 on student.day1 = day_1.id join day day_2 on student.day2 = day_2.id join teacher on student.id_teacher = teacher.id  WHERE day1 is NOT null AND day2 is NOT null AND course_time is NOT null AND id_teacher is NOT null $where ORDER BY priceid ASC, day1,course_time;");
        $private = [];
        $general = [];
        foreach ($class as $key => $value) {
            if ($value->level == 'Private') {
                array_push($private, $value);
            } else {
                array_push($general, $value);
            }
        }
        $day = DB::table('day',)->get();
        return view('reedemPoint.saldo-awal', compact('private', 'general', 'day', 'teachers', 'level'));
    }

    public function createSaldoAwal($priceId, Request $request)
    {
        $reqDay1 = $request->day1;
        $reqDay2 = $request->day2;
        $reqTime = $request->time;
        $reqTeacher = $request->teacher;
        // $reqAmpm = $request->ampm;
        $student = "";
        $day = DB::table('day')->get();
        $cek = Attendance::where('price_id', $priceId)
            ->where('date', date('Y-m-d'))
            ->where('day1', $reqDay1)
            ->where('day2', $reqDay2)
            ->where('course_time', $reqTime)
            ->orderBy('id', 'DESC')
            ->first();

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
            }
            $data = (object)[
                'type' => 'update',
                'id' => $class->id,
                'attendanceId' => $cek->id,
                'comment' => $cek->activity,
                'textBook' => $cek->text_book,
                'excerciseBook' => $cek->excercise_book,
                'students' => $detail,
            ];
            // return $data;
        } else {

            $data = (object)[
                'type' => 'create',
                'id' => $class->id,
                'attendanceId' => 0,
                'comment' => '',
                'textBook' => '',
                'excerciseBook' => '',
                'students' => [],
            ];
        }


        $student = Students::where('priceid', $class->id)
            ->where("day1", $reqDay1)
            ->where("day2", $reqDay2)
            ->where('course_time', $reqTime);
        if (Auth::guard('teacher')->check() == true && Auth::guard('teacher')->user()->id != 21 && Auth::guard('teacher')->user()->id != 20) {
            $student = $student->where('id_teacher', Auth::guard('teacher')->user()->id);
        } else {
            $student = $student->where('id_teacher', $reqTeacher);
        }

        $student =   $student->get();


        $pointCategories = PointCategories::all();
        // return $student;
        return view('reedemPoint.form-saldo-awal', compact('title', 'data', 'student', 'pointCategories', 'day'));
    }

    public function storeSaldoAwal(Request $request)
    {
        try {
            foreach ($request->studentId as $key => $value) {
                $student = Students::find($value);

                $pointHistory = PointHistory::where('student_id', $student->id)->where('keterangan', 'Opening Balance')->first();
                if ($pointHistory == null) {
                    $model = new PointHistory();
                    $model->student_id = $student->id;
                    $model->date = now();
                    $model->total_point = $request->saldo_awal[$key];
                    $model->keterangan = 'Opening Balance';
                    $model->type = 'in';
                    $model->balance_in_advanced = $student->total_point;
                    $model->save();
                } else {
                    $pointHistory->date = now();
                    $pointHistory->total_point = $request->saldo_awal[$key];
                    $pointHistory->balance_in_advanced = $student->total_point;
                    $pointHistory->save();
                }
                $student->total_point = $request->saldo_awal[$key];
                $student->save();
            }
            return redirect()->back()->withStatus('Data berhasil diperbarui ');
        } catch (Exception $e) {
            //throw $th;
            ddd($e);
        } catch (QueryException $e) {
            ddd($e);
        }
    }

    public function historiAjax($id)
    {

        // $data = PointHistory::where('student_id', $id)->limit(10)->orderBy('date', 'DESC')->get();
        $query = "
            SELECT ph.*, s.id as student_id, s.name as student_name, s.email as student_email
            FROM point_histories ph
            JOIN students s ON ph.student_id = s.id
            WHERE ph.student_id = ?
            ORDER BY ph.date DESC
            LIMIT 10
        ";

        // Menjalankan query
        $data = DB::select($query, [$id]);
        return $data;
    }

    public function histori(Request $request)
    {
        $title = 'Point Histories';
        $student = Students::where('status', 'ACTIVE')->get();
        $data = [];
        if ($request->from && $request->to) {
            $data = PointHistory::with('student')->whereBetween('date', [$request->from, $request->to]);

            if ($request->student) {
                $data = $data->where('student_id', $request->student)->orderBy('date', 'ASC');
            }
            $data = $data->where('keterangan', '!=', 'Opening Balance')->get();
        }

        // dd($data);
        return view('reedemPoint.history-point', compact('title', 'data', 'student'));
    }

    public function giftPoint(Request $request)
    {
        $student = Students::find($request->idStudent);
        $point = new PointHistory();
        $point->student_id = $request->idStudent;
        $point->date = date('Y-m-d');
        $point->total_point = $request->addPoint;
        $point->keterangan = 'Gift';
        $point->type = 'in';
        $point->balance_in_advanced = $student->total_point;
        $point->save();
        Students::where('id', $request->idStudent)->update([
            'total_point' => $student->total_point + $request->addPoint,
        ]);
        return redirect()->back()->withStatus('Data berhasil diperbarui ');
    }
}
