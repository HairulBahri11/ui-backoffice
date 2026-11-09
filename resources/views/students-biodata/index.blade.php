@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Student Biodata</h2>
                    <h5 class="text-white op-7 mb-2">Search a student, then choose which history to view</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="page-inner mt--5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-search text-primary mr-2"></i>Find Student</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('students-biodata.index') }}" method="get" id="biodata-form">
                            <div class="form-row align-items-end">
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <label for="status-select" class="font-weight-bold text-dark">Status</label>
                                    <select name="status" id="status-select" class="form-control">
                                        <option value="">All</option>
                                        <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                        <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="student-select" class="font-weight-bold text-dark">Student</label>
                                    <select name="student" id="student-select" class="form-control" style="width:100%">
                                        <option value=""></option>
                                        @if ($selectedStudent)
                                        <option value="{{ $selectedStudent->id }}" selected>
                                            {{ $selectedStudent->label }}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label for="section-select" class="font-weight-bold text-dark">History</label>
                                    <select name="section" id="section-select" class="form-control"
                                        onchange="this.form.submit()">
                                        @foreach ($sections as $key => $item)
                                        <option value="{{ $key }}" {{ $key == $section ? 'selected' : '' }}>
                                            {{ $item['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex">
                                    <button class="btn btn-primary flex-grow-1" type="submit">
                                        <i class="fas fa-eye mr-1"></i> Show
                                    </button>
                                    @if ($selectedStudent)
                                    <a href="{{ route('students-biodata.index') }}"
                                        class="btn btn-outline-secondary ml-2" title="Reset">
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

        @if ($student)
        <div class="card mb-3">
            <div class="card-body d-flex align-items-center flex-wrap">
                <div class="avatar avatar-lg mr-3">
                    <span class="avatar-title rounded-circle bg-primary text-white" style="font-size: 1.4rem;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">
                        {{ ucwords($student->name) }}
                        <span class="badge {{ strtoupper($student->status) == 'ACTIVE' ? 'badge-success' : 'badge-secondary' }} ml-1"
                            style="font-size: 11px;">{{ strtoupper($student->status ?: '-') }}</span>
                    </h5>
                    <small class="text-muted">
                        <i class="fas fa-hashtag mr-1"></i>{{ $student->id }}
                        <span class="mx-1">&middot;</span>
                        <i class="fas fa-layer-group mr-1"></i>{{ $student->class_name ?? '-' }}
                        <span class="mx-1">&middot;</span>
                        <i class="fas fa-chalkboard-teacher mr-1"></i>{{ $student->teacher_name ?? '-' }}
                    </small>
                </div>
            </div>
        </div>

        @include($sections[$section]['view'], ['student' => $student, 'data' => $data])
        @elseif ($selectedStudent === null && request()->filled('student'))
        @include('students-biodata.components.empty', [
        'icon' => 'fa-user-slash',
        'title' => 'Student not found',
        'text' => 'The selected student does not exist.',
        ])
        @else
        @include('students-biodata.components.empty', [
        'icon' => 'fa-user-graduate',
        'title' => 'Select a student',
        'text' => 'Search a student by name or ID above, then pick the history you want to view.',
        ])
        @endif
    </div>
</div>

<script>
    $(function() {
        // semua tabel riwayat di components memakai class "data-table" (pagination, search, sort)
        $('table.data-table').each(function() {
            var $table = $(this);
            $table.DataTable({
                pageLength: $table.data('page-length') || 10,
                lengthMenu: [10, 25, 50, 100],
                order: [], // pertahankan urutan dari server
                language: {
                    emptyTable: $table.data('empty') || 'No data available'
                }
            });
        });

        $('#student-select').select2({
            placeholder: '---Search Student (name or ID)---',
            allowClear: true,
            ajax: {
                url: "{{ route('students-biodata.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        status: $('#status-select').val()
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                }
            }
        }).on('select2:select select2:clear', function() {
            document.getElementById('biodata-form').submit();
        });
    });
</script>
@endsection
