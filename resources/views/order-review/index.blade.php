@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Order Review & Test Paper</h2>
                        {{-- <h5 class="text-white op-7 mb-2">Free Bootstrap 4 Admin Dashboard</h5> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            @if (session('status'))
                <script>
                    swal("Berhasil", "{{ session('status') }}!", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-success'
                            }
                        },
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    swal("Gagal", "{{ session('status') }}!", {
                        icon: "danger",
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
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">Data Order Review & Test Paper</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Auth::guard('teacher')->user() == null)
                                <form action="" method="GET">
                                    <div class="row mb-4 justify-content-end">
                                        <div class="col-md-3">
                                            <label for="">From Date</label>
                                            <input type="date" name="from" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="">To Date</label>
                                            <input type="date" name="to" class="form-control">
                                        </div>
                                        <div class="col-md-2 mt-4">
                                            <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i>
                                                Filter</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            @if (Auth::guard('teacher')->user() == null)
                                                <th>Teacher</th>
                                            @endif
                                            <th>Class</th>
                                            <th>Review / Test</th>
                                            <th>Due Date</th>
                                            <th>Time</th>
                                            <th>Date Time</th>
                                            <th>QTY</th>
                                            <th>Comment</th>
                                            @if (Auth::guard('teacher')->user() == null)
                                                <th>Confirm</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            @php
                                                $class = explode(' ', $item->class);
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                @if (Auth::guard('teacher')->user() == null)
                                                    <td>{{ $item->teacher->name }}</td>
                                                @endif
                                                <td>{{ !empty($class[5]) ? (!empty($class[6]) ? $class[0] . ' ' . $class[1] . ' ' . $class[2] . ' ' . $class[3] . ' ' . $class[4] : $class[0] . ' ' . $class[1] . ' ' . $class[2] . ' ' . $class[3]) : $class[0] . ' ' . $class[1] . ' ' . $class[2] }}
                                                </td>
                                                <td>{{ $item->review_test }}</td>
                                                <td>{{ Carbon\Carbon::parse($item->due_date)->format('d M Y') }}</td>
                                                <td>{{ !empty($class[5]) ? (!empty($class[6]) ? $class[6] : $class[5]) : $class[4] }}
                                                </td>
                                                <td> {{ Carbon\Carbon::parse($item->created_at)->format('d M Y h:i A') }}
                                                </td>
                                                <td>{{ $item->qty }}</td>
                                                <td>{{ $item->comment }}</td>

                                                @if (Auth::guard('teacher')->user() == null)
                                                    <td>
                                                        @if ($item->is_done == 0)
                                                            <a href="javascript:void(0)"
                                                                onclick="confirm({{ $item->id }})"
                                                                class="btn btn-sm btn-success">Done</a>
                                                            <br>
                                                        @endif
                                                        <a href="javascript:void(0)" data-toggle="modal"
                                                            data-target="#exampleModal" data-id="{{ $item->id }}"
                                                            data-class="{{ $item->class }}"
                                                            data-teacher="{{ $item->teacher->name }}"
                                                            class="btn btn-sm btn-primary modalAction">Add Comment</a>

                                                        {{-- @if ($item->is_done == 1)
                                                            <form action="{{ url('review') . '/' . $item->id }}"
                                                                method="POST" class="form-inline"
                                                                id="deleteOrderView{{ $item->id }}">
                                                                @method('delete')
                                                                @csrf
                                                            </form>
                                                            <a href="javascript:void(0)"
                                                                onclick="deleted({{ $item->id }})"
                                                                class="btn btn-sm btn-danger">Delete</a>
                                                        @endif --}}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                <form action="" id="formModal" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Comment</label>
                            <input type="text" class="form-control comment" id="comment" name="comment" value=""
                                required>
                            <small class="form-text text-danger error"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="add-button">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('.modalAction').click(function() {
            var id = $(this).data('id');
            var teacher = $(this).data('teacher');
            var clas = $(this).data('class');
            $('#exampleModalLabel').html('Comment class ' + clas + ' for ' + capitalize(teacher));
            $('#formModal').attr('action', "{{ url('review-comment') }}/" + id);

        });

        function capitalize(str) {
            strVal = '';
            str = str.split(' ');
            for (var chr = 0; chr < str.length; chr++) {
                strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
            }
            return strVal
        }

        function confirm(id) {
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
                    window.location = "{{ url('review-done') . '/' }}" + id
                }
            });
        }

        function deleted(id) {
            swal("Are you sure ?", "Data will be deleted", {
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
                    $('#deleteOrderView' + id).submit();
                }
            });
        }
    </script>
@endsection
