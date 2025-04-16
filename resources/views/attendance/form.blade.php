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
                        <button type="button" class="btn btn-sm btn-warning mb-2" data-toggle="modal"
                            data-target="#exampleModal" data-modal-type="attendance">
                            <span class="text-bold">Detail Attendence and Agenda</span>
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
                                    @foreach ($student as $item)
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
                                                        //     $agenda = $agenda->where('student_id', $it->id);
                                                        // } else {
                                                        // $agenda = $agenda->orWhereRaw('(' . $whereRaw . ')');
                                                        // $agenda = $agenda->orWhere('student_id', $it->id);
                                                        // }
                                                        $birthDayPoint = 0;
                                                        if ($it->birthday == date('M d')) {
                                                            $birthDayPoint = 30;
                                                        }
                                                    @endphp
                                                    <tr style="height: 40px!important">
                                                        <td class="text-center" style="">{{ $no }}
                                                        </td>
                                                        <td style="">{{ ucwords($it->name) }}
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
                                                                    //     $isChecked = true;
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
                                                            <div class="row">
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
                                                                {{ $totalPoint }}</h5>
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
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Topic</label>
                                        <textarea name="comment" class="form-control" id="" cols="30" rows="3">{{ $data->type == 'update' ? $data->comment : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Text Book</label>
                                        <input type="text" class="form-control"
                                            value="{{ $data->type == 'update' ? $data->textBook : '' }}" name="textBook">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Excercise Book</label>
                                        <input type="text" class="form-control"
                                            value="{{ $data->type == 'update' ? $data->excerciseBook : '' }}"
                                            name="excerciseBook">

                                    </div>

                                </div>
                            </div>
                            {{-- <div class="row"> --}}
                            {{-- <div class="col-md-6"> --}}
                            <div class="form-group">
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
                                                    ->first();
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
                                                    <br>Excercise Book :
                                                    {{ $item->excercise_book != null ? $item->excercise_book : '-' }}
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
                        <h5 class="modal-title" id="combinedDetailModalLabel">Detail Attendance and Agenda</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <nav>
                            <div class="nav nav-tabs mb-4" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-attendance-tab" data-toggle="tab"
                                    data-target="#nav-attendance" type="button" role="tab"
                                    aria-controls="nav-attendance" aria-selected="true">Detail Kehadiran</button>
                                <button class="nav-link" id="nav-agenda-tab" data-toggle="tab" data-target="#nav-agenda"
                                    type="button" role="tab" aria-controls="nav-agenda"
                                    aria-selected="false">Detail Agenda</button>
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

    </div>
    <script>
        $(document).ready(function() {
            $('#categories' + i).val(0);
            var dataCtgr = JSON.parse('{!! $pointCategories !!}');
            var len = $('.cekBox').length;

            for (let i = 1; i <= len; i++) {
                var pointDay = (parseInt({{ Request::get('day1') }}, 10) === 5 ||
                        parseInt({{ Request::get('day1') }}, 10) === 6 ||
                        parseInt({{ Request::get('day2') }}, 10) === 5 ||
                        parseInt({{ Request::get('day2') }}, 10) === 6 ||
                        parseInt({{ Request::get('day1') }}, 10) === parseInt({{ Request::get('day2') }}, 10)) ?
                    20 : 10;

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

        function confirm_update() {
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
                    $('#form-submit').submit();
                }
            });
            $('#form-submit').submit(function() {
                $('#btn-success').addClass('disabled');
            });
        }

        function confirm_submit() {
            swal("Are you sure ?", "Data will be submitted", {
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
                    $('#form-submit').submit();
                }
            });
            $('#form-submit').submit(function() {
                $('#btn-success').addClass('disabled');
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
                    <p><strong>Excercise Book:</strong> ${agenda.excercise_book || '-'}</p>
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
    </script>
@endpush
