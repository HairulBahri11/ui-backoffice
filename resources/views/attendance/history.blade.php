@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Attendance History</h2>
                    <h5 class="text-white op-7 mb-2">View a student's attendance history by class</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="page-inner mt--5">
        @if (session('message'))
        <script>
            swal("Successful", "{{ session('message') }}!", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                },
            });
        </script>
        @endif

        @php
        $classHistory = collect();
        if (Request::get('student')) {
        $dayNames = DB::table('day')->pluck('day', 'id');
        $classHistory = DB::table('attendance_details')
        ->join('attendances', 'attendances.id', 'attendance_details.attendance_id')
        ->join('price', 'price.id', 'attendances.price_id')
        ->where('attendance_details.student_id', Request::get('student'))
        ->where('attendance_details.is_deleted', '0')
        ->select('price.id as price_id', 'price.program', 'attendances.day1', 'attendances.day2',
        'attendances.course_time', 'attendances.date')
        ->orderByDesc('attendances.date')
        ->get()
        ->unique('price_id')
        ->values()
        ->map(function ($c) use ($dayNames) {
        $dayOne = $dayNames[$c->day1] ?? '';
        $dayTwo = $dayNames[$c->day2] ?? '';
        $days = $dayOne . ($dayTwo && $dayTwo != $dayOne ? ' & ' . $dayTwo : '');
        $c->value = $c->price_id;
        $c->label = trim($c->program . ' ' . $days . ' ' . $c->course_time);
        return $c;
        });
        }
        @endphp

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-filter text-primary mr-2"></i>Filter</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="get">
                            <div class="form-row align-items-end">
                                <div class="col-md-5 mb-3 mb-md-0">
                                    <label for="student-select" class="font-weight-bold text-dark">Student</label>
                                    <select name="student" id="student-select" class="form-control select2"
                                        onchange="this.form.submit()">
                                        <option value="">---Choose Student---</option>
                                        @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ $student->id == Request::get('student') ? 'selected' : '' }}>
                                            {{ ucwords($student->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (Request::get('student'))
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="class-select" class="font-weight-bold text-dark">Class</label>
                                    <select name="class" id="class-select" class="form-control select2"
                                        onchange="this.form.submit()">
                                        <option value="">---Choose Class---</option>
                                        @foreach ($classHistory as $c)
                                        <option value="{{ $c->value }}"
                                            {{ $c->value == Request::get('class') ? 'selected' : '' }}>
                                            {{ $c->label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-3 d-flex">
                                    <button class="btn btn-primary flex-grow-1" type="submit">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    @if (Request::get('student'))
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary ml-2"
                                        title="Reset filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (Request::get('student') && Request::get('class'))
        @php
        $attendanceData = DB::table('attendance_details')
        ->select(
        'attendance_details.*',
        'attendances.date',
        'attendances.activity_class',
        'attendances.topic_page',
        'attendances.excercise_book',
        'attendances.flashcard_page',
        'teacher.name as teacher_name',
        )
        ->join('attendances', 'attendances.id', 'attendance_details.attendance_id')
        ->join('teacher', 'teacher.id', 'attendances.teacher_id')
        ->where('attendance_details.student_id', Request::get('student'))
        ->where('attendances.price_id', Request::get('class'))
        ->where('attendance_details.is_deleted', '0')
        ->orderBy('attendances.date', 'desc')
        ->get();
        @endphp

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-layer-group text-info mr-2"></i>
                            {{ $classHistory->firstWhere('price_id', (int) Request::get('class'))->label ?? '' }}
                        </h4>
                        <span class="badge badge-count badge-secondary">{{ $attendanceData->count() }} session(s)</span>
                    </div>
                    <div class="card-body">
                        @if ($attendanceData->count() != 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover table-head-bg-info">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Teacher</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Activity</th>
                                        <th class="text-center">Book / Pages</th>
                                        <th class="text-center">Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendanceData as $no => $item)
                                    @php
                                    if ($item->is_absent == '1') {
                                    $statusLabel = 'Present';
                                    $statusBadge = 'badge-success';
                                    } elseif ($item->is_permission == '1') {
                                    $statusLabel = 'Permission';
                                    $statusBadge = 'badge-info';
                                    } elseif ($item->is_alpha == '1') {
                                    $statusLabel = 'Alpha';
                                    $statusBadge = 'badge-danger';
                                    } else {
                                    $statusLabel = 'Pending';
                                    $statusBadge = 'badge-secondary';
                                    }
                                    $book = collect([
                                    $item->topic_page ? 'Text Book: ' . $item->topic_page : null,
                                    $item->excercise_book ? 'Exercise: ' . $item->excercise_book : null,
                                    $item->flashcard_page ? 'Flashcard: ' . $item->flashcard_page : null,
                                    ])->filter()->implode('<br>');
                                    $comment = collect([
                                    $item->comment_teacher ? 'Teacher: ' . $item->comment_teacher : null,
                                    $item->comment_staff ? 'Staff: ' . $item->comment_staff : null,
                                    ])->filter()->implode('<br>');
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no + 1 }}</td>
                                        <td class="text-center">
                                            {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M Y') : '-' }}
                                        </td>
                                        <td>{{ $item->teacher_name }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>{{ $item->activity_class ?? '-' }}</td>
                                        <td>{!! $book !== '' ? $book : '-' !!}</td>
                                        <td>{!! $comment !== '' ? $comment : '-' !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-2x mb-2"></i>
                            <h5>No attendance records found</h5>
                            <p class="mb-0">This student doesn't have any attendance records for this class.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @elseif (Request::get('student'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-layer-group fa-2x mb-2"></i>
                        <h5>Select a class</h5>
                        <p class="mb-0">Choose one of this student's classes above and click Filter to view their
                            attendance history.</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h5>Select a student</h5>
                        <p class="mb-0">Choose a student above to view their class history.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
