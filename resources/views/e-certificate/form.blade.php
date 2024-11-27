@extends('template.app')

@section('content')
    <div class="content">
        <div class="page-inner py-5 panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div class="">
                    <h2 class="text-white pb-2 fw-bold">Last Score Student</h2>
                    <ul class="breadcrumbs">
                        <li class="nav-home text-white">
                            <a href="#">
                                <i class="flaticon-home text-white"></i>
                            </a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">Last Score Student</a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">{{ $class->program }}</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="page-inner mt--5">
            @if (session('status'))
                <script>
                    swal("Success!", "{{ session('status') }}!", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        },
                    });
                </script>
            @endif
            @if ($errors->any())
                <script>
                    swal("Failed!", "Please fill all status student!", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        },
                    });
                </script>
            @endif
            <form action="{{ route('e-certificate.store') }}" method="POST" id="formSubmit">
                @csrf
                @foreach ($students as $student)
                    {{-- @if ($student->is_certificate != true) --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ $student->name }} Score</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Skill</th>
                                                        @if ($class->id >= 43 && $class->id <= 45)
                                                            <th>
                                                                Test 1
                                                            </th>
                                                        @elseif ($class->id >= 22 && $class->id <= 37)
                                                            <th>
                                                                Test 1
                                                            </th>
                                                            <th>
                                                                Test 2
                                                            </th>
                                                        @else
                                                            <th>
                                                                Test 1
                                                            </th>
                                                            <th>
                                                                Test 2
                                                            </th>
                                                            <th>
                                                                Test 3
                                                            </th>
                                                        @endif
                                                        <th>Average</th>
                                                        <th>Grade</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total_test1 = 0;
                                                        $total_test2 = 0;
                                                        $total_test3 = 0;
                                                        $total_test = 0;
                                                    @endphp
                                                    @foreach ($testItem as $item)
                                                        @php
                                                            $score1 = DB::table('student_scores')
                                                                ->join(
                                                                    'student_score_details',
                                                                    'student_score_details.student_score_id',
                                                                    'student_scores.id',
                                                                )
                                                                ->select(
                                                                    'student_scores.*',
                                                                    'student_score_details.score as score_test',
                                                                    'student_score_details.test_item_id',
                                                                )
                                                                ->where('student_id', $student->id)
                                                                ->where('price_id', $class->id)
                                                                ->where('test_id', 1)
                                                                ->where('student_score_details.test_item_id', $item->id)
                                                                ->first();
                                                            $score2 = DB::table('student_scores')
                                                                ->join(
                                                                    'student_score_details',
                                                                    'student_score_details.student_score_id',
                                                                    'student_scores.id',
                                                                )
                                                                ->select(
                                                                    'student_scores.*',
                                                                    'student_score_details.score as score_test',
                                                                    'student_score_details.test_item_id',
                                                                )
                                                                ->where('student_id', $student->id)
                                                                ->where('price_id', $class->id)
                                                                ->where('test_id', 2)
                                                                ->where('student_score_details.test_item_id', $item->id)
                                                                ->first();
                                                            $score3 = DB::table('student_scores')
                                                                ->join(
                                                                    'student_score_details',
                                                                    'student_score_details.student_score_id',
                                                                    'student_scores.id',
                                                                )
                                                                ->select(
                                                                    'student_scores.*',
                                                                    'student_score_details.score as score_test',
                                                                    'student_score_details.test_item_id',
                                                                )
                                                                ->where('student_id', $student->id)
                                                                ->where('price_id', $class->id)
                                                                ->where('test_id', 3)
                                                                ->where('student_score_details.test_item_id', $item->id)
                                                                ->first();
                                                            //$score_test1 = $score1 ? $score1->score_test : 0;
                                                            //$score_test2 = $score2 ? $score2->score_test : 0;
                                                            //$score_test3 = $score3 ? $score3->score_test : 0;

                                                            //$score_test1
                                                            //$score_test2
                                                            //$score_test3

                                                            $divider = 0;

                                                            $score_test1 = 0;
                                                            $score_test2 = 0;
                                                            $score_test3 = 0;

                                                            if ($score1) {
                                                                if ($score1->score_test != 0) {
                                                                    $score_test1 = $score1->score_test;
                                                                    $divider += 1;
                                                                }
                                                            }
                                                            if ($score2) {
                                                                if ($score2->score_test != 0) {
                                                                    $score_test2 = $score2->score_test;
                                                                    $divider += 1;
                                                                }
                                                            }
                                                            if ($score3) {
                                                                if ($score3->score_test != 0) {
                                                                    $score_test3 = $score3->score_test;
                                                                    $divider += 1;
                                                                }
                                                            }

                                                            if ($divider == 0) {
                                                                $divider = 1;
                                                            }

                                                            //$score_test = round(($score_test1 + $score_test2 + $score_test3) / 3);

                                                            $score_test = round(
                                                                ($score_test1 + $score_test2 + $score_test3) / $divider,
                                                            );

                                                            $total_test1 += $score_test1;
                                                            $total_test2 += $score_test2;
                                                            $total_test3 += $score_test3;
                                                            $total_test += $score_test;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            @if ($class->id >= 43 && $class->id <= 45)
                                                                <td>{{ $score_test1 }}</td>
                                                            @elseif($class->id >= 22 && $class->id <= 37)
                                                                <td>{{ $score_test1 }}</td>
                                                                <td>{{ $score_test2 }}</td>
                                                            @else
                                                                <td>{{ $score_test1 }}</td>
                                                                <td>{{ $score_test2 }}</td>
                                                                <td>{{ $score_test3 }}</td>
                                                            @endif
                                                            <td>{{ $score_test }}</td>
                                                            <td>{{ Helper::getGrade($score_test) }}</td>
                                                            @if ($loop->iteration == 1)
                                                                <td rowspan="{{ count($testItem) + 1 }}">
                                                                    {{-- <input
                                                                            type="checkbox"
                                                                            id="studentId{{ $student->id }}"
                                                                            value="{{ $student->id }}"
                                                                            onclick="onClickFollowUp({{ $student->id }})"> --}}
                                                                    <input type="text" name='student_id[]'
                                                                        value="{{ $student->id }}" style="display: none">
                                                                    <select name="status[]"
                                                                        id="studentId{{ $student->id }}"
                                                                        class="form-control select2">
                                                                        <option value="">---Choose Status---</option>
                                                                        <option value="1">Passed</option>
                                                                        <option value="0">Failed</option>
                                                                        <option value="2">Follow Up</option>
                                                                    </select>
                                                                </td>
                                                                <td rowspan="{{ count($testItem) + 1 }}">
                                                                    {{-- <form
                                                                    action="{{ url('e-certificate') . '/' . $student->id . '?type=done' }}"
                                                                    method="POST" id="submitForm{{ $student->id }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                </form>
                                                                <a href="javascript:void(0)" class="btn btn-success btn-sm"
                                                                    onclick="done({{ $student->id }})">Done</a> --}}
                                                                    {{-- @if ($student->is_certificate == false)
                                                                            <input type="checkbox" name="student[]"
                                                                                class="studentId"
                                                                                value="{{ $student->id }}">
                                                                        @endif --}}
                                                                    <a class="btn btn-sm btn-warning dropdown-toggle"
                                                                        type="button" id="dropdownMenuButton"
                                                                        data-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        Edit
                                                                    </a>
                                                                    <div class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenuButton">
                                                                        @if ($class->id >= 43 && $class->id <= 45)
                                                                            @if ($score1)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $student->priceid . '&student=' . $student->id . '&test=1&id_test=' . $score1->id . '&date=' . $score1->date . '&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="dropdown-item">Test 1</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $student->priceid . '&student=' . $student->id . '&test=1&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    1</a>
                                                                            @endif
                                                                        @elseif($class->id >= 22 && $class->id <= 37)
                                                                            @if ($score1)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $student->priceid . '&student=' . $student->id . '&test=1&id_test=' . $score1->id . '&date=' . $score1->date . '&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="dropdown-item">Test 1</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $student->priceid . '&student=' . $student->id . '&test=1&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    1</a>
                                                                            @endif
                                                                            @if ($score2)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $student->priceid . '&student=' . $student->id . '&test=2&id_test=' . $score2->id . '&date=' . $score2->date . '&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="dropdown-item">Test 2</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $student->priceid . '&student=' . $student->id . '&test=2&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    2</a>
                                                                            @endif
                                                                        @else
                                                                            @if ($score1)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $student->priceid . '&student=' . $student->id . '&test=1&id_test=' . $score1->id . '&date=' . $score1->date . '&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="dropdown-item">Test 1</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $student->priceid . '&student=' . $student->id . '&test=1&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    1</a>
                                                                            @endif
                                                                            @if ($score2)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $student->priceid . '&student=' . $student->id . '&test=2&id_test=' . $score2->id . '&date=' . $score2->date . '&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="dropdown-item">Test 2</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $student->priceid . '&student=' . $student->id . '&test=2&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    2</a>
                                                                            @endif
                                                                            @if ($score3)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $student->priceid . '&student=' . $student->id . '&test=3&id_test=' . $score3->id . '&date=' . $score3->date . '&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="dropdown-item">Test 3</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $student->priceid . '&student=' . $student->id . '&test=3&day1=' . $student->day1 . '&day2=' . $student->day2 . '&teacher=' . $student->id_teacher . '&time=' . $student->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    3</a>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        @if ($class->id >= 43 && $class->id <= 45)
                                                            <td>Total</td>
                                                            <td>{{ round($total_test1 / count($testItem)) }}</td>
                                                            <td>{{ round($total_test / count($testItem)) }}</td>
                                                            <td>{{ Helper::getGrade(round($total_test / count($testItem))) }}
                                                            </td>
                                                        @elseif($class->id >= 22 && $class->id <= 37)
                                                            <td colspan="2">Total</td>
                                                            <td>{{ round($total_test1 / count($testItem)) }}</td>
                                                            <td>{{ round($total_test2 / count($testItem)) }}</td>
                                                            <td>{{ round($total_test / count($testItem)) }}</td>
                                                            <td>
                                                                {{ Helper::getGrade(round($total_test / count($testItem))) }}
                                                            </td>
                                                        @else
                                                            <td colspan="2">Total</td>
                                                            <td>{{ round($total_test1 / count($testItem)) }}</td>
                                                            <td>{{ round($total_test2 / count($testItem)) }}</td>
                                                            <td>{{ round($total_test3 / count($testItem)) }}</td>
                                                            <td>{{ round($total_test / count($testItem)) }}</td>
                                                            <td>
                                                                {{ Helper::getGrade(round($total_test / count($testItem))) }}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- @endif --}}
                @endforeach
                <div class="d-flex justify-content-end">
                    {{-- <button class="btn btn-info btn-sm mr-3" id="buttonSubmitFollowUp" type="button" data-toggle="modal"
                        data-target="#exampleModal" disabled>Submit Follow Up</button> --}}
                    {{-- <button class="btn btn-info btn-sm mr-3" id="buttonSubmitFollowUp" type="button"
                        onclick="submitFollowUp()" disabled>Submit Follow Up</button> --}}
                    <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#exampleModal"
                        onclick="checkCheked()">Done</button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Set date certificate</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="date" name="date_certificate" class="form-control" id="date_certificate"
                                    value="{{ old('date_certificate') }}">
                                <span style="color:red" id="error-date-certificate">Please fill date certificate</span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="confirm()" id="saveChanges">Save
                                    changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form action="{{ route('follow-up.store') }}" method="POST" id="submitFollowUp">
        @csrf
        <div id="idSiswaFollowUp">
            <input type="hidden" name="old_day1" value="{{ Request::get('day1') }}">
            <input type="hidden" name="old_day2" value="{{ Request::get('day2') }}">
            <input type="hidden" name="old_teacher" value="{{ Request::get('teacher') }}">
            <input type="hidden" name="old_time" value="{{ Request::get('time') }}">
            <input type="hidden" name="old_class" value="{{ $class->id }}">
        </div>
    </form>
    <script>
        $('#error-date-certificate').hide();

        function done(id) {
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
                    $('#submitForm' + id).submit();
                }
            });
        }

        function checkCheked() {
            var numberChecked = $('.studentId:checked').length;
            if (numberChecked != 0) {
                $('#saveChanges').prop('disabled', false);
            }
        }

        function confirm() {
            const date = $('#date_certificate').val();
            if (date == '') {
                $('#error-date-certificate').show();
            } else {
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
                        $('#formSubmit').submit();
                    }
                });
            }
        }

        const dataStudent = {
            id: [],
            old_day1: "{{ Request::get('day1') }}",
            old_day2: "{{ Request::get('day2') }}",
            old_teacher: "{{ Request::get('teacher') }}",
            old_time: "{{ Request::get('time') }}",
            old_class: "{{ $class->id }}",
        };

        function onClickFollowUp(id) {
            const checkbox = document.getElementById('studentId' + id);
            if (checkbox.checked) {
                if (dataStudent.id.indexOf(id) === -1) {
                    dataStudent.id.push(id);
                    var newElement = $(`<input type="hidden" id="idSiswa${id}" name="id[]" value="${id}">`);
                    $("#idSiswaFollowUp").append(newElement);
                    if (dataStudent.id.length != 0) {
                        $('#buttonSubmitFollowUp').prop('disabled', false);
                    } else {
                        $('#buttonSubmitFollowUp').prop('disabled', true);
                    }
                }
            } else {
                const index = dataStudent.id.indexOf(id);
                $("#idSiswa" + id).remove();
                if (index !== -1) {
                    dataStudent.id.splice(index, 1);
                    if (dataStudent.id.length != 0) {
                        $('#buttonSubmitFollowUp').prop('disabled', false);
                    } else {
                        $('#buttonSubmitFollowUp').prop('disabled', true);
                    }
                }
            }
        }

        function submitFollowUp() {
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
                    $('#submitFollowUp').submit();
                }
            });
            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': "{{ csrf_token() }}"
            //     }
            // });
            // $.ajax({
            //     type: "POST",
            //     url: "{{ route('follow-up.store') }}",
            //     data: dataStudent,
            //     success: function(response) {
            //         swal({
            //             title: 'Success!',
            //             text: response,
            //             icon: 'success',
            //             confirmButtonText: 'OK'
            //         }).then(function() {
            //             window.location.href = "{{ route('e-certificate.index') }}";
            //         });
            //     }
            // });
        }
    </script>
@endsection
