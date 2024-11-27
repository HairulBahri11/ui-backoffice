<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>UI Payment - Backend</title>
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
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands",
                    "simple-line-icons"
                ],
                urls: ['{{ url('/') }}/assets/css/fonts.min.css']
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->

    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/atlantis.min.css">
    <script src="{{ url('/') }}/assets/js/core/jquery.3.2.1.min.js"></script>
    <style>
        .custom-checkbox-size::before,
        .custom-checkbox-size::after {
            top: 200px !important;
            width: 200px !important;
            height: 200px !important;
        }

        .custom-control-lg .custom-control-label::before,
        .custom-control-lg .custom-control-label::after {
            top: 0.1rem !important;
            left: -2rem !important;
            width: 1.75rem !important;
            height: 1.75rem !important;
        }

        .custom-control-lg .custom-control-label {
            margin-left: 0.5rem !important;
            font-size: 1rem !important;
        }
    </style>


    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/demo.css">
    <script src="{{ url('/') }}/assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <link rel="stylesheet" href="{{ url('/') }}/assets/dropify/dist/css/dropify.min.css">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
