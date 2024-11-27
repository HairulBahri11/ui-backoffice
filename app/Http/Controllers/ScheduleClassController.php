<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Students;

class ScheduleClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $day = DB::table('day')->get();
        $class = DB::table('price')->get();
        $teacher = DB::table('teacher')->get();
        $staff = DB::table('staff')->get();
        $data = '';
        if ($request->class) {
            $data = DB::table('student')->where('priceid', $request->class)->where('status', 'ACTIVE')->get();
        }
        return view('schedule-class.index', compact('day', 'class', 'data', 'teacher', 'staff'));
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
        try {
            $day2 = $request->day2 != null ? $request->day2 : $request->day1;
            DB::table('student')
                ->where('priceid', $request->class)
                ->where('id_teacher', $request->teacher)
                ->where('day1', $request->day1)
                ->where('day2', $day2)
                ->where('course_time', $request->time)
                ->update(['day1' => null, 'day2' => null, 'id_teacher' => null, 'course_time' => null, 'id_staff' => null]);
            DB::transaction(function () use ($request, $day2) {
                if ($request->upcls) {
                    foreach ($request->upcls as $key => $value) {
                        // DB::table('student')
                        //     ->where('id', $value)
                        //     ->update(['day1' => null]);
                        $update = ['day1' => $request->day1, 'day2' => $day2, 'id_teacher' => $request->teacher, 'course_time' => $request->time, 'id_staff' => $request->staff, 'course_hour' => $request->course_time, 'is_class_new' => false];
                        DB::table('student')
                            ->where('id', $value)
                            ->update($update);
                    }
                }
            });
            return redirect()->back()->with('message', "Student's Schedule Updated!");
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $update = [
                    'day1' => null,
                    'day2' => null,
                    'course_time' => null,
                    'id_teacher' => null,
                    'is_class_new' => false,
                ];
                Students::where('priceid', $request->priceid)->where('day1', $request->day1)->where('day2', $request->day2)->where('course_time', $request->course_time)->where('id_teacher', $request->id_teacher)->update($update);
            });

            return redirect()->back()->with('message', 'Berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('message', 'Terjadi kesalahan pada database : ' . $e->getMessage());
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
    public function destroy($id)
    {
        //
    }
}
