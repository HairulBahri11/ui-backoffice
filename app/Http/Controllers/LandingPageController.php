<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Staff;
use App\Models\Students;
use App\Models\ReedemItems;
use App\Models\ReedemPoint;
use App\Models\PointHistory;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Models\StudentScoreDetail;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function redeemPoint(Request $request)
    {
        try {
            $class = Price::all();
            // $students = Students::join('price as p', 'p.id', 'student.priceid')
            //     ->select('student.name', 'student.id', 'student.total_point')
            //     ->get();
            $students = Students::where('status', 'ACTIVE')->get();
            $item = ReedemItems::all();
            $title = 'Landing Page Reedem Point';
            return view('landing-page.redeem-point', compact('students', 'title', 'item', 'class'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function redeemPointStudent($id)
    {
        $student = Students::find($id);
        return $student;
    }

    public function storeReedemPoint(Request $request)
    {
        $student = $request->id_student == null ? $request->student : $request->id_student;
        $findPointStudent = Students::find($student);
        ReedemPoint::create([
            'point' => intval($request->total_point),
            'student_id' => $student,
        ]);
        PointHistory::create([
            'student_id' => $student,
            'date' => date('Y-m-d'),
            'total_point' => intval($request->total_point),
            'type' => 'redeem',
            'keterangan' => 'Reedem Point',
            'balance_in_advanced' => $findPointStudent->total_point,
        ]);
        $newPoint = intval($request->point) - intval($request->total_point);
        Students::where('id', $student)
            ->update([
                'total_point' =>  $newPoint,
            ]);
        return redirect('/landing-page/redeem-point')->with('status', 'Success Reedem Point');
    }

    public function eCertificate($id)
    {
        try {
            $student = Students::with('teacher')->find($id);
            if ($student->is_certificate == true) {
                $class = Price::find($student->priceid);
                $score1 = StudentScore::where('student_id', $id)->where('price_id', $class->id)->where('test_id', 1)->first();
                if ($score1) {
                    $writing1 = StudentScoreDetail::where('student_score_id', $score1->id)->where('test_item_id', 1)->first();
                    $speaking1 = StudentScoreDetail::where('student_score_id', $score1->id)->where('test_item_id', 2)->first();
                    $reading1 = StudentScoreDetail::where('student_score_id', $score1->id)->where('test_item_id', 3)->first();
                    $listening1 = StudentScoreDetail::where('student_score_id', $score1->id)->where('test_item_id', 4)->first();
                    $grammar1 = StudentScoreDetail::where('student_score_id', $score1->id)->where('test_item_id', 5)->first();
                    $vocabulary1 = StudentScoreDetail::where('student_score_id', $score1->id)->where('test_item_id', 6)->first();
                    $average_score1 = $score1->average_score;
                } else {
                    $writing1 = 0;
                    $speaking1 = 0;
                    $reading1 = 0;
                    $listening1 = 0;
                    $grammar1 = 0;
                    $vocabulary1 = 0;
                    $average_score1 = 0;
                }
                // 2
                $score2 = StudentScore::where('student_id', $id)->where('price_id', $class->id)->where('test_id', 2)->first();
                if ($score2) {
                    $writing2 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 1)->first();
                    $speaking2 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 2)->first();
                    $reading2 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 3)->first();
                    $listening2 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 4)->first();
                    $grammar2 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 5)->first();
                    $vocabulary2 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 6)->first();
                    $average_score2 = $score2->average_score;
                } else {
                    $writing2 = 0;
                    $speaking2 = 0;
                    $reading2 = 0;
                    $listening2 = 0;
                    $grammar2 = 0;
                    $vocabulary2 = 0;
                    $average_score2 = 0;
                }

                // 3
                $score3 = StudentScore::where('student_id', $id)->where('price_id', $class->id)->where('test_id', 3)->first();
                if ($score3) {
                    $writing3 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 1)->first();
                    $speaking3 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 2)->first();
                    $reading3 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 3)->first();
                    $listening3 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 4)->first();
                    $grammar3 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 5)->first();
                    $vocabulary3 = StudentScoreDetail::where('student_score_id', $score2->id)->where('test_item_id', 6)->first();
                    $average_score3 = $score3->average_score;
                } else {
                    $writing3 = 0;
                    $speaking3 = 0;
                    $reading3 = 0;
                    $listening3 = 0;
                    $grammar3 = 0;
                    $vocabulary3 = 0;
                    $average_score3 = 0;
                }
                return view('landing-page.e-certificate', compact('student', 'class', 'score1', 'writing1', 'speaking1', 'reading1', 'listening1', 'grammar1', 'vocabulary1', 'average_score1', 'score2', 'writing2', 'speaking2', 'reading2', 'listening2', 'grammar2', 'vocabulary2', 'average_score2', 'score3', 'writing3', 'speaking3', 'reading3', 'listening3', 'grammar3', 'vocabulary3', 'average_score3', 'id'));
            } else {
                return view('landing-page.not-found', compact('id'));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function extraPoint()
    {
        try {
            $title = 'Landing Page Extra Point';
            $students = Students::where('status', 'ACTIVE')->whereNotNull('priceid')->whereNotNull('course_time')
                ->orderBy('id', 'asc')->get();
            return view('landing-page.extra-point', compact('students', 'title'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function storeExtraPoint(Request $request)
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
                'balance_in_advanced' => $request->point_history,
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
                return redirect('landing-page/extra-point?student=' . $request->student_id)->with('status', 'Success add Extra Exam Point');
            } else {
                DB::rollBack();
                session()->flash('error', 'Failed to add extra exam point');
                return redirect('landing-page/extra-point?student=' . $request->student_id)->with('Error', 'Failed to add extra exam point');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Data gagal disimpan: ' . $e->getMessage());
            return redirect('landing-page/extra-point?student=' . $request->student_id)->with('Error', 'Data gagal disimpan: ' . $e->getMessage());
        }
    }
}
