<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Score;
use App\Models\Students;
use App\Models\StudentScore;
use App\Models\StudentScoreDetail;
use App\Models\TestItems;
use App\Models\Tests as ModelsTests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Test;
use SebastianBergmann\CodeCoverage\Report\Xml\Tests;

use function PHPSTORM_META\type;

class ScoreController extends Controller
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
            $level = Price::get();
            if (Auth::guard('teacher')->check() == true) {
                $where = 'AND id_teacher = ' . Auth::guard('teacher')->user()->id;
            }
            if ($request->branch && Auth::guard('staff')->check() == true) {
                $where = $where . ' AND branch_id = ' . $request->branch;
            }
            if ($request->level && Auth::guard('staff')->check() == true) {
                $where = $where . ' AND priceid = ' . $request->level;
            }
            if ($request->level && Auth::guard('teacher')->check() == true) {
                $where = $where . ' AND priceid = ' . $request->level . ' AND id_teacher =' . Auth::guard('teacher')->user()->id;
            }
            $class = DB::select("SELECT DISTINCT priceid,day1,day2,course_time,id_teacher,price.level,price.program,day_1.day day_one,day_2.day day_two,teacher.name teacher_name,
             student.id_teacher as teacher_id,  student.day1 as d1, student.day2 as d2, branch.location   from student
             join price on student.priceid = price.id
             join day day_1 on student.day1 = day_1.id
             join day day_2 on student.day2 = day_2.id
             join teacher on student.id_teacher = teacher.id
             join branch on student.branch_id = branch.id

              WHERE day1 is NOT null AND day2 is NOT null AND course_time is NOT null AND id_teacher is NOT null AND student.status = 'ACTIVE' $where ORDER BY priceid ASC, day1,course_time;");


            // dd($class);
            return view('score.form', compact('class', 'level'));
        } catch (\Throwable $th) {
            // throw $th;
        }
    }

    public function formCreate(Request $request)
    {
        try {
            $reqTest = $request->test;
            $test = ModelsTests::find($reqTest);
            $testItem = '';
            if ($request->class == 1 || $request->class == 2 || $request->class == 3 || $request->class == 4 || $request->class == 5 || $request->class == 6) {
                $testItem = TestItems::where('id', '!=', 5)->where('id', '!=', 6)->get();
            } else {
                $testItem = TestItems::get();
            }
            $reqClass = $request->class;
            $students = Students::with('score')->where('priceid', $reqClass)->where('day1', $request->day1)->where('day2', $request->day2)->where('id_teacher', $request->teacher)->where('course_time', $request->time)->where('status', 'ACTIVE')->get();
            return view('score.form-index', compact('test', 'testItem', 'students'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $students = Students::where('status', 'ACTIVE')->get();
        $test = ModelsTests::all();
        $item = '';
        if ($request->class == 1 || $request->class == 2 || $request->class == 3 || $request->class == 4 || $request->class == 5 || $request->class == 6) {
            $item = TestItems::where('id', '!=', 5)->where('id', '!=', 6)->orderBy('id', 'ASC')->get();
        } else {
            $item = TestItems::orderBy('id', 'ASC')->get();
        }
        $class = Price::all();
        $title = 'Input Score';
        $data = (object)[
            'type' => 'create',
        ];
        return view('score.form-create', compact('class', 'test', 'title', 'data', 'item', 'students'));
    }

    public function createLastFrom(Request $request)
    {
        $students = Students::where('status', 'ACTIVE')->get();
        $test = ModelsTests::all();
        $item = '';
        if ($request->class == 1 || $request->class == 2 || $request->class == 3 || $request->class == 4 || $request->class == 5 || $request->class == 6) {
            $item = TestItems::where('id', '!=', 5)->where('id', '!=', 6)->orderBy('id', 'ASC')->get();
        } else {
            $item = TestItems::orderBy('id', 'ASC')->get();
        }
        $class = Price::all();
        $title = 'Input Score';
        $data = (object)[
            'type' => 'create',
        ];
        return view('score.form-create-last', compact('class', 'test', 'title', 'data', 'item', 'students'));
    }

    public function lastForm(Request $request)
    {
        $student = Students::find($request->id);
        $class = Price::find($request->class);
        $testItem = '';
        if ($request->class == 1 || $request->class == 2 || $request->class == 3 || $request->class == 4 || $request->class == 5 || $request->class == 6) {
            $testItem = TestItems::where('id', '!=', 5)->where('id', '!=', 6)->get();
        } else {
            $testItem = TestItems::get();
        }
        $studentScore1 = StudentScore::where('student_id', $request->id)->where('price_id', $request->class)->where('test_id', 1)->count();
        $studentScore2 = StudentScore::where('student_id', $request->id)->where('price_id', $request->class)->where('test_id', 2)->count();
        $studentScore3 = StudentScore::where('student_id', $request->id)->where('price_id', $request->class)->where('test_id', 3)->count();
        return view('score.last', compact('student', 'class', 'testItem', 'studentScore1', 'studentScore2', 'studentScore3'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $scores = new StudentScore;
            $scores->test_id = $request->test;
            $scores->student_id = $request->student;
            $scores->average_score = round($request->total);
            $scores->comment = $request->comment ?? '-';
            $scores->price_id = $request->classt;
            $scores->date = $request->date;
            $scores->created_at = Carbon::now();
            $scores->updated_at = Carbon::now();
            $scores->save();
            for ($i = 0; $i < count($request->items); $i++) {
                StudentScoreDetail::create([
                    'student_score_id' => $scores->id,
                    'test_item_id' => $request->items[$i],
                    'score' => $request->score[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            if ($request->day1 != null) {
                return redirect('score/form-create?test=' . $request->test . '&class=' . $request->classt . '&day1=' . $request->day1 . '&day2=' . $request->day2 . '&teacher=' . $request->teacher . '&time=' . $request->time)->with('success', 'Success add Score');
            } else {
                return redirect()->back()->with('success', 'Success add Score');
            }
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to save data');
            return $th;

            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function show(Score $score)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function edit(Score $score)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $score)
    {
        // return $request->all();
        try {
            StudentScore::where('id', $score)->update([
                'average_score' => round($request->total),
                'comment' => $request->comment ?? '-',
                'price_id' => $request->classt,
                'date' => $request->date,
                'updated_at' => Carbon::now(),
            ]);
            for ($i = 0; $i < count($request->items); $i++) {
                StudentScoreDetail::where('id',  $request->idScore[$i])
                    ->update([
                        'score' => $request->score[$i],
                        'updated_at' => Carbon::now(),
                    ]);
            }
            if ($request->day1 != null) {
                return redirect('score/form-create?test=' . $request->test . '&class=' . $request->classt . '&day1=' . $request->day1 . '&day2=' . $request->day2 . '&teacher=' . $request->teacher . '&time=' . $request->time)->with('success', 'Success add Score');
            } else {
                return redirect()->back()->with('success', 'Success edit Score');
            }
        } catch (\Throwable $th) {
            return $th;
            return back()->with('error', 'Failed to update data');
        }
        return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function destroy(Score $score)
    {
        //
    }

    public function filter(Request $request)
    {
        try {
            $data = [];
            $detail = [];
            $query = StudentScore::where('test_id', $request->test)
                ->where('student_id', $request->student)
                ->where('date', $request->date)
                ->first();

            if ($query) {
                $detail = StudentScoreDetail::where('student_score_id', $query->id)->get();
                // return $detail;
            } else {
                $detail = [];
            }
            $data  = ([
                'data' => $query,
                'detail' => $detail,
            ]);
            return $data;
        } catch (\Throwable $th) {
            return $th;
            //throw $th;
        }
        return $request;
    }

    public function filterStudent(Request $request)
    {

        try {
            $query = [];
            $query = Students::where('status', 'ACTIVE')->where('priceid', $request->class)->get();
            return $query;

            // return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
    }

    public function filterStudentByName(Request $request)
    {

        try {
            $query = [];
            $query = Students::where('name', 'like', '%' . $request->name . '%')->get();
            return $query;

            // return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
    }

    public function historyTest(Request $request)
    {
        try {
            $students = Students::where('status', 'ACTIVE')->orderBy('id', 'asc')->get();
            return view('score.history', compact('students'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function createLast($priceId, Request $request)
    {
        $students = Students::where('status', 'ACTIVE')->where('id_teacher', $request->teacher)->where('course_time', $request->time)->where('day1', $request->day1)->where('day2', $request->day2)->where('priceid', $priceId)->get();
        return view('score.form-last-class', compact('students'));
    }

    public function ajaxLastClass(Request $request)
    {
        $students = DB::table('mutasi_siswa as ms')
            ->join('student as s', 's.id', 'ms.student_id')
            ->join('price as p', 'p.id', 'ms.price_id')
            ->select(
                'ms.student_id',
                'ms.price_id',
                'p.program',
            )
            ->where('student_id', $request->id)
            ->get();
        return $students;
    }
}
