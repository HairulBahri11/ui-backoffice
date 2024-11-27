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
                            <h4 class="card-title">Certificate Authorization</h4>
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
                                    {{-- {{ dd($item) }} --}}
                                    <div class="col-sm-6 col-md-4 ">
                                        <div class="card">
                                            <div class="card-body">
                                                <span style="font-size: 16px">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <i class="fa fas fa-angle-right"></i>
                                                            @php
                                                                $studentCount = DB::table('student')
                                                                    ->where('priceid', $item->priceid)
                                                                    ->where('day1', $item->day1)
                                                                    ->where('day2', $item->day2)
                                                                    ->where('id_teacher', $item->id_teacher)
                                                                    ->where('course_time', $item->course_time)
                                                                    ->get();
                                                                // dd($studentCount);

                                                                $countStudent = [];
                                                                foreach ($studentCount as $keyS => $valueS) {
                                                                    array_push($countStudent, $valueS);
                                                                    // if ($valueS->is_certificate == true) {
                                                                    //     array_push($countStudent, $valueS);
                                                                    // }
                                                                }
                                                            @endphp
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
                                                    <a href="{{ url('e-certificate/') . '/' . $item->priceid . '?day1=' . $item->d1 . '&day2=' . $item->d2 . '&teacher=' . $item->teacher_id . '&time=' . $item->course_time }}"
                                                        class="btn btn-xs btn-{{ count($studentCount) == count($countStudent) ? 'success' : 'primary' }}">View</a>
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

                    $('#getClassLast').empty();
                    $('#getClassLast').append(`
                        <option value="">---Choose Class---</option>
                `);
                    $(response).each(function(i, v) {
                        // response = JSON.parse(v.price);


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
