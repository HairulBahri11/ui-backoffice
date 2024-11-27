@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Students Follow Up</h2>
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
                                <form action="{{route('bulk.follow-up')}}" method="POST" id="submitPassed">
                                    @method('delete')
                                    @csrf
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Student Name</th>
                                            <th>Previous Class</th>
                                            <th>Previous Schedule</th>
                                            <th>Previous Teacher</th>
                                            <th>Final Test</th>
                                            {{-- @if (Auth::guard('teacher')->check() == true)
                                            <th>Failed</th>
                                            <th>Pass</th>
                                            @endif --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $item)
                                                @php
                                                    $score1 = DB::table('student_scores')
                                                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                                        ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                                        ->where('student_id', $item->student_id)
                                                        ->where('price_id', $item->old_price_id)
                                                        ->where('test_id', 1)
                                                        ->first();
                                                    $score2 = DB::table('student_scores')
                                                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                                        ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                                        ->where('student_id', $item->student_id)
                                                        ->where('price_id', $item->old_price_id)
                                                        ->where('test_id', 2)
                                                        ->first();
                                                    $score3 = DB::table('student_scores')
                                                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                                        ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                                        ->where('student_id', $item->student_id)
                                                        ->where('price_id', $item->old_price_id)
                                                        ->where('test_id', 3)
                                                        ->first();
                                                    // modify to get class
                                                    $class_nya = $item->class->program;
                                                    $day1_nya = $item->day1;
                                                    $day2_nya = $item->day2;
                                                    $course_time_nya = $item->course_time;

                                                    // modify
                                                    // Convert full day names to their abbreviations
                                                    $day1_nya = substr($day1_nya, 0, 3); // Fri
                                                    $day2_nya = substr($day2_nya, 0, 3); // Mon

                                                    $modify_class =
                                                        $class_nya .
                                                        ' ' .
                                                        $day1_nya .
                                                        ' ' .
                                                        $day2_nya .
                                                        ' On ' .
                                                        $course_time_nya;

                                                    $order_reviews_nya = DB::table('order_reviews')
                                                        ->where('id_teacher', $item->old_teacher_id)
                                                        ->where('class', $modify_class)
                                                        ->where('test_id', '3')
                                                        ->where('type', 'test')
                                                        ->get();
                                                    $order_reviews_nya = $order_reviews_nya->toArray();
                                                @endphp
                                                <tr>
                                                    <td>{{ $item->student_id }}</td>
                                                    <td>{{ ucwords($item->student->name) }}</td>
                                                    <td>{{ $item->class->program }}</td>
                                                    <td>{{ $item->day1 . '-' . $item->day2 . '/' . $item->course_time }}
                                                    </td>
                                                    <td>{{ $item->teacher->name }}</td>
                                                    <td>
                                                        @if ($order_reviews_nya != null)
                                                            {{ Carbon\Carbon::parse($order_reviews_nya[0]->due_date)->format('d M Y') }}
                                                            <br>
                                                        @else
                                                            empty
                                                        @endif

                                                    </td>
                                                    {{-- @if (Auth::guard('teacher')->check() == true)
                                                    <td>
                                                        <input type="radio" name="is_passed[{{$item->student_id}}]" value="false" onchange="failed({{$item->student_id}})">
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="is_passed[{{$item->student_id}}]" value="true" onchange="passed({{$item->student_id}})">
                                                    </td>
                                                    @endif --}}
                                                    {{-- <td><input type="checkbox" name="student_id[]"
                                                            id="studentId{{ $item->id }}"
                                                            onclick="onClickFollowUp({{ $item->id }})"
                                                            value="{{ $item->id }}"></td> --}}

                                                    <td class=" d-flex justify-content-between align-items-center">
                                                            @if (Auth::guard('staff')->check() == true)
                                                            {{-- <button type="button" onclick="confirm({{ $item->id }})"
                                                                class="btn btn-xs btn-warning"
                                                                    data-toggle="modal"
                                                                    data-target="#exampleModal"><i class="fas fa-edit"></i></button> --}}
                                                                    <a class="btn btn-xs btn-warning" href="{{ url('/e-certificate/'.$item->student_id.'?day1=1&day2=3&teacher=4&time=15:00&price_id='. $item->old_price_id.'&type=show') }}"
                                                                        id="dropdownMenuButton">
                                                                        Edit
                                                                    </a>
                                                                @endif
                                                                @if (Auth::guard('teacher')->check() == true)

                                                                    <a class="btn btn-xs btn-warning dropdown-toggle" type="button"
                                                                        id="dropdownMenuButton" data-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                        Edit
                                                                    </a>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                        @if ($item->old_class_id >= 43 && $item->old_class_id <= 45)
                                                                            @if ($score1)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=1&id_test=' . $score1->id . '&date=' . $score1->date . '&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="dropdown-item">Test 1</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=1&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    1</a>
                                                                            @endif
                                                                        @elseif($item->old_class_id >= 22 && $item->old_class_id <= 37)
                                                                            @if ($score1)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=1&id_test=' . $score1->id . '&date=' . $score1->date . '&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="dropdown-item">Test 1</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=1&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    1</a>
                                                                            @endif
                                                                            @if ($score2)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=2&id_test=' . $score2->id . '&date=' . $score2->date . '&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="dropdown-item">Test 2</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=2&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    2</a>
                                                                            @endif
                                                                        @else
                                                                            @if ($score1)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=1&id_test=' . $score1->id . '&date=' . $score1->date . '&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="dropdown-item">Test 1</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=1&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    1</a>
                                                                            @endif
                                                                            @if ($score2)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=2&id_test=' . $score2->id . '&date=' . $score2->date . '&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="dropdown-item">Test 2</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=2&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    2</a>
                                                                            @endif
                                                                            @if ($score3)
                                                                                <a href="{{ url('score/create?type=edit&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=3&id_test=' . $score3->id . '&date=' . $score3->date . '&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="dropdown-item">Test 3</a>
                                                                            @else
                                                                                <a class="dropdown-item"
                                                                                    href="{{ url('score/create?type=create&class=') . $item->old_price_id . '&student=' . $item->student_id . '&test=3&day1=' . $item->old_day_1 . '&day2=' . $item->old_day_2 . '&teacher=' . $item->old_teacher_id . '&time=' . $item->course_time }}"
                                                                                    class="btn btn-sm btn-primary">Test
                                                                                    3</a>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                    </td>
                                                </tr>
                                                <input type="text" value="{{$item->student_id}}" name="student_id[]" id="studentIdDis{{$item->student_id}}" disabled style="display:none">
                                        @endforeach
                                    </tbody>
                                    </table>
                                    @if (Auth::guard('teacher')->check() == true)
                                    <div class="d-flex justify-content-end mt-4">
                                            <button type="button" class="btn btn-primary" style="text-align: end" onclick="submitPassed()">Save changes</button>
                                    </div>
                                    @endif
                                </form>
                            </div>
                            {{-- <div class="mt-4" style="text-align: end;">
                                <button type="button" onclick="submitBulkDelete()" id="buttonBulkDelete"
                                    class="btn btt-lg btn-danger" disabled><i class="fas fa-trash"></i> Bulk Delete</button>
                                <form action="{{ route('bulk.follow-up') }}" method="POST" class="form-inline"
                                    id="formBulkDelete">
                                    @method('delete')
                                    @csrf
                                    <div id="idSiswaFollowUp"></div>
                                </form>
                            </div> --}}
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
                                        <input class="form-check-input" type="checkbox" value="true" id="defaultCheck1" name="promoted">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Promoted to next grade
                                        </label>
                                    </div>
                                </div>
                                <div id="form_new">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email2">New Class</label>
                                            <select name="new_class" id="new_class" class="form-control">
                                                <option value="">---Choose Class</option>
                                                @foreach ($class as $classData)
                                                    <option value="{{$classData->id}}">{{$classData->program}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email2">New Teacher</label>
                                            <select name="new_teacher" id="new_teacher" class="form-control">
                                                <option value="">---Choose Teacher</option>
                                                @foreach ($teacher as $teacherData)
                                                    <option value="{{$teacherData->id}}">{{$teacherData->name}}</option>
                                                @endforeach
                                            </select>
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
                                            <select name="new_day1" id="new_day1" class="form-control">
                                                <option value="">---Choose day</option>
                                                @foreach ($day as $dayData)
                                                    <option value="{{$dayData->id}}">{{$dayData->day}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email2">New Day 2</label>
                                            <select name="new_day2" id="new_day2" class="form-control">
                                                <option value="">---Choose day</option>
                                                @foreach ($day as $dayData)
                                                    <option value="{{$dayData->id}}">{{$dayData->day}}</option>
                                                @endforeach
                                            </select>
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
            $('#studentIdDis'+id).prop('disabled', false);
        }
        function passed(id) {
            $('#studentIdDis'+id).prop('disabled', false);
        }
        $('#defaultCheck1').change(function() {
            if(this.checked) {
                $("#form_new").show();
            }else{
                $("#form_new").hide();
            }
        });
        function confirm(id) {
            $('#formDelete').attr('action', "{{url('/')}}/follow-up/" + id);
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
