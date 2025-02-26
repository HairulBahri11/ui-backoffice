@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Student's Birthday ({{ date('F') }}) </h2>

                        {{-- <h5 class="text-white op-7 mb-2">Free Bootstrap 4 Admin Dashboard</h5> --}}
                    </div>
                    <div class="ml-md-auto py-2 py-md-0">
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            @if (session('status'))
                <script>
                    swal("Success", "{{ session('status') }}!", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-success'
                            }
                        },
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    swal("Failed", "{{ session('error') }}!", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        },
                    });
                </script>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Students</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Birthday</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Teacher</th>

                                            {{-- <th>Age</th> --}}
                                            {{-- <th>Birthday This Month?</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($student_birthday as $student)
                                            <tr>
                                                <td>{{ $student['id'] }}</td>
                                                <td>{{ date('d M', strtotime($student['birthday'])) }}</td>
                                                <td>{{ $student['name'] }}</td>
                                                <td>{{ $student['class'] }} -
                                                    {{ $student['day1'] . $student['day2'] . ' - ' . $student['course_time'] }}
                                                </td>
                                                <td>{{ $student['teacher'] }}</td>

                                                {{-- <td>{{ $student['age'] == 0 ? '-' : $student['age'] }}</td> --}}
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
