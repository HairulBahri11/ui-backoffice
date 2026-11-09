<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentBiodataController extends Controller
{
    /**
     * Menu dropdown riwayat. Key = nilai query string `section`,
     * `view` = komponen blade yang menampilkan datanya.
     */
    public const SECTIONS = [
        'biodata' => [
            'label' => 'Biodata (Registration History & Barcode)',
            'icon' => 'fa-id-card',
            'view' => 'students-biodata.components.biodata',
        ],
        'movement' => [
            'label' => 'Class Movement',
            'icon' => 'fa-random',
            'view' => 'students-biodata.components.movement',
        ],
        'attendance' => [
            'label' => 'Class Attendance',
            'icon' => 'fa-user-check',
            'view' => 'students-biodata.components.attendance',
        ],
        'tests' => [
            'label' => 'Test History',
            'icon' => 'fa-clipboard-list',
            'view' => 'students-biodata.components.tests',
        ],
        'certificates' => [
            'label' => 'Certification History',
            'icon' => 'fa-certificate',
            'view' => 'students-biodata.components.certificates',
        ],
        'points' => [
            'label' => 'Points History',
            'icon' => 'fa-star',
            'view' => 'students-biodata.components.points',
        ],
        'payments' => [
            'label' => 'Payment History',
            'icon' => 'fa-money-bill-wave',
            'view' => 'students-biodata.components.payments',
        ],
        'status' => [
            'label' => 'Active / Inactive History',
            'icon' => 'fa-exchange-alt',
            'view' => 'students-biodata.components.status',
        ],
    ];

    public function index(Request $request)
    {
        $sections = self::SECTIONS;
        $section = array_key_exists($request->get('section'), $sections) ? $request->get('section') : 'biodata';

        $student = null;
        $selectedStudent = null;
        $data = [];

        if ($request->filled('student')) {
            $student = $this->findStudent($request->get('student'));
            $selectedStudent = $student;

            if ($student) {
                $data = match ($section) {
                    'movement' => $this->movement($student->id),
                    'attendance' => $this->attendance($student->id),
                    'tests' => $this->tests($student->id),
                    'certificates' => $this->certificates($student),
                    'points' => $this->points($student),
                    'payments' => $this->payments($student->id),
                    'status' => $this->status($student),
                    default => $this->biodata($student),
                };
            }
        }

        return view('students-biodata.index', compact('sections', 'section', 'student', 'selectedStudent', 'data'));
    }

    /**
     * Sumber data select2 pencarian student (nama atau ID). Param `status`: ACTIVE / INACTIVE, kosong = semua.
     */
    public function search(Request $request)
    {
        $term = trim((string) $request->get('q'));
        $status = strtoupper((string) $request->get('status'));
        $perPage = 30;
        $page = max(1, (int) $request->get('page', 1));

        $query = DB::table('student')->select('id', 'name', 'status')->orderBy('name');
        if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $query->where('status', $status);
        }
        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
                if (ctype_digit($term)) {
                    $q->orWhere('id', (int) $term);
                }
            });
        }

        $rows = $query->offset(($page - 1) * $perPage)->limit($perPage + 1)->get();

        return response()->json([
            'results' => $rows->take($perPage)->map(fn($s) => [
                'id' => (int) $s->id,
                'text' => $this->studentLabel($s),
            ])->values(),
            'pagination' => ['more' => $rows->count() > $perPage],
        ]);
    }

    private function studentLabel($student): string
    {
        $label = ucwords($student->name) . ' (#' . (int) $student->id . ')';

        return strtoupper((string) $student->status) === 'ACTIVE' ? $label : $label . ' - Inactive';
    }

    private function findStudent($id): ?object
    {
        $student = DB::table('student')
            ->leftJoin('price', 'price.id', 'student.priceid')
            ->leftJoin('teacher', 'teacher.id', 'student.id_teacher')
            ->select('student.*', 'price.program as class_name', 'teacher.name as teacher_name')
            ->where('student.id', (int) $id)
            ->first();

        if ($student) {
            $student->id = (int) $student->id;
            $student->label = $this->studentLabel($student);
        }

        return $student;
    }

    /* ---------------------------------------------------------------
     | Biodata + Registration history + Barcode
     * ------------------------------------------------------------- */
    private function biodata(object $student): array
    {
        $days = DB::table('day')->pluck('day', 'id');

        $profile = DB::table('student')
            ->leftJoin('staff', 'staff.id', 'student.id_staff')
            ->leftJoin('branch', 'branch.id', 'student.branch_id')
            ->where('student.id', $student->id)
            ->select('staff.name as staff_name', 'branch.location as branch_name')
            ->first();

        $parents = DB::table('parent_students')
            ->join('parents', 'parents.id', 'parent_students.parent_id')
            ->where('parent_students.student_id', $student->id)
            ->select('parents.name', 'parents.no_hp', 'parents.gender')
            ->get();

        $registrationPayments = DB::table('paydetail')
            ->join('payment', 'payment.id', 'paydetail.paymentid')
            ->where('paydetail.studentid', $student->id)
            ->where('paydetail.category', 'REGISTRATION')
            ->select('payment.id as receipt', 'payment.paydate', 'payment.method', 'payment.username', 'paydetail.amount')
            ->orderBy('payment.paydate')
            ->get();

        return [
            'profile' => $profile,
            'schedule' => $this->scheduleLabel($days[$student->day1] ?? null, $days[$student->day2] ?? null, $student->course_time),
            'parents' => $parents,
            'registrationPayments' => $registrationPayments,
            'barcode' => $this->barcode($student),
        ];
    }

    /* ---------------------------------------------------------------
     | Class movement (Jump, Transfer & Update Class)
     * ------------------------------------------------------------- */
    private function movement(int $studentId): array
    {
        $days = DB::table('day')->pluck('day', 'id');

        // hanya dari student_class_movements, diisi AttendanceController
        $moves = DB::table('student_class_movements as m')
            ->leftJoin('price as fp', 'fp.id', 'm.from_price_id')
            ->leftJoin('price as tp', 'tp.id', 'm.to_price_id')
            ->leftJoin('teacher as ft', 'ft.id', 'm.from_teacher_id')
            ->leftJoin('teacher as tt', 'tt.id', 'm.to_teacher_id')
            ->where('m.student_id', $studentId)
            ->select('m.*', 'fp.program as from_class', 'tp.program as to_class', 'ft.name as from_teacher', 'tt.name as to_teacher')
            ->orderByDesc('m.created_at')
            ->orderByDesc('m.id')
            ->get()
            ->each(function ($move) use ($days) {
                $move->from_schedule = $this->scheduleLabel($days[$move->from_day1] ?? null, $days[$move->from_day2] ?? null, $move->from_course_time);
                $move->to_schedule = $this->scheduleLabel($days[$move->to_day1] ?? null, $days[$move->to_day2] ?? null, $move->to_course_time);
            });

        return ['moves' => $moves];
    }

    private function barcode(object $student): array
    {
        $svg = null;
        try {
            $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            $svg = 'data:image/svg+xml;base64,' . base64_encode(
                $generator->getBarcode((string) $student->id, $generator::TYPE_CODE_128, 2, 70)
            );
        } catch (\Throwable $e) {
            // barcode tidak bisa dibuat: komponen menampilkan pesan kosong
        }

        // file jpg hasil generate massal (/get-barcode-student) dipakai tombol download & print
        $file = $student->barcode ? public_path('barcode/' . $student->barcode) : null;

        return [
            'image' => $svg,
            'hasFile' => $file !== null && is_file($file),
        ];
    }

    /* ---------------------------------------------------------------
     | Class attendance
     * ------------------------------------------------------------- */
    private function attendance(int $studentId): array
    {
        $days = DB::table('day')->pluck('day', 'id');

        $rows = DB::table('attendance_details')
            ->join('attendances', 'attendances.id', 'attendance_details.attendance_id')
            ->join('price', 'price.id', 'attendances.price_id')
            ->leftJoin('teacher', 'teacher.id', 'attendances.teacher_id')
            ->where('attendance_details.student_id', $studentId)
            ->where('attendance_details.is_deleted', '0')
            ->select(
                'attendance_details.*',
                'attendances.date',
                'attendances.day1',
                'attendances.day2',
                'attendances.course_time',
                'attendances.activity_class',
                'attendances.topic_page',
                'attendances.excercise_book',
                'attendances.flashcard_page',
                'price.id as price_id',
                'price.program',
                'teacher.name as teacher_name'
            )
            ->orderByDesc('attendances.date')
            ->orderByDesc('attendance_details.id')
            ->get();

        $summary = ['present' => 0, 'permission' => 0, 'alpha' => 0, 'pending' => 0];

        $rows->each(function ($row) use (&$summary) {
            if ($row->is_absent == '1') {
                $row->status_key = 'present';
                $row->status_label = 'Present';
                $row->status_badge = 'badge-success';
            } elseif ($row->is_permission == '1') {
                $row->status_key = 'permission';
                $row->status_label = 'Permission';
                $row->status_badge = 'badge-info';
            } elseif ($row->is_alpha == '1') {
                $row->status_key = 'alpha';
                $row->status_label = 'Alpha';
                $row->status_badge = 'badge-danger';
            } else {
                $row->status_key = 'pending';
                $row->status_label = 'Pending';
                $row->status_badge = 'badge-secondary';
            }
            $summary[$row->status_key]++;
        });

        // kelas terbaru di atas (urutan kemunculan pertama = tanggal terbaru)
        $classes = $rows->groupBy('price_id')->map(function ($sessions) use ($days) {
            $latest = $sessions->first();

            return [
                'label' => trim($latest->program . ' ' . $this->scheduleLabel(
                    $days[$latest->day1] ?? null,
                    $days[$latest->day2] ?? null,
                    $latest->course_time
                )),
                'sessions' => $sessions,
            ];
        })->values();

        return ['classes' => $classes, 'summary' => $summary, 'total' => $rows->count()];
    }

    /* ---------------------------------------------------------------
     | Test history
     * ------------------------------------------------------------- */
    private function tests(int $studentId): array
    {
        $testItems = DB::table('test_items')->get();

        $scores = DB::table('student_scores')
            ->join('tests', 'tests.id', 'student_scores.test_id')
            ->leftJoin('price', 'price.id', 'student_scores.price_id')
            ->leftJoin('student', 'student.id', 'student_scores.student_id')
            ->leftJoin('teacher', 'teacher.id', 'student.id_teacher')
            ->where('student_scores.student_id', $studentId)
            ->select(
                'student_scores.*',
                'tests.name as test_name',
                'price.program as class',
                'teacher.name as teacher_name'
            )
            ->orderByDesc('student_scores.date')
            ->get();

        $details = DB::table('student_score_details')
            ->whereIn('student_score_id', $scores->pluck('id'))
            ->get()
            ->groupBy('student_score_id')
            ->map(fn($items) => $items->keyBy('test_item_id'));

        $scores->each(function ($score) use ($details) {
            $grade = \Helper::getGrade($score->average_score);
            $score->grade_badge = in_array($grade, ['A', 'B']) ? 'badge-success' : ($grade == 'C' ? 'badge-warning' : 'badge-danger');
            $score->details = $details->get($score->id, collect());
        });

        return [
            'testItems' => $testItems,
            'scoresByClass' => $scores->groupBy(fn($score) => $score->class ?? '-'),
            'total' => $scores->count(),
        ];
    }

    /* ---------------------------------------------------------------
     | Certification history
     * ------------------------------------------------------------- */
    private function certificates(object $student): array
    {
        $days = DB::table('day')->pluck('day', 'id');

        $history = DB::table('history-certificate')
            ->leftJoin('price', 'price.id', 'history-certificate.price_id')
            ->leftJoin('teacher', 'teacher.id', 'history-certificate.teacher_id')
            ->where('history-certificate.student_id', $student->id)
            ->select('history-certificate.*', 'price.program as class', 'teacher.name as teacher_name')
            ->orderByDesc('history-certificate.date_certificate')
            ->orderByDesc('history-certificate.id')
            ->get()
            ->each(function ($row) use ($days) {
                $row->schedule = $this->scheduleLabel($days[$row->day_1] ?? null, $days[$row->day_2] ?? null, $row->course_time);
            });

        $files = DB::table('certificate_student')
            ->leftJoin('price', 'price.id', 'certificate_student.price_id')
            ->where('certificate_student.student_id', $student->id)
            ->select('certificate_student.file', 'certificate_student.created_at', 'price.program as class')
            ->orderByDesc('certificate_student.created_at')
            ->get();

        return ['history' => $history, 'files' => $files];
    }

    /* ---------------------------------------------------------------
     | Points history
     * ------------------------------------------------------------- */
    private function points(object $student): array
    {
        $history = DB::table('point_histories')
            ->where('student_id', $student->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        // Balance before dihitung ulang, bukan dari point_histories.balance_in_advanced (nilainya sama
        // untuk beberapa poin yang tercatat bersamaan). Mulai dari saldo sekarang, mundur dari transaksi
        // terbaru: saldo sebelum = saldo sesudah - poin masuk (atau + poin keluar).
        $running = (int) $student->total_point;
        $history->each(function ($row) use (&$running) {
            $row->balance_after = $running;
            $running -= $row->type == 'in' ? (int) $row->total_point : -(int) $row->total_point;
            $row->balance_before = $running;
        });

        return [
            'history' => $history,
            'balance' => $student->total_point,
        ];
    }

    /* ---------------------------------------------------------------
     | Payment history
     * ------------------------------------------------------------- */
    private function payments(int $studentId): array
    {
        $history = DB::table('paydetail')
            ->join('payment', 'payment.id', 'paydetail.paymentid')
            ->where('paydetail.studentid', $studentId)
            ->select(
                'paydetail.*',
                'payment.paydate',
                'payment.method',
                'payment.bank',
                'payment.username'
            )
            ->orderByDesc('payment.paytime')
            ->orderByDesc('paydetail.id')
            ->get();

        // pembayaran yang dibatalkan tetap tampil tetapi tidak masuk total
        $total = $history->filter(fn($row) => strtoupper(trim($row->category)) !== 'CANCEL')->sum('amount');

        $bills = DB::table('payment_bill_detail')
            ->where('student_id', $studentId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return ['history' => $history, 'total' => $total, 'bills' => $bills];
    }

    /* ---------------------------------------------------------------
     | Active / inactive history
     * ------------------------------------------------------------- */
    private function status(object $student): array
    {
        // student_review = satu baris per kejadian inactive (alasan dari category_review)
        $reviews = DB::table('student_review')
            ->leftJoin('category_review', 'category_review.id', 'student_review.review_id')
            ->leftJoin('teacher', 'teacher.id', 'student_review.teacher_id')
            ->where('student_review.student_id', $student->id)
            ->select(
                'student_review.id',
                'student_review.date_inactive',
                'student_review.date_active',
                'student_review.program',
                'student_review.explanation',
                'category_review.category_name as reason',
                'teacher.name as teacher_name'
            )
            ->get()
            ->keyBy('id');

        // student_reactivation = satu baris per kejadian aktif kembali; review_id menunjuk student_review.id
        $reactivations = DB::table('student_reactivation')
            ->where('student_id', $student->id)
            ->get();

        $events = collect([(object) [
            'type' => 'registered', 'date' => $student->entrydate, 'rank' => 0, 'seq' => 0,
            'program' => null, 'reason' => null, 'explanation' => 'Registered as a student', 'person' => null,
        ]]);

        foreach ($reviews as $review) {
            $events->push((object) [
                'type' => 'inactive', 'date' => $review->date_inactive, 'rank' => 1, 'seq' => $review->id,
                'program' => $review->program, 'reason' => $review->reason,
                'explanation' => $review->explanation, 'person' => $review->teacher_name,
            ]);
        }

        foreach ($reactivations as $reactivation) {
            $review = $reviews->get($reactivation->review_id);
            $events->push((object) [
                'type' => 'active', 'date' => $reactivation->date_active, 'rank' => 2, 'seq' => $reactivation->id,
                'program' => $review->program ?? null, 'reason' => $review->reason ?? null,
                'explanation' => $review ? 'Reactivated after being inactive since ' . Carbon::parse($review->date_inactive)->format('d M Y') : 'Reactivated',
                'person' => $reactivation->user_name,
            ]);
        }

        // student_review.date_active tanpa baris student_reactivation pasangannya tetap ditampilkan
        $reactivatedReviewIds = $reactivations->pluck('review_id')->all();
        foreach ($reviews as $review) {
            if ($review->date_active && !in_array($review->id, $reactivatedReviewIds)) {
                $events->push((object) [
                    'type' => 'active', 'date' => $review->date_active, 'rank' => 2, 'seq' => $review->id,
                    'program' => $review->program, 'reason' => $review->reason,
                    'explanation' => 'Reactivated after being inactive since ' . Carbon::parse($review->date_inactive)->format('d M Y'), 'person' => null,
                ]);
            }
        }

        // terbaru di atas; pada tanggal yang sama urutan: aktif kembali, inactive, registrasi
        $events = $events->sort(fn($a, $b) => [$b->date, $b->rank, $b->seq] <=> [$a->date, $a->rank, $a->seq])->values();

        return [
            'events' => $events,
            'inactiveCount' => $events->where('type', 'inactive')->count(),
            'activeCount' => $events->where('type', 'active')->count(),
        ];
    }

    private function scheduleLabel(?string $day1, ?string $day2, ?string $time): string
    {
        $days = trim(($day1 ?? '') . ($day2 && $day2 !== $day1 ? ' & ' . $day2 : ''));

        return trim($days . ' ' . ($time ?? ''));
    }
}
