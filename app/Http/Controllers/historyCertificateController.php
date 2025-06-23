<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class historyCertificateController extends Controller
{

    public function index(Request $request)
    {
        // 1. Ambil data students untuk dropdown filter (selalu dibutuhkan)
        $students = Students::where('status', 'ACTIVE')
            ->whereNotNull('priceid') // Menggunakan whereNotNull lebih eksplisit daripada '!=', null
            ->get();

        // 2. Inisialisasi variabel untuk history certificate
        $history_certificate = collect(); // Koleksi kosong secara default
        $testItem = collect(); // Koleksi kosong secara default untuk test items

        // 3. Logika filter hanya dijalankan jika ada parameter 'student' di request
        if ($request->has('student') && $request->input('student') != '') {
            $studentId = $request->input('student');


            // Query untuk history certificate
            $history_certificate = DB::table('history-certificate')
                ->select(
                    'history-certificate.*',
                    'student.name',
                    'dayOne.day as day1',
                    'dayTwo.day as day2',
                    'price.program as class',
                    'teacher.name as teacher_name'
                )
                ->join('student', 'student.id', '=', 'history-certificate.student_id')
                ->join('day as dayOne', 'dayOne.id', '=', 'history-certificate.day_1') // Perbaikan alias tabel
                ->join('day as dayTwo', 'dayTwo.id', '=', 'history-certificate.day_2') // Perbaikan alias tabel
                ->join('price', 'price.id', '=', 'history-certificate.price_id')
                ->join('teacher', 'teacher.id', '=', 'student.id_teacher')
                ->where('history-certificate.student_id', $studentId) // Pastikan kolom yang benar
                ->get();
        }

        // 4. Kirim data ke view
        return view('history-certificate.index', compact('students', 'history_certificate', 'testItem'));
    }
}
