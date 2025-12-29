<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Login</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="{{ url('/') }}/assets/img/icon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ url('/') }}/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Lato:300,400,700,900"]
            },
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands", "simple-line-icons"
                ],
                urls: ['{{ url(' / ') }}/assets/css/fonts.min.css']
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/atlantis.css">
    <!-- Sweet Alert -->
    <script src="{{ url('/') }}/assets/js/plugin/sweetalert/sweetalert.min.js"></script>
</head>

<style>
    /* Default for larger screens */
    .box-img {
        height: 400px;
        /* Adjust this to suit your needs */
    }

    .box-img img {
        width: 100%;
        object-fit: cover;
    }

    /* For mobile screens (up to 768px) */
    @media (max-width: 768px) {
        .box-img {
            height: auto;
            /* Let the height adjust automatically */
        }

        .box-img img {
            height: auto;
            width: 100%;
            /* Ensure the width stays responsive */
        }
    }
</style>

<body class="login">
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn" style="width:800px;">
            <!--<h3 class="text-center">Sign In To Admin</h3>-->
            @if (session('message'))
                <script>
                    swal("{{ session('message') }}", {
                        buttons: false,
                        timer: 3000,
                        icon: "error",

                    });
                </script>
            @endif
            <!--<div class="login-form">-->
            <!--    <form action="{{ url('login') }}" method="POST">-->
            <!--        @csrf-->
            <!--        <div class="form-group">-->
            <!--            <label for="username" class="placeholder"><b>Username</b></label>-->
            <!--            <input id="username" name="email" type="text"-->
            <!--                class="form-control @error('email')
is-invalid
@enderror" required>-->
            <!--            @error('email')
    -->
                <!--                <label class="mt-1" style="color: red">{{ $message }}</label>-->
                <!--
@enderror-->
            <!--        </div>-->
            <!--        <div class="form-group">-->
            <!--            <label for="password" class="placeholder"><b>Password</b></label>-->

            <!--            <div class="position-relative">-->
            <!--                <input id="password" name="password" type="password"-->
            <!--                    class="form-control @error('password')
is-invalid
@enderror" required>-->
            <!--                <div class="show-password">-->
            <!--                    <i class="icon-eye"></i>-->
            <!--                </div>-->
            <!--                @error('password')
    -->
                <!--                    <label class="mt-1" style="color: red">{{ $message }}</label>-->
                <!--
@enderror-->
            <!--            </div>-->
            <!--        </div>-->
            <!--        <div class="form-group">-->
            <!--            <label for="password" class="placeholder"><b>Login As</b></label>-->

            <!--            <div class="position-relative">-->
            <!--                <select name="type" id="" class="form-control" required>-->
            <!--                    <option value="" selected disabled>---Choose Teacher/Staff---</option>-->
            <!--                    <option value="teacher">Teacher</option>-->
            <!--                    <option value="staff">Staff</option>-->
            <!--                </select>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--        <div class="form-group form-action-d-flex mb-3">-->
            <!--            <div class="custom-control custom-checkbox">-->
            <!--                <input type="checkbox" class="custom-control-input" id="rememberme">-->
            <!--                <label class="custom-control-label m-0" for="rememberme">Remember Me</label>-->
            <!--            </div>-->
            <!--            <input type="submit" value="Login"-->
            <!--                class="btn btn-primary col-md-5 float-right mt-3 mt-sm-0 fw-bold">-->

            <!--        </div>-->
            <!--    </form>-->
            <!--    {{-- <div class="login-account">-->
            <!--        <span class="msg">Don't have an account yet ?</span>-->
            <!--        <a href="#" id="show-signup" class="link">Sign Up</a>-->
            <!--    </div> --}}-->
            <!--</div>-->
            <div class="row">
                <div class="col-md-6">
                    <div class="box-img d-flex justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/logoui.png') }}" alt="" style=" object-fit: cover;">
                    </div>
                </div>

                <div class="col-md-6 ">
                    <h3 class="text-center ">Sign in to Admin SEKALI LAGI DONG</h3>
                    <div class="login-form">
                        <form action="{{ url('login') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="username" class="placeholder"><b>Username</b></label>
                                <input id="username" name="email" type="text"
                                    class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')
                                    <label class="mt-1" style="color: red">{{ $message }}</label>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password" class="placeholder"><b>Password</b></label>

                                <div class="position-relative">
                                    <input id="password" name="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" required>
                                    <div class="show-password">
                                        <i class="icon-eye"></i>
                                    </div>
                                    @error('password')
                                        <label class="mt-1" style="color: red">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="placeholder"><b>Login As</b></label>

                                <div class="position-relative">
                                    <select name="type" id="" class="form-control" required>
                                        <option value="" selected disabled>---Choose Teacher/Staff---</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="staff">Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-action-d-flex mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="rememberme">
                                    <label class="custom-control-label m-0" for="rememberme">Remember Me</label>
                                </div>
                                <input type="submit" value="Login"
                                    class="btn btn-primary col-md-5 float-right mt-3 mt-sm-0 fw-bold">

                            </div>
                        </form>
                        <!--<a href="{{ url('tes') }}" id="tes" class="link">tes</a>-->


                        {{-- <div class="login-account">
                            <span class="msg">Don't have an account yet ?</span>
                            <a href="#" id="show-signup" class="link">Sign Up</a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="container container-signup animated fadeIn">
            <h3 class="text-center">Sign Up</h3>
            <div class="login-form">
                <div class="form-group">
                    <label for="fullname" class="placeholder"><b>Fullname</b></label>
                    <input id="fullname" name="fullname" type="text" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email" class="placeholder"><b>Email</b></label>
                    <input id="email" name="email" type="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="passwordsignin" class="placeholder"><b>Password</b></label>
                    <div class="position-relative">
                        <input id="passwordsignin" name="passwordsignin" type="password" class="form-control"
                            required>
                        <div class="show-password">
                            <i class="icon-eye"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmpassword" class="placeholder"><b>Confirm Password</b></label>
                    <div class="position-relative">
                        <input id="confirmpassword" name="confirmpassword" type="password" class="form-control"
                            required>
                        <div class="show-password">
                            <i class="icon-eye"></i>
                        </div>
                    </div>
                </div>
                <div class="row form-sub m-0">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="agree" id="agree">
                        <label class="custom-control-label" for="agree">I Agree the terms and conditions.</label>
                    </div>
                </div>
                <div class="row form-action">
                    <div class="col-md-6">
                        <a href="#" id="show-signin" class="btn btn-danger btn-link w-100 fw-bold">Cancel</a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" class="btn btn-primary w-100 fw-bold">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ url('/') }}/assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="{{ url('/') }}/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="{{ url('/') }}/assets/js/core/popper.min.js"></script>
    <script src="{{ url('/') }}/assets/js/core/bootstrap.min.js"></script>
    <script src="{{ url('/') }}/assets/js/atlantis.min.js"></script>
</body>

</html>
