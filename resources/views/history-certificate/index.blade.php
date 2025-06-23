@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                        <h5 class="text-white op-7 mb-2">History Certificate </h5>
                    </div>

                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            @if (session('message'))
                <script>
                    swal("Successful", "{{ session('message') }}!", {
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
                            <h4 class="card-title">History Certificate</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('history-certificate.index') }}" method="get">
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label for="">Student</label>
                                        <select name="student" id="student_select" class="form-control select2">
                                            <option value="">---Choose Student---</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ $student->id == request()->get('student') ? 'selected' : '' }}>
                                                    {{ ucwords($student->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2" style="margin-top:20px;">
                                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i>
                                            Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <hr>
                        {{-- Bagian tabel hanya akan ditampilkan jika ada student yang dipilih DAN ada hasil query --}}
                        @if (request()->has('student') && request()->input('student') != '')
                            {{-- Pastikan ada hasil dari query history_certificate --}}
                            @if ($history_certificate->count() > 0)
                                {{-- Menggunakan count() untuk Collection Laravel --}}
                                <div class="mt-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Class</th>
                                                        <th class="text-center">Teacher</th>
                                                        <th class="text-center">Date Certificate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @foreach ($history_certificate as $item)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>
                                                                <strong>{{ $item->class }}</strong> - {{ $item->day1 }}
                                                                - {{ $item->day2 }} - {{ $item->course_time }}
                                                            </td>
                                                            <td>{{ $item->teacher_name }}</td>
                                                            <td>
                                                                {{ $item->date_certificate ? Carbon\Carbon::parse($item->date_certificate)->format('d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Jika student dipilih tapi tidak ada data history certificate --}}
                                <div class="mt-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Class</th>
                                                        <th class="text-center">Teacher</th>
                                                        <th class="text-center">Date Certificate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            <h1>Data not found</h1>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Jika belum ada student yang dipilih (halaman pertama kali dimuat) --}}
                            <div class="mt-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">Class</th>
                                                    <th class="text-center">Teacher</th>
                                                    <th class="text-center">Date Certificate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <h1>Please select a student.</h1>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>



    </div>
    </div>
@endsection
