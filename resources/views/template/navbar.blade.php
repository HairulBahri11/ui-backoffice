<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header justify-content-center" data-background-color="white">

        <a href="{{ url('/dashboard') }}" class="logo">
            <img src="{{ asset('assets/img/logoui.png') }}" width="60px" alt="navbar brand" class="navbar-brand">
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

        <div class="container-fluid px-5">

            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">

                {{-- {{ dd($student_birthday_notification) }} --}}
                {{-- Notifikasi Ulang Tahun --}}
                @if (!empty($student_birthday_notification))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarBirthdayDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{-- Main Gift Icon --}}
                            <i class="fas fa-gift position-relative"></i>

                            {{-- Conditional Badge for Notification Count --}}
                            @if (count($student_birthday_notification) > 0)
                                <span class="badge badge-pill badge-danger birthday-notification-badge">
                                    {{ count($student_birthday_notification) }}
                                </span>
                            @endif
                        </a>
                        {{-- Add 'scrollable-dropdown-menu' class here --}}
                        <div class="dropdown-menu dropdown-menu-right scrollable-dropdown-menu"
                            aria-labelledby="navbarBirthdayDropdown">
                            <h6 class="dropdown-header">Students' Birthdays Point!</h6> {{-- Changed header for clarity --}}

                            <div class="dropdown-divider"></div>
                            @forelse ($student_birthday_notification as $student)
                                {{-- Use @forelse for empty state handling --}}
                                {{-- Highlight today's birthdays --}}
                                @if ($student['is_today_birthday'])
                                    <a class="dropdown-item text-primary font-weight-bold" href="#">
                                        🎉 Happy Birthday, {{ $student['name'] }}! 🎉
                                        <br><small class="text-muted">Today!
                                            ({{ $student['age'] == 0 ? 'unknown' : $student['age'] }} years old)
                                            {{-- Changed 0 to unknown for age --}}
                                        </small>
                                        <br><small class="text-muted">{{ $student['teacher'] }}
                                            | {{ $student['day1'] }} {{ $student['day2'] }} | {{ $student['class'] }} |
                                            {{ $student['course_time'] }}</small> {{-- Added space between day1 and day2 --}}
                                    </a>
                                @else
                                    <a class="dropdown-item" href="#">
                                        🎈 {{ $student['name'] }}
                                        <br><small class="text-muted">Birthday on:
                                            {{ \Carbon\Carbon::parse($student['birthday'])->format('F d') }}
                                            ({{ $student['age'] == 0 ? 'unknown' : $student['age'] }} years old)
                                        </small>
                                        <br><small class="text-muted">{{ $student['teacher'] }}
                                            | {{ $student['day1'] }} {{ $student['day2'] }} | {{ $student['class'] }}
                                            |
                                            {{ $student['course_time'] }}</small>
                                    </a>
                                @endif
                                @if (!$loop->last)
                                    <div class="dropdown-divider"></div>
                                @endif
                            @empty {{-- This block runs if $student_birthday_notification is empty --}}
                                <span class="dropdown-item text-center text-muted">No birthdays this week.</span>
                            @endforelse
                            <div class="dropdown-divider"></div>
                        </div>
                    </li>
                @endif

                <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ asset('assets/img/profile.png') }}" class="avatar-img rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg"><img src="{{ asset('assets/img/profile.png') }}"
                                            alt="image profile" class="avatar-img rounded-circle"></div>
                                    <div class="u-text">
                                        <h4>{{ session('nama') }}</h4>
                                        <p class="text-muted">{{ session('email') }}</p><a
                                            href="{{ url('/user/' . session('id')) }}"
                                            class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#mdlLogout">Logout</a>
                                {{-- <a class="dropdown-item" href="{{ url('logout') }}" data-toggle="modal" data-target="#exampleModal">Logout</a> --}}
                            </li>
                        </div>
                    </ul>
                </li>

            </ul>
        </div>


    </nav>

    <!-- End Navbar -->
</div>
