@include('template.head')

<body>
    <div class="wrapper">
        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header justify-content-center" data-background-color="blue">

                <a href="{{ url('/landing-page/redeem-point') }}" class="logo">
                    <img src="{{ asset('assets/img/ui4.png') }}" width="100px" alt="navbar brand" class="navbar-brand">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                    data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="icon-menu"></i>
                    </span>
                </button>
                <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="icon-menu"></i>
                    </button>
                </div>
            </div>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">

                <div class="container-fluid">

                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">



                    </ul>
                </div>


            </nav>

            <!-- End Navbar -->
        </div>


        <!-- Sidebar -->
        <div class="sidebar sidebar-style-2">
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <div class="user">
                        <div class="avatar-sm float-left mr-2">
                            <img src="../assets/img/profile.png" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                                <span>
                                    <!-- {{ session('nama') }} -->
                                    {{-- Admin UI Payment --}}
                                    <span class="user-level">Guest</span>

                                </span>
                            </a>

                        </div>
                    </div>
                    <ul class="nav nav-primary">
                        <li class="nav-item {{ Request::segment(2) == 'redeem-point' ? 'active' : '' }}">
                            <a href="{{ url('/landing-page/redeem-point') }}" class="collapsed">
                                <i class="fas fa-download"></i>
                                <p>Redeem Point</p>
                            </a>

                        </li>
                        <li class="nav-item {{ Request::segment(2) == 'extra-point' ? 'active' : '' }}">
                            <a href="{{ url('/landing-page/extra-point') }}" class="collapsed">
                                <i class="fas fa-star"></i>
                                <p>Extra Point</p>
                            </a>

                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- End Sidebar -->

        <div class="main-panel">
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
                                    <a href="#" class="text-white">{{ $title }}</a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="page-inner mt--5">
                    @if (session('status'))
                        <script>
                            swal("Success!", "{{ session('status') }}!", {
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
                            <div class="card">
                                <div class="card-header">
                                    {{-- <h4 class="card-title">Test History</h4> --}}

                                </div>
                                <div class="card-body">
                                    <form action="" method="get">
                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label for="">Student</label>
                                                <select name="student" id="" class="form-control select2">
                                                    <option value="">---Choose Student---</option>
                                                    @foreach ($students as $student)
                                                        <option value="{{ $student->id }}"
                                                            {{ $student->id == Request::get('student') ? 'selected' : '' }}>
                                                            {{ ucwords($student->name) }}</option>
                                                    @endforeach
                                                </select>


                                            </div>

                                            <div class="col-md-3">
                                                <label for="">Id Student</label>
                                                <input type="number" name="id_student" id="id_student"
                                                    class="form-control" value="{{ Request::get('id_student') }}">


                                            </div>


                                            <div class="col-md-4" style="margin-top:20px;">
                                                <button class="btn btn-primary" type="submit"><i
                                                        class="fas fa-filter"></i>
                                                    Filter</button>
                                            </div>
                                        </div>
                                </div>
                                </form>
                                <hr>
                                @if (Request::get('student') || Request::get('id_student'))
                                    @php
                                        // Build the base query for student_point
                                        $student_point_query = DB::table('student')
                                            ->join('price', 'price.id', 'student.priceid')
                                            ->join('teacher', 'teacher.id', 'student.id_teacher')
                                            ->join('day as day1', 'day1.id', 'student.day1')
                                            ->join('day as day2', 'day2.id', 'student.day2')
                                            ->select(
                                                'student.*',
                                                'student.id as idstudent',
                                                'price.program as class',
                                                'teacher.name as teacher_name',
                                                'day1.day as day1_name',
                                                'day2.day as day2_name',
                                            );
                                        if (Request::filled('id_student')) {
                                            $student_point_query->where('student.id', Request::get('id_student'));
                                        } elseif (Request::filled('student')) {
                                            // Use 'student' only if 'id_student' is not filled, but 'student' is filled
                                            $student_point_query->where('student.id', Request::get('student'));
                                        } else {
                                            $student_point_query->whereRaw('0 = 1'); // Forces no results
                                        }

                                        $student_point = $student_point_query->first();

                                        // Build the base query for extra_exam
                                        $extra_exam_query = DB::table('extra_exam_point')
                                            ->join('student', 'student.id', 'extra_exam_point.student_id')
                                            ->join('teacher', 'teacher.id', 'extra_exam_point.teacher_id')
                                            ->join('price', 'price.id', 'extra_exam_point.price_id')
                                            ->join('day as day1', 'day1.id', 'extra_exam_point.day1')
                                            ->join('day as day2', 'day2.id', 'extra_exam_point.day2')
                                            ->select(
                                                'extra_exam_point.*',
                                                'student.id as idstudent',
                                                'student.name as student_name',
                                                'price.program as class',
                                                'teacher.name as teacher_name',
                                                'day1.day as day1',
                                                'day2.day as day2',
                                            );

                                        if (Request::filled('id_student')) {
                                            $extra_exam_query->where(
                                                'extra_exam_point.student_id',
                                                Request::get('id_student'),
                                            );
                                        } elseif (Request::filled('student')) {
                                            // Use 'student' only if 'id_student' is not filled, but 'student' is filled
                                            $extra_exam_query->where(
                                                'extra_exam_point.student_id',
                                                Request::get('student'),
                                            );
                                        } else {
                                            $extra_exam_query->whereRaw('0 = 1'); // Forces no results
                                        }

                                        $extra_exam = $extra_exam_query->get();
                                    @endphp
                                    @if (!empty($student_point))
                                        <div class="p-4">
                                            <div class="row">
                                                <div class="col-md-5 p-4">
                                                    <div class="card">
                                                        <div class="card-body d-flex align-items-center p-4 rounded-3 shadow-sm"
                                                            style="background: linear-gradient(135deg, #f6f9fc, #eef2f7);">

                                                            <div>
                                                                <h5 class="card-title fw-bold text-primary mb-2">
                                                                    {{ $student_point->name }}</h5>
                                                                <p class="mb-2"><i
                                                                        class="fas fa-id-card text-muted me-2"></i>
                                                                    <strong>ID:</strong>
                                                                    {{ $student_point->idstudent }}
                                                                </p>
                                                                <p class="mb-2"><i
                                                                        class="fas fa-school text-muted me-2"></i>
                                                                    <strong>Class:</strong>
                                                                    {{ $student_point->class . ' - ' . $student_point->day1_name . '' . $student_point->day2_name . ' ' . $student_point->course_time ?? '' }}
                                                                </p>
                                                                <p class="mb-2"><i
                                                                        class="fas fa-chalkboard-teacher text-muted me-2"></i>
                                                                    <strong>Teacher:</strong>
                                                                    {{ $student_point->teacher_name }}
                                                                </p>
                                                                <p class="mb-0">
                                                                    <i class="fas fa-star text-warning me-2"></i>
                                                                    <strong>Point:</strong> <span
                                                                        class="badge bg-success rounded-pill px-3 py-2 text-white font-bold"
                                                                        style="font-size: 14px">{{ $student_point->total_point }}</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-7">
                                                    <div class="card">
                                                        <div class="card-header bg-info text-white rounded">
                                                            <h5 class="mb-0">Add Extra Point</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <form
                                                                action="{{ route('landing-page.extra-point.store') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="student_id"
                                                                    value="{{ $student_point->idstudent }}">
                                                                <input type="hidden" name="price_id"
                                                                    value="{{ $student_point->priceid }}">
                                                                <input type="hidden" name="teacher_id"
                                                                    value="{{ $student_point->id_teacher }}">
                                                                <input type="hidden" name="day1"
                                                                    value="{{ $student_point->day1 }}">
                                                                <input type="hidden" name="day2"
                                                                    value="{{ $student_point->day2 }}">
                                                                <input type="hidden" name="course_time"
                                                                    value="{{ $student_point->course_time }}">
                                                                <input type="hidden" name="category"
                                                                    value="extra-exam">
                                                                <input type="hidden" name="point_history"
                                                                    value="{{ $student_point->total_point }}">
                                                                <div class="mb-3">
                                                                    <label for="point"
                                                                        class="form-label">Point</label>
                                                                    <input type="number" class="form-control"
                                                                        name="point" id="point" required
                                                                        min="0">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="description"
                                                                        class="form-label">Description</label>
                                                                    <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                                                                </div>



                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="fas fa-save"></i> Save Point
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="basic-datatables"
                                                    class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">No</th>
                                                            <th class="text-center">Name</th>
                                                            <th class="text-center">Class</th>
                                                            <th class="text-center">Teacher</th>
                                                            <th class="text-center">Point</th>
                                                            <th class="text-center">Description</th>
                                                            <th class="text-center">Entry date</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = 1;
                                                        @endphp
                                                        @foreach ($extra_exam as $item)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>{{ $item->student_name }}</td>
                                                                <td>{{ $item->class . ' ' . $item->day1 . ' ' . $item->day2 . ' ' . $item->course_time }}
                                                                </td>
                                                                <td>{{ $item->teacher_name }}</td>

                                                                <td>{{ $item->point }}</td>
                                                                <td>{{ $item->description }}</td>
                                                                <td>{{ date('d M Y', strtotime($item->tgl_input)) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-danger">
                                            <strong>No Data</strong>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
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
                            <a href="{{ url('/advertise') }}"><button type="button"
                                    class="btn btn-success">Yes</button></a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="container-fluid">

                <div class="copyright ml-auto">
                    2023, made with <i class="fa fa-heart heart text-danger"></i> by <a
                        href="{{ url('/dashboard') }}">UI Payment</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- Custom template | don't include it in your project! -->
    <div class="custom-template">
        <div class="title">Settings</div>
        <div class="custom-content">
            <div class="switcher">
                <div class="switch-block">
                    <h4>Logo Header</h4>
                    <div class="btnSwitch">
                        <button type="button" class="changeLogoHeaderColor" data-color="dark"></button>
                        <button type="button" class="selected changeLogoHeaderColor" data-color="blue"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="purple"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="light-blue"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="green"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="orange"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="red"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="white"></button>
                        <br />
                        <button type="button" class="changeLogoHeaderColor" data-color="dark2"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="blue2"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="purple2"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="light-blue2"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="green2"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="orange2"></button>
                        <button type="button" class="changeLogoHeaderColor" data-color="red2"></button>
                    </div>
                </div>
                <div class="switch-block">
                    <h4>Navbar Header</h4>
                    <div class="btnSwitch">
                        <button type="button" class="changeTopBarColor" data-color="dark"></button>
                        <button type="button" class="changeTopBarColor" data-color="blue"></button>
                        <button type="button" class="changeTopBarColor" data-color="purple"></button>
                        <button type="button" class="changeTopBarColor" data-color="light-blue"></button>
                        <button type="button" class="changeTopBarColor" data-color="green"></button>
                        <button type="button" class="changeTopBarColor" data-color="orange"></button>
                        <button type="button" class="changeTopBarColor" data-color="red"></button>
                        <button type="button" class="changeTopBarColor" data-color="white"></button>
                        <br />
                        <button type="button" class="changeTopBarColor" data-color="dark2"></button>
                        <button type="button" class="selected changeTopBarColor" data-color="blue2"></button>
                        <button type="button" class="changeTopBarColor" data-color="purple2"></button>
                        <button type="button" class="changeTopBarColor" data-color="light-blue2"></button>
                        <button type="button" class="changeTopBarColor" data-color="green2"></button>
                        <button type="button" class="changeTopBarColor" data-color="orange2"></button>
                        <button type="button" class="changeTopBarColor" data-color="red2"></button>
                    </div>
                </div>
                <div class="switch-block">
                    <h4>Sidebar</h4>
                    <div class="btnSwitch">
                        <button type="button" class="selected changeSideBarColor" data-color="white"></button>
                        <button type="button" class="changeSideBarColor" data-color="dark"></button>
                        <button type="button" class="changeSideBarColor" data-color="dark2"></button>
                    </div>
                </div>
                <div class="switch-block">
                    <h4>Background</h4>
                    <div class="btnSwitch">
                        <button type="button" class="changeBackgroundColor" data-color="bg2"></button>
                        <button type="button" class="changeBackgroundColor selected" data-color="bg1"></button>
                        <button type="button" class="changeBackgroundColor" data-color="bg3"></button>
                        <button type="button" class="changeBackgroundColor" data-color="dark"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="mdlLogout" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure to logout?</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ url('logout') }}"><button type="button" class="btn btn-success">Yes</button></a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Custom template -->
    </div>
    @include('template.script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                closeOnSelect: true
            });
        });
        $('#btnSubmit').click(function() {
            var point = $('#total_point').val();
            swal("Are you sure to reedem " + point + " point?", "Data will be updated", {
                icon: "info",
                buttons: {
                    confirm: {
                        className: 'btn btn-success',
                        text: 'Sure'
                    },
                    dismiss: {
                        className: 'btn btn-secondary',
                        text: 'Cancel'
                    },
                },
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result == true) {
                    $('#form-submit').submit();
                }
            });
        });
    </script>
</body>

</html>
