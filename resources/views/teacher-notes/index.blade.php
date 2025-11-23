@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Teacher's Notes</h2>
                    </div>
                    <div class="ml-md-auto py-2 py-md-0">
                        {{-- Assuming the route for creating a reminder is 'teacher-notes/create' --}}
                        <a href="{{ url('/teacher-notes/create') }}" class="btn btn-secondary btn-round">Add Data</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            @if (session('success'))
                <script>
                    swal("Berhasil", "{{ session('success') }}!", {
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
                    swal("Gagal", "{{ session('error') }}!", { // Corrected 'session('status')' to 'session('error')'
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
                            <h4 class="card-title">Teacher's Notes Data</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Teacher</th>
                                            <th>Staff</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Created On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Assuming the data is passed as $data --}}
                                        @foreach ($data as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->teacher->name }}</td>
                                                <td>{{ $item->staff->name }}</td>
                                                <td>{{ $item->category }}</td>
                                                <td>{{ $item->description }}</td>
                                                <!-- <td>{{ $item->status }}</td> -->
                                                <td>
                                                    @if ($item->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif ($item->status == 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $item->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at }}</td>
                                                <td class=" d-flex">
                                                    {{-- Update the route names from 'tests' to 'teacher-notes' or your actual resource name --}}
                                                    <form action="{{ route('teacher-notes.destroy', $item->id) }}" method="POST"
                                                        class="form-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <a href="{{ url('/teacher-notes/' . $item->id . '/edit') }}"
                                                            class="btn btn-xs btn-info mr-2 "><i
                                                                class="fas fa-edit"></i></a>
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        <button type="submit"
                                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ??')"
                                                            class="btn btn-xs btn-primary"><i
                                                                class="fas fa-trash"></i></button>
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