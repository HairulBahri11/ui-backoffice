@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Test History</h2>
                    <h5 class="text-white op-7 mb-2">View a student's test score history by class</h5>
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-filter text-primary mr-2"></i>Filter</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="get">
                            <div class="form-row align-items-end">
                                <div class="col-md-8 col-lg-9 mb-3 mb-md-0">
                                    <label for="student-select" class="font-weight-bold text-dark">Student</label>
                                    <select name="student" id="student-select" class="form-control select2">
                                        <option value="">---Choose Student---</option>
                                        @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ $student->id == Request::get('student') ? 'selected' : '' }}>
                                            {{ ucwords($student->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3 d-flex">
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

        @if (Request::get('student'))
        @php
        $testItem = DB::table('test_items')->get();
        $studentScore = DB::table('student_scores')
        ->select(
        'student_scores.*',
        'student.name',
        'tests.name as test_name',
        'price.program as class',
        'teacher.name as teacher_name',
        )
        ->join('student', 'student.id', 'student_scores.student_id')
        ->join('tests', 'tests.id', 'student_scores.test_id')
        ->join('price', 'price.id', 'student_scores.price_id')
        ->join('teacher', 'teacher.id', 'student.id_teacher')
        ->where('student_id', Request::get('student'))
        ->orderBy('student_scores.date', 'desc')
        ->get();
        $scoresByClass = $studentScore->groupBy('class');
        @endphp

        @if (count($studentScore) != 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body d-flex align-items-center flex-wrap">
                        <div class="avatar avatar-lg mr-3">
                            <span class="avatar-title rounded-circle bg-primary text-white"
                                style="font-size: 1.4rem;">
                                {{ strtoupper(substr($studentScore->first()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ ucwords($studentScore->first()->name) }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-user-graduate mr-1"></i>
                                {{ $scoresByClass->count() }} class(es)
                            </small>
                        </div>
                    </div>
                </div>

                @foreach ($scoresByClass as $className => $scores)
                <div class="card mb-3 border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold text-dark">
                            <i class="fas fa-layer-group text-info mr-2"></i>{{ $className }}
                        </span>
                        <span class="badge badge-count badge-secondary">{{ $scores->count() }} test(s)</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($scores as $item)
                            @php
                            $grade = Helper::getGrade($item->average_score);
                            $gradeBadge = in_array($grade, ['A', 'B'])
                            ? 'badge-success'
                            : ($grade == 'C'
                            ? 'badge-warning'
                            : 'badge-danger');
                            $details = DB::table('student_score_details')
                            ->where('student_score_id', $item->id)
                            ->get()
                            ->keyBy('test_item_id');
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="border rounded p-3 h-100" style="background:#fafafa;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong class="text-dark">{{ $item->test_name }}</strong>
                                        <span class="badge {{ $gradeBadge }}" style="font-size: 12px;">
                                            Average &middot; {{ $item->average_score }}
                                        </span>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i>{{ $item->teacher_name }}
                                        <span class="mx-1">&middot;</span>
                                        <i class="fas fa-calendar-alt mr-1"></i>{{ $item->date ? Carbon\Carbon::parse($item->date)->format('d M Y') : '-' }}
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        @foreach ($testItem as $itemTestValue)
                                        <span class="badge badge-light border mr-1 mb-1"
                                            style="font-size: 11px;">
                                            {{ $itemTestValue->name }}:
                                            {{ $details->get($itemTestValue->id)->score ?? '-' }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @if ($item->comment)
                                    <div class="mt-2 small text-dark"
                                        style="border-top:1px dashed #ddd; padding-top:6px;">
                                        <i class="fas fa-comment-dots text-muted mr-1"></i>{{ $item->comment }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                        <h5>No test records found</h5>
                        <p class="mb-0">This student doesn't have any test scores yet.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h5>Select a student</h5>
                        <p class="mb-0">Choose a student above and click Filter to view their test history.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection