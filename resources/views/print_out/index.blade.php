@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Print Out</h2>
                    <h5 class="text-white op-7-5">Document generation and printing system menu</h5>
                </div>

                <!-- add button -->
                @if(Auth::guard('teacher')->check() == true)
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="{{ url('/print-out/create') }}" class="btn btn-secondary btn-round">Add Data</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        @if (session('success'))
        <script>
            swal("Success", "{{ session('success') }}!", {
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
            swal("Error", "{{ session('error') }}!", {
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
                        <h4 class="card-title">Documents Ready to Print</h4>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Teacher</th>
                                        <th>Class & Schedule</th>
                                        <th>Notes</th>
                                        <th>Created On</th>
                                        <th class="text-center" style="width: 15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    $day = [
                                    1 => 'Monday',
                                    2 => 'Tuesday',
                                    3 => 'Wednesday',
                                    4 => 'Thursday',
                                    5 => 'Friday',
                                    6 => 'Saturday',
                                    7 => 'Sunday'
                                    ];
                                    @endphp
                                    @foreach ($printOut as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->teacher->name ?? $item->teacher_id }}</td>
                                        <td>
                                            {{ $item->price->program ?? $item->class ?? $item->class_id }} <br>
                                            <small class="text-muted">
                                                {{ $day[$item->day1_id] ?? ' - ' }} &
                                                {{ $day[$item->day2_id] ?? ' - ' }}
                                                ({{ $item->course_time }})
                                            </small>
                                        </td>
                                        <td class="text-muted">{{ $item->note ?? '-' }}</td>
                                        <td>{{ now()->parse($item->created_at)->format('d M Y h:i A') }}</td>
                                        <td class="d-flex flex-row justify-content-center align-items-center">
                                            <a href="{{ url( $item->file_link) }}" target="_blank" class="btn btn-sm btn-primary" title="Print Document">
                                                <i class="fas fa-print mr-1"></i> Print Out
                                            </a>
                                            <!-- delete -->
                                            <form action="{{ route('print-out.destroy', $item->id) }}" method="POST"
                                                class="form-inline">
                                                @method('delete')
                                                @csrf


                                                <button type="submit"
                                                    onclick="return confirm('are you sure you want to delete this data ??')"
                                                    class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>

                                        </td>
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
@endsection