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
                            <h4 class="card-title">Schedule Class</h4>

                        </div>
                        <div class="card-body">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Class</label>
                                        <select name="class" id="" class="form-control select2" required>
                                            <option value="">---Select Class---</option>
                                            @foreach ($class as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ Request::get('class') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->program }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Teacher</label>
                                        <select name="teacher" id="" class="form-control select2" required>
                                            <option value="">---Select Teacher---</option>
                                            @foreach ($teacher as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ Request::get('teacher') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Day 1</label>
                                        <select name="day1" id="" class="form-control select2" required>
                                            <option value="">---Select Day---</option>
                                            @foreach ($day as $itema)
                                                <option
                                                    value="{{ $itema->id }}"{{ Request::get('day1') == $itema->id ? 'selected' : '' }}>
                                                    {{ $itema->day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Day 2</label>
                                        <select name="day2" id="" class="form-control select2">
                                            <option value="">---Select Day---</option>
                                            @foreach ($day as $itema)
                                                <option
                                                    value="{{ $itema->id }}"{{ Request::get('day2') == $itema->id ? 'selected' : '' }}>
                                                    {{ $itema->day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="">Time</label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="time" class="form-control" name="time"
                                                    value="{{ Request::get('time') ? Request::get('time') : '' }}"
                                                    required>
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <select name="ampm" id="" class="form-control select2" required>
                                                    <option value="AM"
                                                        {{ Request::get('ampm') == 'AM' ? 'selected' : '' }}>AM</option>
                                                    <option value="PM"
                                                        {{ Request::get('ampm') == 'PM' ? 'selected' : '' }}>PM</option>
                                                </select> --}}
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="">Staff</label>
                                        <select name="staff" id="" class="form-control select2">
                                            <option value="">---Select Staff---</option>
                                            @foreach ($staff as $items)
                                                <option value="{{ $items->id }}"
                                                    {{ Request::get('staff') == $items->id ? 'selected' : '' }}>
                                                    {{ $items->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 radioInput" style="text-align: center">
                                        <label for="">1 Hour</label>
                                        <input type="radio" id="" name="course_hour" value="1"
                                            class="form-control" style="margin-top:15px;"
                                            {{ Request::get('course_hour') == 1 ? 'checked' : '' }}>
                                    </div>
                                    <div class="col-md-2 radioInput" style="text-align: center">
                                        <label for="">2 Hour</label>
                                        <input type="radio" id="" name="course_hour" value="2"
                                            class="form-control" style="margin-top:15px;"
                                            {{ Request::get('course_hour') == 2 ? 'checked' : '' }}>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i>
                                            Filter</button>
                                    </div>
                                </div>
                        </div>
                        </form>
                    </div>
                    <hr>
                    @if (Request::get('teacher') && Request::get('class') && Request::get('time'))
                        <form action="{{ url('/schedule-class') }}" method="POST">
                            @csrf
                            <input type="hidden" name="class" value="{{ Request::get('class') }}">
                            <input type="hidden" name="teacher" value="{{ Request::get('teacher') }}">
                            <input type="hidden" name="staff" value="{{ Request::get('staff') }}">
                            <input type="hidden" name="day1" value="{{ Request::get('day1') }}">
                            <input type="hidden" name="day2" value="{{ Request::get('day2') }}">
                            <input type="hidden" name="time" value="{{ Request::get('time') }}">
                            <input type="hidden" name="day2" value="{{ Request::get('day2') }}">
                            <input type="hidden" name="course_time" value="{{ Request::get('course_hour') }}">
                            <div class="card-body">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="10px"><input type="checkbox"
                                                                id="checkAll">
                                                        </th>
                                                        <th class="text-center">Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $key = 0;
                                                    @endphp
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            <td>
                                                                @if (Request::get('day2'))
                                                                    <input type="checkbox" name="upcls[]"
                                                                        class="updateCheck" id="upCls{{ $key++ }}"
                                                                        value="{{ $item->id }}"
                                                                        {{ Request::get('day1') == $item->day1 && Request::get('day2') == $item->day2 && Request::get('teacher') == $item->id_teacher && Request::get('time') == $item->course_time ? 'checked' : '' }}>
                                                                @else
                                                                    <input type="checkbox" name="upcls[]"
                                                                        class="updateCheck" id="upCls{{ $key++ }}"
                                                                        value="{{ $item->id }}"
                                                                        {{ Request::get('day1') == $item->day1 && Request::get('teacher') == $item->id_teacher && Request::get('time') == $item->course_time ? 'checked' : '' }}>
                                                                @endif
                                                            </td>
                                                            <td
                                                                style="{{ $item->day1 != null && $item->day2 != null && $item->id_teacher != null && $item->course_time != null ? 'color:red' : '' }}">
                                                                {{ ucwords($item->name) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-action mt-3">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>



    </div>
    </div>
    <script>
        console.log("{{ Request::get('class') }}");
        if ("{{ Request::get('class') == 39 || Request::get('class') == 42 }}") {
            $('.radioInput').show()
        } else {
            $('.radioInput').hide()
        }
        $('#checkAll').change(function() {
            if ($(this).is(':checked')) {
                $('.updateCheck').prop('checked', true);
            } else {
                $('.updateCheck').prop('checked', false);
            }
        });

        $('select[name="class"]').change(function() {
            console.log($(this).val());
            var id = $(this).val();
            if (id == 42 || id == 39) {
                $('.radioInput').show()
            } else {
                $('.radioInput').hide()

            }

        });
    </script>
@endsection
