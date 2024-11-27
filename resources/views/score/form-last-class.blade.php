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
                    <h2 class="text-white pb-2 fw-bold">Score</h2>
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
                            <a href="#" class="text-white">Score</a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">List Student</a>
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
            @if (session('success'))
                <script>
                    swal("Success!", "{{ session('success') }}!", {
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
                            <h4 class="card-title">List Student</h4>
                        </div>
                        <div class="card-body">

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @foreach ($students as $key => $item)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td><a href="{{ url('score/create?type=create&class=') . $item->priceid . '&student=' . $item->id . '&test=' . 1 . '&day1=' . $item->day1 . '&day2=' . $item->day2 . '&teacher=' . $item->id_teacher . '&time=' . $item->course_time }}"
                                                                class="btn btn-primary btn-sm modalGetClass"
                                                                data-toggle="modal" data-target="#chooseScoreModal"
                                                                data-day1="{{ $item->day1 }}"
                                                                data-day2="{{ $item->day2 }}"
                                                                data-class="{{ $item->priceid }}"
                                                                data-teacher="{{ $item->id_teacher }}"
                                                                data-id="{{ $item->id }}"
                                                                data-time="{{ $item->course_time }}">Add Score</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="chooseScoreModal" tabindex="-1" aria-labelledby="chooseScoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chooseScoreModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Class</label>
                        <input type="text" id="id" value="0">
                        <input type="text" id="day1" value="0">
                        <input type="text" id="day2" value="0">
                        <input type="text" id="teacher" value="0">
                        <input type="text" id="time" value="0">
                        <select name="class" id="priceid" class="form-control select2 select2-hidden-accessible"
                            style="width:100%;">
                            <option value="">---Choose Class---</option>
                        </select>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.modalGetClass').click(function() {
            var id = $(this).data('id');
            var day1 = $(this).data('day1');
            var day2 = $(this).data('day2');
            var teacher = $(this).data('teacher');
            var time = $(this).data('time');
            $('#id').val(id);
            $('#day1').val(day1);
            $('#day2').val(day2);
            $('#teacher').val(teacher);
            $('#time').val(time);
        });
    </script>
@endsection
