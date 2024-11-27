@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                        <h5 class="text-white op-7 mb-2">Dashboard </h5>
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
                            <h4 class="card-title">Test History</h4>

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
                                    {{-- <div class="col-md-5 mb-3">
                                        <label for="">Date</label>
                                        <input type="date" name="date" class="form-control"
                                            value="{{ Request::get('date') }}">
                                    </div> --}}
                                    <div class="col-md-2" style="margin-top:20px;">
                                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i>
                                            Filter</button>
                                    </div>
                                </div>
                        </div>
                        </form>
                        <hr>
                        @if (Request::get('student'))
                            @php
                                $testItem = DB::table('test_items')->get();
                                $studentScore = DB::table('student_scores')
                                    ->select('student_scores.*', 'student.name', 'tests.name as test_name', 'price.program as class', 'teacher.name as teacher_name')
                                    ->join('student', 'student.id', 'student_scores.student_id')
                                    ->join('tests', 'tests.id', 'student_scores.test_id')
                                    ->join('price', 'price.id', 'student_scores.price_id')
                                    ->join('teacher', 'teacher.id', 'student.id_teacher')
                                    // ->where('date', Request::get('date'))
                                    ->where('student_id', Request::get('student'))
                                    ->get();
                            @endphp
                            @if (count($studentScore) != 0)
                                <div class=" mt-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Test</th>
                                                        <th class="text-center">Level</th>
                                                        <th class="text-center">Teacher</th>
                                                        @foreach ($testItem as $itemTest)
                                                            <th class="text-center">{{ $itemTest->name }}</th>
                                                        @endforeach
                                                        <th class="text-center">Average</th>
                                                        <th class="text-center">Grade</th>
                                                        <th class="text-center">Comment For Student</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($studentScore as $item)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->test_name }}</td>
                                                            <td>{{ $item->class }}</td>
                                                            <td>{{ $item->teacher_name }}</td>
                                                            @foreach ($testItem as $itemTestKey => $itemTestValue)
                                                                @php
                                                                    $detail = DB::table('student_score_details')
                                                                        ->where('student_score_id', $item->id)
                                                                        ->where('test_item_id', $itemTestValue->id)
                                                                        ->first();
                                                                @endphp
                                                                <td>{{ $detail == null ? '-' : $detail->score }}</td>
                                                            @endforeach
                                                            <td>{{ $item->average_score }}</td>
                                                            <td>{{ Helper::getGrade($item->average_score) }}</td>
                                                            <td>{{ $item->comment }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class=" mt-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Test</th>
                                                        @foreach ($testItem as $itemTest)
                                                            <th class="text-center">{{ $itemTest->name }}</th>
                                                        @endforeach
                                                        <th class="text-center">Average</th>
                                                        <th class="text-center">Grade</th>
                                                        <th class="text-center">Comment For Student</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $countTest = count($testItem) + 6;
                                                    @endphp
                                                    <tr>
                                                        <td colspan="{{ $countTest }}" class="text-center">
                                                            <h1>Data not found</h1>
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
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
