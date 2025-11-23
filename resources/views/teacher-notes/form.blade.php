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
                            <a href="#" class="text-white">Notes</a>
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
                    swal("Berhasil!", "{{ session('status') }}!", {
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
                    swal("Gagal!", "{{ session('error') }}!", {
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
                    {{-- Sesuaikan rute aksi form ke 'Notes.store' atau 'Notes.update' --}}
                    <form
                        action="{{ $data->type == 'create' ? route('teacher-notes.store') : route('teacher-notes.update', $data->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($data->type != 'create')
                            @method('POST')
                        @endif
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ $data->type == 'create' ? 'Add Notes' : 'Edit Notes' }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- FIELD: teacher_id --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="teacher_id">Teacher</label>
                                           <select name="teacher_id" id="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror select2">
                                                <option value="" disabled {{ old('teacher_id', $data->teacher_id) == '' ? 'selected' : '' }}>Choose Teacher</option>
                                                @foreach ($teacher as $t)
                                                    <option value="{{ $t->id }}" {{ old('teacher_id', $data->teacher_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('teacher_id')
                                                <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>

                                   

                                    {{-- FIELD: category (Menggunakan Select/Dropdown) --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select class="form-control @error('category') is-invalid @enderror" name="category" id="category">
                                                <option value="" disabled {{ old('category', $data->category) == '' ? 'selected' : '' }}>Choose Category</option>
                                                @php $categories = ['New Student', 'Move Student', 'Stop Student' ,'Other']; @endphp
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat }}" {{ old('category', $data->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                @endforeach
                                            </select>
                                            @error('category')
                                                <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- FIELD: description --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                                                placeholder="Detail Description">{{ old('description', $data->description) }}</textarea>

                                            @error('description')
                                                <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    {{-- Catatan: created_at biasanya diisi otomatis, tidak perlu form input. --}}

                                </div>

                            </div>
                            <div class="card-action mt-3">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="button" data-toggle="modal" data-target="#mdlCancel"
                                    class="btn btn-danger">Cancel</button>
                            </div>
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
                        {{-- Sesuaikan link cancel --}}
                        <a href="{{ url('/teacher-notes') }}"><button type="button" class="btn btn-success">Yes</button></a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection