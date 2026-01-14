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

                                             // 2. Ambil data attendance terakhir secara aman
        $star = DB::table('attendances')
            ->leftJoin('teacher as t2', 'attendances.assist_id', '=', 't2.id')
            ->where('attendances.price_id', $item->priceid)
            ->where('attendances.day1', $item->day1)
            ->where('attendances.day2', $item->day2)
            ->where('attendances.course_time', $item->course_time)
            ->where('attendances.teacher_id', $item->id_teacher)
            ->select('attendances.star', 't2.name as assist_name', 'attendances.is_presence')
            ->orderBy('attendances.date', 'desc') // Ambil yang paling baru
            ->first();

        $assistName = $star ? $star->assist_name : null;
        $classStar = $star ? $star->star : null; // Simpan star di variabel agar aman  
                                        
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
                                                    <b style="font-size: 12px; color:rgb(127, 127, 255)">({{ $item->location }})</b> <br>
                                                    <b>{{ $item->day_one }}
                                                        {{ $item->day1 != $item->day2 ? '&' : '' }}
                                                        {{ $item->day1 != $item->day2 ? $item->day_two : '' }}</b>
                                                    <br>
                                                   
                                                    <b>{{ $item->course_time }}</b> <span>
                                                                   @if ($classStar)
                        @if ($classStar == 1)
                            (<i class="fas fa-star"></i>)
                        @elseif ($classStar == 2)
                            (<i class="fas fa-star"></i><i class="fas fa-star"></i>)
                        @else
                            (Star {{ $classStar }})
                        @endif
                    @endif
                                                                </span> <br>
                                                    <i>{{ $student_total . ' Students' }}</i>
                                                    @if(!empty($assistName))
                                                        <p style="font-size: 11px; color:rgb(127, 127, 255)">Assist:{{ $assistName }}</p>
                                                    @endif
                                                   

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

                                                            <div class="dropdown">
                                                    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button"
                                                        id="dropdownMenuButton_{{ $key }}" data-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton_{{ $key }}">
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            data-toggle="modal" data-target="#editStarModal"
                                                            data-priceid="{{ $item->program }}"
                                                            data-idteacher="{{ $item->teacher_name }}"
                                                            data-day1="{{ $item->day_one }}"
                                                            data-day2="{{ $item->day_two }}"
                                                            data-time="{{ $item->course_time }}"
                                                            data-day1-fix="{{ $item->day1 }}"
                                                            data-day2-fix="{{ $item->day2 }}"
                                                            data-idteacher-fix="{{ $item->id_teacher }}"
                                                            data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-star"></i> Star</a>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#editAssistModal"
                                                            data-priceid="{{ $item->program }}"
                                                            data-idteacher="{{ $item->teacher_name }}"
                                                            data-day1="{{ $item->day_one }}"
                                                            data-day2="{{ $item->day_two }}"
                                                            data-time="{{ $item->course_time }}"
                                                            data-day1-fix="{{ $item->day1 }}"
                                                            data-day2-fix="{{ $item->day2 }}"
                                                            data-idteacher-fix="{{ $item->id_teacher }}"
                                                            data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-handshake"></i> Assist Class</a>
                                                        <!-- remove assist class -->

                                                        @if($assistName)
                                                         <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#removeAssistModal"
                                                            data-priceid="{{ $item->program }}"
                                                            data-idteacher="{{ $item->teacher_name }}"
                                                            data-day1="{{ $item->day_one }}"
                                                            data-day2="{{ $item->day_two }}"
                                                            data-time="{{ $item->course_time }}"
                                                            data-day1-fix="{{ $item->day1 }}"
                                                            data-day2-fix="{{ $item->day2 }}"
                                                            data-idteacher-fix="{{ $item->id_teacher }}"
                                                            data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-user-times"></i> Remove Assist</a>
                                                        @endif

                                                        <!-- jika ada star -->
                                                         @if($star && $star->star != null)
                                                          <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#removeStarModal"
                                                            data-priceid="{{ $item->program }}"
                                                            data-idteacher="{{ $item->teacher_name }}"
                                                            data-day1="{{ $item->day_one }}"
                                                            data-day2="{{ $item->day_two }}"
                                                            data-time="{{ $item->course_time }}"
                                                            data-day1-fix="{{ $item->day1 }}"
                                                            data-day2-fix="{{ $item->day2 }}"
                                                            data-idteacher-fix="{{ $item->id_teacher }}"
                                                            data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-trash-alt"></i> Remove Star</a>
                                                        @endif


                                                    </div>
                                                </div>
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

                                        
    $star = DB::table('attendances')
        ->leftJoin('teacher as t2', 'attendances.assist_id', '=', 't2.id')
        ->where('attendances.price_id', $itemSemiPrivate->priceid)
        ->where('attendances.day1', $itemSemiPrivate->day1)
        ->where('attendances.day2', $itemSemiPrivate->day2)
        ->where('attendances.course_time', $itemSemiPrivate->course_time)
        ->where('attendances.teacher_id', $itemSemiPrivate->id_teacher)
        ->select('attendances.*', 't2.name as assist_name')
        ->orderBy('attendances.date', 'desc') // Tambahkan ini agar mendapat star terbaru
        ->first();

    // Gunakan null coalescing atau ternary untuk mencegah error
    $assistName = $star ? $star->assist_name : null;
    $classStar = $star ? $star->star : null; 

                                        
                                        
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
                                                    <b style="font-size: 12px; color:rgb(127, 127, 255)">({{ $itemSemiPrivate->location }})</b> <br>
                                                    <b>{{ $itemSemiPrivate->day_one }}
                                                        {{ $itemSemiPrivate->day1 != $itemSemiPrivate->day2 ? '&' : '' }}
                                                        {{ $itemSemiPrivate->day1 != $itemSemiPrivate->day2 ? $itemSemiPrivate->day_two : '' }}</b>
                                                    <br>
                                                    <b>{{ $itemSemiPrivate->course_time }}</b>
                                                        <span>
                                                                   @if ($classStar != null)
                        @if ($classStar == 1)
                            (<i class="fas fa-star"></i>)
                        @elseif ($classStar == 2)
                            (<i class="fas fa-star"></i><i class="fas fa-star"></i>)
                        @else
                            (Star {{ $classStar }})
                        @endif
                    @endif 
                                                        </span>
                                                     <br>
                                                    <i>{{ $student_total_semi_private . ' Students' }}</i>
                                                    @if(!empty($assistName))
                                                        <p style="font-size: 11px; color:rgb(127, 127, 255)">Assist:{{ $assistName }}</p>
                                                    @endif

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
                                                            <div class="dropdown">
                                                    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button"
                                                        id="dropdownMenuButton_{{ $keySemiPrivate }}" data-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton_{{ $keySemiPrivate }}">
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            data-toggle="modal" data-target="#editStarModal"
                                                            data-priceid="{{ $itemSemiPrivate->program }}"
                                                            data-idteacher="{{ $itemSemiPrivate->teacher_name }}"
                                                            data-day1="{{ $itemSemiPrivate->day_one }}"
                                                            data-day2="{{ $itemSemiPrivate->day_two }}"
                                                            data-time="{{ $itemSemiPrivate->course_time }}"
                                                            data-day1-fix="{{ $itemSemiPrivate->day1 }}"
                                                            data-day2-fix="{{ $itemSemiPrivate->day2 }}"
                                                            data-idteacher-fix="{{ $itemSemiPrivate->id_teacher }}"
                                                            data-priceid-fix="{{ $itemSemiPrivate->priceid }}"><i class="fas fa-star"></i> Star</a>
                                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#editAssistModal"
                                                            data-priceid="{{ $itemSemiPrivate->program }}"
                                                            data-idteacher="{{ $itemSemiPrivate->teacher_name }}"
                                                            data-day1="{{ $itemSemiPrivate->day_one }}"
                                                            data-day2="{{ $itemSemiPrivate->day_two }}"
                                                            data-time="{{ $itemSemiPrivate->course_time }}"
                                                            data-day1-fix="{{ $itemSemiPrivate->day1 }}"
                                                            data-day2-fix="{{ $itemSemiPrivate->day2 }}"
                                                            data-idteacher-fix="{{ $itemSemiPrivate->id_teacher }}"
                                                            data-priceid-fix="{{ $itemSemiPrivate->priceid }}"><i class="fas fa-handshake"></i> Assist Class</a>
                                                        @if($assistName)
                                                         <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#removeAssistModal"
                                                            data-priceid="{{ $itemSemiPrivate->program }}"
                                                            data-idteacher="{{ $itemSemiPrivate->teacher_name }}"
                                                            data-day1="{{ $itemSemiPrivate->day_one }}"
                                                            data-day2="{{ $itemSemiPrivate->day_two }}"
                                                            data-time="{{ $itemSemiPrivate->course_time }}"
                                                            data-day1-fix="{{ $itemSemiPrivate->day1 }}"
                                                            data-day2-fix="{{ $itemSemiPrivate->day2 }}"
                                                            data-idteacher-fix="{{ $itemSemiPrivate->id_teacher }}"
                                                            data-priceid-fix="{{ $itemSemiPrivate->priceid }}"><i class="fas fa-user-times"></i> Remove Assist</a>
                                                        @endif
                                                        <!-- jika ada star -->
                                                         @if($star && $star->star != null)
                                                          <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#removeStarModal"
                                                            data-priceid="{{ $itemSemiPrivate->program }}"
                                                            data-idteacher="{{ $itemSemiPrivate->teacher_name }}"
                                                            data-day1="{{ $itemSemiPrivate->day_one }}"
                                                            data-day2="{{ $itemSemiPrivate->day_two }}"
                                                            data-time="{{ $itemSemiPrivate->course_time }}"
                                                            data-day1-fix="{{ $itemSemiPrivate->day1 }}"
                                                            data-day2-fix="{{ $itemSemiPrivate->day2 }}"
                                                            data-idteacher-fix="{{ $itemSemiPrivate->id_teacher }}"
                                                            data-priceid-fix="{{ $itemSemiPrivate->priceid }}"><i class="fas fa-trash-alt"></i> Remove Star</a>
                                                        @endif
                                                    </div>
                                                </div>
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
                                                        <b style="font-size: 11px; color:rgb(127, 127, 255)">({{ $item->location }})</b> <br>
                                                        
                                                        <b>{{ $item->day_one }}
                                                            {{ $item->day1 != $item->day2 ? '&' : '' }}
                                                            {{ $item->day1 != $item->day2 ? $item->day_two : '' }}</b>
                                                        <br>
                                                        <b>{{ $item->course_time }}</b>
                                                        <span>
                                                            @php
    // Ambil data attendance beserta join dalam satu kali panggil secara aman
    $star = DB::table('attendances')
        ->leftJoin('teacher as t2', 'attendances.assist_id', '=', 't2.id')
        ->where('attendances.price_id', $item->priceid)
        ->where('attendances.day1', $item->day1)
        ->where('attendances.day2', $item->day2)
        ->where('attendances.course_time', $item->course_time)
        ->where('attendances.teacher_id', $item->id_teacher)
        ->select('attendances.star', 't2.name as assist_name')
        ->orderBy('attendances.date', 'desc') // Mengambil data absensi terbaru
        ->first();

    // Definisikan variabel penampung agar tidak error saat dipanggil di bawah
    $assistName = $star ? $star->assist_name : null;
    $classStar = $star ? $star->star : null; 
@endphp

                                                           
                                                            @if ($classStar != null)
                                                            @if ($classStar == 1)
                                                            (<i class="fas fa-star"></i>)
                                                            @elseif ($classStar == 2)
                                                            (<i class="fas fa-star"></i><i class="fas fa-star"></i>)
                                                            @else
                                                            Star {{ $classStar }}
                                                            @endif
                                                            @endif
                                                            @if (!empty($assistName))
                                                                <p style="font-size: 11px; color:rgb(127, 127, 255)">Assist:{{ $assistName }}</p>
                                                        @endif

                                                        </span>
                                                         

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

                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary btn-xs dropdown-toggle" type="button"
                                                                    id="dropdownMenuButton_Private_{{ $key }}_{{ $keyStudentName }}"
                                                                    data-toggle="dropdown" aria-expanded="false">
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_Private_{{ $key }}_{{ $keyStudentName }}">
                                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                                        data-toggle="modal" data-target="#editAssistModal"
                                                                        data-priceid="{{ $item->program }}"
                                                                        data-idteacher="{{ $item->teacher_name }}"
                                                                        data-day1="{{ $item->day_one }}"
                                                                        data-day2="{{ $item->day_two }}"
                                                                        data-time="{{ $item->course_time }}"
                                                                        data-day1-fix="{{ $item->day1 }}"
                                                                        data-day2-fix="{{ $item->day2 }}"
                                                                        data-idteacher-fix="{{ $item->id_teacher }}"
                                                                        data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-handshake"></i> Assist Class</a>
                                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                                        data-toggle="modal" data-target="#editStarModal"
                                                                        data-priceid="{{ $item->program }}"
                                                                        data-idteacher="{{ $item->teacher_name }}"
                                                                        data-day1="{{ $item->day_one }}"
                                                                        data-day2="{{ $item->day_two }}"
                                                                        data-time="{{ $item->course_time }}"
                                                                        data-day1-fix="{{ $item->day1 }}"
                                                                        data-day2-fix="{{ $item->day2 }}"
                                                                        data-idteacher-fix="{{ $item->id_teacher }}"
                                                                        data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-star"></i> Star</a>
                                                                    @if($assistName)
                                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#removeAssistModal"
                                                                        data-priceid="{{ $item->program }}"
                                                                        data-idteacher="{{ $item->teacher_name }}"
                                                                        data-day1="{{ $item->day_one }}"
                                                                        data-day2="{{ $item->day_two }}"
                                                                        data-time="{{ $item->course_time }}"
                                                                        data-day1-fix="{{ $item->day1 }}"
                                                                        data-day2-fix="{{ $item->day2 }}"
                                                                        data-idteacher-fix="{{ $item->id_teacher }}"
                                                                        data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-user-times"></i> Remove Assist</a>
                                                                    @endif
                                                                    <!-- jika ada star -->
                                                                     @if($classStar != null)
                                                                      <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#removeStarModal"
                                                                        data-priceid="{{ $item->program }}"
                                                                        data-idteacher="{{ $item->teacher_name }}"
                                                                        data-day1="{{ $item->day_one }}"
                                                                        data-day2="{{ $item->day_two }}"
                                                                        data-time="{{ $item->course_time }}"
                                                                        data-day1-fix="{{ $item->day1 }}"
                                                                        data-day2-fix="{{ $item->day2 }}"
                                                                        data-idteacher-fix="{{ $item->id_teacher }}"
                                                                        data-priceid-fix="{{ $item->priceid }}"><i class="fas fa-trash-alt"></i> Remove Star</a>
                                                                     @endif
                                                                </div>
                                                            </div>


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

<!-- star Modal -->
<div class="modal fade" id="editStarModal" tabindex="-1" role="dialog"
    aria-labelledby="starModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="starModalLabel">Choose Star</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('update-star') }}" method="POST" id="formStarSelection">
                @csrf

                <div class="modal-body text-center">
                    <p class="mb-4">Choose the number of Star icons that match:</p>

                    <input type="hidden" name="selected_star" id="selected_star_input">

                    <input type="hidden" name="priceid" id="modal_priceid">
                    <input type="hidden" name="day1" id="modal_day1">
                    <input type="hidden" name="day2" id="modal_day2">
                    <input type="hidden" name="course_time" id="modal_course_time">
                    <input type="hidden" name="id_teacher" id="modal_id_teacher">
                    <div class="d-flex justify-content-around align-items-center">

                        <button type="button" class="btn btn-outline-success btn-huge star-select-btn" data-star-value="1" style="width: 45%; height: 120px; font-size: 24px;">
                            <i class="fas fa-star fa-3x d-block mb-1"></i>
                            <span class="d-block" style="font-size: 16px;">Star 1</span>
                        </button>

                        <button type="button" class="btn btn-outline-primary btn-huge star-select-btn" data-star-value="2" style="width: 45%; height: 120px; font-size: 24px;">
                            <i class="fas fa-star fa-2x d-inline"></i>
                            <i class="fas fa-star fa-2x d-inline"></i>
                            <span class="d-block mt-1" style="font-size: 16px;">Star 2</span>
                        </button>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitStarSelection" disabled>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- assist modal -->
<!-- assist modal -->
<div class="modal fade" id="editAssistModal" tabindex="-1" role="dialog" aria-labelledby="assistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg">
            
            {{-- Header Modal --}}
            <div class="modal-header bg-primary text-white p-3 rounded-top-lg">
                <h5 class="modal-title font-weight-bold" id="assistModalLabel">
                    <i class="fas fa-edit mr-2"></i> Settings for Class Assistance
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('set-assistant') }}" method="POST" id="formAssistSelection">
                @csrf

                <div class="modal-body">
                    {{-- Hidden Inputs --}}
                    <input type="hidden" name="old_priceid" id="assist_modal_priceid">
                    <input type="hidden" name="old_teacher_id" id="assist_modal_old_teacher_id">
                    <input type="hidden" name="old_day1" id="assist_modal_old_day1">
                    <input type="hidden" name="old_day2" id="assist_modal_old_day2">
                    <input type="hidden" name="old_course_time" id="assist_modal_old_course_time">

                    {{-- Teacher Selection --}}
                    <div class="form-group mb-4 p-3 border rounded-lg bg-light">
                        <label for="new_teacher_id" class="font-weight-bold text-dark d-block">
                            <i class="fas fa-user-tie mr-1"></i> Select Teacher
                        </label>
                        <p class="text-secondary small mb-2">Current Teacher: <span id="current_teacher_name" class="font-weight-medium">N/A</span></p>
                        <select class="form-control custom-select" id="new_teacher_id" name="assistant_teacher_id">
                            <option value="" selected>-- Do not change Teacher --</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Day Clearing Options --}}
                    <h6 class="mb-3 text-primary font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> Clear Course Days</h6>
                    <div class="row">
                        {{-- Day 1 --}}
                        <div class="col-md-6 mb-3">
                            <div class="card p-3 h-100 border-info">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="assist_day1" value="true" id="clear_day1_check" class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-info" for="clear_day1_check">
                                        Day 1
                                    </label>
                                    <span class="d-block text-sm text-muted current-day-status mt-1">
                                        <span id="current_day1" class="font-weight-medium">N/A</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Day 2 --}}
                        <div class="col-md-6 mb-3">
                            <div class="card p-3 h-100 border-info">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="assist_day2" value="true" id="clear_day2_check" class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-info" for="clear_day2_check">
                                        Day 2
                                    </label>
                                    <span class="d-block text-sm text-muted current-day-status mt-1">
                                        <span id="current_day2" class="font-weight-medium">N/A</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="submitAssistSelection">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- remove assist class modal -->
<div class="modal fade" id="removeAssistModal" tabindex="-1" role="dialog" aria-labelledby="removeAssistModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeAssistModalLabel">Remove Class Assistant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('remove-assistant') }}" method="POST" id="formRemoveAssist">
                @csrf
                <input type="hidden" name="priceid" id="remove_assist_priceid">
                <input type="hidden" name="teacher_id" id="remove_assist_teacher_id">
                <input type="hidden" name="day1" id="remove_assist_day1">
                <input type="hidden" name="day2" id="remove_assist_day2">
                <input type="hidden" name="course_time" id="remove_assist_course_time">
                <div class="modal-body">
                    <p class="fw-bold">Are you sure you want to remove the assistant from this class?</p>
                    <p class="text-secondary">This action will clear the assistant assignment for the selected class.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove Assistant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- remove star modal -->
<div class="modal fade" id="removeStarModal" tabindex="-1" role="dialog" aria-labelledby="removeStarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeStarModalLabel">Remove Star Icon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('remove-star') }}" method="POST" id="formRemoveStar">
                @csrf
                <input type="hidden" name="priceid" id="remove_star_priceid">
                <input type="hidden" name="teacher_id" id="remove_star_teacher_id">
                <input type="hidden" name="day1" id="remove_star_day1">
                <input type="hidden" name="day2" id="remove_star_day2">
                <input type="hidden" name="course_time" id="remove_star_course_time">
                <div class="modal-body">
                    <p class="fw-bold">Are you sure you want to remove the star icon from this class?</p>
                    <p class="text-secondary">This action will clear the star icon assignment for the selected class.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove Star</button>
                </div>
            </form>
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

    <script>
    $(document).ready(function() {
        // --- LOGIKA PEMILIHAN STAR (Sudah ada, hanya dipertahankan) ---
        $('.star-select-btn').on('click', function() {
            // Hapus highlight dari semua tombol
            $('.star-select-btn').removeClass('active border-5 shadow-lg');

            // Tambahkan highlight ke tombol yang dipilih
            $(this).addClass('active border-5 shadow-lg');

            // Ambil nilai star dan masukkan ke input hidden
            var selectedStar = $(this).data('star-value');
            $('#selected_star_input').val(selectedStar);

            // Aktifkan tombol Lanjutkan
            $('#submitStarSelection').prop('disabled', false);
        });

        // --- LOGIKA PENGAMBILAN DATA DARI TOMBOL PEMICU MODAL (BARU) ---
        $('#editStarModal').on('show.bs.modal', function(event) {

            $('body').focus();
            var button = $(event.relatedTarget); // Tombol yang memicu modal

            // Ambil data dari atribut data-* tombol pemicu
            var priceid = button.data('priceid');
            var day1 = button.data('day1');
            var day2 = button.data('day2');
            var courseTime = button.data('time');
            var idTeacher = button.data('idteacher');

            var id_teacher_fix = button.data('idteacher-fix');
            var priceid_fix = button.data('priceid-fix');
            var day1_fix = button.data('day1-fix');
            var day2_fix = button.data('day2-fix');
            var studentId_fix = button.data('studentid-fix');
            var student_name = button.data('studentname');

            var dataClass = priceid + " " + day1 + "," + day2 + "," + courseTime + "," + idTeacher;
            // console.log(dataClass);


            // Isi input hidden di dalam modal
            var modal = $(this);
            modal.find('#modal_priceid').val(priceid_fix);
            modal.find('#modal_day1').val(day1_fix);
            modal.find('#modal_day2').val(day2_fix);
            modal.find('#modal_course_time').val(courseTime);
            modal.find('#modal_id_teacher').val(id_teacher_fix);
            modal.find('#starModalLabel').text('Choose Star for Class: ' + dataClass);

            // Pastikan tombol Lanjutkan dinonaktifkan dan pilihan star direset saat modal dibuka
            modal.find('#selected_star_input').val('');
            modal.find('#submitStarSelection').prop('disabled', true);
            modal.find('.star-select-btn').removeClass('active border-5 shadow-lg');
        });

        // --- LOGIKA RESET MODAL KETIKA DITUTUP (Diperbarui untuk menggunakan #editStarModal) ---
        $('#editStarModal').on('hidden.bs.modal', function() {
            // Reset pilihan star dan status tombol
            $('.star-select-btn').removeClass('active border-5 shadow-lg');
            $('#selected_star_input').val('');
            $('#submitStarSelection').prop('disabled', true);

            // Reset input hidden data kontekstual
            $('#modal_priceid').val('');
            $('#modal_day1').val('');
            $('#modal_day2').val('');
            $('#modal_course_time').val('');
            $('#modal_id_teacher').val('');
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Logika saat modal dibuka (untuk reset dan isi data)
        $('#editAssistModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Ambil Data Lama
            var oldPriceId = button.data('priceidFix');
            var oldTeacherId = button.data('idteacherFix');
            var oldDay1Fix = button.data('day1Fix');
            var oldDay2Fix = button.data('day2Fix');
            var oldCourseTime = button.data('time');

            var oldTeacherName = button.data('idteacher');
            var displayDay1 = button.data('day1');
            var displayDay2 = button.data('day2');

            // 1. Isi Hidden Fields (Kriteria WHERE)
            modal.find('#assist_modal_priceid').val(oldPriceId);
            modal.find('#assist_modal_old_teacher_id').val(oldTeacherId);
            modal.find('#assist_modal_old_day1').val(oldDay1Fix);
            modal.find('#assist_modal_old_day2').val(oldDay2Fix);
            modal.find('#assist_modal_old_course_time').val(oldCourseTime);

            // 2. RESET Checkboxes dan Dropdown Guru
            modal.find('#clear_day1_check').prop('checked', false);
            modal.find('#clear_day2_check').prop('checked', false);

            modal.find('#new_teacher_id').val(oldTeacherId || "");

            // 3. Update Label Display
            modal.find('#current_teacher_name').text(oldTeacherName || 'N/A');
            modal.find('#current_day1').text(displayDay1 || 'Empty');
            modal.find('#current_day2').text(displayDay2 || 'Empty');
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#removeAssistModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Ambil Data dari tombol pemicu
            var priceId = button.data('priceidFix');
            var teacherId = button.data('idteacherFix');
            var day1 = button.data('day1Fix');
            var day2 = button.data('day2Fix');
            var courseTime = button.data('time');

            // var classData = priceId + " " + day1 + "," + day2 + "," + courseTime + "," + teacherId;
            // Update judul modal dengan informasi kelas
            // modal.find('.modal-title').text('Remove Assist for Class: ' + classData);

            // Isi input hidden di dalam modal
            modal.find('#remove_assist_priceid').val(priceId);
            modal.find('#remove_assist_teacher_id').val(teacherId);
            modal.find('#remove_assist_day1').val(day1);
            modal.find('#remove_assist_day2').val(day2);
            modal.find('#remove_assist_course_time').val(courseTime);
        });

        // Remove Star Modal
        $('#removeStarModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Ambil Data dari tombol pemicu
            var priceId = button.data('priceidFix');
            var teacherId = button.data('idteacherFix');
            var day1 = button.data('day1Fix');
            var day2 = button.data('day2Fix');
            var courseTime = button.data('time');

            // var classData = priceId + " " + day1 + "," + day2 + "," + courseTime + "," + teacherId;
            // Update judul modal dengan informasi kelas
            // modal.find('.modal-title').text('Remove Assist for Class: ' + classData);

            // Isi input hidden di dalam modal
            modal.find('#remove_star_priceid').val(priceId);
            modal.find('#remove_star_teacher_id').val(teacherId);
            modal.find('#remove_star_day1').val(day1);
            modal.find('#remove_star_day2').val(day2);
            modal.find('#remove_star_course_time').val(courseTime);
        });
    });
</script>
@endsection
