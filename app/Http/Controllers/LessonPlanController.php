<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonPlanController extends Controller
{
    public function index(Request $request)
    {
        // Fitur 2: Menentukan default hari berjalan (1 = Monday, ..., 7 = Sunday)
        $currentDayOfWeek = Carbon::now()->dayOfWeekIso;

        $query = LessonPlan::with(['teacher', 'price']);

        if (Auth::guard('teacher')->check() && Auth::guard('teacher')->id() != 21) {
            // Fitur 3: Teacher Area
            $teacherId = Auth::guard('teacher')->id();
            $query->where('teacher_id', $teacherId);

            // Filter hari untuk teacher (jika tidak dipilih, default ke hari berjalan)
            $dayFilter = $request->query('day', $currentDayOfWeek);
            if ($dayFilter) {
                $query->where(function ($q) use ($dayFilter) {
                    $q->where('day1', $dayFilter)->orWhere('day2', $dayFilter);
                });
            }
        } else {
            // Fitur 4: Superadmin Area dengan multi-filter (Teacher, Day, Tanggal, Class)
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            if ($request->filled('day')) {
                $dayFilter = $request->day;
                $query->where(function ($q) use ($dayFilter) {
                    $q->where('day1', $dayFilter)->orWhere('day2', $dayFilter);
                });
            } else if (!$request->filled('teacher_id') && !$request->filled('date') && !$request->filled('class_id')) {
                // Jika superadmin tidak memfilter apapun, default tampilkan hari berjalan
                $query->where(function ($q) use ($currentDayOfWeek) {
                    $q->where('day1', $currentDayOfWeek)->orWhere('day2', $currentDayOfWeek);
                });
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            if ($request->filled('class_id')) {
                $query->where('class', $request->class_id);
            }
        }

        $lessonPlans = $query->orderBy('created_at', 'desc')->get();

        // Ambil data master pendukung filter Superadmin
        $teachers = DB::table('teacher')->get();
        $classes = DB::table('price')->get();

        return view('lesson-plan.index', compact('lessonPlans', 'teachers', 'classes'));
    }

    public function create()
    {
        if (!Auth::guard('teacher')->check()) {
            return redirect()->back()->with('error', 'You are not authorized to create a lesson plan.');
        }

        // Komentar asli di view: jam 15:01 - 23:59 tidak boleh create data hari berjalan
        if (Carbon::now()->format('H:i') >= '15:01' && Carbon::now()->format('H:i') <= '23:59') {
            return redirect()->route('lesson-plan.index')->with('error', 'Cannot create data after 15:00 on the current day.');
        }

        return view('lesson-plan.create');
    }

    // Fungsi helper privat untuk mengecek validasi waktu edit/update sesuai Fitur 1
    private function isEditable($createdAt)
    {
        $createdDate = Carbon::parse($createdAt)->startOfDay();
        $today = Carbon::today();

        // Jika data dibuat hari ini, hanya bisa diedit SEBELUM jam 15:00 (Jam 3 Sore)
        if ($createdDate->equalTo($today)) {
            return Carbon::now()->format('H:i') < '15:00';
        }

        // Hari berikutnya / sesudahnya bebas bisa diedit kapan saja
        return $createdDate->lessThan($today);
    }

    public function edit($id)
    {
        $item = DB::table('lesson_plan')->where('id', $id)->first();

        if (!$item) {
            return redirect()->route('lesson-plan.index')->with('error', 'Data Lesson Plan tidak ditemukan.');
        }

        // Fitur 1: Cek Batasan Waktu Edit
        if (!$this->isEditable($item->created_at)) {
            return redirect()->route('lesson-plan.index')->with('error', 'This lesson plan can no longer be edited (Time limit exceeded).');
        }

        $item->teacher = DB::table('teacher')->where('id', $item->teacher_id)->first();
        $item->price = DB::table('price')->where('id', $item->class)->first();

        $totalStudents = DB::table('student')
            ->where('priceid', $item->class)
            ->where('status', 'ACTIVE')
            ->count();

        return view('lesson-plan.edit', compact('item', 'totalStudents'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('lesson_plan')->where('id', $id)->first();
        if (!$item) {
            return redirect()->route('lesson-plan.index')->with('error', 'Data tidak ditemukan.');
        }

        // Fitur 1: Cek Batasan Waktu Update ke Database
        if (!$this->isEditable($item->created_at)) {
            return redirect()->route('lesson-plan.index')->with('error', 'This lesson plan can no longer be updated.');
        }

        $request->validate([
            'topic'       => 'required|string|max:255',
            'flashcards'  => 'nullable|string',
            'exercise'    => 'nullable|string',
            'activity'    => 'nullable|string',
        ]);

        DB::table('lesson_plan')
            ->where('id', $id)
            ->update([
                'topic'      => $request->topic,
                'flashcards' => $request->flashcards,
                'exercise'   => $request->exercise,
                'activity'   => $request->activity,
            ]);

        return redirect()->route('lesson-plan.index')->with('success', 'Lesson Plan Updated Successfully!');
    }

    public function getClassesByDay(Request $request)
    {
        $day = $request->query('day');
        $teacherId = Auth::guard('teacher')->id();

        // Mapping ID hari ke nama hari bahasa Inggris untuk Carbon
        $dayNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];

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

        // Map data untuk mengecek apakah waktu rilis gembok (1 minggu dari day2 terakhir) sudah terpenuhi
        $classes = $classes->map(function ($item) use ($teacherId, $dayNames) {

            // 1. Cari data lesson plan terakhir yang dibuat oleh teacher untuk kelas & jam ini
            $lastLessonPlan = DB::table('lesson_plan')
                ->where('teacher_id', $teacherId)
                ->where('class', $item->priceid)
                ->where('course_time', $item->course_time)
                ->orderBy('created_at', 'desc')
                ->first();

            $isLocked = false;

            if ($lastLessonPlan) {
                $createdAt = \Carbon\Carbon::parse($lastLessonPlan->created_at);

                // 2. Tentukan target hari mengajar kedua (day2) dari lesson plan yang terakhir dibuat itu
                $day2TargetName = $dayNames[$item->day2] ?? null;

                if ($day2TargetName) {
                    // Ambil tanggal day2 yang jatuh pada rentang minggu pembuatan lesson plan tersebut
                    $lastDay2Date = $createdAt->copy()->next($day2TargetName);

                    // Jika ternyata next() melompat ke minggu depannya, kembalikan ke minggu rilisnya yang tepat
                    if ($lastDay2Date->diffInDays($createdAt) > 6) {
                        $lastDay2Date = $createdAt->copy()->previous($day2TargetName);
                    }

                    // 3. Batas gembok: 1 minggu (7 hari) setelah hari day2 dari siklus materi tersebut
                    $lockUntilDate = $lastDay2Date->copy()->addWeek()->startOfDay();

                    // Jika waktu sekarang belum melewati tanggal pembukaan gembok, maka statusnya READONLY
                    if (\Carbon\Carbon::now()->startOfDay()->lessThan($lockUntilDate)) {
                        $isLocked = true;
                    }
                }
            }

            $item->already_filled_this_week = $isLocked;
            return $item;
        });

        return response()->json($classes);
    }

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
