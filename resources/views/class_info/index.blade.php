@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background: linear-gradient(-45deg, #01c293, #009c76) !important">
        <div class="page-inner py-5">
            <h2 class="text-white pb-2 fw-bold">Class Monitoring</h2>
            <h5 class="text-white op-7 mb-2">Track activity progress.</h5>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET" class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">CLASS PROGRAM</label>
                        <select name="class" class="form-control select2">
                            <option value="">-- All Classes --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class') == $c->id ? 'selected' : '' }}>{{ $c->program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">TEACHER</label>
                        <select name="teacher" class="form-control select2">
                            <option value="">-- All Teachers --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ request('teacher') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block fw-bold shadow-sm" style="height: 40px;">
                            <i class="fas fa-search mr-1"></i> SEARCH DATA
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @forelse ($student_class as $group)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-post card-round shadow-sm border-0 h-100 overflow-hidden">
                    <div class="card-header border-0 p-3" style="background: rgba(1, 194, 147, 0.08);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h4 class="fw-bold text-primary mb-1">{{ $group->program ?? 'Program Not Found' }}</h4>
                                <p class="text-muted small mb-0 font-weight-bold">
                                    <i class="fas fa-user-tie mr-1 text-success"></i> {{ $group->teacher_name ?? 'N/A' }}
                                </p>
                            </div>
                            <span class="badge badge-primary px-3 py-2" style="border-radius: 8px;">{{ $group->total_student }} Students</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="small fw-bold text-dark">
                                <i class="far fa-calendar-check text-muted mr-1"></i> {{ $group->day1 }} & {{ $group->day2 }}
                            </div>
                            <div class="small fw-bold text-dark">
                                <i class="far fa-clock text-muted mr-1"></i> {{ $group->course_time }}
                            </div>
                        </div>

                        <div class="separator-dashed my-2"></div>

                        <div class="mb-3 mt-2 p-3 rounded" style="background-color: #f8f9fa; border-left: 4px solid #01c293;">
    <h6 class="fw-bold text-muted small text-uppercase mb-2">Last Activity</h6>
    
    @if($group->last_class)
        <small class="text-success fw-bold d-block mb-1">
            <i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($group->last_class)->format('d M Y') }}
        </small>
        
        {{-- Deskripsi Aktivitas --}}
        <p class="text-dark small mb-2 font-italic" style="line-height: 1.4;">
            "{{ $group->last_activity }}"
        </p>

        {{-- Info Buku (Tambahan Baru) --}}
        <div class="d-flex flex-wrap border-top pt-2 mt-2" style="gap: 15px;">
            @if($group->last_text_book)
            <div class="small">
                <span class="text-muted d-block" style="font-size: 0.75rem;">Text Book:</span>
                <span class="fw-bold text-dark"><i class="fas fa-book mr-1"></i> {{ $group->last_text_book }}</span>
            </div>
            @endif

            @if($group->last_exercise_book)
            <div class="small">
                <span class="text-muted d-block" style="font-size: 0.75rem;">Exercise Book:</span>
                <span class="fw-bold text-dark"><i class="fas fa-edit mr-1"></i> {{ $group->last_exercise_book }}</span>
            </div>
            @endif
        </div>
    @else
        <p class="text-danger font-italic small mb-0">No activity recorded yet.</p>
    @endif
</div>

                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Student List</h6>
                        <div class="custom-scroll" style="max-height: 150px; overflow-y: auto;">
                            @foreach($group->students as $student)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom-faded">
                                <span class="small text-dark"><i class="fas fa-circle mr-2" style="font-size: 6px; color: #01c293;"></i>{{ $student->name }}</span>
                                <small class="text-muted" style="font-size: 10px;">
                                    First Attendance: {{ $student->first_attendance ? \Carbon\Carbon::parse($student->first_attendance)->format('d M Y') : '-' }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-md-12">
                <div class="card border-0 shadow-sm py-5">
                    <div class="card-body text-center">
                        @if(request()->filled('class') || request()->filled('teacher'))
                            <i class="fas fa-search-minus fa-4x text-danger op-4 mb-3"></i>
                            <h3 class="fw-bold text-danger">Data Not Found</h3>
                            <p class="text-muted">Sorry, we couldn't find any active classes matching your filter.</p>
                        @else
                            <i class="fas fa-layer-group fa-4x text-muted op-4 mb-3"></i>
                            <h3 class="fw-bold">No Data Selected</h3>
                            <p class="text-muted">Please select a class program or teacher above to start monitoring.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $student_class->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<style>
    .card-post { transition: all 0.3s; }
    .card-post:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .separator-dashed { border-bottom: 1px dashed #eee; }
    .custom-scroll { scrollbar-width: thin; }
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
    .border-bottom-faded { border-bottom: 1px solid #f8f9fa; }
    .border-bottom-faded:last-child { border-bottom: none; }
</style>
@endsection