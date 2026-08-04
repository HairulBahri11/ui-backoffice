<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonPlanController extends Controller
{
    public function index(Request $request)
    {
        // Fitur 2: Menentukan default hari berjalan (1 = Monday, ..., 7 = Sunday)
        $currentDayOfWeek = Carbon::now()->dayOfWeekIso;

        $query = LessonPlan::with(['teacher', 'price']);

        // ==========================================
        // FILTER DEFAULT: HANYA MINGGU INI 
        // Diaktifkan jika: tidak ada 'show_all' DAN tidak ada filter spesifik (seperti date/teacher/class)
        // ==========================================
        if (!$request->has('show_all') && !$request->filled('date') && !$request->filled('teacher_id') && !$request->filled('class_id')) {
            $query->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

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

        if (!$this->isWithinCreateWindow()) {
            return redirect()->route('lesson-plan.index')->with('error', $this->createWindowMessage());
        }

        return view('lesson-plan.create');
    }

    // Fitur: Batasan jam create lesson plan. Hari Minggu tidak boleh sama sekali,
    // hari Sabtu hanya jam 08:00-13:00, hari lain tidak boleh create data setelah jam 15:00 hari berjalan.
    private function isWithinCreateWindow()
    {
        $now = Carbon::now();

        if ($now->isSunday()) {
            return false;
        }

        if ($now->isSaturday()) {
            return $now->format('H:i') >= '08:00' && $now->format('H:i') <= '13:00';
        }

        return $now->format('H:i') < '15:01';
    }

    private function createWindowMessage()
    {
        $now = Carbon::now();

        if ($now->isSunday()) {
            return 'Lesson plans cannot be created on Sunday.';
        }

        if ($now->isSaturday()) {
            return 'On Saturday, lesson plans can only be created between 08:00 and 13:00.';
        }

        return 'Cannot create data after 15:00 on the current day.';
    }

    // Gabungkan dua input page (start & end) jadi satu string dipisah '&', contoh: "1-3" & "5" -> "1-3&5"
    private function combinePageRange($start, $end)
    {
        $start = trim((string) $start);
        $end = trim((string) $end);

        if ($start === '' && $end === '') {
            return null;
        }

        if ($start === '') {
            return $end;
        }

        if ($end === '') {
            return $start;
        }

        return $start . '&' . $end;
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
            'topic_start' => 'nullable|string|max:50',
            'topic_end'   => 'nullable|string|max:50',
            'flashcards_start' => 'nullable|string|max:50',
            'flashcards_end' => 'nullable|string|max:50',
        ]);

        // 1. Gabungkan range Topic/Textbook, contoh: "1-3" & "5" -> "1-3&5"
        $topicPage = $this->combinePageRange($request->topic_start, $request->topic_end);

        // 2. Gabungkan range Flashcards (opsional, cek jika diisi)
        $flashcardPage = $this->combinePageRange($request->flashcards_start, $request->flashcards_end);

        DB::table('lesson_plan')
            ->where('id', $id)
            ->update([
                'topic'      => $request->topic,
                'flashcards' => $request->flashcards,
                'exercise'   => $request->exercise,
                'activity'   => $request->activity,
                'topic_page' => $topicPage,
                'flashcard_page' => $flashcardPage,
            ]);

        return redirect()->route('lesson-plan.index')->with('success', 'Lesson Plan Updated Successfully!');
    }

    public function getClassesByDay(Request $request)
    {
        $day = $request->query('day');
        $teacherId = Auth::guard('teacher')->id();

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
                'price.level',
                'student.priceid',
                'student.course_time',
                'student.day1',
                'student.day2',
                'day_one.day as day1_name',
                'day_two.day as day2_name',
                DB::raw('COUNT(student.id) as total_students'),
                DB::raw("GROUP_CONCAT(student.name SEPARATOR ', ') as student_names")
            )
            ->groupBy(
                'price.program',
                'price.level',
                'student.priceid',
                'student.course_time',
                'student.day1',
                'student.day2',
                'day_one.day',
                'day_two.day'
            )
            ->orderBy('student.course_time', 'asc')
            ->get();

        // Loop data untuk validasi gembok berdasarkan Minggu Berjalan (Current Calendar Week)
        $classes = $classes->map(function ($item) use ($teacherId, $day) {

            // 1. Ambil data lesson plan TERAKHIR untuk kombinasi kelas, jam, guru, DAN hari spesifik ini
            // (for_day membedakan kelas yang jadwalnya 2 hari berbeda, mis. Tuesday & Wednesday,
            // supaya masing-masing hari punya kesempatan create sendiri-sendiri dalam 1 minggu.
            // Data lama yang for_day-nya masih NULL tetap dianggap mengunci semua hari, sesuai perilaku lama.)
            $lastLessonPlan = DB::table('lesson_plan')
                ->where('teacher_id', $teacherId)
                ->where('class', $item->priceid)
                ->where('course_time', $item->course_time)
                ->where(function ($query) use ($day) {
                    $query->where('for_day', $day)
                        ->orWhereNull('for_day');
                })
                ->orderBy('created_at', 'desc')
                ->first();

            $isLocked = false;

            if ($lastLessonPlan) {
                $createdAt = \Carbon\Carbon::parse($lastLessonPlan->created_at);

                // 2. LOGIKA KUNCI: Ambil awal minggu (Senin) dari tanggal pembuatan lesson plan terakhir tersebut
                $lastPlanStartOfWeek = $createdAt->copy()->startOfWeek()->format('Y-m-d');

                // Ambil awal minggu (Senin) dari waktu SEKARANG (saat guru mengakses form)
                $currentStartOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');

                // Jika awal minggunya SAMA, artinya guru SUDAH PERNAH membuat lesson plan untuk kelas ini di minggu ini.
                // Jika awal minggunya BERBEDA (seperti kasus dibuat tanggal 5 Juli [minggu lalu] vs diakses tanggal 6 Juli [minggu baru]), gembok otomatis LEPAS.
                if ($lastPlanStartOfWeek === $currentStartOfWeek) {
                    $isLocked = true;
                }
            }

            // Return status gembok ke view
            $item->already_filled_this_week = $isLocked;
            return $item;
        });

        return response()->json($classes);
    }

    public function getLastAgenda(Request $request)
    {
        // ambil price_id, day1, day2, course_time dant student_id kalok ada
        $priceId = $request->query('price_id');
        $day1 = $request->query('day1');
        $day2 = $request->query('day2');
        $courseTime = $request->query('course_time');
        $studentId = $request->query('student_id');
        $teacherId = Auth::guard('teacher')->id();

        $lastAgenda = Attendance::where('price_id', $priceId)
            ->where(function ($query) use ($day1, $day2) {
                $query->where('day1', $day1)
                    ->orWhere('day2', $day2);
            })
            ->where('teacher_id', $teacherId)
            ->where('course_time', $courseTime)
            ->orderBy('date', 'desc')
            ->first();

        return response()->json($lastAgenda);
    }

    public function store(Request $request)
    {
        if (!$this->isWithinCreateWindow()) {
            return redirect()->route('lesson-plan.index')->with('error', $this->createWindowMessage());
        }

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
                    // Hari spesifik yang dipilih di dropdown "Choose Day" saat batch ini dibuat,
                    // dipakai buat membedakan slot Tuesday vs Wednesday pada kelas yang jadwalnya 2 hari beda.
                    'for_day'      => $request->selected_day,
                    'course_time'  => $plan['course_time'],
                    'topic'        => $plan['topic'],
                    'flashcards'   => $plan['flashcards'] ?? '-',
                    'exercise'     => $plan['exercise'] ?? '-',
                    'activity'     => $plan['activity'] ?? '-',
                    'topic_page'   => $this->combinePageRange($plan['topic_start'] ?? null, $plan['topic_end'] ?? null) ?? '-',
                    'flashcard_page' => $this->combinePageRange($plan['flashcards_start'] ?? null, $plan['flashcards_end'] ?? null) ?? '-',
                    'created_at'   => now(),
                ]);
                $insertedCount++;
            }

            DB::commit(); // Simpan permanen jika semua perulangan sukses

            return redirect()->route('lesson-plan.index')
                ->with('success', "Successfully saved $insertedCount Lesson Plan(s)!");
        } catch (\Exception $e) {
            Log::error('Error saving lesson plans: ' . $e->getMessage());
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

        // Fitur: Data hanya boleh dihapus di hari yang sama dengan hari pembuatannya
        if (!Carbon::parse($lessonPlan->created_at)->isToday()) {
            return redirect()->route('lesson-plan.index')
                ->with('error', 'This lesson plan can no longer be deleted (past its creation day).');
        }

        // Hapus data dari database
        DB::table('lesson_plan')->where('id', $id)->delete();

        return redirect()->route('lesson-plan.index')
            ->with('success', 'Lesson Plan deleted successfully.');
    }
}
