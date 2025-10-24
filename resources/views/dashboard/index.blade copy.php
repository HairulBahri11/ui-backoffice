@extends('template.app')
@section('content')

<style>
    /* =======================================
   ENHANCED FLOATING STICKY NOTE STYLES
   ======================================= */

/* 1. Define the Blinking Animation (More subtle flash) */
@keyframes blinker {
    0%, 100% { box-shadow: 0 6px 15px rgba(0, 72, 255, 0.6); } /* Stronger shadow/glow at start/end */
    50% { box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4); } /* Softer shadow in the middle */
}

/* 2. Floating Container (Positioning remains the same) */
.floating-reminder-container {
    position: fixed; 
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    display: flex;
    flex-direction: column; 
    align-items: flex-end; 
}

/* 3. Style for Each Notifcation (The Note Card itself) */
.blinking-note {
    /* Applying Animation */
    animation: blinker 2s ease-in-out infinite; /* Slower, smoother animation */
    
    /* Card Aesthetics */
    background: linear-gradient(135deg, #6aa0feff, #a58fffff); /* Soft gradient background */
    color: #333; /* Darker text for readability */
    padding: 15px;
    border-radius: 10px;
    margin-top: 15px; /* Increased spacing */
    width: 280px; /* Slightly wider card */
    font-size: 14px;
    font-family: 'Arial', sans-serif;
    transition: transform 0.2s ease-in-out; /* Smooth transition */
}

/* Hover Effect: Lifts the card and stops the blinking */
.blinking-note:hover {
    animation: none; 
    transform: translateY(-5px); /* Lift effect */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); /* Stronger shadow on hover */
    background: linear-gradient(135deg, #6aa0feff, #a58fffff); /* Slightly darker on hover */
}

.blinking-note h6 {
    color: #2a38a5ff; /* Deep red for emphasis on title */
}

.note-source {
    font-size: 0.85em;
    font-style: italic;
    color: rgba(51, 51, 51, 0.8);
    border-top: 1px dashed rgba(51, 51, 51, 0.3); /* Dashed line separator */
    padding-top: 8px;
    margin-top: 10px;
    display: block; /* Ensure it takes full width */
}

/* Style for the 'All Reminders Completed' message */
.floating-reminder-container > .btn-success {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                    <h5 class="text-white op-7 mb-2">Dashboard
                    </h5>
                </div>

            </div>
        </div>
    </div>

{{-- Floating Container --}}
<div class="floating-reminder-container">

    @foreach ($teacher_reminders as $item)
        {{-- Only display if the status is NOT 'completed' --}}
        @if (strtolower($item->status) !== 'completed')
            
           
            <div class="blinking-note">
                 <i class="fas fa-exclamation-circle mr-2"></i><span class="font-weight-bold">Reminder</span>
                <h6 style="font-weight: bold; margin-bottom: 5px;">
                    {{ $item->teacher ? $item->teacher->name : 'Teacher ID: ' . $item->teacher_id }} - 
                    <span style="font-size: 0.9em; color: #555;">[{{ $item->category }}]</span>
                </h6>
                
                {{-- Reminder Description (Limited text) --}}
                <p style="margin-bottom: 0;">
                    {{ \Illuminate\Support\Str::limit(strip_tags($item->description), 200, '...') }}
                </p>
                
                {{-- Source Staff --}}
                <span class="note-source">
                    From: 
                    {{-- Assumes the Staff relationship is eager-loaded ($item->staff->name) --}}
                    @if ($item->staff)
                        {{ $item->staff->name }}
                    @else
                        Staff ID: {{ $item->staff_id }}
                    @endif
                </span>
                
                {{-- ================================================= --}}
                {{-- FORM BUTTON TO UPDATE STATUS TO 'COMPLETED' (DONE) --}}
                {{-- ================================================= --}}
                <form 
                    action="{{ route('teacher-reminder.update_status', $item->id) }}" 
                    method="POST" 
                    style="margin-top: 10px; text-align: right;"> {{-- Align button to the right --}}
                    
                    @csrf
                    @method('PUT')

                    {{-- Hidden input to send the new status value --}}
                    <input type="hidden" name="status" value="Completed">

                    <button type="submit"
                        onclick="return confirm('Are you sure you want to mark this reminder as completed?')"
                        class="btn btn-sm btn-secondary" 
                        style="font-size: 12px; padding: 3px 8px;"
                        title="Mark as Done">
                        <i class="fas fa-check"></i> Done
                    </button>
                </form>
                {{-- ================================================= --}}

            </div>
        @endif
   @endforeach
    
</div>

    <div class="container mb-5 mt-5">
        <div class="row">
            {{-- @foreach ($arr as $key => $item) --}}
            {{-- @php
                        // cek apakah due_date sudah melawati 14 hari, jika iya tampilkan jika tidak jangan tampilkan
                        // $due_date = Carbon\Carbon::parse($item->due_date);
                        // $interval = Carbon\Carbon::now()->diffInDays($due_date);
                        // if ($interval <= 14) {
                        //     $hidden_data = '';
                        // } else {
                        //     $hidden_data = 'hidden';
                        // }
                        // cek datanya apakah
                    @endphp --}}
            {{-- <div class="col-md-3">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="height: 120px">
                            <strong>Remember to Input Score in </strong> <br> <span style="font-size: 12px">
                                {{ $item->class . ' - ' . $item->review_test }}</span>
            <p><i class="fas fa-info-circle"> {{ $item->name }}</i></p>
        </div>

    </div> --}}
    @php

    // dd($arr);
    // Mengelompokkan array secara manual
    // $groupedItems = [];
    // $testing = '';
    // foreach ($arr as $item) {
    // $groupedItems[$item->program , $item->day1 , $item->day2 , $item->course_time][] = $item;
    // }
    $groupedItems = [];
    $testing = '';

    foreach ($arr as $item) {
    if ($item->type == 'test') {
    $key =
    'Remember to input the scores of :' .
    $item->review_test .
    '-' .
    $item->program .
    '-' .
    $item->day1 .
    ' ' .
    $item->day2 .
    '-' .
    $item->course_time;
    } else {
    $key =
    'Remember to input : ' .
    $item->review_test .
    '-' .
    $item->program .
    '-' .
    $item->day1 .
    ' ' .
    $item->day2 .
    '-' .
    $item->course_time;
    }

    $groupedItems[$key][] = $item;
    }

    // dd($groupedItems);

    @endphp

    @foreach ($groupedItems as $program => $items)
    {{-- {{ dd($groupedItems) }} --}}
    <div class="col-md-4 box">
        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="height: auto">

            @if (!empty($items))
            {{-- @php
                                    $firstItem = $items[0]; // Mengambil elemen pertama dari $items
                                @endphp --}}
            <p class="mb-2" style="font-size: 15px;">
                {{ $program }}
                {{-- {{ $program . ' ' . $firstItem->day1 . ' ' . $firstItem->day2 . ' at ' . $firstItem->course_time }} --}}
            </p>
            @endif
            {{-- <p class="mb-2" style="font-size: 15px;">
                                {{ $program . ' ' . $item->day1 . ' ' . $item->day2 . ' at ' . $item->course_time }}
            </p> --}}

            @foreach ($items as $item)
            <p><i class="fas fa-info-circle"> {{ $item->name }}</i></p>
            @endforeach
        </div>
    </div>
    @endforeach
    {{-- @endforeach --}}
</div>
</div>



<div class="page-inner mt--5">
    @if (session('message'))
    @if ($arr != null)
    <script>
        swal("Need to follow up", "", {
            icon: "info",
            buttons: {
                confirm: {
                    className: 'btn btn-success'
                },
                dismiss: {
                    className: 'btn btn-secondary'
                },
            },
        }).then((result) => {
            console.log(result);
            /* Read more about isConfirmed, isDenied below */
            if (result == true) {
                window.location = "{{ url('/attendance/reminder') }}"
            }
        });
    </script>
    @endif
    @endif
    @if (Auth::guard('teacher')->check() == false)
    <div class="row mt--2">
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-warning card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="flaticon-users"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Student</p>

                                <h4 class="card-title">{{ $data->student }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-info card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Parent</p>
                                {{-- <h4 class="card-title">{{ $data->parent }}</h4> --}}
                                <h4 class="card-title"> {{ $parent }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-success card-round">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Teacher </p>
                                <h4 class="card-title">{{ $data->teacher }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Announcement</h4>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($data->announces)
                                    <!--<img style="width: 100%"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                src="{{ url('/storage') . '/' . $data->announces->banner }}" alt="">-->
                                    <img style="width: 100%" src="{{ url($data->announces->banner) }}"
                                        alt="">
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    @if ($data->announces)
                                    <p>{{ $data->announces->description }}</p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body border-1" style="background-color: #cdcdcd">
                    <h5 class="card-title text-center text-white mb-2" style="font-weight: bold">🎉 Birthday Point
                    </h5>
                    {{-- <small class="text-white text-center text-danger">*Ignore if the birthday field has already
                                been
                                filled in</small> --}}

                    {{-- {{ $student_birthday_notification }} --}}

                    <ul class="list-group list-group-flush">
                        @php
                        $todayMonthDay = date('m-d'); // Format bulan-tanggal hari ini
                        $todayDay = date('l'); // Nama hari ini (Monday, Tuesday, dll.)

                        // Ambil daftar student_id yang sudah memiliki point_category_id = 7 di tahun ini
                        $students_with_points = DB::table('attendance_detail_points')
                        ->where('point_category_id', 7)
                        ->whereYear('created_at', date('Y'))
                        ->pluck('attendance_detail_id') // Ambil ID attendance_details
                        ->toArray();

                        // Ambil daftar student_id dari attendance_details
                        $students_with_attendance = DB::table('attendance_details')
                        ->whereIn('id', $students_with_points)
                        ->pluck('student_id') // Ambil student_id
                        ->toArray();

                        // Filter siswa yang belum memiliki point_category_id = 7 tahun ini
                        $birthday_points = array_filter($student_birthday, function ($s) use (
                        $todayMonthDay,
                        $todayDay,
                        $students_with_attendance,
                        ) {
                        // Jika student_id sudah ada dalam daftar attendance_detail_points, maka tidak ditampilkan
                        if (in_array($s['id'], $students_with_attendance)) {
                        return false;
                        }

                        // Ambil bulan dan tanggal dari ulang tahun siswa
                        $studentMonthDay = date('m-d', strtotime($s['birthday']));

                        // Hitung batas akhir tampilan (7 hari setelah ulang tahun)
                        $birthday_plus_7 = date('m-d', strtotime($s['birthday'] . ' +7 days'));

                        // Cek apakah hari ini dalam rentang ulang tahun hingga 7 hari setelahnya
                        $is_within_7_days =
                        $todayMonthDay >= $studentMonthDay && $todayMonthDay <= $birthday_plus_7;

                            // Cek apakah hari ini cocok dengan day1 atau day2 siswa
                            $is_matching_day=in_array($todayDay, [$s['day1'], $s['day2']]);

                            return $is_within_7_days && $is_matching_day;
                            });

                            // dd($birthday_points);

                            @endphp
                            @if (!empty($student_birthday_notification))
                            @foreach ($student_birthday_notification as $student)
                            <li
                            class="list-group-item mt-2 bg-white rounded shadow-sm p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">👦 {{ $student['name'] }}</h6>
                                <p class="mb-1 text-muted text-small">
                                    🏫 <strong>Class:</strong> {{ $student['class'] }} -
                                    {{ $student['day1'] . '' . $student['day2'] . ' ' . $student['course_time'] }}
                                    <br>
                                    👨‍🏫 <strong>Teacher:</strong> {{ $student['teacher'] ?? 'Unknown' }}
                                    <br>
                                    📅<strong>Birthday:</strong>
                                    {{ date('F j', strtotime($student['birthday'])) }}

                                </p>
                            </div>
                            <span class="badge bg-danger text-white rounded-pill px-3 py-2 fs-6">
                                {{ $student['age'] }} yrs
                            </span>
                            </li>
                            @endforeach
                            @else
                            <li class="list-group-item text-muted text-center bg-light rounded shadow-sm p-3">
                                🎉 No birthday points today.
                            </li>
                            @endif

                    </ul>
                </div>
            </div>
        </div>



    </div>
</div>
</div>
@endsection