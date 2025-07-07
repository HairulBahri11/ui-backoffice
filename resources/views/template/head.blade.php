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

    {{-- <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/atlantis.min.css">
    <script src="{{ url('/') }}/assets/js/core/jquery.3.2.1.min.js"></script> --}}
    <style>
        /* Ensure the icon is the positioning context for the badge */
        .position-relative {
            position: relative !important;
        }

        .birthday-notification-badge {
            position: absolute;
            top: -5px;
            /* Adjust as needed for vertical position */
            right: -8px;
            /* Adjust as needed for horizontal position */
            font-size: 0.7em;
            /* Smaller font size for the badge number */
            padding: 0.3em 0.5em;
            /* Padding around the number */
            border-radius: 50%;
            /* Makes it round */
            line-height: 1;
            /* Prevents stretching if digits are tall */
            min-width: 20px;
            /* Ensures it's wide enough for double digits */
            text-align: center;
            /* Centers the text */
            z-index: 100;
            /* Ensure it appears above other elements if overlaps */
        }

        /* Optional: Add a subtle bounce animation for attention */
        @keyframes bounce-in {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
            }

            100% {
                transform: scale(1);
            }
        }

        .birthday-notification-badge.animate {
            animation: bounce-in 0.5s ease-out;
        }

        /* --- Styles for Scrollable Dropdown --- */
        .scrollable-dropdown-menu {
            max-height: 300px;
            /* Adjust this value as needed (e.g., 250px, 400px) */
            overflow-y: auto;
            /* Enables vertical scrolling */
            -webkit-overflow-scrolling: touch;
            /* Improves scrolling performance on touch devices */
        }
    </style>


    {{-- <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/demo.css">
    <script src="{{ url('/') }}/assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <link rel="stylesheet" href="{{ url('/') }}/assets/dropify/dist/css/dropify.min.css">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}


    <!-- CSS Frameworks -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/atlantis.min.css">

    <!-- CSS Plugin Styles -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/dropify/dist/css/dropify.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- CSS Just for demo purpose (Optional) -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/demo.css">

    <!-- JS Core Libraries -->
    <script src="{{ url('/') }}/assets/js/core/jquery.3.2.1.min.js"></script>

    <!-- JS Plugins -->
    <script src="{{ url('/') }}/assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
