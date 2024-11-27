@extends('template.app')

@section('content')
    <div class="content">
        <div class="page-inner py-5 panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div class="">
                    <h2 class="text-white pb-2 fw-bold">{{ $title }}</h2>
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
                            <a href="#" class="text-white">Parents</a>
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
                    <form
                        action="{{ $data->type == 'create' ? route('parents.store') : route('parents.update', $data->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($data->type != 'create')
                            @method('PUT')
                        @endif
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ $data->type == 'create' ? 'Tambah Data' : 'Edit Data' }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email2">Phone Number</label>
                                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                                value="{{ $data->no_hp }}" name="no_hp" placeholder="Phone Number">
                                            @error('no_hp')
                                                <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email2">Gender</label>
                                            <select name="gender"
                                                class="form-control @error('gender') is-invalid @enderror" id="">
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ $data->gender == 'Male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="Female" {{ $data->gender == 'Female' ? 'selected' : '' }}>
                                                    Female
                                                </option>
                                            </select>
                                            @error('gender')
                                                <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        @if ($data->type == 'edit')
                                            @php
                                                $student = App\Models\ParentStudents::where('parent_id', $data->id)->get();
                                            @endphp
                                            @foreach ($student as $s)
                                                @php
                                                    $studentDetail = App\Models\Students::where('id', $s->student_id)->first();
                                                @endphp
                                                <li>{{ $studentDetail->name }}</li>
                                            @endforeach
                                        @endif
                                    </div>

                                </div>

                            </div>
                            <div class="card-action mt-3">
                                <button type="submit" class="btn btn-success">Submit</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
