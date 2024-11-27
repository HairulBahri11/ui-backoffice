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
    </style>
    <div class="content">
        <div class="page-inner py-5 panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div class="">
                    <h2 class="text-white pb-2 fw-bold">Opening Balance</h2>
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
                            <a href="#" class="text-white">Opening Balance</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="page-inner mt--5">
            @if (session('status'))
                <script>
                    swal("Succes!", "{{ session('status') }}!", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-primary'
                            }
                        },
                    });
                </script>
            @endif
            <div class="row">

                <div class="col-md-12">
                    <form action="{{ url('/saldo-awal') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    {{ $data->type == 'create' ? 'Opening Balance' : 'Edit Opening Balance' }}</h4>
                            </div>
                            <input type="hidden" name="day1" value="{{ request()->get('day1') }}">
                            <input type="hidden" name="day2" value="{{ request()->get('day2') }}">
                            <input type="hidden" name="time" value="{{ request()->get('time') }}">
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
                                                        <th class="text-center">Opening Balance</th>
                                                        <th class="text-center"></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($student as $it)
                                                        <tr style="height: 40px!important">
                                                            <td class="text-center" style="">{{ $no }}
                                                            </td>
                                                            <td style="">{{ ucwords($it->name) }}</td>
                                                            <input type="hidden"
                                                                {{ $it->total_point != 0 ? 'readonly disabled' : '' }}
                                                                name="studentId[]" value="{{ $it->id }}">
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="saldo_awal[]" value="{{ $it->total_point }}"
                                                                    {{ $it->total_point != 0 ? 'readonly disabled' : '' }}>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)"
                                                                    class="btn btn-success btn-sm addPointButton"
                                                                    data-toggle="modal" data-target="#mdlTambahPoint"
                                                                    data-id="{{ $it->id }}"
                                                                    data-name="{{ ucwords($it->name) }}">Add
                                                                    Point</a>
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


                            </div>
                            <div class="card-action mt-3">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="button" data-toggle="modal" data-target="#mdlCancel"
                                    class="btn btn-danger">Cancel</button>
                            </div>
                            {{-- @endif --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal" id="mdlTambahPoint" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nameStudent">Add Point Student</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="submitPoint" action="{{ url('pemberian-saldo') }}" method="POST">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <label for="">Point</label>
                                <input type="number" class="form-control" name="addPoint" id="addPoint" required>
                                <input type="hidden" class="form-control" name="idStudent" id="idStudent" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="submitButton">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>

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
    </div>
    <script>
        $(document).ready(function() {
            $('#categories' + i).val(0);
            var dataCtgr = JSON.parse('{!! $pointCategories !!}');
            var len = $('.cekBox').length;

            for (let i = 1; i <= len; i++) {
                var pointDay = {{ Request::get('day1') }} == 5 || {{ Request::get('day1') }} == 6 ||
                    {{ Request::get('day2') }} == 5 || {{ Request::get('day2') }} == 6 ? 20 : 10;
                console.log(pointDay);
                var totalPoint = parseInt($("#totalPoint" + i).text());
                $('#cbAbsent' + i).click('change', function() {
                    if ($(this).is(':checked')) {
                        $("#inPointAbsent" + i).text(parseInt(pointDay));
                        $("#totalPoint" + i).text(parseInt($("#totalPoint" + i).text()) + pointDay);
                        $("#inpTotalPoint" + i).val(parseInt($("#inpTotalPoint" + i).val() != '' ? $(
                            "#inpTotalPoint" + i).val() : 0) + pointDay);
                    } else {
                        $("#totalPoint" + i).text(parseInt($("#totalPoint" + i).text()) - pointDay);
                        $("#inpTotalPoint" + i).val(parseInt($("#inpTotalPoint" + i).val() != '' ? $(
                            "#inpTotalPoint" + i).val() : 0) - pointDay);
                        $("#inPointAbsent" + i).text(0);
                    }
                });

                $('#categories' + i).change(function() {
                    var tmpTotalPoint = 0;
                    var getVal = $('#categories' + i).val();
                    dataCtgr.forEach(element => {
                        getVal.forEach(x => {
                            if (element.id.toString() == x.toString()) {
                                tmpTotalPoint += element.point;
                            }
                        })
                    });

                    $("#totalPoint" + i).text(tmpTotalPoint + parseInt($("#inPointAbsent" + i).text()));
                    $("#inpTotalPoint" + i).val(tmpTotalPoint + parseInt($("#inPointAbsent" + i).text()));

                });
                // $('#point_category' + i).keyup(function() {
                //     var tmpTotalPoint = 0;
                //     var point = isNaN(parseInt($('#point_category' + i).val())) == true ? 0 : parseInt($(
                //         '#point_category' + i).val());
                //     $("#totalPoint" + i).text(point + parseInt($("#inPointAbsent" + i).text()));
                //     $("#inpTotalPoint" + i).val(point + parseInt($("#inPointAbsent" + i).text()));

                // });
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

        $('.addPointButton').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#idStudent').val(id);
            $('#nameStudent').html('Add Point ' + name);
        });
    </script>

    <script></script>
@endsection
