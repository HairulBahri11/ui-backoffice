<?php

namespace App\Http\Controllers\API;

use Helper;
use setasign\Fpdi\Fpdi;
use App\Models\FollowUp;
use App\Models\Students;
use App\Models\TestItems;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SertificateController extends Controller
{
    public function generateCertificate($studentId)
    {
        $kidSeries = [
            "Kid 1",
            "Kid 2",
            "Kid 3",
            "Kid 4",
            "Kid 5",
            "Kid 6",
            "Starter",
            "Teen 1",
            "Teen 2",
            "Teen 3",
            "Teen 4",
            "Teen 5",
            "Teen 6",
            "Teen 7",
            "Teen 8",
            "Adult 1",
            "Adult 2",
            "Adult 3",
            "Adult 4",
            "Adult 5",
            "Adult 6",
            "Adult 7",
            "Adult 8",
            "Adult 9",
            "Adult 10",
            "Conversation 1",
            "Conversation 2",
            "Conversation 3",
            "Conversation 4",
            "Conversation 5",
            "Conversation 6",
            "Private",
            "Advance",
            "Kids Club",
            "Semi Private",
            "Advanced 1",
            "Advanced 2",
            "Advanced 3"
        ];

        $preToddleToddle = [
            "Pre Toddle 1",
            "Pre Toddle 2",
            "Toddle 1",
            "Toddle 2",
            "Toddle 3",
            "Toddle 4"
        ];
        $student = Students::with('teacher')->findOrFail($studentId);
        $countClass = DB::table('student_scores')
            ->join('price', 'student_scores.price_id', '=', 'price.id')
            ->select('price.program', 'price.id')
            ->where('student_id', $studentId)
            ->groupBy('student_scores.student_id', 'student_scores.price_id')
            ->get();
        new \App\Libraries\Pdf();
        $pdf = new Fpdi('L', 'mm', 'A4');
        $pdf->AddPage();

        foreach ($countClass as $class) {

            $score = StudentScore::where('student_id', $studentId)
                ->where('price_id', $class->id)
                ->first();

            if (!$score) {
                continue;
            }

            $averageScores = $this->calculateAverageScorePerItem($studentId, $class->id);

            if (in_array($class->program, $preToddleToddle)) {
                $this->renderTodTemplate($pdf, $student, $score, $class, $averageScores);
            } elseif (in_array($class->program, $kidSeries)) {
                $this->renderKidTemplate($pdf, $student, $score, $class, $averageScores);
            }
        }

        // $filename = time() . '.pdf';
        // $pdfContent = $pdf->Output('', 'S'); // 'S' = return as string
        ob_clean();
        flush();
        $pdfContent = $pdf->Output('', 'S');
        return $pdfContent;
    }

    private function calculateAverageScorePerItem($studentId, $priceId)
    {
        $items = [];
        $score_total = 0;
        $item_count = 0; // Tambahkan counter untuk jumlah item yang dihitung

        foreach (TestItems::get() as $item) {
            $scores = DB::table('student_scores')
                ->join('student_score_details', 'student_score_details.student_score_id', '=', 'student_scores.id')
                ->select('student_score_details.score as score_test')
                ->where('student_id', $studentId)
                ->where('price_id', $priceId)
                ->where('student_score_details.test_item_id', $item->id)
                ->get();

            $nonZeroScores = $scores->filter(function ($s) {
                return $s->score_test != 0;
            });

            $divider = $nonZeroScores->count() ?: 1;
            $average = round($nonZeroScores->sum('score_test') / $divider);

            $items[] = [
                'item_id' => $item->id,
                'average' => $average,
                'grade' => Helper::getGrade($average),
            ];

            $score_total += $average;
            $item_count++; // Increment counter setiap item diproses
        }

        $overall = $item_count > 0 ? round($score_total / $item_count) : 0; // Hitung rata-rata berdasarkan jumlah item yang ada

        return [
            'items' => $items,
            'average' => $overall,
            'grade' => Helper::getGrade($overall)
        ];
    }

    private function renderTodTemplate($pdf, $student, $score, $class, $averageScores)
    {
        $pdf->Image(public_path('certificate/template/pretod-tod.jpg'), 0, 0, 297, 210);
        $pdf->SetFont('Arial', 'B', '35');
        $pdf->SetXY(87, 65);
        $pdf->Cell(120, 20, $student->name, '', 0, 'C');

        $pdf->SetFont('Arial', '', '15');
        $pdf->SetXY(87, 87);
        $pdf->Cell(120, 10, $class->program . ' Class', '', 0, 'C');

        $pdf->SetXY(87, 93);
        $pdf->Cell(120, 10, date('Y'), '', 0, 'C');

        $pdf->SetFont('Arial', '', 12);
        foreach ($averageScores['items'] as $item) {
            switch ($item['item_id']) {
                case 1:
                    $pdf->SetXY(123, 119);
                    break;
                case 2:
                    $pdf->SetXY(123, 128);
                    break;
                case 3:
                    $pdf->SetXY(197, 119);
                    break;
                case 4:
                    $pdf->SetXY(197, 128);
                    break;
            }
            $pdf->Cell(40, 10, $item['average'] . '/' . $item['grade'], '', 0, 'L');
        }

        $pdf->SetFont('Arial', 'B', '45');
        $pdf->SetXY(133, 130);
        $pdf->Cell(40, 70, $averageScores['average'] . '/' . $averageScores['grade'], '', 0, 'C');

        $pdf->SetFont('Arial', '', '15');
        $pdf->SetXY(60, 175);
        $pdf->Cell(80, 10, $score->teacher_name ?? '-', '', 0, 'C');
    }

    private function renderKidTemplate($pdf, $student, $score, $class, $averageScores)
    {
        $pdf->Image(public_path('certificate/template/kid-advanced.jpg'), 0, 0, 297, 210);
        $pdf->SetFont('Arial', 'B', '35');
        $pdf->SetXY(87, 60);
        $pdf->Cell(120, 20, $student->name, '', 0, 'C');

        $pdf->SetFont('Arial', '', '15');
        $pdf->SetXY(87, 78);
        $pdf->Cell(120, 10, $class->program . ' Class', '', 0, 'C');

        $pdf->SetXY(87, 85);
        $pdf->Cell(120, 10, date('Y'), '', 0, 'C');

        $pdf->SetFont('Arial', '', 12);
        foreach ($averageScores['items'] as $item) {
            switch ($item['item_id']) {
                case 1:
                    $pdf->SetXY(122, 107);
                    break;
                case 2:
                    $pdf->SetXY(122, 116);
                    break;
                case 3:
                    $pdf->SetXY(196, 107);
                    break;
                case 4:
                    $pdf->SetXY(196, 116);
                    break;
            }
            $pdf->Cell(40, 10, $item['average'] . '/' . $item['grade'], '', 0, 'L');
        }

        $pdf->SetFont('Arial', 'B', '45');
        $pdf->SetXY(133, 120);
        $pdf->Cell(40, 70, $averageScores['average'] . '/' . $averageScores['grade'], '', 0, 'C');

        $pdf->SetFont('Arial', '', '15');
        $pdf->SetXY(60, 172);
        $pdf->Cell(80, 10, $score->teacher_name ?? '-', '', 0, 'C');
    }

    public function apiGenerateCertificate($studentId)
    {
        $pdfContent = $this->generateCertificate($studentId);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="certificate.pdf"');
    }


    // public function getNewResult($studentId, Request $request)
    // {
    //     try {
    //         $followUp = FollowUp::where('student_id', $studentId)->first();

    //         if ($followUp) {
    //             $class = FollowUp::where('student_id', $studentId)
    //                 ->join('price', 'price.id', 'follow_up.old_price_id')
    //                 ->join('student', 'student.id', 'follow_up.student_id')
    //                 ->select('price.program', 'student.is_certificate', 'student.date_certificate', 'follow_up.old_price_id as priceid')
    //                 ->first();
    //         } else {
    //             $class = Students::join('price', 'price.id', 'student.priceid')
    //                 ->select('price.program', 'student.is_certificate', 'student.date_certificate', 'student.priceid')
    //                 ->where('student.id', $studentId)->first();
    //         }

    //         $getStudent = $followUp ? FollowUp::where('student_id', $studentId)->first() : Students::with('teacher')->findOrFail($studentId);
    //         $getStudent->priceid = $class->priceid;

    //         $score = StudentScore::join('tests as t', 't.id', 'student_scores.test_id')
    //             ->join('price as p', 'p.id', 'student_scores.price_id')
    //             ->select(
    //                 'p.program',
    //                 't.name',
    //                 'student_scores.average_score',
    //                 'student_scores.id as scoreId',
    //                 'student_scores.comment',
    //                 'student_scores.date',
    //                 'student_scores.price_id'
    //             )
    //             ->where('student_scores.price_id', $request->class ?? $getStudent->priceid)
    //             ->where('student_scores.student_id', $studentId)
    //             ->first();

    //         if (!$score) {
    //             return response()->json([
    //                 'code' => '404',
    //                 'message' => 'Nilai tidak ditemukan.'
    //             ], 404);
    //         }

    //         // Mulai generate PDF
    //         new \App\Libraries\Pdf();
    //         $pdf = new \setasign\Fpdi\Fpdi();
    //         $pdf->SetTitle('Certificate');
    //         $pdf->SetAutoPageBreak(false, 5);
    //         $pdf->AddPage('L');

    //         $template = in_array($score->price_id, [1, 2, 3, 4, 5, 6])
    //             ? 'certificate/template/pretod-tod.jpg'
    //             : 'certificate/template/kid-advanced.jpg';

    //         $pdf->Image(public_path($template), 0, 0, 297, 210);
    //         $pdf->SetFont('Arial', 'B', 35);
    //         $pdf->SetXY(87, 65);
    //         $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

    //         $pdf->SetFont('Arial', 'B', 20);
    //         $pdf->SetXY(87, 82);
    //         $pdf->Cell(120, 10, $score->program, '', 0, 'C');

    //         $pdf->SetFont('Arial', 'B', 15);
    //         $pdf->SetXY(75, 92);
    //         $pdf->Cell(60, 10, $class->date_certificate ? \Carbon\Carbon::parse($class->date_certificate)->format('j F Y') : \Carbon\Carbon::now()->format('j F Y'), 0, 'L');

    //         $score_total = 0;
    //         $items = TestItems::get();
    //         $jumlah_items = count($items);

    //         foreach ($items as $item) {
    //             $score_test = collect([1, 2, 3])->map(function ($test_id) use ($getStudent, $score, $item) {
    //                 return DB::table('student_scores')
    //                     ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
    //                     ->where('student_id', $getStudent->id)
    //                     ->where('price_id', $score->price_id)
    //                     ->where('test_id', $test_id)
    //                     ->where('student_score_details.test_item_id', $item->id)
    //                     ->value('student_score_details.score') ?? 0;
    //             })->filter(function ($val) {
    //                 return $val > 0;
    //             });

    //             $average = $score_test->count() > 0 ? round($score_test->avg()) : 0;
    //             $score_total += $average;

    //             // Posisi XY bisa diatur sesuai kebutuhan template Anda
    //             // Ini contoh sederhana untuk dua template
    //             $pdf->SetFont('Arial', 'B', 20);
    //             if (in_array($score->price_id, [1, 2, 3, 4, 5, 6])) {
    //                 // Pretod-Tod (4 item)
    //                 switch ($item->id) {
    //                     case 1:
    //                         $pdf->SetXY(123, 119);
    //                         break;
    //                     case 2:
    //                         $pdf->SetXY(123, 128);
    //                         break;
    //                     case 3:
    //                         $pdf->SetXY(197, 119);
    //                         break;
    //                     case 4:
    //                         $pdf->SetXY(197, 128);
    //                         break;
    //                 }
    //             } else {
    //                 // Kids-Advanced (6 item)
    //                 switch ($item->id) {
    //                     case 1:
    //                         $pdf->SetXY(73, 119);
    //                         break;
    //                     case 2:
    //                         $pdf->SetXY(73, 128);
    //                         break;
    //                     case 3:
    //                         $pdf->SetXY(147, 119);
    //                         break;
    //                     case 4:
    //                         $pdf->SetXY(147, 128);
    //                         break;
    //                     case 5:
    //                         $pdf->SetXY(231, 119);
    //                         break;
    //                     case 6:
    //                         $pdf->SetXY(231, 128);
    //                         break;
    //                 }
    //             }

    //             $pdf->Cell(40, 10, $average . '/' . Helper::getGrade($average), '', 0, 'L');
    //         }

    //         $score_average = round($score_total / $jumlah_items);

    //         $pdf->SetFont('Arial', 'B', 45);
    //         $pdf->SetXY(133, 130);
    //         $pdf->Cell(40, 70, $score_average . '/' . Helper::getGrade($score_average), '', 0, 'C');

    //         $pdf->SetFont('Arial', 'B', 20);
    //         $pdf->SetXY(200, 187);
    //         $pdf->Cell(40, 10, $getStudent->teacher->name ?? '', '', 0, 'L');

    //         if ($getStudent->teacher) {
    //             $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/' . $getStudent->teacher->signature, 215, 170, 19.2, 12.6);
    //         }

    //         $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/principal.png', 70, 170, 19.2, 12.6);

    //         $pdfContent = $pdf->Output('', 'S');
    //         return response($pdfContent, 200)
    //             ->header('Content-Type', 'application/pdf')
    //             ->header('Content-Disposition', 'inline; filename="certificate.pdf"');
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code' => '400',
    //             'error' => 'internal server error',
    //             'message' => $th->getMessage(),
    //         ], 500);
    //     }
    // }


    // public function getNewResult($studentId, Request $request)

    // {
    //     try {

    //         $classId = $request->input('class');

    //         if (!$classId) {
    //             return response()->json([
    //                 'code' => '400',
    //                 'message' => 'Class ID is required.',
    //             ], 400);
    //         }

    //         // Ambil data student & teacher
    //         $getStudent = Students::with('teacher')->findOrFail($studentId);

    //         // Ambil informasi class berdasarkan classId (price_id)
    //         $class = DB::table('price')
    //             ->join('student', 'student.id', '=', DB::raw($studentId))
    //             ->select('price.program', 'student.is_certificate', 'student.date_certificate', DB::raw("'$classId' as priceid"))
    //             ->where('price.id', $classId)
    //             ->first();


    //         $getStudent->priceid = $classId;

    //         // Ambil score utama
    //         $score = StudentScore::join('tests as t', 't.id', 'student_scores.test_id')
    //             ->join('price as p', 'p.id', 'student_scores.price_id')
    //             ->select(
    //                 'p.program',
    //                 't.name',
    //                 'student_scores.average_score',
    //                 'student_scores.id as scoreId',
    //                 'student_scores.comment',
    //                 'student_scores.date',
    //                 'student_scores.price_id'
    //             )
    //             ->where('student_scores.price_id', $classId)
    //             ->where('student_scores.student_id', $studentId)
    //             ->first();

    //         if (!$score) {
    //             return response()->json([
    //                 'code' => '404',
    //                 'message' => 'Score not found for this class.',
    //             ], 404);
    //         }

    //         // Start generate PDF
    //         new \App\Libraries\Pdf();
    //         $pdf = new \setasign\Fpdi\Fpdi();
    //         $pdf->SetTitle('Certificate');
    //         $pdf->SetAutoPageBreak(false, 5);
    //         $pdf->AddPage('L');

    //         $template = in_array($score->price_id, [1, 2, 3, 4, 5, 6])
    //             ? 'certificate/template/pretod-tod.jpg'
    //             : 'certificate/template/kid-advanced.jpg';

    //         $pdf->Image(public_path($template), 0, 0, 297, 210);
    //         $pdf->SetFont('Arial', 'B', 35);
    //         $pdf->SetXY(87, 65);
    //         $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

    //         $pdf->SetFont('Arial', 'B', 20);
    //         $pdf->SetXY(87, 82);
    //         $pdf->Cell(120, 10, $score->program, '', 0, 'C');

    //         $pdf->SetFont('Arial', 'B', 15);
    //         $pdf->SetXY(75, 92);
    //         $pdf->Cell(60, 10, $class->date_certificate ? \Carbon\Carbon::parse($class->date_certificate)->format('j F Y') : \Carbon\Carbon::now()->format('j F Y'), 0, 'L');

    //         // Hitung skor detail
    //         $score_total = 0;
    //         $items = TestItems::get();
    //         $jumlah_items = count($items);

    //         foreach ($items as $item) {
    //             $score_test = collect([1, 2, 3])->map(function ($test_id) use ($getStudent, $score, $item) {
    //                 return DB::table('student_scores')
    //                     ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
    //                     ->where('student_id', $getStudent->id)
    //                     ->where('price_id', $score->price_id)
    //                     ->where('test_id', $test_id)
    //                     ->where('student_score_details.test_item_id', $item->id)
    //                     ->value('student_score_details.score') ?? 0;
    //             })->filter(function ($val) {
    //                 return $val > 0;
    //             });

    //             $average = $score_test->count() > 0 ? round($score_test->avg()) : 0;
    //             $score_total += $average;

    //             $pdf->SetFont('Arial', 'B', 20);
    //             if (in_array($score->price_id, [1, 2, 3, 4, 5, 6])) {
    //                 switch ($item->id) {
    //                     case 1:
    //                         $pdf->SetXY(123, 119);
    //                         break;
    //                     case 2:
    //                         $pdf->SetXY(123, 128);
    //                         break;
    //                     case 3:
    //                         $pdf->SetXY(197, 119);
    //                         break;
    //                     case 4:
    //                         $pdf->SetXY(197, 128);
    //                         break;
    //                 }
    //             } else {
    //                 switch ($item->id) {
    //                     case 1:
    //                         $pdf->SetXY(73, 119);
    //                         break;
    //                     case 2:
    //                         $pdf->SetXY(73, 128);
    //                         break;
    //                     case 3:
    //                         $pdf->SetXY(147, 119);
    //                         break;
    //                     case 4:
    //                         $pdf->SetXY(147, 128);
    //                         break;
    //                     case 5:
    //                         $pdf->SetXY(231, 119);
    //                         break;
    //                     case 6:
    //                         $pdf->SetXY(231, 128);
    //                         break;
    //                 }
    //             }

    //             $pdf->Cell(40, 10, $average . '/' . Helper::getGrade($average), '', 0, 'L');
    //         }

    //         $score_average = round($score_total / $jumlah_items);
    //         $pdf->SetFont('Arial', 'B', 45);
    //         $pdf->SetXY(133, 130);
    //         $pdf->Cell(40, 70, $score_average . '/' . Helper::getGrade($score_average), '', 0, 'C');

    //         $pdf->SetFont('Arial', 'B', 20);
    //         $pdf->SetXY(200, 187);
    //         $pdf->Cell(40, 10, $getStudent->teacher->name ?? '', '', 0, 'L');

    //         if ($getStudent->teacher) {
    //             $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/' . $getStudent->teacher->signature, 215, 170, 19.2, 12.6);
    //         }

    //         $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/principal.png', 70, 170, 19.2, 12.6);

    //         $pdfContent = $pdf->Output('', 'S');
    //         return response($pdfContent, 200)
    //             ->header('Content-Type', 'application/pdf')
    //             ->header('Content-Disposition', 'inline; filename=\"certificate.pdf\"');
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'code' => '500',
    //             'error' => 'Internal Server Error',
    //             'message' => $th->getMessage(),
    //         ], 500);
    //     }
    // }

    public function getNewResult($studentId, Request $request)
    {
        try {

            $classId = $request->input('class');

            if (!$classId) {
                return response()->json([
                    'code' => '400',
                    'message' => 'Class ID is required.',
                ], 400);
            }

            // Ambil data student & teacher
            $getStudent = Students::with('teacher')->findOrFail($studentId);

            // Ambil informasi class berdasarkan classId (price_id)
            $class = DB::table('price')
                ->where('id', $classId)
                ->select('program')
                ->first();

            $getStudent->priceid = $classId;
            $getStudent->program = $class->program ?? '';

            // Ambil score utama
            $score = StudentScore::join('tests as t', 't.id', 'student_scores.test_id')
                ->join('price as p', 'p.id', 'student_scores.price_id')
                ->select(
                    'p.program',
                    't.name',
                    'student_scores.average_score',
                    'student_scores.id as scoreId',
                    'student_scores.comment',
                    'student_scores.date',
                    'student_scores.price_id'
                )
                ->where('student_scores.price_id', $classId)
                ->where('student_scores.student_id', $studentId)
                ->first();

            if (!$score) {
                return response()->json([
                    'code' => '404',
                    'message' => 'Score not found for this class.',
                ], 404);
            }

            // Start generate PDF
            new \App\Libraries\Pdf();
            $pdf = new \setasign\Fpdi\Fpdi();
            $pdf->SetTitle('Certificate');
            $pdf->SetAutoPageBreak(false, 5);
            $pdf->AddPage('L');

            $template = in_array($score->price_id, [1, 2, 3, 4, 5, 6])
                ? 'certificate/template/pretod-tod.jpg'
                : 'certificate/template/kid-advanced.jpg';

            $pdf->Image(public_path($template), 0, 0, 297, 210);
            $pdf->SetFont('Arial', 'B', 35);
            $pdf->SetXY(87, 65);
            $pdf->Cell(120, 20, $getStudent->name, '', 0, 'C');

            $pdf->SetFont('Arial', 'B', 20);
            $pdf->SetXY(87, 82);
            $pdf->Cell(120, 10, $score->program, '', 0, 'C');

            $pdf->SetFont('Arial', 'B', 15);
            $pdf->SetXY(75, 92);
            $pdf->Cell(60, 10, $getStudent->date_certificate ? \Carbon\Carbon::parse($getStudent->date_certificate)->format('j F Y') : \Carbon\Carbon::now()->format('j F Y'), 0, 'L');

            $pdf->SetFont('Arial', 'B', 20);
            if (in_array($score->price_id, [1, 2, 3, 4, 5, 6])) {
                // Skor Productive Skills (Writing & Speaking)
                $productiveSkillsScore = DB::table('student_scores')
                    ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                    ->join('test_items', 'test_items.id', 'student_score_details.test_item_id')
                    ->where('student_id', $getStudent->id)
                    ->where('price_id', $score->price_id)
                    ->whereIn('test_items.name', ['Writing', 'Speaking'])
                    ->avg('student_score_details.score') ?? 0;
                $pdf->SetXY(123, 119);
                $pdf->Cell(40, 10, round($productiveSkillsScore, 0) . '/' . Helper::getGrade(round($productiveSkillsScore, 0)), '', 0, 'L');

                // Skor Receptive Skills (Reading & Listening)
                $receptiveSkillsScore = DB::table('student_scores')
                    ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                    ->join('test_items', 'test_items.id', 'student_score_details.test_item_id')
                    ->where('student_id', $getStudent->id)
                    ->where('price_id', $score->price_id)
                    ->whereIn('test_items.name', ['Reading', 'Listening'])
                    ->avg('student_score_details.score') ?? 0;
                $pdf->SetXY(197, 119);
                $pdf->Cell(40, 10, round($receptiveSkillsScore, 0) . '/' . Helper::getGrade(round($receptiveSkillsScore, 0)), '', 0, 'L');

                // Rata-rata Keseluruhan
                $overallScore = round(($productiveSkillsScore + $receptiveSkillsScore) / 2);
                $pdf->SetFont('Arial', 'B', 45);
                $pdf->SetXY(133, 130);
                $pdf->Cell(40, 70, $overallScore . '/' . Helper::getGrade($overallScore), '', 0, 'C');
            } else {
                // Logika untuk template kid-advanced (dibagi 6 items)
                $items = TestItems::get();
                $score_total = 0;
                $jumlah_items = count($items);
                $itemCoordinates = [
                    1 => [73, 119],
                    2 => [73, 128],
                    3 => [147, 119],
                    4 => [147, 128],
                    5 => [231, 119],
                    6 => [231, 128],
                ];

                foreach ($items as $item) {
                    $studentScoreDetail = DB::table('student_scores')
                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                        ->where('student_id', $getStudent->id)
                        ->where('price_id', $score->price_id)
                        ->where('student_score_details.test_item_id', $item->id)
                        ->value('student_score_details.score') ?? 0;

                    $average = round($studentScoreDetail);
                    $score_total += $average;

                    if (isset($itemCoordinates[$item->id])) {
                        $pdf->SetXY($itemCoordinates[$item->id][0], $itemCoordinates[$item->id][1]);
                        $pdf->Cell(40, 10, $average . '/' . Helper::getGrade($average), '', 0, 'L');
                    }
                }

                $score_average = $jumlah_items > 0 ? round($score_total / $jumlah_items) : 0;
                $pdf->SetFont('Arial', 'B', 45);
                $pdf->SetXY(133, 130);
                $pdf->Cell(40, 70, $score_average . '/' . Helper::getGrade($score_average), '', 0, 'C');
            }

            $pdf->SetFont('Arial', 'B', 20);
            $pdf->SetXY(200, 187);
            $pdf->Cell(40, 10, $getStudent->teacher->name ?? '', '', 0, 'L');

            if ($getStudent->teacher && $getStudent->teacher->signature) {
                $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/' . $getStudent->teacher->signature, 215, 170, 19.2, 12.6);
            }

            $pdf->Image('https://ui-payment.primtechdev.com/upload/signature/principal.png', 70, 170, 19.2, 12.6);

            $pdfContent = $pdf->Output('', 'S');
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename=\"certificate.pdf\"');
        } catch (\Throwable $th) {
            return response()->json([
                'code' => '500',
                'error' => 'Internal Server Error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
