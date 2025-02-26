<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $students = Students::where('status', 'ACTIVE')->whereNotNull('priceid')->whereNotNull('course_time')
                ->orderBy('id', 'asc')->get();
            return view('extra-exam.index', compact('students'));
        } catch (\Throwable $th) {
            //throw $th;
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

        // add to tabel extra_exam_point and point_history
        try {
            DB::beginTransaction();

            # Add extra exam point 
            $extra_exam_point = DB::table('extra_exam_point')->insert([
                'student_id'    => $request->student_id,
                'price_id'      => $request->price_id,
                'teacher_id'    => $request->teacher_id,
                'day1'          => $request->day1,
                'day2'          => $request->day2,
                'course_time'   => $request->course_time,
                'category'      => $request->category,
                'point'         => $request->point,
                'description'       => $request->description,
                'tgl_input'     => now()->format('Y-m-d'),
                'updated_at'    => now(),
            ]);

            # Add point history
            $point_history = DB::table('point_histories')->insert([
                'student_id'        => $request->student_id,
                'keterangan'        => $request->category,
                'total_point'       => $request->point,
                'type'              => 'in',
                'balance_in_advanced' => $request->point_history + $request->point,
                'date'              => now()->format('Y-m-d'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $student = Students::where('id', $request->student_id)->first();
            if ($student) {
                $student->total_point += $request->point;
                $student->save();
            }

            // Cek apakah semua operasi berhasil
            if ($extra_exam_point && $point_history) {
                DB::commit();
                session()->flash('success', 'Success add Extra Exam Point');
            } else {
                DB::rollBack();
                session()->flash('error', 'Failed to add extra exam point');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Data gagal disimpan: ' . $e->getMessage());
        }

        return redirect('/extra-point?student=' . $request->student_id);
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
    public function destroy($id)
    {
        //
    }
}
