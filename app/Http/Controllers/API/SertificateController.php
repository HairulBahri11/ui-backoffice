<?php

namespace App\Http\Controllers\API;

use Helper;
use setasign\Fpdi\Fpdi;
use App\Models\Students;
use App\Models\TestItems;
use App\Models\StudentScore;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SertificateController extends Controller
{
    public function generateCertificate($studentId)
    {
        $student = Students::with('teacher')->findOrFail($studentId);
        $countClass = DB::table('student_scores')
            ->join('price', 'student_scores.price_id', '=', 'price.id')
            ->select('price.program', 'price.id')
            ->where('student_id', $studentId)
            ->groupBy('student_scores.student_id', 'student_scores.price_id')
            ->get();
        new \App\Libraries\Pdf();
        $pdf = new Fpdi('L', 'mm', 'A4');


        foreach ($countClass as $class) {
            $pdf->AddPage();
            $score = StudentScore::where('student_id', $studentId)
                ->where('price_id', $class->id)
                ->first();

            if (!$score) {
                continue;
            }

            $averageScores = $this->calculateAverageScorePerItem($studentId, $class->id);

            if (in_array($class->program, ['Pre-TOD', 'TOD'])) {
                $this->renderTodTemplate($pdf, $student, $score, $class, $averageScores);
            } elseif (in_array($class->program, ['K1', 'K2', 'ADVANCED'])) {
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
}
