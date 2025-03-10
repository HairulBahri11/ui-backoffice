@extends('template.app')
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .rounded-3 {
        border-radius: 0.75rem;
    }

    .shadow-sm {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 0.9rem;
    }

    .rounded-pill {
        border-radius: 50rem;
    }

    .text-primary {
        color: #4a90e2 !important;
    }

    .bg-success {
        background-color: #28a745 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .fw-bold {
        font-weight: 600;
    }

    .mb-1 {
        margin-bottom: 0.25rem;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .me-2 {
        margin-right: 0.5rem;
    }

    .me-3 {
        margin-right: 1rem;
    }

    .p-4 {
        padding: 1.5rem;
    }

    .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
</style>
@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Extra Exam Point</h2>

                    </div>

                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            @if (session('success'))
                <script>
                    swal("Successful", "{{ session('success') }}", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-success'
                            }
                        }
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    swal("Error", "{{ session('error') }}", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        }
                    });
                </script>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <h4 class="card-title">Test History</h4> --}}

                        </div>
                        <div class="card-body">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label for="">Student</label>
                                        <select name="student" id="" class="form-control select2">
                                            <option value="">---Choose Student---</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ $student->id == Request::get('student') ? 'selected' : '' }}>
                                                    {{ ucwords($student->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4" style="margin-top:20px;">
                                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i>
                                            Filter</button>
                                    </div>
                                </div>
                        </div>
                        </form>
                        <hr>
                        @if (Request::get('student'))
                            @php
                                $student_point = DB::table('student')
                                    ->join('price', 'price.id', 'student.priceid')
                                    ->join('teacher', 'teacher.id', 'student.id_teacher')
                                    ->join('day as day1', 'day1.id', 'student.day1')
                                    ->join('day as day2', 'day2.id', 'student.day2')
                                    ->select(
                                        'student.*',
                                        'student.id as idstudent',
                                        'price.program as class',
                                        'teacher.name as teacher_name',
                                        'day1.day as day1_name',
                                        'day2.day as day2_name',
                                    )
                                    ->where('student.id', Request::get('student'))
                                    ->first();

                                $extra_exam = DB::table('extra_exam_point')
                                    ->join('student', 'student.id', 'extra_exam_point.student_id')
                                    ->join('teacher', 'teacher.id', 'extra_exam_point.teacher_id')
                                    ->join('price', 'price.id', 'extra_exam_point.price_id')

                                    ->join('day as day1', 'day1.id', 'extra_exam_point.day1')
                                    ->join('day as day2', 'day2.id', 'extra_exam_point.day2')
                                    ->select(
                                        'extra_exam_point.*',
                                        'student.id as idstudent',
                                        'student.name as student_name',
                                        'price.program as class',
                                        'teacher.name as teacher_name',

                                        'day1.day as day1',
                                        'day2.day as day2',
                                    )
                                    ->where('extra_exam_point.student_id', Request::get('student'))
                                    ->get();

                            @endphp
                            @if (!empty($student_point))
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-5 p-4">
                                            <div class="card">
                                                <div class="card-body d-flex align-items-center p-4 rounded-3 shadow-sm"
                                                    style="background: linear-gradient(135deg, #f6f9fc, #eef2f7);">

                                                    <div>
                                                        <h5 class="card-title fw-bold text-primary mb-2">
                                                            {{ $student_point->name }}</h5>
                                                        <p class="mb-2"><i class="fas fa-id-card text-muted me-2"></i>
                                                            <strong>ID:</strong> {{ $student_point->idstudent }}
                                                        </p>
                                                        <p class="mb-2"><i class="fas fa-school text-muted me-2"></i>
                                                            <strong>Class:</strong>
                                                            {{ $student_point->class . ' - ' . $student_point->day1_name . '' . $student_point->day2_name . ' ' . $student_point->course_time ?? '' }}
                                                        </p>
                                                        <p class="mb-2"><i
                                                                class="fas fa-chalkboard-teacher text-muted me-2"></i>
                                                            <strong>Teacher:</strong> {{ $student_point->teacher_name }}
                                                        </p>
                                                        <p class="mb-0">
                                                            <i class="fas fa-star text-warning me-2"></i>
                                                            <strong>Point:</strong> <span
                                                                class="badge bg-success rounded-pill px-3 py-2 text-white font-bold"
                                                                style="font-size: 14px">{{ $student_point->total_point }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <div class="card">
                                                <div class="card-header bg-info text-white rounded">
                                                    <h5 class="mb-0">Add Extra Exam Point</h5>
                                                </div>
                                                <div class="card-body">
                                                    <form action="{{ route('extra-point.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="student_id"
                                                            value="{{ $student_point->idstudent }}">
                                                        <input type="hidden" name="price_id"
                                                            value="{{ $student_point->priceid }}">
                                                        <input type="hidden" name="teacher_id"
                                                            value="{{ $student_point->id_teacher }}">
                                                        <input type="hidden" name="day1"
                                                            value="{{ $student_point->day1 }}">
                                                        <input type="hidden" name="day2"
                                                            value="{{ $student_point->day2 }}">
                                                        <input type="hidden" name="course_time"
                                                            value="{{ $student_point->course_time }}">
                                                        <input type="hidden" name="category" value="extra-exam">
                                                        <input type="hidden" name="point_history"
                                                            value="{{ $student_point->total_point }}">
                                                        <div class="mb-3">
                                                            <label for="point" class="form-label">Point</label>
                                                            <input type="number" class="form-control" name="point"
                                                                id="point" required min="0">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Description</label>
                                                            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                                                        </div>



                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-save"></i> Save Point
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="basic-datatables"
                                            class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">Class</th>
                                                    <th class="text-center">Teacher</th>
                                                    <th class="text-center">Point</th>
                                                    <th class="text-center">Description</th>
                                                    <th class="text-center">Entry date</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @foreach ($extra_exam as $item)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $item->student_name }}</td>
                                                        <td>{{ $item->class . ' ' . $item->day1 . ' ' . $item->day2 . ' ' . $item->course_time }}
                                                        </td>
                                                        <td>{{ $item->teacher_name }}</td>

                                                        <td>{{ $item->point }}</td>
                                                        <td>{{ $item->description }}</td>
                                                        <td>{{ date('d M Y', strtotime($item->tgl_input)) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <strong>No Data</strong>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>



    </div>
    </div>
@endsection
