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
    public function index()
    {
        // Cek student_birthday
        $teacherId = Auth::guard('teacher')->id(); // Ambil ID guru jika login

        // Query awal
        $query = Students::where('status', 'ACTIVE')
            ->whereNotNull('course_time')
            ->whereNotNull('priceid')
            ->with(['class', 'teacher'])
            ->orderBy('name', 'asc');

        // Jika login sebagai teacher, filter berdasarkan id_teacher
        if ($teacherId) {
            $query->where('id_teacher', $teacherId);
        }

        $student_list_active = $query->get();

        $student_birthday = [];

        foreach ($student_list_active as $item) {
            $birthdayString = trim($item->birthday); // Hapus spasi ekstra

            // Pastikan format tanggal valid sebelum diproses
            if (preg_match('/^\d{4} [A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                // Format "2019 November 24" → "2019-11-24"
                $date = Carbon::createFromFormat('Y F d', $birthdayString);
            } elseif (preg_match('/^[A-Za-z]+ \d{1,2}$/', $birthdayString)) {
                // Format "November 24" → "2025-11-24" (asumsi tahun ini)
                $birthdayString .= ' ' . now()->year;
                $date = Carbon::createFromFormat('F d Y', $birthdayString);
            } else {
                continue; // Lewati jika format salah
            }

            // Cek apakah ulang tahun hari ini
            if ($date->format('m-d') === now()->format('m-d')) {
                $className = $item->class->program ?? 'Unknown';
                $teacherName = $item->teacher->name ?? 'Unknown';

                $student_birthday[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'birthday' => $item->birthday, // Format "YYYY-MM-DD"
                    'class' => $className,
                    'teacher' => $teacherName,
                    'age' => now()->diffInYears($date), // Hitung umur
                    'is_today_birthday' => 1 // Tandai ulang tahun hari ini
                ];
            }
        }

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
