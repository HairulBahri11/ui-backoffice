@extends('template.app')

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
                    //     $groupedItems[$item->program , $item->day1 , $item->day2 , $item->course_time][] = $item;
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
                                'Remember to input :  ' .
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
                            <h5 class="card-title text-center text-white">🎉 Today's Birthday</h5>
                            <ul class="list-group list-group-flush">
                                @php
                                    $today_birthdays = array_filter(
                                        $student_birthday,
                                        fn($s) => $s['is_today_birthday'],
                                    );
                                @endphp

                                @if (!empty($today_birthdays))
                                    @foreach ($today_birthdays as $student)
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center bg-light rounded my-1 shadow-sm">
                                            <div>
                                                <strong>👦 {{ $student['name'] }}</strong><br>
                                                <small>🏫 Class: {{ $student['class'] }}</small><br>
                                                <small>👨‍🏫 Teacher:
                                                    {{ $student['teacher'] ?? 'Unknown' }}</small>
                                            </div>
                                            <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                                                {{ $student['age'] }} yrs
                                            </span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="list-group-item text-muted text-center bg-white rounded shadow-sm">No
                                        birthdays today.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
@endsection
