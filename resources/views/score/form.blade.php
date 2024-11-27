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
                    swal("Success", "{{ session('message') }}!", {
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
                            <h4 class="card-title">Student's Score</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="level" id="" class="form-control select2">
                                            <option value="">---Choose Class---</option>
                                            @foreach ($level as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ Request::get('level') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->program }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i>
                                            Filter</button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                @foreach ($class as $key => $item)
                                    @php
                                        $test = [];
                                        if ($item->priceid >= 22 && $item->priceid <= 37) {
                                            $test = App\Models\Tests::where('id', '!=', 3)->get();
                                        } elseif ($item->priceid >= 43 && $item->priceid <= 45) {
                                            $test = App\Models\Tests::where('id', 1)->get();
                                        } else {
                                            $test = App\Models\Tests::get();
                                        }

                                        // $cek_data = DB::table('attendances')
                                        //     ->where('price_id', 17)
                                        //     ->where('day1', 5)
                                        //     ->where('day2', 5)
                                        //     ->where('teacher_id', 4)
                                        //     ->where('course_time', '17:00')
                                        //     ->first();
                                        // dd($cek_data);

                                    @endphp
                                    <div class="col-sm-6 col-md-4 ">
                                        <div class="card">
                                            <div class="card-body">
                                                <span style="font-size: 16px">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <i class="fa fas fa-angle-right"></i>
                                                            @if ($item->program == 'Private' || $item->program == 'Semi Private')
                                                                @php
                                                                    $studentName = DB::table('student')
                                                                        ->where('priceid', $item->priceid)
                                                                        ->where('day1', $item->day1)
                                                                        ->where('day2', $item->day2)
                                                                        ->where('id_teacher', $item->id_teacher)
                                                                        ->where('course_time', $item->course_time)
                                                                        ->first();
                                                                @endphp
                                                                <b>{{ ucwords($studentName->name) }}</b>
                                                            @else
                                                                <b> {{ $item->program }}</b>
                                                            @endif
                                                        </div>
                                                        <div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <b>{{ $item->day_one }}
                                                        {{ $item->day_two != $item->day_one ? ' & ' . $item->day_two : '' }}</b>
                                                    <br>
                                                    <b>{{ $item->course_time }}</b>
                                                </span>

                                                <div class="d-flex justify-content-between mt-4">
                                                    <div class="fw-bold">{{ $item->teacher_name }}</div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-primary dropdown-toggle"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            View
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @foreach ($test as $itemt)
                                                                <a class="dropdown-item"
                                                                    href="{{ url('score/form-create?test=') . $itemt->id . '&class=' . $item->priceid . '&day1=' . $item->d1 . '&day2=' . $item->d2 . '&teacher=' . $item->teacher_id . '&time=' . $item->course_time }}">{{ $itemt->name }}</a>
                                                            @endforeach
                                                            {{-- <a class="dropdown-item" id="getStudent{{ $key }}"
                                                                href="{{ url('score/form-last/' . $item->priceid) . '?teacher=' . $item->teacher_id . '&day1=' . $item->d1 . '&day2=' . $item->d2 . '&time=' . $item->course_time }}">Last
                                                                Score
                                                                Student</a> --}}
                                                            <a href="javascript:void(0)" class="dropdown-item"
                                                                onclick="getStudent({{ $key }})"
                                                                id="lastScore{{ $key }}" data-toggle="modal"
                                                                data-target="#exampleModal"
                                                                data-priceid="{{ $item->priceid }}"
                                                                data-teacher="{{ $item->teacher_id }}"
                                                                data-day1="{{ $item->day1 }}"
                                                                data-day2="{{ $item->day2 }}"
                                                                data-time="{{ $item->course_time }}">
                                                                Last
                                                                Score
                                                                Student
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email2">Student</label>
                                <select name="" id="getStudentLast" class="form-control">
                                    <option value="">---Choose Student---</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email2">Class</label>
                                <select name="" id="getClassLast" class="form-control">
                                    <option value="">---Choose Class---</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-12">
                            <div class="form-group">
                                <label for="email2">Test</label>
                                <select name="" id="getTestLast" class="form-control">
                                    <option value="">---Choose Test---</option>
                                    @foreach ($test as $itemt)
                                        <option value="{{ $itemt->id }}">{{ $itemt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="createScore">Create Score Test</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getStudent(key) {
            var selector = $('#lastScore' + key)
            var priceid = $(selector).data('priceid');
            var day1 = $(selector).data('day1');
            var day2 = $(selector).data('day2');
            var teacher = $(selector).data('teacher');
            var time = $(selector).data('time');
            // Ajax student
            $.ajax({
                type: "get",
                url: "{{ url('attendance/get-student/') }}?class=" + priceid + "&day1=" + day1 + "&day2=" + day2 +
                    "&time=" + time + "&teacher=" + teacher,
                dataType: "json",
                success: function(response) {
                    $('#getStudentLast').empty();
                    $('#getStudentLast').append(`
                            <option value="">---Choose Student---</option>
                    `);
                    $(response).each(function(i, v) {
                        $('#getStudentLast').append(`
                                    <option value="${v.id}">${v.name}</option>
                            `);
                    });
                }
            });
        }

        $('#getStudentLast').change(function() {
            $.ajax({
                type: "get",
                url: "{{ url('score/ajax-last-class/') }}?id=" + $(this).val(),
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    $('#getClassLast').empty();
                    $('#getClassLast').append(`
                        <option value="">---Choose Class---</option>
                `);
                    $(response).each(function(i, v) {
                        $('#getClassLast').append(`
                                <option value="${v.price_id}">${v.program}</option>
                        `);
                    });
                }
            });
        });

        $('#createScore').click(function() {
            var student = $('#getStudentLast').val();
            var price = $('#getClassLast').val();
            var test = $('#getTestLast').val();
            if (price != '') {
                // window.location.href = "{{ url('score/create-last') }}?type=create&class=" + price + "&student=" +
                //     student +
                //     "&test=" + test;

                window.location.href = "{{ url('score/last') }}?id=" + student + "&class=" + price;
            } else {
                swal("Warning", "Choose class!", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            className: 'btn btn-success'
                        }
                    },
                });
            }
        });
    </script>
@endsection
