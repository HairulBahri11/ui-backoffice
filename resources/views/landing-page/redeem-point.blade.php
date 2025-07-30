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
                            <form action="{{ url('/landing-page/store-redeem-point') }}" method="POST"
                                enctype="multipart/form-data" id="form-submit">
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Reedem Point</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- <div class="col-md-3">

                                                <label for="email2">Class</label>
                                                <select class="form-control select2 select2-hidden-accessible" style="width:100%;"
                                                    name="class" id="class">
                                                    <option value="">Select class</option>
                                                    @foreach ($class as $st)
                                                        <option value="{{ $st->id }}">{{ $st->program }}
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
                                                    name="student" id="student">
                                                    <option value="">Select Student</option>

                                                </select>
                                                @error('student')
                                                    <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                                @enderror


                                            </div> --}}
                                            <div class="col-md-3">

                                                <label for="email2">Students</label>
                                                <select class="form-control select2 select2-hidden-accessible"
                                                    style="width:100%;" name="student" id="student">
                                                    <option value="">Select Student</option>
                                                    @foreach ($students as $item)
                                                        <option value="{{ $item->id }}">{{ ucwords($item->name) }}
                                                        </option>
                                                    @endforeach
                                                    <option value=""></option>
                                                </select>
                                                @error('student')
                                                    <label class="mt-1"
                                                        style="color: red!important">{{ $message }}</label>
                                                @enderror


                                            </div>
                                            <div class="col-md-3">

                                                <label for="email2">Student’s ID</label>
                                                <input type="number" name="id_student" id="id_student"
                                                    class="form-control">
                                                @error('student')
                                                    <label class="mt-1"
                                                        style="color: red!important">{{ $message }}</label>
                                                @enderror


                                            </div>
                                            {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email2">Students</label>
                                                <select class="form-control select2 select2-hidden-accessible"
                                                    style="width:100%;" name="student" id="student">
                                                    <option value="">Select Student
                                                    </option>
                                                    @foreach ($students as $st)

                                                    <option value="{{$st->id}}">{{$st->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('student')
                                                <label class="mt-1" style="color: red!important">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" id="point_show" style="display: none;">
                                                <div class="card">
                                                    <div class="card-body mx-auto">
                                                        <h6 class="card-title" id="student_name"></h6>
                                                        <h1 class="text-center" id="student_point"></h1>
                                                        <input type="hidden" name="point" id="point_total"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div id="target" style="display: none;">
                                            <div class="row ">
                                                {{-- Otomatis --}}
                                                {{-- <div class="col-md-4" style="padding-right: 0px!important">
                                                    <div class="form-group">

                                                        <select name="item[]" class="form-control text-black">
                                                            <option>Select Item</option>
                                                            @foreach ($item as $mt)
                                                                <option value="{{ $mt->id }}">{{ $mt->item }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror

                                                    </div>
                                                </div>
                                                <div class="col-md-3" style="padding-left: 0px!important">
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" placeholder="Quantity reedem"
                                                            required name="qty[]">

                                                    </div>
                                                </div> --}}
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="display table table-striped table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Date</th>
                                                                    <th>Point</th>
                                                                    <th>Keterangan</th>
                                                                    <th>Type</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="history-point">
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td>1</td>
                                                                    <td>1</td>
                                                                    <td>1</td>
                                                                    <td>1</td>
                                                                    <td>1</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" style="padding-left: 0px!important">
                                                    <div class="form-group">
                                                        <input type="number" class="form-control"
                                                            placeholder="Reedem Point" required name="total_point"
                                                            id="total_point">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action mt-3">
                                        <button type="button" class="btn btn-success" id="btnSubmit">Submit</button>
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
                                <a href="{{ url('/advertise') }}"><button type="button"
                                        class="btn btn-success">Yes</button></a>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var dataStudent = {!! json_encode($students) !!};


                $(document).ready(function() {
                    function capitalize(str) {
                        strVal = '';
                        str = str.split(' ');
                        for (var chr = 0; chr < str.length; chr++) {
                            strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
                        }
                        return strVal
                    }

                    $('#student').on('change', function() {
                        $('#history-point').empty();
                        // $('#id_student').val('')
                        var no = 1;
                        var idStudent = $('select[name=student] option').filter(':selected').val();
                        jQuery.each(dataStudent, function(index, item) {
                            if (item.id == idStudent) {

                                $("#point_show").css("display", "block");
                                $("#target").css("display", "block");
                                $(".button-add").css("display", "block");
                                $('#student_name').text(capitalize(item.name));
                                $('#student_point').text(item.total_point);
                                $('#point_total').val(item.total_point);

                            }
                        });
                        $.ajax({
                            type: "get",
                            url: "{{ url('history-point') }}/" + idStudent,
                            dataType: "json",
                            success: function(response) {
                                $.each(response, function(i, v) {
                                    var content = `
                                                    <tr>
                                                        <td>${no++}</td>
                                                        <td>${v.date}</td>
                                                        <td>${v.total_point}</td>
                                                        <td>${v.keterangan}</td>
                                                        <td>${v.type == 'in' ? 'In' : 'Out'}</td>
                                                    </tr>
                                                `
                                    $('#history-point').append(content);
                                });
                            }
                        });
                    });

                    $('#id_student').on('keyup', function() {
                        $('#history-point').empty();
                        $('#student').val(0);
                        var no = 1;
                        var idStudent = $(this).val();
                        jQuery.each(dataStudent, function(index,
                            item) {
                            if (item.id == idStudent) {

                                $("#point_show").css("display", "block");
                                $("#target").css("display", "block");
                                $(".button-add").css("display", "block");
                                $('#student_name').text(item.name);
                                $('#student_point').text(item.total_point);
                                $('#point_total').val(item.total_point);
                            }
                        });
                        $.ajax({
                            type: "get",
                            url: "{{ url('history-point') }}/" + idStudent,
                            dataType: "json",
                            success: function(response) {
                                $.each(response, function(key, value) {
                                    var content = `
                                                    <tr>
                                                        <td>${no++}</td>
                                                        <td>${value.date}</td>
                                                        <td>${value.total_point}</td>
                                                        <td>${value.keterangan}</td>
                                                        <td>${value.type == 'in' ? 'In' : 'Out'}</td>
                                                    </tr>
                                                `
                                    $('#history-point').append(content);
                                });
                            }
                        });
                    });

                    var length = $("#target").length;
                    if (length == 1) {
                        $("#remove-target").hide();
                    }
                    $("#add-target").click(function() {
                        console.log("tambah");
                        var clone = $("#target:first").clone();
                        clone.find("input").val("");
                        $(".button-add").before(clone);
                        length += 1;
                        if (length == 1) {
                            $("#remove-target").hide();
                        } else {
                            $("#remove-target").show();
                        }
                    });
                    $("#remove-target").click(function() {
                        $("#target:last").remove();
                        length -= 1;
                        if (length == 1) {
                            $("#remove-target").hide();
                        } else {
                            $("#remove-target").show();
                        }
                    });
                });
            </script>

            <script>
                $(document).ready(function() {
                    $('#class').on('change', function() {
                        var typeClass = $(this).val();
                        $.ajax({
                            type: 'GET',
                            url: '{{ url('') }}/score/students/filter?class=' + typeClass,
                            dataType: 'JSON',
                            success: function(data) {

                                var $student = $('#student');
                                $student.empty();
                                $student.append('<option value="">Select Student</option>');
                                for (var i = 0; i < data.length; i++) {
                                    $student.append('<option id=' + data[i].id + ' value=' + data[i]
                                        .id + '>' + data[i].name + '</option>');
                                }
                                $student.change();

                            }
                        });
                    });
                });
            </script>


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
                        <a href="{{ url('logout') }}"><button type="button"
                                class="btn btn-success">Yes</button></a>
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
