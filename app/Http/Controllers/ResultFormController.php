<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResultFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = DB::table('student_scores')
            ->select(
                'student_scores.*',
                'student.name',
                'tests.name as test_name',
                'price.program as class',
                'teacher.name as teacher_name'
            )
            ->join('student', 'student.id', '=', 'student_scores.student_id')
            ->join('tests', 'tests.id', '=', 'student_scores.test_id')
            ->join('price', 'price.id', '=', 'student_scores.price_id')
            ->join('teacher', 'teacher.id', '=', 'student.id_teacher')
            ->where('student.status', 'ACTIVE')
            ->whereNotNull('student.priceid')
            ->whereNotNull('student.course_time')
            ->where('student_scores.follow_up', '=', 1)
            ->where('student_scores.average_score', '<=', 59)
            ->where('student_scores.average_score', '>', 0)

            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereBetween('student.priceid', [1, 21])
                        ->whereIn('student_scores.test_id', [1, 2]);
                })->orWhere(function ($subQuery) {
                    $subQuery->where('student.priceid', '>', 21)
                        ->where('student_scores.test_id', 1);
                });
            });

        // **Menambahkan filter hanya jika teacher login**
        if (Auth::guard('teacher')->check()) {
            $result->where('student.id_teacher', Auth::guard('teacher')->user()->id);
            $count_result = $result->count();
        }
        $result->whereColumn('student_scores.price_id', 'student.priceid');


        $resultnya = $result->get();
        $count_result = $result->whereNull('student_scores.staff_comment')->count();

        return view('result-form.index', compact('resultnya', 'count_result'));
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
        //
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
        $student_scores = StudentScore::where('id', $id)->first();
        $student_scores->staff_comment = $request->staff_comment;
        $student_scores->save();

        return redirect('/result-form')->with('status', 'Success update follow up');
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
