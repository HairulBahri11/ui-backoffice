@extends('template.app')
@section('content')

<style>
    /* =======================================
   ENHANCED FLOATING STICKY NOTE STYLES
   ======================================= */

    /* 1. Define the Blinking Animation (More subtle flash) */
    @keyframes blinker {

        0%,
        100% {
            box-shadow: 0 6px 15px rgba(0, 72, 255, 0.6);
        }

        /* Stronger shadow/glow at start/end */
        50% {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        /* Softer shadow in the middle */
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
        animation: blinker 2s ease-in-out infinite;
        /* Slower, smoother animation */
        /* Soft gradient background */
        color: #fefcfcff;
        /* Darker text for readability */
        padding: 10px;
        border-radius: 10px;
        margin-top: 15px;
        /* Increased spacing */
        width: 280px;
        /* Slightly wider card */
        font-size: 14px;
        font-family: 'Arial', sans-serif;
        transition: transform 0.2s ease-in-out;
        /* Smooth transition */
    }

    /* Hover Effect: Lifts the card and stops the blinking */
    .blinking-note:hover {
        animation: none;
        transform: translateY(-5px);
        /* Lift effect */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .blinking-note h6 {
        color: #ffffffff;
        /* Deep red for emphasis on title */
    }

    .note-source {
        font-size: 0.85em;
        font-style: italic;
        color: white;
        border-top: 1px dashed rgba(51, 51, 51, 0.3);
        /* Dashed line separator */
        padding-top: 8px;
        margin-top: 10px;
        display: block;
        /* Ensure it takes full width */
    }

    /* Style for the 'All Reminders Completed' message */
    .floating-reminder-container>.btn-success {
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

        @foreach ($teacher_notes as $item)
        {{-- Only display if the status is NOT 'completed' --}}
        @if (strtolower($item->status) !== 'completed')


        <div class="blinking-note bg-primary">
            <i class="fas fa-exclamation-circle mr-2 mb-3"></i><span class="font-weight-bold">Notes</span>
            <h6 style="font-weight: bold; margin-bottom: 5px;">
                {{ $item->teacher ? $item->teacher->name : 'Teacher ID: ' . $item->teacher_id }} -
                <span style="font-size: 0.9em;">[{{ $item->category }}]</span>
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
                style=" text-align: right;"> {{-- Align button to the right --}}

                @csrf
                @method('PUT')

                {{-- Hidden input to send the new status value --}}
                <input type="hidden" name="status" value="Completed">

                <button type="submit"
                    onclick="return confirm('Are you sure you want to mark this reminder as completed?')"
                    class="btn btn-sm btn-success"
                    style="font-size: 12px; padding: 3px 3px;"
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

        <!-- cek apakah ada teacher_reminder -->
        @if (Auth::guard('teacher')->check() && $teacher_reminders->count() > 0)
        <div class="col-md-4">
            <div class="card p-0 border-0 rounded-3" style="max-width: 400px; background-color: #e7e1bfff;">

                <div class="card-header border-bottom-0 d-flex align-items-center justify-content-between p-3 bg-warning" style=" border-radius: 0.3rem 0.3rem 0 0;">
                    <h6 class="mb-0 text-dark fw-bold text-uppercase small" style="letter-spacing: 0.5px;">
                        🚨 TEACHER REMINDER
                    </h6>
                    <span class="badge text-light rounded-pill small" style="background-color: #ff7700ff;">
                        {{ $teacher_reminders->count() }} total
                    </span>
                </div>

                <div class="card-body p-3">



                    @foreach($teacher_reminders as $item)
                    <div class="mb-3 border-bottom border-light pb-3">

                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <div class="me-3">
                                <span class="text-uppercase fw-normal d-block">FROM:</span>
                                <p class="mb-0 fw-semibold text-dark">{{ $item->staff_id != 20 ? "Ms. Vonny" : "Ms. Dewi" }}</p>
                            </div>
                            <div class="text-end">
                                <span class="text-uppercase fw-normal d-block">TO:</span>
                                <p class="mb-0 fw-semibold text-dark">{{ $item->teacher->name }}</p>
                            </div>
                        </div>

                        <div class="p-3 rounded border-start border-4" style="background-color: #ffffff; border-color: #ff7700ff !important;">
                            <p class="mb-1 text-uppercase fw-bolder small text-warning" style="letter-spacing: 0.5px;">
                                📝 Description
                            </p>
                            <p class="mb-0 text-black compact-text" style="font-size: 0.875rem;">
                                {{ $item->description }} <span class="fw-bold">({{ $item->category }})</span>
                            </p>
                        </div>
                    </div>

                    @if (!$loop->last)
                    <div class="text-center my-3">
                        <hr class="my-0 mx-auto" style="width: 50px; border-top: 1px solid #dee2e6;">
                    </div>
                    @endif
                    @endforeach

                </div>
            </div>
        </div>
        @endif


    </div>
</div>
@endsection