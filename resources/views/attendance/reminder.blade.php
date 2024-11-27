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
                    <h2 class="text-white pb-2 fw-bold">Absence Reminder</h2>
                    <ul class="breadcrumbs">
                        <li class="nav-home text-white">
                            <a href="#">
                                <i class="flaticon-home text-white"></i>
                            </a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        {{-- <li class="nav-item text-white">
                            <a href="#" class="text-white">Attendance</a>
                        </li> --}}
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">Absence Reminder</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="page-inner mt--5">
            @if (session('message'))
                <script>
                    swal("Success!", "{{ session('message') }}!", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-success',
                            }
                        },
                    });
                </script>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Absence Reminder</h4>
                             @if (Auth::guard('staff')->user() != null)
                                <span class="text-danger fw-bold"> (Filter data first to view data!!!)</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="email2">Level</label>
                                                <select class="form-control select2 select2-hidden-accessible"
                                                    style="width:100%;" name="level" id="level">
                                                    <option value="">Select Level</option>
                                                    @foreach ($class as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ Request::get('level') == $item->id ? 'selected' : '' }}>
                                                            {{ $item->program }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if (Auth::guard('staff')->user() != null)
                                                <div class="col-md-5">
                                                    <label for="email2">Teacher</label>
                                                    <select class="form-control select2 select2-hidden-accessible"
                                                        style="width:100%;" name="teacher" id="teacher">
                                                        <option value="">Select Teacher</option>
                                                        @foreach ($teachers as $item1)
                                                            <option value="{{ $item1->id }}"
                                                                {{ Request::get('teacher') == $item1->id ? 'selected' : '' }}>
                                                                {{ $item1->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="col-md-2" style="margin-top:20px">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i>
                                                    Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="">
                                        {{-- @if (Request::get('student')) --}}
                                        <table id="basic-datatables"
                                            class="table table-sm display table table-striped table-hover table-head-bg-info">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">Teacher</th>
                                                    <th class="text-center">Level</th>
                                                    <th class="text-center">Teacher's Comment</th>
                                                    <th class="text-center">Staff's Comment</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @foreach ($data as $item)
                                                    <tr>
                                                        {{--<td>{{ $loop->iteration }}</td>--}}
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ ucwords($item->name) }}</td>
                                                        <td>{{ $item->teacher != null ? $item->teacher : '-' }}</td>
                                                        <td>{{ $item->program }}</td>
                                                        <td>{{ $item->comment_teacher != null ? $item->comment_teacher : '-' }}
                                                        </td>
                                                        <td>{{ $item->comment_staff != null ? $item->comment_staff : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $item->absent_date }}
                                                            
                                                            {{-- @foreach ($item as $key => $value)
                                                                {{ $value->date }}{{ $loop->last ? '' : ',' }}
                                                            @endforeach --}}
                                                            {{-- {{ $item->date != null ? $item->date : '-' }},
                                                            {{ $item->date != null ? $item->date : '-' }} --}}
                                                        </td>
                                                        {{-- <td>{{ $item->program }}</td> --}}
                                                        <td>
                                                            @if ($item->is_done == true)
                                                                <input type="checkbox" class="custom-checkbox-size" checked
                                                                    style="pointer-events:none">
                                                                <!--<a href="{{ url('attendance/reminder-absen') . '?student=' . $item->student_id }}"
                                                                    class="btn btn-sm btn-danger text-white">Hapus</a>-->
                                                                <a href="{{ url('attendance/reminder-absen') . '?student=' . $item->absent_id }}"
                                                                    class="btn btn-sm btn-danger text-white">Hapus</a>
                                                            @else
                                                                <!--<a href="{{ url('attendance/reminder-done') . '?student=' . $item->student_id }}"
                                                                    class="btn btn-sm btn-primary text-white">Done</a>-->
                                                                <a href="{{ url('attendance/reminder-done') . '?student=' . $item->absent_id }}"
                                                                    class="btn btn-sm btn-primary text-white">Done</a>
                                                                <!--<a href="javascript:void(0)"
                                                                    data-tipe="{{ Auth::guard('staff')->user() != null ? 'staff' : 'teacher' }}"
                                                                    data-id="{{ $item->id }}"
                                                                    data-name="{{ ucwords($item->name) }}"
                                                                    class="btn btn-sm btn-success text-white modalAction"
                                                                    data-toggle="modal"
                                                                    data-target="#exampleModal">Comment</a>-->
                                                                <a href="javascript:void(0)"
                                                                    data-tipe="{{ Auth::guard('staff')->user() != null ? 'staff' : 'teacher' }}"
                                                                    data-id="{{ $item->absent_id }}"
                                                                    data-name="{{ ucwords($item->name) }}"
                                                                    class="btn btn-sm btn-success text-white modalAction"
                                                                    data-toggle="modal"
                                                                    data-target="#exampleModal">Comment</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                        {{-- <div class="pull-right">
                                            @php
                                                $page = Request::get('page') != null ? Request::get('page') : 1;
                                                $next = $page + 1;
                                                $previous = $page - 1;
                                            @endphp
                                            @if (Request::get('page') != 1 && Request::get('page') != null)
                                                <a href="{{ url('attendance/reminder?page=') . $previous }}"
                                                    class="btn btn-sm btn-secondary">
                                                    < Previous</a>
                                            @endif
                                            @if (Request::get('page') < $totalPages || Request::get('page') == null || Request::get('page') == 1)
                                                <a href="{{ url('attendance/reminder?page=') . $next }}"
                                                    class="btn btn-sm btn-secondary">
                                                    Next ></a>
                                            @endif
                                        </div> --}}
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>
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
            <form id="formModal" method="POST">
                @csrf
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
                                    <label for="email2">Comment</label>
                                    <textarea name="comment" id="" cols="30" rows="10" class="form-control" placeholder="Comment"
                                        required></textarea>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function capitalize(str) {
            strVal = '';
            str = str.split(' ');
            for (var chr = 0; chr < str.length; chr++) {
                strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
            }
            return strVal
        }
        $('.modalAction').click(function() {
            var id = $(this).data('id');
            var tipe = $(this).data('tipe');
            var name = $(this).data('name');
            $('#exampleModalLabel').html('Comment student ' + name + ' for ' + capitalize(tipe));
            $('#formModal').attr('action', "{{ url('attendance/reminder-comment/') }}/" + id + '?type=' + tipe);
            console.log($(this).data('id'));
            console.log($(this).data('tipe'));

        });
    </script>
@endsection
