<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class birthdayPoinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $teacherId = Auth::guard('teacher')->id(); // Ambil ID guru jika login

    //     // Ambil siswa aktif
    //     $query = Students::where('status', 'ACTIVE')
    //         ->whereNotNull('course_time')
    //         ->whereNotNull('priceid')
    //         ->with(['class', 'teacher'])
    //         ->join('day as day_one', 'day_one.id', '=', 'student.day1')
    //         ->join('day as day_two', 'day_two.id', '=', 'student.day2')
    //         ->select('student.*', 'day_one.day as day1_name', 'day_two.day as day2_name')
    //         ->orderBy('student.name', 'asc');
    //     if ($teacherId) {
    //         $query->where('id_teacher', $teacherId);
    //     }

    //     $student_list_active = $query->get();

    //     $student_birthday = [];

    //     foreach ($student_list_active as $item) {
    //         $birthdayString = trim($item->birthday);

    //         if (preg_match('/^\d{4} [A-Za-z]+ \d{1,2}$/', $birthdayString)) {
    //             $date = Carbon::createFromFormat('Y F d', $birthdayString);
    //         } elseif (preg_match('/^[A-Za-z]+ \d{1,2}$/', $birthdayString)) {
    //             $birthdayString .= ' ' . now()->year;
    //             $date = Carbon::createFromFormat('F d Y', $birthdayString);
    //         } else {
    //             continue;
    //         }

    //         if ($date->format('m') === now()->format('m')) {
    //             $className = $item->class->program ?? 'Unknown';
    //             $teacherName = $item->teacher->name ?? 'Unknown';

    //             $student_birthday[] = [
    //                 'id' => $item->id,
    //                 'name' => $item->name,
    //                 'birthday' => $date->format('Y-m-d'),
    //                 'class' => $className,
    //                 'day1' => $item->day1_name,
    //                 'day2' => $item->day2_name,
    //                 'course_time' => $item->course_time,
    //                 'teacher' => $teacherName,
    //                 'age' => now()->diffInYears($date),
    //                 'is_this_month_birthday' => 1
    //             ];
    //         }
    //     }

    //     // **Sorting berdasarkan tanggal ulang tahun dalam bulan ini**
    //     $student_birthday = collect($student_birthday)->sortBy(function ($item) {
    //         return Carbon::parse($item['birthday'])->day; // Urutkan dari tanggal 1 ke 31
    //     })->values()->all();

    //     return view('birthday-point.index', compact('student_birthday'));
    // }


    public function index()
    {
        $teacherId = Auth::guard('teacher')->id();

        $query = Students::where('status', 'ACTIVE')
            ->whereNotNull('course_time')
            ->whereNotNull('priceid')
            ->with(['class', 'teacher'])
            ->join('day as day_one', 'day_one.id', '=', 'student.day1')
            ->join('day as day_two', 'day_two.id', '=', 'student.day2')
            ->select('student.*', 'day_one.day as day1_name', 'day_two.day as day2_name')
            ->orderBy('student.name', 'asc');

        if ($teacherId) {
            $query->where('id_teacher', $teacherId);
        }

        $student_list_active = $query->get();
        $student_birthday = [];
        $currentMonth = now()->month;

        foreach ($student_list_active as $item) {
            $birthdayString = trim($item->birthday);
            $date = null;

            try {
                // Cek Format 1: "1995 March 24" (Y F d)
                if (preg_match('/^\d{4}\s+[A-Za-z]+\s+\d{1,2}$/', $birthdayString)) {
                    $date = Carbon::createFromFormat('Y F d', $birthdayString);
                }
                // Cek Format 2: "March 24" (F d)
                elseif (preg_match('/^[A-Za-z]+\s+\d{1,2}$/', $birthdayString)) {
                    // Gunakan tahun sekarang sebagai placeholder agar Carbon bisa memproses bulan & tanggal
                    $date = Carbon::createFromFormat('F d', $birthdayString);
                }
            } catch (\Exception $e) {
                continue; // Skip jika format benar-benar rusak
            }

            // Jika berhasil diparsing dan bulannya sama dengan bulan ini
            if ($date && $date->month === $currentMonth) {
                $className = $item->class->program ?? 'Unknown';
                $teacherName = $item->teacher->name ?? 'Unknown';

                // Cek apakah data asli punya tahun untuk menghitung umur
                $hasYear = preg_match('/\d{4}/', $birthdayString);

                $student_birthday[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'birthday' => $date->format('F d'), // Tampilkan format cantik "March 24"
                    'class' => $className,
                    'day1' => $item->day1_name,
                    'day2' => $item->day2_name,
                    'course_time' => $item->course_time,
                    'teacher' => $teacherName,
                    'age' => $hasYear ? now()->diffInYears($date) : '-', // Umur "-" jika tahun tidak ada
                    'day_sort' => $date->day // Helper untuk sorting
                ];
            }
        }

        // Sorting berdasarkan tanggal (1-31)
        $student_birthday = collect($student_birthday)->sortBy('day_sort')->values()->all();
        // dd($student_birthday);

        return view('birthday-point.index', compact('student_birthday'));
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
