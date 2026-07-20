@extends('template.app')

@section('content')
<style>
    .table th {
        font-size: 14px;

        padding: 0 25px !important;
        height: 35px;
        vertical-align: middle !important;
    }

    .table td {
        height: 35px !important;
        padding: 8px 16px !important;
    }

    .permission {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .permission input[type=checkbox] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .permissionCheckBox {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: green;
    }

    /* On mouse-over, add a grey background color */
    .permission::hover input[type=checkbox]~.permissionCheckBox {
        background-color: green;
    }

    /* When the checkbox is checked, add a blue background */
    .permission input[type=checkbox]::checked~.permissionCheckBox {
        background-color: green;
    }

    /* Create the permissionCheckBox/indicator (hidden when not checked) */
    /*.permissionCheckBox:after {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    content: "";
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    position: absolute;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    display: none;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }*/



    span.permissionCheckBox.checked::after {
        content: "";
        position: absolute;
        display: block;
    }

    /* Show the permissionCheckBox when checked */
    /*.permission input[type=checkbox]:checked~.permissionCheckBox:after {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    display: block;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }*.

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                /* Style the permissionCheckBox/indicator */
    .permission .permissionCheckBox::after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    /* Hide the browser's default checkbox */
    .alpha input[type=checkbox] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .alphaCheckBox {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: red;
    }

    /* On mouse-over, add a grey background color */
    .alpha::hover input[type=checkbox]~.alphaCheckBox {
        background-color: red;
    }

    /* When the checkbox is checked, add a blue background */
    .alpha input[type=checkbox]::checked~.alphaCheckBox {
        background-color: red;
    }

    /* Create the alphaCheckBox/indicator (hidden when not checked) */
    /*.alphaCheckBox:after {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    content: "";
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    position: absolute;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    display: none;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }*/

    span.alphaCheckBox.checked::after {
        content: "";
        position: absolute;
        display: block;
    }

    /* Show the alphaCheckBox when checked */
    /*.alpha input[type=checkbox]:checked~.alphaCheckBox:after {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    display: block;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }*/



    /* Style the alphaCheckBox/indicator */
    .alpha .alphaCheckBox::after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .rowheaders td:nth-of-type(1) {
        font-style: italic;
    }

    .rowheaders th:nth-of-type(3),
    .rowheaders td:nth-of-type(2) {
        text-align: right;
    }

    th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    th[scope=row] {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 1;
        background: white;
        border: 1px solid #48abf7 !important;
    }

    th[scope=row] {
        vertical-align: top;
        background: white;
        border: 1px solid #48abf7 !important;
    }

    th:not([scope=row]):first-child {
        left: 0;
        z-index: 3;
    }

    a.disabled {
        pointer-events: none;
        cursor: default;
    }

    .agenda-scroll {
        white-space: nowrap;
        scroll-snap-type: x mandatory;
    }

    .agenda-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
</style>
<div class="content">
    <div class="page-inner py-5 panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
            <div class="">
                <h2 class="text-white pb-2 fw-bold">{{ $title }}</h2>
                @if (count($attendance) > 0)
                @if ($attendance[0]->mutasi_teacher != null)
                @php
                $tgl_mutasi = $attendance[0]->tgl_mutasi;
                $teacher = \App\Models\Teacher::where('id', $attendance[0]->mutasi_teacher)->first();
                @endphp

                @if ($teacher != null)
                <h5 class="text-white pb-2 fw-italic">
                    Previous Teacher was {{ $teacher->name }} stopped at {{ $tgl_mutasi }}
                    <i class="fas fa-ban-circle fa-2x bg-danger text-white fw-italic"></i>
                </h5>
                @else
                <h5 class="text-white pb-2 fw-italic">
                    Stopped at {{ $tgl_mutasi }}
                    <i class="fas fa-ban-circle fa-2x bg-danger text-white fw-italic"></i>
                </h5>
                @endif
                @endif
                @endif



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
                        <a href="#" class="text-white">Attendance</a>
                    </li>
                    <li class="separator text-white">
                        <i class="flaticon-right-arrow text-white"></i>
                    </li>
                    <li class="nav-item text-white">
                        <a href="#" class="text-white">{{ $title }}</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="page-inner mt--5">
        @if (session('status'))
        <script>
            swal("Gagal!", "{{ session('status') }}!", {
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
                <div class="card card-body">
                    <button type="button" class="btn btn-sm btn-secondary mb-2" data-toggle="modal" data-target="#lessonPlanModal">
                        <span class="text-bold">Detail of Lesson Plan</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning mb-2" data-toggle="modal"
                        data-target="#exampleModal" data-modal-type="attendance">
                        <span class="text-bold">Detail of Attendence and Agenda</span>
                    </button>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                            <thead>
                                <tr>
                                    <th width="10%" class="rowheaders">Nama</th>


                                    @foreach ($attendance as $key => $item)
                                    <th width="5%">{{ date('d/m', strtotime($item->date)) }}
                                        @if ($key < 2)
                                            <!-- Hanya dua item pertama yang dapat diedit -->
                                            @if (Request::segment(2) != 'edit')
                                            <br>
                                            <a href="{{ url('attendance/edit/') . '/' . $item->id . '?day1=' . Request::get('day1') . '&day2=' . Request::get('day2') . '&time=' . Request::get('time') . '&teacher=' . Request::get('teacher') . '&class=' . $data->id . '&new=' . Request::get('new') }}"
                                                class="btn btn-sm btn-success">Edit
                                            </a>
                                            <br>
                                            @endif
                                            @endif
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($student as $item)
                                        <tr>
                                            <th width="10%" scope="row">{{ $item->name }}</th>
                                @if (Request::segment(2) == 'edit')
                                @foreach ($attendance as $i)
                                @php
                                $cek = App\Models\AttendanceDetail::where(
                                'attendance_id',
                                $i->id,
                                )->where('student_id', $item->id);
                                $count = $cek->count();
                                if ($count == 1 && $cek->first()->is_absent == '1') {
                                $absen = true;
                                } else {
                                $absen = false;
                                }
                                @endphp
                                @if ($count == 1 && $cek->first()->is_absent == '1')
                                <td width="5%">
                                    <span class="fa fa-check"></span>
                                </td>
                                @elseif ($count == 1 && $cek->first()->is_permission == true)
                                <td width="5%" bgcolor='green'></td>
                                @elseif ($count == 1 && $cek->first()->is_alpha == true)
                                <td width="5%" bgcolor='red'></td>
                                @else
                                <td width="5%"></td>
                                @endif
                                @endforeach
                                @else
                                @foreach ($attendance as $i)
                                @php
                                $cek = App\Models\AttendanceDetail::where(
                                'attendance_id',
                                $i->id,
                                )->where('student_id', $item->id);
                                $count = $cek->count();
                                if ($count == 1 && $cek->first()->is_absent == '1') {
                                $absen = true;
                                } else {
                                $absen = false;
                                }
                                @endphp
                                @if ($count == 1 && $cek->first()->is_absent == '1')
                                <td width="5%">
                                    <span class="fa fa-check"></span>
                                </td>
                                @elseif ($count == 1 && $cek->first()->is_permission == true)
                                <td width="5%" bgcolor='green'></td>
                                @elseif ($count == 1 && $cek->first()->is_alpha == true)
                                <td width="5%" bgcolor='red'></td>
                                @else
                                <td width="5%"></td>
                                @endif
                                @endforeach
                                @endif
                                </tr>
                                @endforeach --}}

                                @php
                                // It's best to prepare this data in your controller or service provider
                                // and pass it to the view, rather than doing it directly in the Blade file.
                                // Assuming $student_birthday_notification is the array you provided.

                                $birthdayStudentNames = collect($student_birthday_notification)
                                ->pluck('name')
                                ->toArray();
                                @endphp

                                @foreach ($student as $item)
                                <tr>
                                    <th width="10%" scope="row">
                                        {{ $item->name }}
                                        @if (in_array($item->name, $birthdayStudentNames))
                                        <span class="badge badge-pill badge-primary"
                                            style="background-color: #ff69b4; margin-left: 5px;">
                                            <i class="fas fa-birthday-cake" style="color: white;"
                                                title="Happy Birthday!"></i>
                                            <span class="sr-only">Birthday Student!</span> {{-- For accessibility --}}
                                        </span>
                                        @endif
                                    </th>
                                    @if (Request::segment(2) == 'edit')
                                    @foreach ($attendance as $i)
                                    @php
                                    $cek = App\Models\AttendanceDetail::where(
                                    'attendance_id',
                                    $i->id,
                                    )->where('student_id', $item->id);
                                    $count = $cek->count();
                                    $attendanceDetail = $cek->first(); // Get the first result if it exists
                                    @endphp
                                    <td width="5%">
                                        @if ($count == 1 && $attendanceDetail->is_absent == '1')
                                        <span class="fa fa-check"></span>
                                        @elseif ($count == 1 && $attendanceDetail->is_permission == true)
                                        <span class="fas fa-hand-paper" style="color: green;"
                                            title="Permission"></span> {{-- Example: Permission icon --}}
                                        @elseif ($count == 1 && $attendanceDetail->is_alpha == true)
                                        <span class="fas fa-times" style="color: red;"
                                            title="Alpha"></span> {{-- Example: Absent icon --}}
                                        @endif
                                    </td>
                                    @endforeach
                                    @else
                                    {{-- This block is identical to the 'edit' block, consider consolidating if possible --}}
                                    @foreach ($attendance as $i)
                                    @php
                                    $cek = App\Models\AttendanceDetail::where(
                                    'attendance_id',
                                    $i->id,
                                    )->where('student_id', $item->id);
                                    $count = $cek->count();
                                    $attendanceDetail = $cek->first();
                                    @endphp
                                    <td width="5%">
                                        @if ($count == 1 && $attendanceDetail->is_absent == '1')
                                        <span class="fa fa-check"></span>
                                        @elseif ($count == 1 && $attendanceDetail->is_permission == true)
                                        <span class="fas fa-hand-paper" style="color: green;"
                                            title="Permission"></span>
                                        @elseif ($count == 1 && $attendanceDetail->is_alpha == true)
                                        <span class="fas fa-times" style="color: red;"
                                            title="Alpha"></span>
                                        @endif
                                    </td>
                                    @endforeach
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                @if (Request::segment(2) == 'edit')
                <form action="{{ url('attendance/update', $data->id) }}" method="POST"
                    enctype="multipart/form-data" id="form-submit">
                    @csrf
                    @else
                    <form
                        action="{{ $data->type == 'create' ? url('attendance/store') : url('attendance/update', $data->id) }}"
                        method="POST" enctype="multipart/form-data" id="form-submit">
                        @csrf
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ $data->type == 'create' ? 'Presence' : 'Edit Presence' }}</h4>
                            </div>
                            <input type="hidden" name="day1" value="{{ request()->get('day1') }}">
                            <input type="hidden" name="day2" value="{{ request()->get('day2') }}">
                            <input type="hidden" name="time" value="{{ request()->get('time') }}">
                            <input type="hidden" name="teacher" value="{{ request()->get('teacher') }}">
                            <input type="hidden" name="is_new" value="{{ request()->get('new') }}">
                            <div class="card-body">
                                <input type="hidden" readonly name="priceId" value="{{ $data->id }}">
                                <input type="hidden" readonly name="attendanceId" value="{{ $data->attendanceId }}">

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center" scope="col" class="w-5"
                                                            style="min-width:3px;">Presence</th>
                                                        <th class="text-center">Absent</th>
                                                        <th class="text-center">In-Point</th>
                                                        <th class="text-center">Category</th>
                                                        {{-- <th class="text-center">In Point Category</th> --}}
                                                        <th class="text-center">
                                                            Total
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @php
                                                    $agenda = App\Models\AttendanceDetail::join(
                                                    'attendances',
                                                    'attendance_details.attendance_id',
                                                    'attendances.id',
                                                    )->where('price_id', $priceId);

                                                    $no = 1;
                                                    $whereRaw = '';
                                                    $countAgenda = 0;
                                                    @endphp
                                                    @foreach ($student as $keyIt => $it)
                                                    @php
                                                    $or = $keyIt + 1 != $loop->count ? ' or ' : '';
                                                    $whereRaw .= 'student_id = ' . $it->id . $or;
                                                    $countAgenda++;
                                                    // if ($keyIt == 0) {
                                                    // $agenda = $agenda->where('student_id', $it->id);
                                                    // } else {
                                                    // $agenda = $agenda->orWhereRaw('(' . $whereRaw . ')');
                                                    // $agenda = $agenda->orWhere('student_id', $it->id);
                                                    // }
                                                    $birthDayPoint = 0;
                                                    if ($it->birthday == date('M d')) {
                                                    $birthDayPoint = 30;
                                                    }

                                                    $birthdayStudentNames = collect($student_birthday_notification)
                                                    ->pluck('name')
                                                    ->toArray();
                                                    @endphp
                                                    <tr style="height: 40px!important">
                                                        <td class="text-center" style="">{{ $no }}
                                                        </td>
                                                        <td style="">
                                                            {{ $it->name }}
                                                            @if (in_array($it->name, $birthdayStudentNames))
                                                            <span class="badge badge-pill badge-primary"
                                                                style="background-color: #ff69b4; margin-left: 5px;">
                                                                <i class="fas fa-birthday-cake" style="color: white;"
                                                                    title="Happy Birthday!"></i>
                                                                <span class="sr-only">Birthday Student!</span>
                                                                {{-- For accessibility --}}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <input type="hidden" readonly name="studentId[]"
                                                            value="{{ $it->id }}">
                                                        <td class=" text-center" scope="col"
                                                            style="width:3px!important;">
                                                            <input type="hidden" name="isAbsent[{{ $no }}][]"
                                                                value="0">
                                                            @php
                                                            $cekAbsen = \DB::table('attendance_details')
                                                            ->where('attendance_id', $data->attendanceId)
                                                            ->where('student_id', $it->id)
                                                            ->where('is_absent', '1')
                                                            ->count();
                                                            $studentPointCategory = [];
                                                            $getStudentPointCategory = \DB::table(
                                                            'attendance_detail_points',
                                                            )
                                                            ->join(
                                                            'attendance_details',
                                                            'attendance_detail_points.attendance_detail_id',
                                                            'attendance_details.id',
                                                            )
                                                            ->where('student_id', $it->id)
                                                            ->where('attendance_id', $data->attendanceId)
                                                            ->get();

                                                            foreach ($getStudentPointCategory as $k => $v) {
                                                            array_push(
                                                            $studentPointCategory,
                                                            $v->point_category_id,
                                                            );
                                                            }

                                                            $isChecked = false;
                                                            if ($data->type == 'create') {
                                                            $isChecked = false;
                                                            } else {
                                                            // if ($data->students[$no - 1]->student_id == $it->id && $data->students[$no - 1]->is_absent == '1') {
                                                            // $isChecked = true;
                                                            // }
                                                            if ($cekAbsen == 0) {
                                                            $isChecked = false;
                                                            } else {
                                                            $isChecked = true;
                                                            }
                                                            }
                                                            @endphp
                                                            <input type="checkbox" class="form-check-input cekBox"
                                                                id="cbAbsent{{ $no }}" value="1"
                                                                {{ $isChecked ? 'checked' : '' }}
                                                                aria-label="Checkbox for following text input"
                                                                name="isAbsent[{{ $no }}][]"
                                                                data-hour="{{ $it->course_hour }}"
                                                                data-class="{{ $it->priceid }}">

                                                        </td>
                                                        <td class=" text-center" scope="col"">
                                                            @php
                                                                $isCekPermission = false;
                                                                $isCekAlpha = false;
                                                                $cekPermission = \DB::table('attendance_details')
                                                                    ->where('attendance_id', $data->attendanceId)
                                                                    ->where('student_id', $it->id)
                                                                    ->where('is_permission', true)
                                                                    ->count();
                                                                $cekAlpha = \DB::table('attendance_details')
                                                                    ->where('attendance_id', $data->attendanceId)
                                                                    ->where('student_id', $it->id)
                                                                    ->where('is_alpha', true)
                                                                    ->count();
                                                                if ($data->type == 'create') {
                                                                    $isCekAlpha = false;
                                                                    $isCekPermission = false;
                                                                } else {
                                                                    if ($cekPermission == 1) {
                                                                        if ($cekAbsen == 0) {
                                                                            $isCekPermission = true;
                                                                        } else {
                                                                            $isCekPermission = false;
                                                                        }
                                                                    } else {
                                                                        $isCekPermission = false;
                                                                    }
                                                                    if ($cekAlpha == 1) {
                                                                        $isCekAlpha = true;
                                                                    } else {
                                                                        $isCekAlpha = false;
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class=" row">
                                                            <div class="col-6">
                                                                <label class="permission">
                                                                    <input type="hidden"
                                                                        name="isPermission[{{ $no }}][]"
                                                                        value="0">
                                                                    <input type="checkbox" class="cekBoxPermission"
                                                                        name="isPermission[{{ $no }}][]"
                                                                        id="permissionCheckBox{{ $keyIt }}"
                                                                        value="0"
                                                                        onclick="permission('{{ $keyIt }}')"
                                                                        {{ $isCekPermission ? 'checked' : '' }}>
                                                                    <span class="permissionCheckBox"
                                                                        style=""></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="alpha">
                                                                    <input type="hidden"
                                                                        name="isAlpha[{{ $no }}][]"
                                                                        value="0">
                                                                    <input type="checkbox"
                                                                        name="isAlpha[{{ $no }}][]"
                                                                        id="alphaCheckBox{{ $keyIt }}"
                                                                        value="0"
                                                                        onclick="alpha('{{ $keyIt }}')"
                                                                        {{ $isCekAlpha ? 'checked' : '' }}
                                                                        class="form-check-input cekBoxAlpha">
                                                                    <span class="alphaCheckBox" style=""></span>
                                                                </label>
                                                            </div>
                                        </div>
                                        </td>
                                        <td class="text-center" style="">
                                            @php
                                            $isAbsent = false;
                                            if ($data->type == 'create') {
                                            $isAbsent = false;
                                            } else {
                                            if ($cekAbsen == 1) {
                                            $isAbsent = true;
                                            }
                                            }
                                            @endphp

                                            @php
                                            $totalPoint = 0;
                                            @endphp



                                            <h5 id="inPointAbsent{{ $no }}">
                                                @if ($isAbsent)
                                                @php
                                                $pointDay = 0;
                                                $pointHour = 0;
                                                if (
                                                Request::get('day1') == 5 ||
                                                Request::get('day1') == 6 ||
                                                Request::get('day2') == 5 ||
                                                Request::get('day2') == 6 ||
                                                Request::get('day1') == Request::get('day2')
                                                ) {
                                                $pointDay = 20;
                                                } else {
                                                $pointDay = 10;
                                                }

                                                if (
                                                $it->course_hour != null ||
                                                $it->priceid == 42 ||
                                                $it->priceid == 39
                                                ) {
                                                // $totalPoint = $it->course_hour . '0';
                                                $totalPoint = $pointDay;
                                                } else {
                                                $totalPoint = $pointDay;
                                                }
                                                @endphp

                                                {{ $totalPoint }}
                                                @else
                                                0
                                                @endif
                                            </h5>

                                            <input type="hidden" value="{{ $totalPoint }}"
                                                name="isAbsentPoint[{{ $no }}]"
                                                id="isAbsentPoint{{ $no }}">
                                            {{-- {{ $isAbsent ? '10' : '0' }}</h5> --}}
                                        </td>
                                        <td style="">
                                            <select class="form-control select2 select2-hidden-accessible"
                                                style="width:100%;"
                                                name="categories[{{ $no }}][]"
                                                placeholder="Select Category"
                                                id="categories{{ $no }}" multiple="multiple">

                                                @foreach ($pointCategories as $st)
                                                <option value="{{ $st->id }}"
                                                    {{ $data->type == 'update' && in_array(intval($st->id), $studentPointCategory) ? 'selected' : '' }}>
                                                    {{ $st->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            {{-- @if ($birthDayPoint != 0)
                                                                    <span class="center"
                                                                        style="color: red
                                                                ">+
                                                                        Extra Birthday</span>
                                                                @endif --}}
                                            {{-- <input type="text" class="form-control"
                                                                    placeholder="Enter Category"
                                                                    name="category[{{ $no }}][]"
                                            value="{{ $data->type == 'update' ? $data->students[$no - 1]->category : '' }}"> --}}
                                        </td>
                                        {{-- <td>
                                                                <input type="number" class="form-control"
                                                                    placeholder="Enter In Point Category"
                                                                    name="point_category[{{ $no }}][]"
                                        id="point_category{{ $no }}"
                                        value="{{ $data->type == 'update' ? $data->students[$no - 1]->categoryPoint : '' }}">
                                        </td> --}}
                                        <td class="text-center" style="">
                                            @php
                                            $totalPoint = 0;
                                            if ($data->type == 'create') {
                                            $totalPoint = 0;
                                            } else {
                                            $cekTotalPoint = \DB::table('attendance_details')
                                            ->where('attendance_id', $data->attendanceId)
                                            ->where('student_id', $it->id);

                                            if ($cekTotalPoint->count() == 1) {
                                            $getTotalPoint = $cekTotalPoint->first();
                                            $totalPoint = $getTotalPoint->total_point;
                                            }
                                            }
                                            @endphp
                                            <input type="hidden" name="totalPoint[]"
                                                id="inpTotalPoint{{ $no }}"
                                                value="{{ $totalPoint }}" readonly>
                                            <input type="hidden"
                                                name="birthdaypoint[{{ $no }}][]"
                                                value="{{ $birthDayPoint }}">
                                            {{-- <input type="text" name="totalPointCategory[]"
                                                                    id="inpTotalPointCategory{{ $no }}"
                                            value="{{ $totalPoint }}" readonly> --}}
                                            <h5 id="totalPoint{{ $no }}">
                                                {{ $totalPoint }}
                                            </h5>
                                        </td>

                                        </tr>
                                        @php
                                        $no++;
                                        @endphp
                                        @endforeach

                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-3">Agenda</h2>
                            @php
                            // Inisialisasi nilai default
                            $topicStart = '';
                            $topicEnd = '';
                            $flashcardStart = '';
                            $flashcardEnd = '';

                            // Jika tipenya update, pecah string page dari database
                            if (isset($data) && $data->type == 'update') {
                            // Kolom topic_page (misal: "5-8")
                            $topicPages = explode('-', $data->topic_page ?? '');
                            $topicStart = $topicPages[0] ?? '';
                            $topicEnd = $topicPages[1] ?? '';

                            // Kolom flashcard_page (misal: "1-25")
                            $flashcardPages = explode('-', $data->flashcard_page ?? '');
                            $flashcardStart = $flashcardPages[0] ?? '';
                            $flashcardEnd = $flashcardPages[1] ?? '';
                            }
                            @endphp

                            <!-- Section 1: Topic/Textbook -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1 fw-bold text-dark">Topic/Textbook <span class="text-danger">*</span></label>
                                        <!-- Range input untuk halaman/bab -->
                                        <div class="d-flex align-items-center mb-2">
                                            <input type="number" class="form-control text-center" name="topic_start"
                                                value="{{ old('topic_start', $topicStart) }}" style="width: 80px;" required>
                                            <span class="mx-2 fw-bold">—</span>
                                            <input type="number" class="form-control text-center" name="topic_end"
                                                value="{{ old('topic_end', $topicEnd) }}" style="width: 80px;" required>
                                        </div>
                                        <!-- Input utama untuk Text/Topic harusnya pake textarea -->
                                        <textarea class="form-control" name="comment" required placeholder="e.g., Bedouin People">{{ old('comment', ($data->type == 'update' ? $data->comment : '')) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Flashcards -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1 fw-bold text-dark">Flashcards</label>
                                        <!-- Range input untuk nomor flashcard -->
                                        <div class="d-flex align-items-center">
                                            <input type="number" class="form-control text-center" name="flashcards_start"
                                                value="{{ old('flashcards_start', $flashcardStart) }}" style="width: 80px;">
                                            <span class="mx-2 fw-bold">—</span>
                                            <input type="number" class="form-control text-center" name="flashcards_end"
                                                value="{{ old('flashcards_end', $flashcardEnd) }}" style="width: 80px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Exercise/Supplement -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1 fw-bold text-dark">Exercise/Supplement</label>
                                        <textarea class="form-control" name="excerciseBook" rows="2">{{ old('excerciseBook', ($data->type == 'update' ? $data->excerciseBook : '')) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Class Activity -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1 fw-bold text-dark">Class Activity</label>
                                        <textarea class="form-control" name="activity_class" rows="2" placeholder="e.g., Fun quiz">{{ old('activity_class', ($data->type == 'update' ? $data->activity_class : '')) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row"> --}}
                            {{-- <div class="col-md-6"> --}}


                            <!-- <div class="form-group">
                                @php
                                    $tests = DB::table('tests')->get();
                                @endphp
                                <label for="">Review and Test</label>
                                {{-- <select name="id_test" id="" class="form-control"> --}}
                                {{-- <option value="">---Choose Test---</option> --}}
                                <div class="row">
                                    @foreach ($tests as $keyt => $t)
                                        <div class="col-md-1">
                                            @php
                                                $cekOrder = DB::table('order_reviews')
                                                    ->where('id_attendance', $data->attendanceId)
                                                    ->where('test_id', $t->id)
                                                    ->get();

                                            @endphp
                                            <div class="form-group">
                                                <label for="">{{ $t->id }}</label>
                                                <input type="radio" name="id_test[]" class="form-class"
                                                    value="{{ $t->id }}"{{ $data->type == 'update' ? ($cekOrder ? 'checked' : '') : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- </select> --}}
                            </div> -->
                            <div class="form-group">
                                @php
                                $tests = DB::table('tests')->get();
                                @endphp
                                <label for="">Review and Test</label>

                                <div class="row">
                                    @foreach ($tests as $keyt => $t)
                                    @php
                                    $cekOrder = null;

                                    if ($data->attendanceId != 0) {
                                    $cekOrder = DB::table('order_reviews')
                                    ->where('id_attendance', $data->attendanceId)
                                    ->where('test_id', $t->id)
                                    ->first();
                                    }

                                    // Tentukan apakah input harus dimatikan
                                    // Mati jika: BUKAN mode update DAN data sudah ada di order_reviews
                                    $isDisabled = ($data->type != 'update' && $cekOrder);
                                    @endphp

                                    <div class="col-md-1" style="{{ $isDisabled ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                                        <div class="form-group">
                                            <label for="" style="{{ $isDisabled ? 'text-decoration: line-through;' : '' }}">
                                                {{ $t->id }}
                                                @if($isDisabled) <small>(Done)</small> @endif
                                            </label>
                                            <br>
                                            <input type="radio" name="id_test[]" class="form-class"
                                                value="{{ $t->id }}"
                                                {{ $data->type == 'update' && $cekOrder ? 'checked' : '' }}
                                                {{ $isDisabled ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- </div> --}}
                            {{-- </div> --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Review</label>
                                        <input type="date" class="form-control"
                                            value="{{ $data->type == 'update' ? $data->date_review : '' }}"
                                            name="date_review">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Test</label>
                                        <input type="date" class="form-control"
                                            value="{{ $data->type == 'update' ? $data->date_test : '' }}"
                                            name="date_test">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control"
                                value="{{ $data->is_presence != false ? '1' : '0' }}" name="cekAllAbsen"
                                id="cekAllAbsen" class="checkAllAbsen">
                        </div>
                        <div class="card-action mt-3">
                            <!--<a href="javascript:void(0)" onclick="confirm()" class="btn btn-success"-->
                            <!--    id="btn-success">Submit</a>-->
                            @if (Request::segment(2) == 'edit')
                            <a href="javascript:void(0)" onclick="confirm_update()" class="btn btn-success"
                                id="btn-success">Update</a>
                            @else
                            <a href="javascript:void(0)" onclick="confirm_submit()" class="btn btn-success"
                                id="btn-success">Submit</a>
                            @endif
                            <button type="button" data-toggle="modal" data-target="#mdlCancel"
                                class="btn btn-danger">Cancel</button>
                        </div>
                        {{-- @endif --}}
            </div>
            @php
            $agenda = $agenda
            ->where('day1', $reqDay1)
            ->where('day2', $reqDay2)
            ->where('teacher_id', $reqTeacher)
            ->where('course_time', $reqTime);
            // ->where('is_class_new', Request::get('new'));
            if ($countAgenda != 0) {
            $agenda = $agenda->whereRaw('(' . $whereRaw . ')');
            }
            $agenda = $agenda->orderBy('attendances.id', 'DESC')->groupBy('attendances.id')->paginate(4);
            @endphp
            {{-- @if (Auth::guard('teacher')->check() == true) --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Last Agenda</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- {{ dd($agenda) }} --}}
                        @foreach ($agenda as $item)
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <p>{{ $item->date }}
                                        <br>{{ $item->activity }}

                                        <br>Text Book : {{ $item->text_book }}
                                        <br>Exercise Book :
                                        {{ $item->excercise_book != null ? $item->excercise_book : '-' }}
                                        <br>Flashcard Page : {{ $item->flashcard_page != null ? $item->flashcard_page : '-' }}
                                        <br>Activity Class : {{ $item->activity_class != null ? $item->activity_class : '-' }}
                                        <br>Topic Page : {{ $item->topic_page != null ? $item->topic_page : '-' }}
                                    </p>

                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- @endif --}}
            </form>
        </div>
    </div>
</div>
<div class="modal" id="mdlCancel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel the process?</p>
            </div>
            <div class="modal-footer">
                <a href="{{ url('/advertise') }}"><button type="button"
                        class="btn btn-success">Yes</button></a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>



{{-- modal detail agenda and attendance --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="combinedDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="combinedDetailModalLabel">Detail of Attendance and Agenda</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <nav>
                    <div class="nav nav-tabs mb-4" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-attendance-tab" data-toggle="tab"
                            data-target="#nav-attendance" type="button" role="tab"
                            aria-controls="nav-attendance" aria-selected="true">Detail of Attendance</button>
                        <button class="nav-link" id="nav-agenda-tab" data-toggle="tab" data-target="#nav-agenda"
                            type="button" role="tab" aria-controls="nav-agenda"
                            aria-selected="false">Detail of Agenda</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-attendance" role="tabpanel"
                        aria-labelledby="nav-attendance-tab">
                        {{-- <p><strong>Nama Siswa:</strong> <span id="combinedStudentName"></span></p> --}}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>

                                </thead>
                                <tbody id="combinedAttendanceDetailBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-agenda" role="tabpanel" aria-labelledby="nav-agenda-tab">
                        <div class="container-fluid px-2">
                            <div class="row g-3" id="agendaList">
                                <!-- Card agenda akan dimasukkan di sini -->
                            </div>
                        </div>
                    </div>




                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button> --}}
            </div>
        </div>
    </div>
</div>


{{-- modal detail lesson plan (Satu Halaman Langsung) --}}
<div class="modal fade" id="lessonPlanModal" tabindex="-1" aria-labelledby="combinedDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-dark" id="combinedDetailModalLabel">
                    <i class="fas fa-book-open mr-2"></i>Detail of Lesson Plan
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body px-4">

                <!-- cek dulu apakah ada variabel lesson plan, klok ga ada abaikan -->
                @if(isset($lesson_plan))

                {{-- BAGIAN 1: DATA UTAMA (CLASS & SCHEDULE) --}}
                @if(count($lesson_plan) > 0)
                <div class="table-responsive mb-4 shadow-sm rounded">
                    <table class="table table-bordered mb-0">
                        <tbody class="bg-white">
                            <tr>
                                <th width="30%" class="bg-light text-secondary font-weight-bold text-uppercase text-xs align-middle">Teacher Name</th>
                                <td class="align-middle text-dark"><strong>{{ $lesson_plan[0]->teacher_name }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary font-weight-bold text-uppercase text-xs align-middle">Class</th>
                                <td class="align-middle"><span class="badge badge-info px-2 py-1">{{ $lesson_plan[0]->class }} {{ $lesson_plan[0]->day1 }} & {{ $lesson_plan[0]->day2 }} {{ $lesson_plan[0]->course_time }} WIB </span></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-list text-primary mr-2"></i>Lesson Agenda Items</h5>
                    <span class="badge badge-secondary px-2 py-1">Total: {{ count($lesson_plan) }} Items</span>
                </div>

                {{-- BAGIAN 2: LOOPING SEMUA AGENDA MATERI --}}
                @if(isset($lesson_plan))
                <div class="container-fluid px-0">
                    <div class="d-flex flex-nowrap overflow-x-auto pb-3" style="width: 100%; max-width: 100%; overflow-x: auto !important; -webkit-overflow-scrolling: touch;">
                        @foreach($lesson_plan as $item)
                        <div class="flex-shrink-0 mb-1 px-2" style="width: 49%; min-width: 650px;">
                            <div class="card shadow-md h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                    <div class="d-flex align-items-center w-75">
                                        <h6 class="m-0 font-weight-bold text-primary mr-2">{{ $loop->iteration }}. Topic :</h6>
                                        <span class="text-dark font-weight-bold text-truncate mr-2">{{ $item->topic == '-' ? ' ': $item->topic  }} ({{ $item->topic_page ?? '-' }} page)</span>
                                        <button type="button" class="btn btn-xs btn-link p-0 text-muted btn-inline-copy mr-2"
                                            data-copy="{{ $item->topic }}"
                                            title="Copy Topic">
                                            <i class="far fa-copy"></i>
                                        </button>


                                    </div>
                                    <small class="text-muted font-italic text-xs">Created On : {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</small>
                                </div>
                                <div class="card-body bg-white py-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <p class="mb-1 text-xs text-uppercase font-weight-bold text-muted tracking-wider">Flashcards</p>
                                            <div class="d-flex align-items-start p-2 border rounded bg-light" style="min-height: 44px;">
                                                <div class="w-100 text-dark user-select-all font-weight-medium text-sm pr-2" style="word-break: break-word;">
                                                    {{ $item->flashcards ?? '-' }}({{ $item->flashcard_page ?? '-' }} page)
                                                </div>
                                                @if(!empty($item->flashcards))
                                                <button type="button" class="btn btn-xs btn-link p-0 text-muted btn-inline-copy ml-auto"
                                                    data-copy="{{ $item->flashcards }}" title="Copy Flashcards">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <p class="mb-1 text-xs text-uppercase font-weight-bold text-muted tracking-wider">Exercise</p>
                                            <div class="d-flex align-items-start p-2 border rounded bg-light" style="min-height: 44px;">
                                                <div class="w-100 text-dark user-select-all font-weight-medium text-sm pr-2" style="word-break: break-word;">
                                                    {{ $item->exercise ?? '-' }}
                                                </div>
                                                @if(!empty($item->exercise))
                                                <button type="button" class="btn btn-xs btn-link p-0 text-muted btn-inline-copy ml-auto"
                                                    data-copy="{{ $item->exercise }}" title="Copy Exercise">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <p class="mb-1 text-xs text-uppercase font-weight-bold text-muted tracking-wider">Activity</p>
                                            <div class="d-flex align-items-start p-2 border rounded bg-light" style="min-height: 44px;">
                                                <div class="w-100 text-dark user-select-all font-weight-medium text-sm pr-2" style="word-break: break-word;">
                                                    {{ $item->activity ?? '-' }}
                                                </div>
                                                @if(!empty($item->activity))
                                                <button type="button" class="btn btn-xs btn-link p-0 text-muted btn-inline-copy ml-auto"
                                                    data-copy="{{ $item->activity }}" title="Copy Activity">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-primary btn-insert-data"
                                        data-topic="{{ $item->topic }}"
                                        data-topic-page="{{ $item->topic_page ?? '' }}"
                                        data-flashcard-page="{{ $item->flashcard_page ?? '' }}"
                                        data-exercise="{{ $item->exercise ?? '-' }}"
                                        data-activity="{{ $item->activity ?? '-' }}">
                                        <i class="fas fa-plus mr-1"></i> Copy to Agenda Topic
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @endif
                @else
                <div class="text-center text-muted my-5 py-4">
                    <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                    <p class="mb-0 font-weight-medium">No lesson plan data available.</p>
                </div>
                @endif

                @endif

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Script Generator Handler Copy --}}
<script>
    document.querySelectorAll('.btn-inline-copy').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const textToCopy = this.getAttribute('data-copy');

            if (!textToCopy || textToCopy === '-') return;

            navigator.clipboard.writeText(textToCopy).then(() => {
                const icon = this.querySelector('i');

                // Ubah icon jadi checkmark sukses sementara waktu
                icon.className = 'fas fa-check text-success';

                setTimeout(() => {
                    icon.className = 'far fa-copy text-muted';
                }, 1200);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });
    });

    // Script untuk mengisi otomatis ke Textarea
    // Script untuk mengisi otomatis ke Textarea + Notifikasi Toast
    // Script untuk mengisi otomatis ke masing-masing field input Agenda + Notifikasi Toast
    document.querySelectorAll('.btn-insert-data').forEach(button => {
        button.addEventListener('click', function() {
            // 1. Ambil data-attribute dari tombol yang diklik
            const topic = this.getAttribute('data-topic');
            const topicPage = this.getAttribute('data-topic-page');
            const flashcardPage = this.getAttribute('data-flashcard-page');
            const exercise = this.getAttribute('data-exercise');
            const activity = this.getAttribute('data-activity');

            // 2. Set Nilai Utama Topik (Textarea: name="comment")
            const inputComment = document.querySelector('textarea[name="comment"]');
            if (inputComment) inputComment.value = (topic && topic !== '-') ? topic : '';

            // 3. Pecah dan Set Nilai Range Halaman Topic/Textbook
            const inputTopicStart = document.querySelector('input[name="topic_start"]');
            const inputTopicEnd = document.querySelector('input[name="topic_end"]');
            if (topicPage && topicPage.includes('-')) {
                const splitTopic = topicPage.split('-');
                if (inputTopicStart) inputTopicStart.value = splitTopic[0] || '';
                if (inputTopicEnd) inputTopicEnd.value = splitTopic[1] || '';
            } else {
                if (inputTopicStart) inputTopicStart.value = '';
                if (inputTopicEnd) inputTopicEnd.value = '';
            }

            // 4. Pecah dan Set Nilai Range Flashcards
            const inputFlashStart = document.querySelector('input[name="flashcards_start"]');
            const inputFlashEnd = document.querySelector('input[name="flashcards_end"]');
            if (flashcardPage && flashcardPage.includes('-')) {
                const splitFlash = flashcardPage.split('-');
                if (inputFlashStart) inputFlashStart.value = splitFlash[0] || '';
                if (inputFlashEnd) inputFlashEnd.value = splitFlash[1] || '';
            } else {
                if (inputFlashStart) inputFlashStart.value = '';
                if (inputFlashEnd) inputFlashEnd.value = '';
            }

            // 5. Set Nilai Exercise/Supplement
            const inputExercise = document.querySelector('textarea[name="excerciseBook"]');
            if (inputExercise) inputExercise.value = (exercise && exercise !== '-') ? exercise : '';

            // 6. Set Nilai Class Activity
            const inputActivity = document.querySelector('textarea[name="activity_class"]');
            if (inputActivity) inputActivity.value = (activity && activity !== '-') ? activity : '';

            // TAMPILKAN PEMBERITAHUAN (TOAST SUKSES)
            showToastNotification("Already copied to the agenda fields!");
        });
    });

    // Fungsi untuk membuat Alert Toast melayang di pojok layar
    function showToastNotification(message) {
        // Cek apakah elemen toast sudah ada, kalau belum kita buat container-nya
        let toastContainer = document.getElementById('toast-container-materi');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container-materi';
            // Styling agar melayang di pojok kanan atas layar
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Membuat box notifikasi
        const toast = document.createElement('div');
        toast.className = 'alert alert-success shadow-lg text-sm d-flex align-items-center';
        toast.style.minWidth = '280px';
        toast.style.marginBottom = '10px';
        toast.style.transition = 'all 0.4s ease';
        toast.style.opacity = '0';
        toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i> <span>${message}</span>`;

        toastContainer.appendChild(toast);

        // Fade in
        setTimeout(() => {
            toast.style.opacity = '1';
        }, 10);

        // Fade out dan hapus otomatis setelah 2.5 detik
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 2500);
    }
</script>

</div>
<script>
    $(document).ready(function() {
        $('#categories' + i).val(0);
        var dataCtgr = JSON.parse('{!! $pointCategories !!}');
        var len = $('.cekBox').length;

        for (let i = 1; i <= len; i++) {
            var pointDay = Number('{{ (in_array(request("day1"), [5, 6]) || in_array(request("day2"), [5, 6]) || request("day1") == request("day2")) ? 20 : 10 }}');

            var birthDayPoint = 0;

            var totalPoint = Number($("#totalPoint" + i).text()) || 0;

            $('#cbAbsent' + i).on('change', function() {
                var conditionPoint = pointDay;
                var tmpTotalPoint = 0;

                var getVal = $('#categories' + i).val() || [];
                dataCtgr.forEach(element => {
                    getVal.forEach(x => {
                        if (element.id.toString() === x.toString()) {
                            tmpTotalPoint += Number(element.point) || 0;
                        }
                    });
                });

                if ($(this).is(':checked')) {
                    $("#inPointAbsent" + i).text(Number(conditionPoint) || 0);
                    $("#isAbsentPoint" + i).val(Number(conditionPoint) || 0);

                    $("#totalPoint" + i).text(tmpTotalPoint + birthDayPoint + (Number($(
                        "#inPointAbsent" + i).text()) || 0));
                    $("#inpTotalPoint" + i).val(tmpTotalPoint + birthDayPoint + (Number($(
                        "#inPointAbsent" + i).text()) || 0));

                    // Reset alpha dan permission
                    $('#permissionCheckBox' + (i - 1)).next('span').removeClass('checked').val(0).prop(
                        'checked', false);
                    $('#alphaCheckBox' + (i - 1)).next('span').removeClass('checked').val(0).prop(
                        'checked', false);

                } else {
                    $("#inPointAbsent" + i).text(0);
                    $("#isAbsentPoint" + i).val(0);

                    $("#totalPoint" + i).text(tmpTotalPoint + birthDayPoint + (Number($(
                        "#inPointAbsent" + i).text()) || 0));
                    $("#inpTotalPoint" + i).val(tmpTotalPoint + birthDayPoint + (Number($(
                        "#inPointAbsent" + i).text()) || 0));
                }

                // Update cekAllAbsen
                $('#cekAllAbsen').val($('.cekBox:checked').length > 0 ? 1 : 0);
            });

            $('#categories' + i).change(function() {
                var tmpTotalPoint = 0;

                var getVal = $('#categories' + i).val() || [];
                dataCtgr.forEach(element => {
                    getVal.forEach(x => {
                        if (element.id.toString() === x.toString()) {
                            tmpTotalPoint += Number(element.point) || 0;
                        }
                    });
                });

                $("#totalPoint" + i).text(tmpTotalPoint + birthDayPoint + (Number($("#inPointAbsent" +
                    i).text()) || 0));
                $("#inpTotalPoint" + i).val(tmpTotalPoint + birthDayPoint + (Number($("#inPointAbsent" +
                    i).text()) || 0));
                console.log(tmpTotalPoint);
            });
        }



    });

    function filter() {
        var day = $('#day').val();
        var time = $('#time').val();
        // var ampm = $('#ampm').val();
        if (day == '') {
            alert("Day is required")
        } else if (time == '') {
            alert("Time is required")
        }
        // else if (ampm == '') {
        //     alert("AM/PM is required")
        // }
        else {
            window.location.href = "{{ url('/attendance/form/' . $data->id) }}?day=" + day + "&time=" + time;
        }
    }

    function permission(key) {
        if ($('#permissionCheckBox' + key).val() == 0) {
            $('#permissionCheckBox' + key).val(1);
            $('#permissionCheckBox' + key).attr('checked', 'checked');

            $('#permissionCheckBox' + key).next('span').addClass('checked');


            $('#alphaCheckBox' + key).val(0);
            $('#alphaCheckBox' + key).removeAttr('checked');
            $('#alphaCheckBox' + key).next('span').removeClass('checked');

            $('#cbAbsent' + (parseInt(key) + 1)).prop('checked', false);
            $('#cbAbsent' + (parseInt(key) + 1)).trigger('change');

        } else {
            $('#permissionCheckBox' + key).val(0);
            $('#permissionCheckBox' + key).removeAttr('checked');

            $('#permissionCheckBox' + key).next('span').removeClass('checked');
        }
        if ($('.cekBoxPermission:checked').length != 0) {
            $('#cekAllAbsen').val(1);
        } else {
            $('#cekAllAbsen').val(0);
        }
    }

    function alpha(key) {
        if ($('#alphaCheckBox' + key).val() == 0) {
            $('#alphaCheckBox' + key).val(1);
            $('#alphaCheckBox' + key).attr('checked', 'checked');

            $('#alphaCheckBox' + key).next('span').addClass('checked');

            $('#permissionCheckBox' + key).val(0);
            $('#permissionCheckBox' + key).removeAttr('checked');
            $('#permissionCheckBox' + key).next('span').removeClass('checked');

            $('#cbAbsent' + (parseInt(key) + 1)).prop('checked', false);
            $('#cbAbsent' + (parseInt(key) + 1)).trigger('change');

        } else {
            $('#alphaCheckBox' + key).val(0);
            $('#alphaCheckBox' + key).removeAttr('checked');

            $('#alphaCheckBox' + key).next('span').removeClass('checked');
        }

        if ($('.cekBoxAlpha:checked').length != 0) {
            $('#cekAllAbsen').val(1);
        } else {
            $('#cekAllAbsen').val(0);
        }
    }

    // function confirm_update() {
    //     swal("Are you sure ?", "Data will be updated", {
    //         icon: "info",
    //         buttons: {
    //             confirm: {
    //                 className: 'btn btn-success',
    //                 text: 'Ok'
    //             },
    //             dismiss: {
    //                 className: 'btn btn-secondary',
    //                 text: 'Cancel'
    //             },
    //         },
    //     }).then((result) => {
    //         /* Read more about isConfirmed, isDenied below */
    //         if (result == true) {
    //             $('#form-submit').submit();
    //         }
    //     });
    //     $('#form-submit').submit(function() {
    //         $('#btn-success').addClass('disabled');
    //     });
    // }

    // function confirm_submit() {
    //     swal("Are you sure ?", "Data will be submitted", {
    //         icon: "info",
    //         buttons: {
    //             confirm: {
    //                 className: 'btn btn-success',
    //                 text: 'Ok'
    //             },
    //             dismiss: {
    //                 className: 'btn btn-secondary',
    //                 text: 'Cancel'
    //             },
    //         },
    //     }).then((result) => {
    //         /* Read more about isConfirmed, isDenied below */
    //         if (result == true) {
    //             $('#form-submit').submit();
    //         }
    //     });
    //     $('#form-submit').submit(function() {
    //         $('#btn-success').addClass('disabled');
    //     });
    // }

    function confirm_update() {
        // 1. Dapatkan elemen form native DOM
        const form = $('#form-submit')[0];

        // 2. Lakukan validasi HTML5 bawaan browser
        // Jika form tidak valid (ada input 'required' yang kosong, dll.),
        // reportValidity() akan menampilkan pesan kesalahan browser.
        if (!form.checkValidity()) {
            form.reportValidity(); // Ini akan menampilkan pesan validasi
            return; // Hentikan fungsi jika form tidak valid
        }

        // 3. Jika form valid, tampilkan SweetAlert untuk konfirmasi update
        swal("Are you sure ?", "Data will be updated", {
            icon: "info",
            buttons: {
                confirm: {
                    className: 'btn btn-success',
                    text: 'Ok'
                },
                dismiss: { // Gunakan 'cancel' jika Anda menggunakan SweetAlert2
                    className: 'btn btn-secondary',
                    text: 'Cancel'
                },
            },
        }).then((result) => {
            // 4. Jika pengguna mengklik 'Ok' (confirm)
            if (result == true) {
                // Kirimkan formulir secara programatis
                // Form sudah divalidasi di awal, jadi aman untuk dikirim
                $('#form-submit').submit();
            }
            // Jika pengguna mengklik 'Cancel', tidak ada yang dilakukan
        });

        // Ini adalah handler yang akan berjalan setelah formulir benar-benar disubmit (setelah konfirmasi)
        $('#form-submit').submit(function() {
            $('#btn-success').addClass('disabled'); // Pastikan ID ini sesuai dengan tombol submit Anda
            // Anda juga bisa menonaktifkan semua tombol submit
            // $('button[type="submit"]').addClass('disabled');
        });
    }

    function confirm_submit() {
        // 1. Dapatkan elemen form native DOM
        // Menggunakan [0] untuk mendapatkan elemen DOM dari objek jQuery
        const form = $('#form-submit')[0];

        // 2. Lakukan validasi HTML5 bawaan browser
        // checkValidity() akan mengembalikan false jika ada input 'required' yang kosong atau invalid.
        // Jika tidak valid, reportValidity() akan menampilkan pesan error browser.
        if (!form.checkValidity()) {
            form.reportValidity(); // Ini akan menampilkan pesan validasi di browser
            return; // Hentikan fungsi jika form tidak valid
        }

        // 3. Jika form valid, tampilkan SweetAlert untuk konfirmasi
        swal("Are you sure ?", "Data will be submitted", {
            icon: "info",
            buttons: {
                confirm: {
                    className: 'btn btn-success',
                    text: 'Ok'
                },
                dismiss: { // Gunakan 'cancel' jika Anda menggunakan SweetAlert2
                    className: 'btn btn-secondary',
                    text: 'Cancel'
                },
            },
        }).then((result) => {
            // 4. Jika pengguna mengklik 'Ok' (confirm)
            if (result == true) {
                // Kirimkan formulir secara programatis
                // Kali ini, form sudah divalidasi, jadi aman untuk dikirim
                $('#form-submit').submit();
            }
            // Jika pengguna mengklik 'Cancel', tidak ada yang dilakukan
        });

        // Ini adalah handler yang akan berjalan setelah formulir berhasil disubmit
        // Baik melalui tombol submit normal atau $('#form-submit').submit() di atas
        $('#form-submit').submit(function() {
            $('#btn-success').addClass('disabled'); // Pastikan ID ini sesuai dengan tombol submit Anda
            // Anda juga bisa menonaktifkan semua tombol submit untuk mencegah klik ganda
            // $('button[type="submit"]').addClass('disabled');
        });
    }
</script>


@endsection

@push('js')
<script>
    const allAttendanceData = <?php echo isset($all_attendence) ? json_encode($all_attendence) : 'null'; ?>;
    const students = <?php echo json_encode($student); ?>;

    $('#exampleModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const modalType = button.data('modal-type');

        const attendanceTab = $('#nav-attendance-tab');
        const agendaTab = $('#nav-agenda-tab');
        const attendancePane = $('#nav-attendance');
        const agendaPane = $('#nav-agenda');

        attendancePane.removeClass('show active');
        agendaPane.removeClass('show active');
        attendanceTab.removeClass('active');
        agendaTab.removeClass('active');

        if (modalType === 'attendance') {
            attendanceTab.tab('show');
            attendancePane.addClass('show active');

            const combinedAttendanceDetailBody = $('#combinedAttendanceDetailBody');
            combinedAttendanceDetailBody.empty();

            if (allAttendanceData.length > 0 && students.length > 0) {
                const combinedAttendanceDetailBody = $('#combinedAttendanceDetailBody');
                combinedAttendanceDetailBody.empty();

                // 1. Ambil tanggal unik
                const uniqueDates = [...new Set(allAttendanceData.map(a => {
                    return new Date(a.date).toISOString().split('T')[0];
                }))];

                // Buat header
                let tableHTML = '<table class="table table-bordered">';
                tableHTML += '<thead class="bg-info text-white rowheaders"><tr><th>Name</th>';
                uniqueDates.forEach(date => {
                    tableHTML +=
                        `<th class=" text-white">${new Date(date).toLocaleDateString()}</th>`;
                });
                tableHTML += '</tr></thead><tbody>';

                // 2. Loop setiap siswa
                students.forEach(student => {
                    tableHTML += `<tr><td scope="row">${student.name}</td>`;

                    // 3. Loop per tanggal
                    uniqueDates.forEach(date => {
                        const attendanceOnDate = allAttendanceData.find(att => {
                            return new Date(att.date).toISOString().split('T')[
                                0] === date;
                        });

                        let statusCell = '<td></td>';
                        if (attendanceOnDate) {
                            const detail = attendanceOnDate.detail.find(d => d.student_id ==
                                student.id);
                            if (detail) {
                                if (detail.is_absent == '1') {
                                    statusCell = '<td><span class="fa fa-check"></span></td>';
                                } else if (detail.is_permission == '1') {
                                    statusCell = "<td bgcolor='green'></td>";
                                } else if (detail.is_alpha == '1') {
                                    statusCell = "<td bgcolor='red'></td>";
                                }
                            }
                        }
                        tableHTML += statusCell;
                    });

                    tableHTML += '</tr>';
                });

                tableHTML += '</tbody></table>';
                combinedAttendanceDetailBody.append(tableHTML);
            }

        }
        const agendaListContainer = $('#agendaList');
        agendaListContainer.empty();

        if (allAttendanceData.length > 0) {
            allAttendanceData.forEach((agenda, i) => {
                const agendaHTML = `
            <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="agenda-card">
                    <p class="text-center bg-info"><strong>${agenda.date ? formatTanggalIndo(agenda.date) : '-'}</strong></p>
                    <p>${agenda.activity || '-'}</p>
                    <p><strong>Text Book:</strong> ${agenda.text_book || '-'}</p>
                    <p><strong>Exercise Book:</strong> ${agenda.excercise_book || '-'}</p>
                    <p><strong>Flashcard Page:</strong> ${agenda.flashcard_page || '-'}</p>
                    <p><strong>Activity Class:</strong> ${agenda.activity_class || '-'}</p>
                    <p><strong>Topic Page:</strong> ${agenda.topic_page || '-'}</p>
                </div>
            </div>
        `;
                agendaListContainer.append(agendaHTML);
            });
        } else {
            agendaListContainer.html('<div class="col-12"><p>Tidak ada agenda untuk tanggal ini.</p></div>');
        }



    });

    function formatTanggalIndo(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    }

    function updateDateRequiredStatus() {
        let hasValueAfterInteraction = false; // Flag untuk mengecek apakah ada input 'id_test[]' yang memiliki nilai

        // Loop melalui semua input dengan name 'id_test[]'
        $('input[name="id_test[]"]').each(function() {
            if ($(this).val() !==
                '') { // MEMERIKSA: Apakah input id_test[] saat ini memiliki nilai (tidak kosong)?
                hasValueAfterInteraction = true; // Jika ya, set flag menjadi true
                return false; // Hentikan loop karena kita sudah menemukan setidaknya satu nilai
            }
        });

        // Anda bisa menghapus bagian ini jika tidak lagi perlu mencetak semua nilai di konsol
        // let allIdTestValues = [];
        // $('input[name="id_test[]"]').each(function() {
        //     allIdTestValues.push($(this).val());
        // });
        // console.log("Current id_test[] values:", allIdTestValues);


        if (hasValueAfterInteraction) { // KONDISI: JIKA ada 'id_test[]' yang memiliki nilai (setelah interaksi pengguna)
            // Maka, buat date_review dan date_test menjadi required
            $('input[name="date_review"]').prop('required', true);
            $('input[name="date_test"]').prop('required', true);
            console.log("id_test[] ada nilai setelah interaksi. date_review & date_test: REQUIRED.");
        } else { // KONDISI: JIKA TIDAK ADA 'id_test[]' yang memiliki nilai (setelah interaksi pengguna)
            // Maka, buat date_review dan date_test TIDAK required
            $('input[name="date_review"]').prop('required', false);
            $('input[name="date_test"]').prop('required', false);
            console.log("id_test[] kosong setelah interaksi. date_review & date_test: TIDAK REQUIRED.");
        }
    }

    $(document).ready(function() {
        // *** PENTING: HAPUS BARIS INI ***
        // updateDateRequiredStatus(); // Ini yang membuat required aktif saat page load, meskipun tidak ada interaksi

        // Jalankan fungsi updateDateRequiredStatus hanya ketika ada perubahan (interaksi) pada input 'id_test[]'
        $('input[name="id_test[]"]').on('change keyup', function() {
            updateDateRequiredStatus();
        });

        // Opsional: Anda bisa tambahkan trigger on 'focus' jika Anda ingin required aktif bahkan hanya dengan mengklik/memfokuskan input tanpa mengubah nilainya.
        // Tapi berdasarkan penjelasan "ketika di klik baru required" dan "change keyup", ini mungkin sudah cukup.
        // $('input[name="id_test[]"]').on('focus', function() {
        //     // Anda mungkin perlu logika yang lebih halus di sini
        //     // atau simply memanggil updateDateRequiredStatus() jika fokus saja sudah cukup sebagai trigger.
        //     // Namun, biasanya 'change' dan 'keyup' lebih akurat untuk "memiliki value".
        // });
    });
</script>
@endpush