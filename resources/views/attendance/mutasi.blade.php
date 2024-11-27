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
                    <h2 class="text-white pb-2 fw-bold">Jump Level</h2>
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
                            <a href="#" class="text-white">Jump Level</a>
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
                                className: 'btn btn-success'
                            }
                        },
                    });
                </script>
            @endif
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
                    <div class="card">
                        <div class="card-header">
                            @php
                                $getStudent = DB::table('student')
                                    ->where('id', Request::get('student'))
                                    ->first();
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">Jump Level Student
                                        {{ Request::get('student') ? ucwords($getStudent->name) : '' }}</h4>
                                </div>
                                <div class="col-md-6" style="text-align:end;">
                                    @if (Request::get('student'))
                                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#exampleModal"
                                            onclick="addMutasi({{ Request::get('student') }})"><i class="fas fa-plus"></i>
                                            Add Jump Level</a>
                                        <input type="hidden" value="{{ Request::get('student') ? $getStudent->name : '' }}"
                                            id="studentName">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">

                                    <label for="email2">Class</label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width:100%;"
                                        name="price" id="price">
                                        <option value="">Select class</option>
                                        @foreach ($price as $st)
                                            <option value="{{ $st->id }}"
                                                {{ Request::get('class') == $st->id ? 'selected' : '' }}>{{ $st->program }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student')
                                        <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                    @enderror

                                    @error('class')
                                        <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                    @enderror


                                </div>
                                <div class="col-md-3">

                                    <label for="email2">Students</label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width:100%;"
                                        name="student1" id="student1">
                                        <option value="">Select Student</option>

                                    </select>
                                    @error('student')
                                        <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                    @enderror


                                </div>
                                <div class="col-md-3">

                                    <label for="email2">Id Student</label>
                                    <input type="number" name="student2" id="student2" class="form-control"
                                        value="{{ Request::get('student') }}">
                                    @error('student')
                                        <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                    @enderror


                                </div>
                                <div class="col-md-3" style="margin-top:20px">
                                    <button type="submit" class="btn btn-primary" onclick="filter()" id="filter"><i
                                            class="fas fa-filter"></i>
                                        Filter</button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="">
                                        @if (Request::get('student'))
                                            <table
                                                class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th class="text-center">Kelas</th>
                                                        {{-- <th class="text-center">Average Score</th>
                                                        <th class="text-center">Average Grade</th> --}}
                                                        {{-- <th class="text-center">Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (count($data) != 0)
                                                        @php
                                                            $no = 1;
                                                            $score = '';
                                                        @endphp
                                                        @foreach ($data as $item)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>{{ $item->level->program }}</td>
                                                                {{-- <td>{{ $item->score != null ? $item->score->average_score : '-' }}
                                                                </td>
                                                                <td>{{ $item->score != null ? Helper::getGrade($item->score->average_score) : '-' }} --}}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">
                                                                <h4>Data not found!</h4>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>

                                            </table>
                                            <div class="pull-right">
                                                {{ $data->appends(Request::all())->links() }}
                                            </div>
                                        @endif
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/mutasi') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Level</label>
                            <select name="level" id="" class="form-control select2" style="width: 100%">
                                <option value="">---Choose Level---</option>
                                @foreach ($price as $i)
                                    <option value="{{ $i->id }}">{{ $i->program }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" value="{{ Request::get('student') }}" name="student">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function capitalize(str) {
                strVal = '';
                str = str.split(' ');
                for (var chr = 0; chr < str.length; chr++) {
                    strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
                }
                return strVal
            }

            function getStudent() {
                var typeClass = $('#price').val();
                $.ajax({
                    type: 'GET',
                    url: '{{ url('') }}/score/students/filter?class=' + typeClass,
                    dataType: 'JSON',
                    success: function(data) {

                        var $students = $('#student1');
                        $students.empty();
                        $students.append('<option value="">Select Student</option>');
                        for (var i = 0; i < data.length; i++) {
                            $students.append(
                                `<option id='${data[i].id}' value='${data[i].id}' ${data[i].id == "{{ Request::get('student') }}" ? 'selected' : ''}>${capitalize(data[i].name)}</option>`
                            );
                        }
                        $students.change();

                    }
                });
            }
            getStudent();
            $('#price').on('change', function() {
                getStudent()
            });
            $('#student1').on('change', function() {
                $('#student2').val('');
            });
        });
    </script>
    <script>
        function filter() {
            var student1 = $('#student1').val();
            var student2 = $('#student2').val();
            var price = $('#price').val();
            var student = '';
            if (student1 != '' && student2 == '') {
                student = student1
            } else if (student1 != '' && student2 != '') {
                student = student2
                // alert('Pilih salah satu dari student atau id student')
            } else {
                student = student2
            }
            window.location = " {{ url('mutasi?student=') }}" + student + '&class=' + price;
        }

        function addMutasi(id) {
            var name = $('#studentName').val();
            $('#exampleModalLabel').html('Tambah Mutasi ' + name);
        }
    </script>
@endsection
