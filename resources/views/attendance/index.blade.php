@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                        <h5 class="text-white op-7 mb-2">
                            {{ Auth::guard('teacher')->check() == true ? Auth::guard('teacher')->user()->name : Auth::guard('staff')->user()->name }}
                        </h5>
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
            @elseif(session('error'))
                <script>
                    swal("Successful", "{{ session('error') }}!", {
                        icon: "error",
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
                            <h4 class="card-title">Regular</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="get">
                                <div class="row">
                                    @if (Auth::guard('staff')->check() == true)
                                        @php
                                            $branch = DB::table('branch')->get();
                                        @endphp
                                        <div class="col-md-3">
                                            <select name="branch" id="" class="form-control select2">
                                                <option value="">---U&I Location---</option>
                                                @foreach ($branch as $t)
                                                    <option value="{{ $t->id }}"
                                                        {{ Request::get('branch') == $t->id ? 'selected' : '' }}>
                                                        {{ $t->location }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="teacher" id="" class="form-control select2">
                                                <option value="">---Choose Teacher---</option>
                                                @foreach ($teachers as $t)
                                                    <option value="{{ $t->id }}"
                                                        {{ Request::get('teacher') == $t->id ? 'selected' : '' }}>
                                                        {{ $t->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
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
                                    @if (Auth::guard('teacher')->check() == true)
                                        <div class="col-md-3">
                                            <select name="day" id="" class="form-control select2">
                                                <option value="">---Choose Day---</option>
                                                @foreach ($day as $d)
                                                    <option value="{{ $d->id }}"
                                                        {{ Request::get('day') == $d->id ? 'selected' : '' }}>
                                                        {{ $d->day }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i>
                                            Filter</button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                @foreach ($general as $key => $item)
                                  @php

                                        $student_total = \App\Models\Students::where('priceid', $item->priceid)
                                            ->where('course_time', $item->course_time)
                                            ->where('day1', $item->day1)
                                            ->where('day2', $item->day2)
                                            ->where('status', 'ACTIVE')
                                            ->where('id_teacher', $item->id_teacher)
                                            ->count();
                                    @endphp
                                    <div class="col-sm-6 col-md-4 ">
                                        <div class="card">
                                            <div class="card-body">
                                                <span style="font-size: 16px">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <i class="fa fas fa-angle-right"></i>
                                                            <b>
                                                                {{ $item->program }}</b>
                                                            @if ($item->is_class_new == true)
                                                                @php
                                                                    $already_absent = DB::table('attendances')
                                                                        ->where([
                                                                            ['price_id', $item->priceid],
                                                                            ['day1', $item->day1],
                                                                            ['day2', $item->day2],
                                                                            ['course_time', $item->course_time],
                                                                            ['teacher_id', $item->id_teacher],
                                                                            ['is_presence', 1],
                                                                        ])
                                                                        ->first();

                                                                    if ($already_absent != null) {
                                                                        $new_label = 'hidden';
                                                                    } else {
                                                                        $new_label = '';
                                                                    }
                                                                @endphp
                                                                <span style="color: red" {{ $new_label }}>(New!)</span>
                                                            @endif

                                                        </div>
                                                        @if (Auth::guard('staff')->check() == true)
                                                            <div>
                                                                <form action="{{ url('schedule-class/delete') }}"
                                                                    method="POST" class="form-inline">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <input type="hidden" name="priceid"
                                                                        value="{{ $item->priceid }}">
                                                                    <input type="hidden" name="day1"
                                                                        value="{{ $item->day1 }}">
                                                                    <input type="hidden" name="day2"
                                                                        value="{{ $item->day2 }}">
                                                                    <input type="hidden" name="course_time"
                                                                        value="{{ $item->course_time }}">
                                                                    <input type="hidden" name="id_teacher"
                                                                        value="{{ $item->id_teacher }}">
                                                                    <button type="submit"
                                                                        onclick="return confirm('apakah anda yakin ingin menghapus data ??')"
                                                                        class="btn btn-xs btn-danger"
                                                                        style="margin-right: 10px !important;"><i
                                                                            class="fas fa-trash"></i></button>
                                                                    <a href="javascript:void(0)"
                                                                        class="btn btn-xs btn-success" data-toggle="modal"
                                                                        data-target="#editJadwalModal"
                                                                        onclick="updateModalReg({{ $key }})"><i
                                                                            class="fas fa-pencil-alt"></i></a>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <br>
                                                    <b style="font-size: 11px">({{ $item->location }})</b> <br>
                                                    <b>{{ $item->day_one }}
                                                        {{ $item->day1 != $item->day2 ? '&' : '' }}
                                                        {{ $item->day1 != $item->day2 ? $item->day_two : '' }}</b>
                                                    <br>
                                                   
                                                    <b>{{ $item->course_time }}</b> <br>
                                                    <i>{{ $student_total . ' Students' }}</i>

                                                    <input type="hidden" id="regprogramModal{{ $key }}"
                                                        value="{{ $item->program }}">
                                                    <input type="hidden" id="regcourseTimeModal{{ $key }}"
                                                        value="{{ $item->course_time }}">
                                                    <input type="hidden" id="regday1Modal{{ $key }}"
                                                        value="{{ $item->day1 }}">
                                                    <input type="hidden" id="regday2Modal{{ $key }}"
                                                        value="{{ $item->day2 }}">
                                                    <input type="hidden" id="regteacherModal{{ $key }}"
                                                        value="{{ $item->teacher_name }}">
                                                    <input type="hidden" id="regteacherOldModal{{ $key }}"
                                                        value="{{ $item->id_teacher }}">
                                                    <input type="hidden" id="regclassModal{{ $key }}"
                                                        value="{{ $item->priceid }}">
                                                    <input type="hidden" id="regdayOneModal{{ $key }}"
                                                        value="{{ $item->day_one }}">
                                                    <input type="hidden" id="regdayTwoModal{{ $key }}"
                                                        value="{{ $item->day_two }}">
                                                    <input type="hidden" id="regidteacherModal{{ $key }}"
                                                        value="{{ $item->id_teacher }}">
                                                </span>

                                                <div class="d-flex justify-content-between mt-4">
                                                    <div class="fw-bold">{{ $item->teacher_name }}</div>
                                                    
                                                    <div class="d-flex justify-content-between">
                                                    <a href="{{ url('attendance/form/' . $item->priceid . '?day1=' . $item->day1 . '&day2=' . $item->day2 . '&time=' . $item->course_time) . '&teacher=' . $item->id_teacher . '&new=' . $item->is_class_new }}"
                                                        class="btn btn-xs btn-primary">View</a>
                                                        @if (Auth::guard('staff')->check() == true)
                                                            <!-- Button trigger modal -->
                                                            <button type="button" class="btn btn-warning btn-xs"
                                                                data-toggle="modal" data-target="#exampleModal"
                                                                data-priceid="{{ $item->program }}"
                                                                data-idteacher="{{ $item->teacher_name }}"
                                                                data-day1="{{ $item->day_one }}"
                                                                data-day2="{{ $item->day_two }}"
                                                                data-time="{{ $item->course_time }}"
                                                                data-day1-fix="{{ $item->day1 }}"
                                                                data-day2-fix="{{ $item->day2 }}"
                                                                data-idteacher-fix="{{ $item->id_teacher }}"
                                                                data-priceid-fix="{{ $item->priceid }}">



                                                                Transfer
                                                            </button>
                                                        @endif
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
                

                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Semi Private</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($semiPrivate as $keySemiPrivate => $itemSemiPrivate)
                                 @php

                                        $student_total_semi_private = \App\Models\Students::where(
                                            'priceid',
                                            $itemSemiPrivate->priceid,
                                        )
                                            ->where('course_time', $itemSemiPrivate->course_time)
                                            ->where('day1', $itemSemiPrivate->day1)
                                            ->where('day2', $itemSemiPrivate->day2)
                                            ->where('status', 'ACTIVE')
                                            ->where('id_teacher', $itemSemiPrivate->id_teacher)
                                            ->count();
                                    @endphp
                                    <div class="col-sm-6 col-md-4 ">
                                        <div class="card">
                                            <div class="card-body">
                                                <span style="font-size: 16px">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <i class="fa fas fa-angle-right"></i>
                                                            <b>
                                                                {{ $itemSemiPrivate->program }}</b>
                                                            @if ($itemSemiPrivate->is_class_new == true)
                                                                @php
                                                                    $already_absent = DB::table('attendances')
                                                                        ->where([
                                                                            ['price_id', $item->priceid],
                                                                            ['day1', $item->day1],
                                                                            ['day2', $item->day2],
                                                                            ['course_time', $item->course_time],
                                                                            ['teacher_id', $item->id_teacher],
                                                                            ['is_presence', 1],
                                                                        ])
                                                                        ->first();

                                                                    if ($already_absent != null) {
                                                                        $new_label = 'hidden';
                                                                    } else {
                                                                        $new_label = '';
                                                                    }
                                                                @endphp
                                                                <span style="color: red" {{ $new_label }}>(New!)</span>
                                                            @endif
                                                        </div>
                                                        @if (Auth::guard('staff')->check() == true)
                                                            <div>
                                                                <form action="{{ url('schedule-class/delete') }}"
                                                                    method="POST" class="form-inline">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <input type="hidden" name="priceid"
                                                                        value="{{ $itemSemiPrivate->priceid }}">
                                                                    <input type="hidden" name="day1"
                                                                        value="{{ $itemSemiPrivate->day1 }}">
                                                                    <input type="hidden" name="day2"
                                                                        value="{{ $itemSemiPrivate->day2 }}">
                                                                    <input type="hidden" name="course_time"
                                                                        value="{{ $itemSemiPrivate->course_time }}">
                                                                    <input type="hidden" name="id_teacher"
                                                                        value="{{ $itemSemiPrivate->id_teacher }}">
                                                                    <button type="submit"
                                                                        onclick="return confirm('apakah anda yakin ingin menghapus data ??')"
                                                                        class="btn btn-xs btn-danger"
                                                                        style="margin-right: 10px !important;"><i
                                                                            class="fas fa-trash"></i></button>
                                                                    <a href="javascript:void(0)"
                                                                        class="btn btn-xs btn-success" data-toggle="modal"
                                                                        data-target="#editJadwalModal"
                                                                        onclick="updateModalReg({{ $keySemiPrivate }})"><i
                                                                            class="fas fa-pencil-alt"></i></a>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <br>
                                                    <b>{{ $itemSemiPrivate->day_one }}
                                                        {{ $itemSemiPrivate->day1 != $itemSemiPrivate->day2 ? '&' : '' }}
                                                        {{ $itemSemiPrivate->day1 != $itemSemiPrivate->day2 ? $itemSemiPrivate->day_two : '' }}</b>
                                                    <br>
                                                    <b>{{ $itemSemiPrivate->course_time }}</b> <br>
                                                    <i>{{ $student_total_semi_private . ' Students' }}</i>

                                                    <input type="hidden" id="regprogramModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->program }}">
                                                    <input type="hidden" id="regcourseTimeModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->course_time }}">
                                                    <input type="hidden" id="regday1Modal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->day1 }}">
                                                    <input type="hidden" id="regday2Modal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->day2 }}">
                                                    <input type="hidden" id="regteacherModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->teacher_name }}">
                                                    <input type="hidden" id="regteacherOldModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->id_teacher }}">
                                                    <input type="hidden" id="regclassModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->priceid }}">
                                                    <input type="hidden" id="regdayOneModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->day_one }}">
                                                    <input type="hidden" id="regdayTwoModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->day_two }}">
                                                    <input type="hidden" id="regidteacherModal{{ $keySemiPrivate }}"
                                                        value="{{ $itemSemiPrivate->id_teacher }}">
                                                </span>

                                                <div class="d-flex justify-content-between mt-4">
                                                    <div class="fw-bold">{{ $itemSemiPrivate->teacher_name }}</div>
                                                    <div
                                                        class="box d-flex justify-content-between align-items-center gap-2">
                                                        <a href="{{ url('attendance/form/' . $itemSemiPrivate->priceid . '?day1=' . $itemSemiPrivate->day1 . '&day2=' . $itemSemiPrivate->day2 . '&time=' . $itemSemiPrivate->course_time) . '&teacher=' . $itemSemiPrivate->id_teacher . '&new=' . $itemSemiPrivate->is_class_new }}"
                                                            class="btn btn-xs btn-primary">View</a>
                                                        @if (Auth::guard('staff')->check() == true)
                                                            <!-- Button trigger modal -->
                                                            <button type="button" class="btn btn-warning btn-xs"
                                                                data-toggle="modal" data-target="#exampleModal"
                                                                data-priceid="{{ $itemSemiPrivate->program }}"
                                                                data-idteacher="{{ $itemSemiPrivate->teacher_name }}"
                                                                data-day1="{{ $itemSemiPrivate->day_one }}"
                                                                data-day2="{{ $itemSemiPrivate->day_two }}"
                                                                data-time="{{ $itemSemiPrivate->course_time }}"
                                                                data-day1-fix="{{ $itemSemiPrivate->day1 }}"
                                                                data-day2-fix="{{ $itemSemiPrivate->day2 }}"
                                                                data-idteacher-fix="{{ $itemSemiPrivate->id_teacher }}"
                                                                data-priceid-fix="{{ $itemSemiPrivate->priceid }}">



                                                                Transfer
                                                            </button>
                                                        @endif
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Private</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($private as $key => $item)
                                    @php
                                        $studentName = DB::table('student')
                                            ->where('priceid', $item->priceid)
                                            ->where('day1', $item->day1)
                                            ->where('day2', $item->day2)
                                            ->where('id_teacher', $item->id_teacher)
                                            ->where('course_time', $item->course_time)
                                            ->groupBy('id')
                                            ->get();
                                    @endphp
                                    @foreach ($studentName as $keyStudentName => $itemStudentName)
                                        <div class="col-sm-6 col-md-4 ">
                                            <div class="card">
                                                <div class="card-body">
                                                    <span style="font-size: 16px">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <b> {{ ucwords($itemStudentName->name) }}</b>
                                                                <br>
                                                            </div>
                                                            @if (Auth::guard('staff')->check() == true)
                                                                <div>
                                                                    <form action="{{ url('schedule-class/delete') }}"
                                                                        method="POST" class="form-inline">
                                                                        @method('delete')
                                                                        @csrf
                                                                        <input type="hidden" name="priceid"
                                                                            value="{{ $item->priceid }}">
                                                                        <input type="hidden" name="day1"
                                                                            value="{{ $item->day1 }}">
                                                                        <input type="hidden" name="day2"
                                                                            value="{{ $item->day2 }}">
                                                                        <input type="hidden" name="course_time"
                                                                            value="{{ $item->course_time }}">
                                                                        <input type="hidden" name="id_teacher"
                                                                            value="{{ $item->id_teacher }}">
                                                                        <button type="submit"
                                                                            onclick="return confirm('apakah anda yakin ingin menghapus data ??')"
                                                                            class="btn btn-xs btn-danger"
                                                                            style="margin-right: 10px !important;"><i
                                                                                class="fas fa-trash"></i></button>
                                                                        <a href="javascript:void(0)"
                                                                            class="btn btn-xs btn-success"
                                                                            data-toggle="modal"
                                                                            data-target="#editJadwalModal"
                                                                            onclick="updateModalPrv({{ $key }})"><i
                                                                                class="fas fa-pencil-alt"></i></a>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div> <i class="fa fas fa-angle-right"></i>
                                                        <b>{{ $item->day_one }}
                                                            {{ $item->day1 != $item->day2 ? '&' : '' }}
                                                            {{ $item->day1 != $item->day2 ? $item->day_two : '' }}</b>
                                                        <br>
                                                        <b>{{ $item->course_time }}</b>

                                                        <input type="hidden" id="prvprogramModal{{ $key }}"
                                                            value="{{ $item->program }}">
                                                        <input type="hidden" id="prvcourseTimeModal{{ $key }}"
                                                            value="{{ $item->course_time }}">
                                                        <input type="hidden" id="prvday1Modal{{ $key }}"
                                                            value="{{ $item->day1 }}">
                                                        <input type="hidden" id="prvday2Modal{{ $key }}"
                                                            value="{{ $item->day2 }}">
                                                        <input type="hidden" id="prvteacherModal{{ $key }}"
                                                            value="{{ $item->teacher_name }}">
                                                        <input type="hidden" id="prvclassModal{{ $key }}"
                                                            value="{{ $item->priceid }}">
                                                        <input type="hidden" id="prvdayOneModal{{ $key }}"
                                                            value="{{ $item->day_one }}">
                                                        <input type="hidden" id="prvdayTwoModal{{ $key }}"
                                                            value="{{ $item->day_two }}">
                                                        <input type="hidden" id="prvidteacherModal{{ $key }}"
                                                            value="{{ $item->id_teacher }}">
                                                    </span>

                                                    <div class="d-flex justify-content-between mt-4">
                                                        <div class="fw-bold">{{ $item->teacher_name }}</div>
                                                        
                                                        <div
                                                            class="box d-flex justify-content-between align-items-center gap-2">

                                                            <a href="{{ url('attendance/form/' . $item->priceid . '?day1=' . $item->day1 . '&day2=' . $item->day2 . '&time=' . $item->course_time . '&teacher=' . $item->id_teacher . '&new=' . $item->is_class_new . '&student=' . $itemStudentName->id) }}"
                                                                class="btn btn-xs btn-primary">View</a>
                                                                 @if (Auth::guard('staff')->check() == true)
                                                            <button type="button" class="btn btn-warning btn-xs"
                                                                data-toggle="modal" data-target="#exampleModal"
                                                                data-priceid="{{ $item->program }}"
                                                                data-idteacher="{{ $item->teacher_name }}"
                                                                data-day1="{{ $item->day_one }}"
                                                                data-day2="{{ $item->day_two }}"
                                                                data-time="{{ $item->course_time }}"
                                                                data-day1-fix="{{ $item->day1 }}"
                                                                data-day2-fix="{{ $item->day2 }}"
                                                                data-idteacher-fix="{{ $item->id_teacher }}"
                                                                data-priceid-fix="{{ $item->priceid }}"
                                                                 data-studentname="{{ $itemStudentName->name }}"
                                                                    data-studentid-fix = "{{ $itemStudentName->id }}">



                                                                Transfer
                                                            </button>
                                                               @endif

                                                        </div>
                                                                                                                 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                             {{-- modal mutasi class --}}
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Transfer Class</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{-- <p class="fw-bold">Transfer is a feature that allows you to update the entire data of the
                                    selected and ongoing class, in order to replace the current teacher with a new one</p> --}}
                                <div class="card shadow-sm">
                                    <!-- Card Header (optional) -->
                                    <div class="card-header bg-info text-white">
                                        <h5 class="card-title mb-0 text-white">Class Information -  <span
                                                id="modalStudent_name"></span></h5>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Day Schedule -->
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-calendar-day"></i> Day:</h6>
                                                <p class="text-secondary">
                                                    <b><span id="modalDay1"></span>
                                                        <span id="modalDay2"></span></b>
                                                </p>
                                            </div>

                                            <!-- Course Time -->
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-clock"></i> Schedule:</h6>
                                                <p class="text-secondary">
                                                    <b><span id="modalTime"></span></b>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Additional Info -->
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <h6>Class: <span id="modalPriceId" class="text-primary fw-bold"></span>
                                                </h6>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Teacher: <span id="modalIdTeacher"
                                                        class="text-primary fw-bold"></span></h6>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <form action="{{ route('mutasi-class') }}" method="POST" id="formMutasi">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-12 mt-3 mt-3">
                                            <label for="id_teacher" class="form-label fw-bold">
                                                <h6>Transfer Class to</h6>
                                            </label>


                                            @php
                                                $teachers = \App\Models\Teacher::all();
                                            @endphp

                                            @php
                                                $day1 = DB::table('day')->get();
                                                $day2 = DB::table('day')->get();
                                                $staff = DB::table('staff')->get();
                                            @endphp
                                            <div class="boxnya d-flex justify-content-center">
                                                <select name="transfer_teacher" id="transfer_teacher"
                                                    class="form-control">
                                                    <option value="">-Choose Teacher-</option>
                                                    @foreach ($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select name="transfer_staff" id="transfer_staff" class="form-control">
                                                    <option value="">-Choose Staff-</option>
                                                    @foreach ($staff as $st)
                                                        <option value="{{ $st->id }}">{{ $st->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <div class="boxnya d-flex justify-content-between mt-3">
                                                <div class="mb-3">
                                                    {{-- <label for="day1" class="form-label"> Day 1</label> --}}
                                                    <select name="day1" id="day1" class="form-control">
                                                        <option value="">-Choose Day1</option>
                                                        @foreach ($day1 as $d1)
                                                            <option value="{{ $d1->id }}">{{ $d1->day }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    {{-- <label for="day1" class="form-label"> Day 2</label> --}}
                                                    <select name="day2" id="day2" class="form-control">
                                                        <option value="">-Choose Day2</option>
                                                        @foreach ($day2 as $d2)
                                                            <option value="{{ $d2->id }}">{{ $d2->day }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <input type="time" name="course_time" id="course_time"
                                                        class="form-control">
                                                </div>



                                            </div>





                                        </div>

                                    </div>

                            </div>

                            {{-- <input type="text" name="id_attendance" id="id_attendance"> --}}
                            <input type="hidden" name="id_teacher" id="id_teacher_fix">
                            {{-- <input type="hidden" name="mutasi_teacher" id="mutasi_teacher_fix"> --}}
                            <input type="hidden" name="day1_old" id="day1_old">
                            <input type="hidden" name="day2_old" id="day2_old">
                            <input type="hidden" name="course_time_old" id="course_time_old">



                            <input type="hidden" name="priceid" id="priceid-fix">
                            <input type="hidden" name="studentid" id="studentid-fix">


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Are you sure you want to transfer this class ??')">Save
                                    changes</button>
                            </div>
                            </form>
                        </div>
                </div>
                {{-- end modal mutasi class --}}
            </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editJadwalModalLabel">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="POST" id="updateClassModal">
                            @csrf
                            <input type="hidden" id="update_day1" name="update_day1">
                            <input type="hidden" id="update_day2" name="update_day2">
                            <input type="hidden" id="update_class" name="update_class">
                            <input type="hidden" id="update_time" name="update_time">
                            <input type="hidden" id="old_teacher" name="old_teacher">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="">Course Time</label>
                                        <input type="time" class="form-control" name="update_course_time">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Day 1</label>
                                        <select name="update_day_one" id="update_day_one"
                                            class="form-control select2 select2-hidden-accessible" style="width:100%;">
                                            <option value="">---Choose Day 1---</option>
                                            @foreach ($day as $item1)
                                                <option value="{{ $item1->id }}">{{ $item1->day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Day 2</label>
                                        <select name="update_day_two" id=""
                                            class="form-control select2 select2-hidden-accessible" style="width:100%;">
                                            <option value="">---Choose Day 2---</option>
                                            @foreach ($day as $item2)
                                                <option value="{{ $item2->id }}">{{ $item2->day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <select name="update_level" id="update_level"
                                            class="form-control select2 select2-hidden-accessible" style="width:100%;">
                                            <option value="">---Choose Class---</option>
                                            @foreach ($level as $item4)
                                                <option value="{{ $item4->id }}">{{ $item4->program }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Teacher</label>
                                        <select name="update_teacher" id=""
                                            class="form-control select2 select2-hidden-accessible" style="width:100%;">
                                            <option value="">---Choose Teacher---</option>
                                            @foreach ($teachers as $item3)
                                                <option value="{{ $item3->id }}">{{ $item3->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-group">
                                            <label for="">Type</label>
                                            <select name="type" id=""
                                                class="form-control select2 select2-hidden-accessible" style="width:100%;"
                                                required>
                                                <option value="">---Choose Type---</option>
                                                <option value="edit">Edit</option>
                                                <option value="promoted">Promoted</option>
                                            </select>
                                        </div>
                                        <div class="rowStudent">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        function updateModalReg(id) {
            var program = $('#regprogramModal' + id).val();
            var dayOne = $('#regdayOneModal' + id).val();
            var dayTwo = $('#regdayTwoModal' + id).val();
            var courseTime = $('#regcourseTimeModal' + id).val();
            var teacher = $('#regteacherModal' + id).val();
            var teacherOld = $('#regteacherOldModal' + id).val();
            var day1 = $('#regday1Modal' + id).val();
            var day2 = $('#regday2Modal' + id).val();
            var course = $('#regclassModal' + id).val();
            var idTeacher = $('#regidteacherModal' + id).val();
            var day = day1 != day2 ? dayOne + ' & ' + dayTwo : dayOne
            $('#editJadwalModalLabel').html('Update Class ' + program + ' (' + day + ' ' + courseTime +
                ') ' +
                ' ' + teacher);
            $('#updateClassModal').attr('action', '{{ url('attendance/update-class') }}');
            $('#update_day1').val(day1);
            $('#update_day2').val(day2);
            $('#update_class').val(course);
            $('#update_time').val(courseTime);
            $('#old_teacher').val(teacherOld);
            $('input[name="update_course_time"]').val(courseTime);
            $("#update_day_one").select2("val", day1);
            $('select[name="update_day_two"]').select2("val", day2);
            $('#update_level').select2("val", course);
            $('select[name="update_teacher"]').select2("val", idTeacher);
            // $('.rowStudent').empty();
            // $.ajax({
            //     type: "get",
            //     url: "{{ url('attendance/get-student/') }}?class=" + course + "&day1=" + day1 + "&day2=" + day2 +
            //         "&time=" + courseTime + "&teacher=" + idTeacher,
            //     dataType: "json",
            //     success: function(response) {
            //         $(response).each(function(i, v) {
            //             $('.rowStudent').append(`
        //                             <div class="form-group">
        //                                 <label for="">${v.name}</label>
        //                                 <input type="checkbox" name="studentId[]" value="${v.id}">
        //                             </div>
        //                         `);
            //         });
            //     }
            // });
        }

        function updateModalPrv(id) {
            var program = $('#prvprogramModal' + id).val();
            var dayOne = $('#prvdayOneModal' + id).val();
            var dayTwo = $('#prvdayTwoModal' + id).val();
            var courseTime = $('#prvcourseTimeModal' + id).val();
            var teacher = $('#prvteacherModal' + id).val();
            var day1 = $('#prvday1Modal' + id).val();
            var day2 = $('#prvday2Modal' + id).val();
            var course = $('#prvclassModal' + id).val();
            var idTeacher = $('#prvidteacherModal' + id).val();
            var teacherOld = $('#regteacherOldModal' + id).val();
            var day = day1 != day2 ? dayOne + ' & ' + dayTwo : dayOne
            $('#editJadwalModalLabel').html('Update Class ' + program + ' (' + day + ' ' + courseTime +
                ') ' +
                ' ' + teacher);
            $('#updateClassModal').attr('action', '{{ url('attendance/update-class') }}');
            $('#update_day1').val(day1);
            $('#update_day2').val(day2);
            $('#update_class').val(course);
            $('#update_time').val(courseTime);
            $('#old_teacher').val(teacherOld);
            $('input[name="update_course_time"]').val(courseTime);
            $("#update_day_one").select2("val", day1);
            $('select[name="update_day_two"]').select2("val", day2);
            $('select[name="update_level"]').select2("val", 32);
            $('select[name="update_teacher"]').select2("val", idTeacher);
        }
    </script>
   
    
    
    <script>
        $('#exampleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal

            // Extract info from data-* attributes
            var priceid = button.data('priceid');
            var idteacher = button.data('idteacher');
            var day1 = button.data('day1');
            var day2 = button.data('day2');
            var time = button.data('time');
            var id_teacher_fix = button.data('idteacher-fix');
            var priceid_fix = button.data('priceid-fix');
            var day1_fix = button.data('day1-fix');
            var day2_fix = button.data('day2-fix');
            var studentId_fix = button.data('studentid-fix');
            var student_name = button.data('studentname');

            // console.log(studentId_fix);




            // Update the modal's content with the data
            var modal = $(this);
            modal.find('#modalPriceId').text(priceid);
            modal.find('#modalIdTeacher').text(idteacher);
            modal.find('#modalDay1').text(day1);
            modal.find('#modalDay2').text(day1 !== day2 ? ' & ' + day2 : '');
            modal.find('#modalTime').text(time);
            modal.find('#modalStudent_name').text(student_name);

            // send data to hidden input
            $('#priceid-fix').val(priceid_fix);
            $('#id_teacher_fix').val(id_teacher_fix);
            $('#day1_old').val(day1_fix);
            $('#day2_old').val(day2_fix);
            $('#course_time_old').val(time);
            $('#day1').val(day1_fix);
            $('#day2').val(day2_fix);
            $('#course_time').val(time);
            $('#studentid-fix').val(studentId_fix);

            // {{-- <input type="text" name="id_attendance" id="id_attendance"> --}}
            //                     <input type="text" name="id_teacher" id="id_teacher">
            //                     <input type="text" name="mutasi_teacher" id="mutasi_teacher">
            //                     <input type="text" name="day1" id="day1">
            //                     <input type="text" name="day2" id="day2">
            //                     <input type="text" name="course_time" id="course_time">
            //                     <input type="text" name="priceid" id="priceid-fix">

        });
    </script>
@endsection
