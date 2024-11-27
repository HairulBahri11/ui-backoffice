@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Parents </h2>
                        {{-- <h5 class="text-white op-7 mb-2">Free Bootstrap 4 Admin Dashboard</h5> --}}
                    </div>
                    <div class="ml-md-auto py-2 py-md-0">
                        <a href="{{ url('/parents/create') }}" class="btn btn-secondary btn-round">Tambah Data</a>
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
                            <h4 class="card-title">Data Parents</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone Number</th>
                                            <th>Student Name</th>
                                            <th>OTP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr>

                                                <td>{{ ucwords($item->name) }}</td>
                                                <td>{{ $item->no_hp }}</td>
                                                <td>
                                                    @php
                                                        $student = DB::table('parent_students')
                                                            ->select('student.name')
                                                            ->join('student', 'student.id', 'parent_students.student_id')
                                                            ->where('parent_id', $item->id)
                                                            ->get();
                                                    @endphp
                                                    @if (count($student) != 0)
                                                        @foreach ($student as $ks => $vs)
                                                            {{-- {{ $ks++ == count($student) ? 'asd' : 'ccd' }}
                                                            {{ $ks }}
                                                            {{ count($student) }} --}}
                                                            <li>{{ ucwords($vs->name) }}</li>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>{{ $item->otp }}</td>
                                                <td class=" d-flex">
                                                    <form action="{{ route('parents.destroy', $item->id) }}" method="POST"
                                                        class="form-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <a href="{{ url('/parents/' . $item->id . '/edit') }}"
                                                            class="btn btn-xs btn-info mr-2 "><i
                                                                class="fas fa-edit"></i></a>
                                                        {{-- <a href="{{ url('/update-otp/' . $item->id) }}"
                                                            class="btn btn-xs btn-primary mr-2 "><i
                                                                class="fas fa-code"></i></a> --}}
                                                        <a href="{{ url('/parents/' . $item->id) }}"
                                                            class="btn btn-xs btn-warning mr-2 "><i
                                                                class="fas fa-eye"></i></a>
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        <button type="submit"
                                                            onclick="return confirm('apakah anda yakin ingin menghapus data ??')"
                                                            class="btn btn-xs btn-danger"><i
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
