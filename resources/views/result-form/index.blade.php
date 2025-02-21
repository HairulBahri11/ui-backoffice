@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Poor Result Scores</h2>
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
                                <form action="{{ route('bulk.follow-up') }}" method="POST" id="submitPassed">
                                    @method('delete')
                                    @csrf
                                    <table id="basic-datatables" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Student Name</th>
                                                <th>Class</th>
                                                <th>Teacher</th>
                                                <th>Test</th>
                                                <th>Average Score</th>
                                                <th>Category Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($result as $item)
                                                <?php
                                                $get_grade = Helper::getGrade($item->average_score);
                                                
                                                ?>
                                                <tr>
                                                    <td>{{ $item->student_id }}</td>
                                                    <td>{{ ucwords($item->name) }}</td>
                                                    <td>{{ $item->class }}</td>

                                                    <td>{{ $item->teacher_name ?? '-' }}</td>
                                                    <td>{{ $item->test_name }}</td>
                                                    <td class="text-center">{{ $item->average_score }}</td>
                                                    <td class="text-center">
                                                        @if ($get_grade == 'A')
                                                            <span class="text-white bg-success p-2 "
                                                                style="border-radius : 5px">A</span>
                                                        @elseif($get_grade == 'B')
                                                            <span class="text-white bg-primary p-2 "
                                                                style="border-radius : 5px">B</span>
                                                        @elseif($get_grade == 'C')
                                                            <span class="text-white bg-warning p-2 "
                                                                style="border-radius : 5px">C</span>
                                                        @elseif($get_grade == 'D')
                                                            <span class="text-white bg-danger p-2 "
                                                                style="border-radius : 5px">D</span>
                                                        @else
                                                            <span class="text-white bg-dark p-2 "
                                                                style="border-radius : 5px">E</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    {{-- @if (Auth::guard('teacher')->check() == true)
                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="button" class="btn btn-primary" style="text-align: end"
                                                onclick="submitPassed()">Save changes</button>
                                        </div>
                                    @endif --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Promote Class</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" class="form-inline" id="formDelete">
                        @method('delete')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="form-check-input" type="checkbox" value="true" id="defaultCheck1"
                                        name="promoted">
                                    <label class="form-check-label" for="defaultCheck1">
                                        Promoted to next grade
                                    </label>
                                </div>
                            </div>
                            <div id="form_new">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email2">New Class</label>
                                        {{-- <select name="new_class" id="new_class" class="form-control">
                                            <option value="">---Choose Class</option>
                                            @foreach ($class as $classData)
                                                <option value="{{ $classData->id }}">{{ $classData->program }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email2">New Teacher</label>
                                        {{-- <select name="new_teacher" id="new_teacher" class="form-control">
                                            <option value="">---Choose Teacher</option>
                                            @foreach ($teacher as $teacherData)
                                                <option value="{{ $teacherData->id }}">{{ $teacherData->name }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email2">New Course Time</label>
                                        <input type="time" class="form-control" name="new_course_time">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email2">New Day 1</label>
                                        {{-- <select name="new_day1" id="new_day1" class="form-control">
                                            <option value="">---Choose day</option>
                                            @foreach ($day as $dayData)
                                                <option value="{{ $dayData->id }}">{{ $dayData->day }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email2">New Day 2</label>
                                        {{-- <select name="new_day2" id="new_day2" class="form-control">
                                            <option value="">---Choose day</option>
                                            @foreach ($day as $dayData)
                                                <option value="{{ $dayData->id }}">{{ $dayData->day }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitDelete()">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#form_new").hide();

        function failed(id) {
            $('#studentIdDis' + id).prop('disabled', false);
        }

        function passed(id) {
            $('#studentIdDis' + id).prop('disabled', false);
        }
        $('#defaultCheck1').change(function() {
            if (this.checked) {
                $("#form_new").show();
            } else {
                $("#form_new").hide();
            }
        });

        function confirm(id) {
            $('#formDelete').attr('action', "{{ url('/') }}/follow-up/" + id);
        }

        function submitDelete() {
            swal("Are you sure ?", "Data will be updated", {
                icon: "info",
                buttons: {
                    confirm: {
                        className: 'btn btn-success',
                        text: 'Ok'
                    },
                    dismiss: {
                        className: 'btn btn-secondary',
                        text: 'Cancel'
                    },
                },
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result == true) {
                    $('#formDelete').submit();
                }
            });
        }

        function submitPassed() {
            swal("Are you sure ?", "Data will be updated", {
                icon: "info",
                buttons: {
                    confirm: {
                        className: 'btn btn-success',
                        text: 'Ok'
                    },
                    dismiss: {
                        className: 'btn btn-secondary',
                        text: 'Cancel'
                    },
                },
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result == true) {
                    $('#submitPassed').submit();
                }
            });
        }

        const dataStudent = {
            id: [],
        };

        function onClickFollowUp(id) {
            const checkbox = document.getElementById('studentId' + id);
            if (checkbox.checked) {
                if (dataStudent.id.indexOf(id) === -1) {
                    dataStudent.id.push(id);
                    var newElement = $(`<input type="hidden" id="idSiswa${id}" name="student_id[]" value="${id}">`);
                    $("#idSiswaFollowUp").append(newElement);
                    if (dataStudent.id.length != 0) {
                        $('#buttonBulkDelete').prop('disabled', false);
                    } else {
                        $('#buttonBulkDelete').prop('disabled', true);
                    }
                }
            } else {
                const index = dataStudent.id.indexOf(id);
                $("#idSiswa" + id).remove();
                if (index !== -1) {
                    dataStudent.id.splice(index, 1);
                    if (dataStudent.id.length != 0) {
                        $('#buttonBulkDelete').prop('disabled', false);
                    } else {
                        $('#buttonBulkDelete').prop('disabled', true);
                    }
                }
            }
        }

        function submitBulkDelete() {
            swal("Are you sure ?", "Data will be bulk updated", {
                icon: "info",
                buttons: {
                    confirm: {
                        className: 'btn btn-success',
                        text: 'Ok'
                    },
                    dismiss: {
                        className: 'btn btn-secondary',
                        text: 'Cancel'
                    },
                },
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result == true) {
                    $('#formBulkDelete').submit();
                }
            });
        }
    </script>
@endsection
