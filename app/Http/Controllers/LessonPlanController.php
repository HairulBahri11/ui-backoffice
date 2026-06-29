<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonPlanController extends Controller
{
    public function index()
    {

        if (Auth::guard('teacher')->check()) {
            $teacherId = Auth::guard('teacher')->id();
            $lessonPlans = LessonPlan::with('teacher', 'price')->where('teacher_id', $teacherId)->orderBy('created_at', 'desc')->get();
        } else {
            $lessonPlans = LessonPlan::with('teacher', 'price',)->orderBy('created_at', 'desc')->get();
        }

        return view('lesson-plan.index', compact('lessonPlans'));
    }

    public function getClassesByDay(Request $request)
    {
        $day = $request->query('day');

        $teacherId = Auth::guard('teacher')->id();

        // Ambil data dari tabel student yang mana day1 ATAU day2 cocok dengan inputan
        $classes = DB::table('student')
            ->join('price', 'price.id', '=', 'student.priceid')
            ->join('day as day_one', 'day_one.id', '=', 'student.day1')
            ->join('day as day_two', 'day_two.id', '=', 'student.day2')
            ->whereNotNull('student.course_time')
            ->whereNotNull('student.priceid')
            ->where('student.status', 'ACTIVE')
            ->where('student.course_time', '!=', '')
            ->where('student.id_teacher', $teacherId)
            ->where(function ($query) use ($day) {
                $query->where('student.day1', $day)
                    ->orWhere('student.day2', $day);
            })
            ->select(
                'price.program',
                'student.priceid',
                'student.course_time',
                'student.day1',
                'student.day2',
                'day_one.day as day1_name',
                'day_two.day as day2_name',
                DB::raw('COUNT(student.id) as total_students')
            )
            // WAJIB ditambahkan agar data terbagi per kelas & jam mengajar masing-masing
            ->groupBy(
                'price.program',
                'student.priceid',
                'student.course_time',
                'student.day1',
                'student.day2',
                'day_one.day',
                'day_two.day'
            )
            ->get();

        return response()->json($classes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::guard('teacher')->check()) {
            return redirect()->back()->with('error', 'You are not authorized to create a lesson plan.');
        }
        // if (now()->between('15:00', '23:59')) {
        //     return redirect()->route('lesson-plan.index')->with('error', 'You can only create a lesson plan between 15:00 and 00:00.');
        // }
        return view('lesson-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi dasar, pastikan ada paket array plan yang dikirim
        $request->validate([
            'selected_day' => 'required',
            'plans'        => 'required|array|min:1',
            'plans.*.topic' => 'required|string', // Validasi topic wajib diisi di semua card
        ]);

        $teacherId = Auth::guard('teacher')->id();
        $insertedCount = 0;

        // Mulai Database Transaction agar aman saat proses bulk data
        DB::beginTransaction();
        try {
            foreach ($request->plans as $plan) {


                DB::table('lesson_plan')->insert([
                    'teacher_id'   => $teacherId,
                    'class'     => $plan['class_id'],
                    'day1'         => $plan['day1'],
                    'day2'         => $plan['day2'],
                    'course_time'  => $plan['course_time'],
                    'topic'        => $plan['topic'],
                    'flashcards'   => $plan['flashcards'] ?? null,
                    'exercise'     => $plan['exercise'] ?? null,
                    'activity'     => $plan['activity'] ?? null,
                    'created_at'   => now(),
                ]);
                $insertedCount++;
            }

            DB::commit(); // Simpan permanen jika semua perulangan sukses

            return redirect()->route('lesson-plan.index')
                ->with('success', "Successfully saved $insertedCount Lesson Plan(s)!");
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua input jika di tengah jalan ada error internet/database
            return redirect()->back()
                ->with('error', 'An error occurred while saving the data: ' . $e->getMessage());
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
        $item = LessonPlan::with(['teacher', 'price'])->findOrFail($id);
        $totalStudents = DB::table('student')
            ->where('priceid', $item->class)
            ->where('course_time', $item->course_time)
            ->where(function ($query) use ($item) {
                $query->where('day1', $item->day1)
                    ->orWhere('day2', $item->day2);
            })
            ->count();

        return view('lesson-plan.detail', compact('item', 'totalStudents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // 1. Ambil baris data lesson plan yang akan diedit
        $item = DB::table('lesson_plan')->where('id', $id)->first();

        if (!$item) {
            return redirect()->route('lesson-plan.index')->with('error', 'Data Lesson Plan tidak ditemukan.');
        }

        // Ekstraksi opsional jika Anda menggunakan relasi Eloquent / manual join untuk info price & teacher
        // Di sini diasumsikan data query pelengkap disamakan seperti halaman detail:
        $item->teacher = DB::table('teacher')->where('id', $item->teacher_id)->first();
        $item->price = DB::table('price')->where('id', $item->class)->first(); // Menyesuaikan relasi priceid

        // 2. Hitung jumlah siswa aktif di kelas tersebut
        $totalStudents = DB::table('student')
            ->where('priceid', $item->class)
            ->where('status', 'ACTIVE')
            ->count();

        return view('lesson-plan.edit', compact('item', 'totalStudents'));
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
        // 1. Validasi input field pengeditan konten
        $request->validate([
            'topic'       => 'required|string|max:255',
            'flashcards'  => 'nullable|string',
            'exercise'    => 'nullable|string',
            'activity'    => 'nullable|string',
        ]);

        // 2. Lakukan update data konten ke database
        $updated = DB::table('lesson_plan')
            ->where('id', $id)
            ->update([
                'topic'      => $request->topic,
                'flashcards' => $request->flashcards,
                'exercise'   => $request->exercise,
                'activity'   => $request->activity,

            ]);

        return redirect()->route('lesson-plan.index')
            ->with('success', 'Lesson Plan Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Cari data lesson plan memastikan datanya ada
        $lessonPlan = DB::table('lesson_plan')->where('id', $id)->first();

        if (!$lessonPlan) {
            return redirect()->route('lesson-plan.index')
                ->with('error', 'Lesson Plan tidak ditemukan.');
        }

        // Hapus data dari database
        DB::table('lesson_plan')->where('id', $id)->delete();

        return redirect()->route('lesson-plan.index')
            ->with('success', 'Lesson Plan deleted successfully.');
    }
}
