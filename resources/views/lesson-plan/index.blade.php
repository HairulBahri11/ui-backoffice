@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Lesson Plans</h2>
                </div>
                <!-- aku ingin di jam 15:01 - 23.59 tidak bisa create data -->
                @if(Auth::guard('teacher')->check() == true)
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="{{ url('/lesson-plan/create') }}" class="btn btn-secondary btn-round">Add Data</a>
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
                        <h4 class="card-title">Lesson Plans Data</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Teacher</th>
                                        <th>Class</th>
                                        <th>Topic</th>
                                        <th>Created On</th>
                                        <th class="text-center">Action</th>
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
                                    @foreach ($lessonPlans as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->teacher->name ?? $item->teacher_id }}</td>
                                        <td>
                                            {{ $item->price->program ?? $item->class ?? $item->class_id }}
                                            {{ $day[$item->day1] ?? 'Unknown Day' }}
                                            {{ $day[$item->day2] ?? 'Unknown Day' }}
                                            {{ $item->course_time }}
                                        </td>
                                        <td>{{ $item->topic }}</td>
                                        <td>{{ now()->parse($item->created_at)->format('d M Y h:i A') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('lesson-plan.destroy', $item->id) }}" method="POST" class="form-inline d-flex justify-content-center">
                                                @method('delete')
                                                @csrf

                                                <a href="{{ route('lesson-plan.show', $item->id) }}" class="btn btn-xs btn-primary mr-1" title="Detail Data">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ url('/lesson-plan/' . $item->id . '/edit') }}" class="btn btn-xs btn-info mr-1" title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <button type="submit" onclick="return confirm('Are you sure you want to delete this data ??')" class="btn btn-xs btn-danger" title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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