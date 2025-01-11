<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CertificateStudent;
use App\Models\FollowUp;
use App\Models\Price;
use App\Models\Score;
use App\Models\Students;
use App\Models\StudentScore;
use App\Models\StudentScoreDetail;
use App\Models\Tests;
use App\Models\TestItems;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class ScoreController extends Controller
{
    public function getTest()
    {
        try {
            $test = Tests::all();
            return response()->json([
                'code' => '00',
                'payload' => $test,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }

    /*public function getCertificate($studentId){
        try {
            //$test = Tests::all();

            $getStudent = Students::find($studentId);
            $score = StudentScore::join('tests as t', 't.id', 'student_scores.test_id')
                ->join('price as p', 'p.id', 'student_scores.price_id')
                ->select('p.program', 't.name', 'student_scores.average_score', 'student_scores.average_score', 'student_scores.id as scoreId', 'student_scores.comment', 'student_scores.date', 'student_scores.price_id')
                //->where('student_scores.test_id', $testId)
                ->where('student_scores.price_id', $getStudent->priceid)
                ->where('student_scores.student_id', $studentId)
                ->first();

            $file = '';

            if ($score != null) {

                $item = StudentScoreDetail::join('test_items as ti', 'ti.id', 'student_score_details.test_item_id')
                    ->select('ti.name', 'student_score_details.*')
                    ->where('student_score_details.student_score_id', $score->scoreId)
                    ->get();
                $score['grade'] = Helper::getGrade($score->average_score);
                $items = [];
                foreach ($item as $value) {
                    $value['grade'] = Helper::getGrade($value->score);
                    array_push($items, $value);
                }


                $nama_file = time() . '_' . $getStudent->name . '.pdf';

                new \App\Libraries\Pdf();

                $pdf = new \setasign\Fpdi\Fpdi();
                $pdf->SetTitle('Cerfiticate');
                $pdf->SetAutoPageBreak(false, 5);

                $pdf->AddPage('L');

                //tod - pretod
                if(in_array($score->price_id, [1, 2, 3, 4, 5, 6])){
                    $pdf->Image(public_path('certificate/template/pretod-tod.jpg'), 0, 0, 297, 210);

                }
                //kid -advanced
                else{
                    $pdf->Image(public_path('certificate/template/kid-advanced.jpg'), 0, 0, 297, 210);

                    $pdf->SetFont('Arial', 'B', '35');
                    $pdf->SetXY(87, 65);
                    $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

                    $pdf->SetFont('Arial', 'B', '20');
                    $pdf->SetXY(87, 82);
                    $pdf->Cell(120, 10, $score->program, '', 0, 'C');

                    $pdf->SetFont('Arial', 'B', '15');
                    $pdf->SetXY(75, 92);
                    $pdf->Cell(60, 10, \Carbon\Carbon::parse($score->date)->format('j F Y'), 0, 'L');

                    $score_total = 0;

                    foreach(TestItems::get() as $item){
                         $score1 = DB::table('student_scores')
                            ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                            ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                            ->where('student_id', $getStudent->id)
                            ->where('price_id', $score->price_id)
                            ->where('test_id', 1)
                            ->where('student_score_details.test_item_id', $item->id)
                            ->first();
                        $score2 = DB::table('student_scores')
                            ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                            ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                            ->where('student_id', $getStudent->id)
                            ->where('price_id', $score->price_id)
                            ->where('test_id', 2)
                            ->where('student_score_details.test_item_id', $item->id)
                            ->first();
                        $score3 = DB::table('student_scores')
                            ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                            ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                            ->where('student_id', $getStudent->id)
                            ->where('price_id', $score->price_id)
                            ->where('test_id', 3)
                            ->where('student_score_details.test_item_id', $item->id)
                            ->first();

                        $divider = 0;

                        $score_test1 = 0;
                        $score_test2 = 0;
                        $score_test3 = 0;

                        if($score1){
                            if($score1->score_test!=0){
                                $score_test1 = $score1->score_test;
                                $divider +=1;
                            }
                        }

                        if($score2){
                            if($score2->score_test!=0){
                                $score_test2 = $score2->score_test;
                                $divider +=1;
                            }
                        }

                        if($score3){
                            if($score3->score_test!=0){
                                $score_test3 = $score3->score_test;
                                $divider +=1;
                            }
                        }

                        if($divider == 0){
                            $divider = 1;
                        }

                        $score_test = round(($score_test1 + $score_test2 + $score_test3) / $divider);
                        $score_total += $score_test;

                        if($item->id==1){
                            $pdf->SetFont('Arial', 'B', '20');
                            $pdf->SetXY(73, 119);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==2){
                            $pdf->SetXY(73, 128);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==3){
                            $pdf->SetXY(147, 119);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==4){
                            $pdf->SetXY(147, 128);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==5){
                            $pdf->SetXY(231, 119);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==6){
                            $pdf->SetXY(231, 128);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                    }

                    //$pdf->SetFont('Arial', 'B', '20');
                    //$pdf->SetXY(73, 119);
                    //$pdf->Cell(40, 10, $items[0]['score'] . '/' . $items[0]['grade'], '', 0, 'L');

                    //$pdf->SetXY(73, 128);
                    //$pdf->Cell(40, 10, $items[1]['score'] . '/' . $items[1]['grade'], '', 0, 'L');

                    //$pdf->SetXY(147, 119);
                    //$pdf->Cell(40, 10, $items[2]['score'] . '/' . $items[2]['grade'], '', 0, 'L');

                    //$pdf->SetXY(147, 128);
                    //$pdf->Cell(40, 10, $items[3]['score'] . '/' . $items[3]['grade'], '', 0, 'L');

                    //$pdf->SetXY(231, 119);
                    //$pdf->Cell(40, 10, $items[4]['score'] . '/' . $items[4]['grade'], '', 0, 'L');

                    //$pdf->SetXY(231, 128);
                    //$pdf->Cell(40, 10, $items[5]['score'] . '/' . $items[5]['grade'], '', 0, 'L');

                    $pdf->SetFont('Arial', 'B', '45');
                    $pdf->SetXY(87, 155);

                    //$pdf->Cell(120, 20, $score->average_score . '/' . Helper::getGrade($score->average_score), '', 0, 'C');

                    $score_average = round($score_total / 6);
                    $pdf->Cell(120, 20, $score_average . '/' . Helper::getGrade($score_average), '', 0, 'C');

                    $pdf->SetFont('Arial', 'B', '20');
                    $pdf->SetXY(200, 187);
                    $pdf->Cell(40, 10, $getStudent->teacher != null ? $getStudent->teacher->name : '', '', 0, 'L');

                    $pdf->Image(public_path('../../ui/upload/signature/' . $getStudent->teacher->signature), 215, 170, 19.2, 12.6);
                    $pdf->Image(public_path('../../ui/upload/signature/principal.png'), 70, 170, 19.2, 12.6);
                }

                $pdf->Output(public_path('certificate/' . $nama_file), 'F');

                //$score['file'] = public_path('certificate/' . $nama_file);
                $file = asset('certificate/' . $nama_file);

            }

            return response()->json([
                'code' => '00',
                //'payload' => $test,
                'file' => $file,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }*/

    public function getResult($studentId, Request $request)
    {
        try {
            $followUp = FollowUp::where('student_id', $studentId)->first();
            $countClass = DB::select(
                'SELECT price.program,price.id
                    FROM student_scores
                    JOIN price ON student_scores.price_id = price.id
                    WHERE student_id = ' . $studentId . '
                GROUP BY student_scores.student_id, student_scores.price_id;'
            );
            if ($followUp) {
                $class = FollowUp::where('student_id', $studentId)->join('price', 'price.id', 'follow_up.old_price_id')->join('student', 'student.id', 'follow_up.student_id')->select('price.program', 'student.is_certificate', 'student.date_certificate', 'follow_up.old_price_id as priceid')->first();
            } else {
                $class = Students::join('price', 'price.id', 'student.priceid')
                    ->select('price.program', 'student.is_certificate', 'student.date_certificate', 'student.priceid')
                    ->where('student.id', $studentId)->first();
            }
            $sc = StudentScore::where('student_id', $studentId)->where('price_id', $class->priceid)->get();

            if ($followUp) {
                $getStudent = FollowUp::where('student_id', $studentId)->first();
                $getStudent->priceid = $getStudent->old_price_id;
            } else {
                $getStudent = Students::find($studentId);
            }
            $score = StudentScore::join('tests as t', 't.id', 'student_scores.test_id')
                ->join('price as p', 'p.id', 'student_scores.price_id')
                ->select('p.program', 't.name', 'student_scores.average_score', 'student_scores.average_score', 'student_scores.id as scoreId', 'student_scores.comment', 'student_scores.date', 'student_scores.price_id')
                //->where('student_scores.test_id', $testId)
                ->where('student_scores.price_id', $request->class ?? $getStudent->priceid)
                ->where('student_scores.student_id', $studentId)
                ->first();

            $file = '';
            $certificateStudent = CertificateStudent::where('student_id', $studentId)->where('price_id', $request->class ?? $getStudent->priceid)->get();

            if ($score != null) {
                $item = StudentScoreDetail::join('test_items as ti', 'ti.id', 'student_score_details.test_item_id')
                    ->select('ti.name', 'student_score_details.*')
                    ->where('student_score_details.student_score_id', $score->scoreId)
                    ->get();
                $score['grade'] = Helper::getGrade($score->average_score);
                $items = [];
                foreach ($item as $value) {
                    $value['grade'] = Helper::getGrade($value->score);
                    array_push($items, $value);
                }

                if (count($certificateStudent) == 0) {
                    $nama_file = $score->price_id . '-' . time() . '_' . $getStudent->name . '.pdf';

                    new \App\Libraries\Pdf();

                    $pdf = new \setasign\Fpdi\Fpdi();
                    $pdf->SetTitle('Cerfiticate');
                    $pdf->SetAutoPageBreak(false, 5);

                    $pdf->AddPage('L');

                    //tod - pretod
                    if (in_array($score->price_id, [1, 2, 3, 4, 5, 6])) {
                        $pdf->Image(public_path('certificate/template/pretod-tod.jpg'), 0, 0, 297, 210);
                        $pdf->SetFont('Arial', 'B', '35');
                        $pdf->SetXY(87, 65);
                        $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

                        $pdf->SetFont('Arial', 'B', '20');
                        $pdf->SetXY(87, 82);
                        $pdf->Cell(120, 10, $score->program, '', 0, 'C');

                        $pdf->SetFont('Arial', 'B', '15');
                        $pdf->SetXY(75, 92);
                        $pdf->Cell(60, 10, $class->date_certificate != null ? \Carbon\Carbon::parse($class->date_certificate)->format('j F Y') : \Carbon\Carbon::now()->format('j F Y'), 0, 'L');

                        $pdf->SetFont('Arial', 'B', '45');
                        $pdf->SetXY(87, 155);

                        $score_total = 0;

                        foreach (TestItems::get() as $item) {
                            $score1 = DB::table('student_scores')
                                ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                ->where('student_id', $getStudent->id)
                                ->where('price_id', $score->price_id)
                                ->where('test_id', 1)
                                ->where('student_score_details.test_item_id', $item->id)
                                ->first();
                            $score2 = DB::table('student_scores')
                                ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                ->where('student_id', $getStudent->id)
                                ->where('price_id', $score->price_id)
                                ->where('test_id', 2)
                                ->where('student_score_details.test_item_id', $item->id)
                                ->first();
                            $score3 = DB::table('student_scores')
                                ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                ->where('student_id', $getStudent->id)
                                ->where('price_id', $score->price_id)
                                ->where('test_id', 3)
                                ->where('student_score_details.test_item_id', $item->id)
                                ->first();

                            $divider = 0;

                            $score_test1 = 0;
                            $score_test2 = 0;
                            $score_test3 = 0;

                            if ($score1) {
                                if ($score1->score_test != 0) {
                                    $score_test1 = $score1->score_test;
                                    $divider += 1;
                                }
                            }

                            if ($score2) {
                                if ($score2->score_test != 0) {
                                    $score_test2 = $score2->score_test;
                                    $divider += 1;
                                }
                            }

                            if ($score3) {
                                if ($score3->score_test != 0) {
                                    $score_test3 = $score3->score_test;
                                    $divider += 1;
                                }
                            }

                            if ($divider == 0) {
                                $divider = 1;
                            }

                            $score_test = round(($score_test1 + $score_test2 + $score_test3) / $divider);
                            $score_total += $score_test;

                            if ($item->id == 1) {
                                $pdf->SetFont('Arial', 'B', '20');
                                $pdf->SetXY(123, 119);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 2) {
                                $pdf->SetXY(123, 128);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 3) {
                                $pdf->SetXY(197, 119);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 4) {
                                $pdf->SetXY(197, 128);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            }
                        }


                        $pdf->SetFont('Arial', 'B', '45');
                        $score_average = round($score_total / 4);
                        $pdf->SetXY(133, 130);
                        $pdf->Cell(40, 70, $score_average . '/' . Helper::getGrade($score_average), '', 0, 'C');

                        $pdf->SetFont('Arial', 'B', '20');
                        $pdf->SetXY(200, 187);
                        $pdf->Cell(40, 10, $getStudent->teacher != null ? $getStudent->teacher->name : '', '', 0, 'L');

                        $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/' . $getStudent->teacher->signature, 215, 170, 19.2, 12.6);
                        $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/principal.png', 70, 170, 19.2, 12.6);
                    }
                    //kid -advanced
                    else {
                        $pdf->Image(public_path('certificate/template/kid-advanced.jpg'), 0, 0, 297, 210);

                        $pdf->SetFont('Arial', 'B', '35');
                        $pdf->SetXY(87, 65);
                        $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

                        $pdf->SetFont('Arial', 'B', '20');
                        $pdf->SetXY(87, 82);
                        $pdf->Cell(120, 10, $score->program, '', 0, 'C');

                        $pdf->SetFont('Arial', 'B', '15');
                        $pdf->SetXY(75, 92);
                        $pdf->Cell(60, 10, $class->date_certificate != null ? \Carbon\Carbon::parse($class->date_certificate)->format('j F Y') : \Carbon\Carbon::now()->format('j F Y'), 0, 'L');

                        $score_total = 0;

                        foreach (TestItems::get() as $item) {
                            $score1 = DB::table('student_scores')
                                ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                ->where('student_id', $getStudent->id)
                                ->where('price_id', $score->price_id)
                                ->where('test_id', 1)
                                ->where('student_score_details.test_item_id', $item->id)
                                ->first();
                            $score2 = DB::table('student_scores')
                                ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                ->where('student_id', $getStudent->id)
                                ->where('price_id', $score->price_id)
                                ->where('test_id', 2)
                                ->where('student_score_details.test_item_id', $item->id)
                                ->first();
                            $score3 = DB::table('student_scores')
                                ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                ->where('student_id', $getStudent->id)
                                ->where('price_id', $score->price_id)
                                ->where('test_id', 3)
                                ->where('student_score_details.test_item_id', $item->id)
                                ->first();

                            $divider = 0;

                            $score_test1 = 0;
                            $score_test2 = 0;
                            $score_test3 = 0;

                            if ($score1) {
                                if ($score1->score_test != 0) {
                                    $score_test1 = $score1->score_test;
                                    $divider += 1;
                                }
                            }

                            if ($score2) {
                                if ($score2->score_test != 0) {
                                    $score_test2 = $score2->score_test;
                                    $divider += 1;
                                }
                            }

                            if ($score3) {
                                if ($score3->score_test != 0) {
                                    $score_test3 = $score3->score_test;
                                    $divider += 1;
                                }
                            }

                            if ($divider == 0) {
                                $divider = 1;
                            }

                            $score_test = round(($score_test1 + $score_test2 + $score_test3) / $divider);
                            $score_total += $score_test;

                            if ($item->id == 1) {
                                $pdf->SetFont('Arial', 'B', '20');
                                $pdf->SetXY(73, 119);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 2) {
                                $pdf->SetXY(73, 128);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 3) {
                                $pdf->SetXY(147, 119);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 4) {
                                $pdf->SetXY(147, 128);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 5) {
                                $pdf->SetXY(231, 119);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            } else if ($item->id == 6) {
                                $pdf->SetXY(231, 128);
                                $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                            }
                        }

                        $pdf->SetFont('Arial', 'B', '45');
                        $pdf->SetXY(87, 155);

                        $score_average = round($score_total / 6);
                        $pdf->Cell(120, 20, $score_average . '/' . Helper::getGrade($score_average), '', 0, 'C');

                        $pdf->SetFont('Arial', 'B', '20');
                        $pdf->SetXY(200, 187);
                        $pdf->Cell(40, 10, $getStudent->teacher != null ? $getStudent->teacher->name : '', '', 0, 'L');

                        $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/' . $getStudent->teacher->signature, 215, 170, 19.2, 12.6);
                        $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/principal.png', 70, 170, 19.2, 12.6);
                    }

                    $pdf->Output(public_path('certificate/' . $nama_file), 'F');
                    $studentCertificate = new CertificateStudent();
                    $studentCertificate->student_id = $studentId;
                    $studentCertificate->price_id = $request->class ?? $getStudent->priceid;
                    $studentCertificate->file = asset('certificate/' . $nama_file);
                    $studentCertificate->save();

                    $file = $studentCertificate->file;
                } else {
                    $filecertificate = CertificateStudent::where('student_id', $studentId)->where('price_id', $request->class ?? $getStudent->priceid)->first();
                    $file = $filecertificate->file;
                }
            }


            if (count($sc) != 0) {
                $test = Tests::count();
                $totalScore = 0;
                $totalTest = 0;
                foreach ($sc as $s) {
                    $totalScore += $s->average_score;
                    $totalTest += 1;
                }
                $total = $totalScore / $totalTest;


                // get $file just name of certificate
                // https://primtech-sistem.com/ui-payment-backoffice/public/certificate/4-1713942111_Kennard Ethan Lie.pdf
                $fileName = explode('/certificate/' , $file)[1];
                // contoh outputnya
                // 4-1713942111_Kennard Ethan Lie.pdf
            

                $data = ([
                    'total_score' => $total,
                    'class' => $class->program,
                    'is_certificate' => $class->is_certificate,
                    'grade' => Helper::getGrade($total),
                    'total_test' => $test,
                    'total_test_passed' => $totalTest,
                    'file' => $fileName,
                    'class_id' => $class->priceid,
                    'count_class' => count($countClass)
                ]);
            } else {
                // $fileName = explode('/certificate/' , $file)[0];
                $data = ([
                    'total_score' => 0,
                    'class' => $class->program,
                    'is_certificate' => $class->is_certificate,
                    'grade' => Helper::getGrade(0),
                    'total_test' => 0,
                    'total_test_passed' => 0,
                    'file' => $file,
                    'class_id' => $class->priceid,
                    'count_class' => count($countClass)
                ]);
            }
            return response()->json([
                'code' => '00',
                'payload' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error ' . $th,
                'message' => $th,
            ], 403);
        }
    }


    public function getScoreByTest($studentId, $testId, Request $request)
    {
        try {
            $getStudent = Students::find($studentId);
            $score = StudentScore::join('tests as t', 't.id', 'student_scores.test_id')
                ->join('price as p', 'p.id', 'student_scores.price_id')
                ->select('p.program', 't.name', 'student_scores.average_score', 'student_scores.average_score', 'student_scores.id as scoreId', 'student_scores.comment', 'student_scores.date', 'student_scores.price_id')
                ->where('student_scores.test_id', $testId);
            if ($request->class) {
                $score = $score->where('student_scores.price_id', $request->class);
            } else {
                $score = $score->where('student_scores.price_id', $getStudent->priceid);
            }

            $score = $score->where('student_scores.student_id', $studentId)
                ->first();
            if ($score != null) {

                $item = StudentScoreDetail::join('test_items as ti', 'ti.id', 'student_score_details.test_item_id')
                    ->select('ti.name', 'student_score_details.*')
                    ->where('student_score_details.student_score_id', $score->scoreId)
                    ->get();
                $score['grade'] = Helper::getGrade($score->average_score);
                $items = [];
                foreach ($item as $value) {
                    $value['grade'] = Helper::getGrade($value->score);
                    array_push($items, $value);
                }


                /*$nama_file = time() . '_' . $getStudent->name . '.pdf';

                new \App\Libraries\Pdf();

                $pdf = new \setasign\Fpdi\Fpdi();
                $pdf->SetTitle('Cerfiticate');
                $pdf->SetAutoPageBreak(false, 5);

                $pdf->AddPage('L');

                //tod - pretod
                if(in_array($score->price_id, [1, 2, 3, 4, 5, 6])){
                    $pdf->Image(public_path('certificate/template/pretod-tod.jpg'), 0, 0, 297, 210);

                }
                //kid -advanced
                else{
                    $pdf->Image(public_path('certificate/template/kid-advanced.jpg'), 0, 0, 297, 210);

                    $pdf->SetFont('Arial', 'B', '35');
                    $pdf->SetXY(87, 65);
                    $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

                    $pdf->SetFont('Arial', 'B', '20');
                    $pdf->SetXY(87, 82);
                    $pdf->Cell(120, 10, $score->program, '', 0, 'C');

                    $pdf->SetFont('Arial', 'B', '15');
                    $pdf->SetXY(75, 92);
                    $pdf->Cell(60, 10, \Carbon\Carbon::parse($score->date)->format('j F Y'), 0, 'L');


                    foreach(TestItems::get() as $item){
                         $score1 = DB::table('student_scores')
                            ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                            ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                            ->where('student_id', $getStudent->id)
                            ->where('price_id', $score->price_id)
                            ->where('test_id', 1)
                            ->where('student_score_details.test_item_id', $item->id)
                            ->first();
                        $score2 = DB::table('student_scores')
                            ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                            ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                            ->where('student_id', $getStudent->id)
                            ->where('price_id', $score->price_id)
                            ->where('test_id', 2)
                            ->where('student_score_details.test_item_id', $item->id)
                            ->first();
                        $score3 = DB::table('student_scores')
                            ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                            ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                            ->where('student_id', $getStudent->id)
                            ->where('price_id', $score->price_id)
                            ->where('test_id', 3)
                            ->where('student_score_details.test_item_id', $item->id)
                            ->first();

                        $divider = 0;

                        $score_test1 = 0;
                        $score_test2 = 0;
                        $score_test3 = 0;

                        if($score1){
                            if($score1->score_test!=0){
                                $score_test1 = $score1->score_test;
                                $divider +=1;
                            }
                        }

                        if($score2){
                            if($score2->score_test!=0){
                                $score_test2 = $score2->score_test;
                                $divider +=1;
                            }
                        }

                        if($score3){
                            if($score3->score_test!=0){
                                $score_test3 = $score3->score_test;
                                $divider +=1;
                            }
                        }

                        if($divider == 0){
                            $divider = 1;
                        }

                        $score_test = round(($score_test1 + $score_test2 + $score_test3) / $divider);

                        Helper::getGrade($score->score_test);

                        if($item->id==1){
                            $pdf->SetFont('Arial', 'B', '20');
                            $pdf->SetXY(73, 119);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==2){
                            $pdf->SetXY(73, 128);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==3){
                            $pdf->SetXY(147, 119);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==4){
                            $pdf->SetXY(147, 128);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==5){
                            $pdf->SetXY(231, 119);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                        else if($item->id==6){
                            $pdf->SetXY(231, 128);
                            $pdf->Cell(40, 10, $score_test . '/' . Helper::getGrade($score_test), '', 0, 'L');
                        }
                    }

                    //$pdf->SetFont('Arial', 'B', '20');
                    //$pdf->SetXY(73, 119);
                    //$pdf->Cell(40, 10, $items[0]['score'] . '/' . $items[0]['grade'], '', 0, 'L');

                    //$pdf->SetXY(73, 128);
                    //$pdf->Cell(40, 10, $items[1]['score'] . '/' . $items[1]['grade'], '', 0, 'L');

                    //$pdf->SetXY(147, 119);
                    //$pdf->Cell(40, 10, $items[2]['score'] . '/' . $items[2]['grade'], '', 0, 'L');

                    //$pdf->SetXY(147, 128);
                    //$pdf->Cell(40, 10, $items[3]['score'] . '/' . $items[3]['grade'], '', 0, 'L');

                    //$pdf->SetXY(231, 119);
                    //$pdf->Cell(40, 10, $items[4]['score'] . '/' . $items[4]['grade'], '', 0, 'L');

                    //$pdf->SetXY(231, 128);
                    //$pdf->Cell(40, 10, $items[5]['score'] . '/' . $items[5]['grade'], '', 0, 'L');

                    $pdf->SetFont('Arial', 'B', '45');
                    $pdf->SetXY(87, 155);
                    $pdf->Cell(120, 20, $score->average_score . '/' . Helper::getGrade($score->average_score), '', 0, 'C');

                    $pdf->SetFont('Arial', 'B', '20');
                    $pdf->SetXY(200, 187);
                    $pdf->Cell(40, 10, $getStudent->teacher != null ? $getStudent->teacher->name : '', '', 0, 'L');

                    $pdf->Image(public_path('../../ui/upload/signature/' . $getStudent->teacher->signature), 215, 170, 19.2, 12.6);
                    $pdf->Image(public_path('../../ui/upload/signature/principal.png'), 70, 170, 19.2, 12.6);
                }

                $pdf->Output(public_path('certificate/' . $nama_file), 'F');

                //$score['file'] = public_path('certificate/' . $nama_file);
                $score['file'] = asset('certificate/' . $nama_file);*/

                $data = ([
                    'score' => $score,
                    'scoreItems' => $items,
                ]);
            } else {
                $data = ([
                    'score' => [],
                    'scoreItems' => [],
                ]);
            };

            return response()->json([
                'code' => '00',
                'payload' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '400',
                'error' => 'internal server error',
                'message' => $th,
            ], 403);
        }
    }
}
