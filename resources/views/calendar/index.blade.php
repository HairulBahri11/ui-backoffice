@extends('template.app')

@section('content')
<style>
    /* CSS Tetap Sama */
    :root {
        --main-color: #01c293;
        --main-hover-bg: #e5f7f2;
        --assist-color: #94233fff;
        --assist-bg: #faf0f0ff;
    }

    .card-day {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #ffffff;
    }

    .card-day:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.04);
    }

    .card-header-day {
        background-color: #f8f9fa;
        border-bottom: 2px solid var(--main-color);
        padding: 10px 15px;
        color: #343a40;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .card-day.border-primary {
        border-color: var(--main-color) !important;
        box-shadow: 0 0 10px rgba(1, 194, 147, 0.2);
    }

    /* MODIFIKASI: Schedule Item menjadi Pointer */
    .schedule-item {
        border-left: 4px solid var(--main-color);
        background-color: #f7fcfb;
        padding: 10px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
        cursor: pointer;
        /* Indikasi bisa diklik */
        position: relative;
    }

    .schedule-item:hover {
        background-color: var(--main-hover-bg);
    }

    .schedule-item.assist {
        border-left: 4px solid var(--assist-color);
        background-color: var(--assist-bg);
    }

    /* BARU: Style untuk List Siswa yang muncul di bawah */
    .student-list-container {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-top: none;
        margin-top: -10px;
        /* Merapatkan dengan item jadwal */
        margin-bottom: 15px;
        padding: 10px 15px;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        font-size: 0.85rem;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
    }

    .time-badge {
        background-color: var(--main-color);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: bold;
    }

    .time-badge.assist {
        background-color: var(--assist-color);
    }

    .role-badge {
        font-size: 0.75em;
        font-weight: 500;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .role-badge.assist {
        background-color: var(--assist-color);
        color: white;
    }

    .card-header-day.is-today {
        background-color: var(--main-color) !important;
        border-bottom: 2px solid #ffffff !important;
        color: white !important;
    }

    .card-header-day.is-today .badge {
        background: white !important;
        color: var(--main-color) !important;
    }
</style>

<div class="content">
    <div class="panel-header" style="background:var(--main-color) !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Teaching Schedule Calendar</h2>
                    @php
                    $teacherName = optional($data->first())->teacher_name ?? ($currentTeacherId ? 'Teacher ID: ' . $currentTeacherId : 'Schedule Overview');
                    @endphp
                    <h5 class="text-white op-7 mb-2">{{ $teacherName }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title mb-0">Weekly Schedule Overview</h4>
                    </div>
                    <div class="card-body">

                        @php
                        use Carbon\Carbon;
                        try {
                        $currentDate = Carbon::parse($startOfWeekDate)->startOfWeek(Carbon::MONDAY);
                        } catch (\Exception $e) {
                        $currentDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
                        }

                        $prevWeek = $currentDate->copy()->subWeek()->format('Y-m-d');
                        $nextWeek = $currentDate->copy()->addWeek()->format('Y-m-d');

                        $weekDays = [];
                        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        for ($i = 0; $i < count($dayOrder); $i++) {
                            $date=$currentDate->copy()->addDays($i);
                            $weekDays[$dayOrder[$i]] = $date;
                            }

                            $schedules = [];
                            foreach ($data as $item) {
                            if ($item->day1_name) { $schedules[$item->day1_name][] = $item; }
                            if ($item->day2_name && $item->day2_name !== $item->day1_name) { $schedules[$item->day2_name][] = $item; }
                            }
                            $teacherQueryString = $currentTeacherId ? '&teacher_id=' . $currentTeacherId : '';
                            @endphp

                            {{-- NAVIGASI MINGGUAN --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded" style="background:#f7fcfb;">
                                <a href="{{ url()->current() }}?start_date={{ $prevWeek }}{ $teacherQueryString }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-chevron-left"></i> Previous Week
                                </a>
                                <h5 class="fw-bold mb-0 text-center">
                                    {{ $weekDays['Monday']->isoFormat('D MMM') }} - {{ $weekDays['Saturday']->isoFormat('D MMM YYYY') }}
                                </h5>
                                <a href="{{ url()->current() }}?start_date={{ $nextWeek }}{{ $teacherQueryString }}" class="btn btn-outline-secondary btn-sm">
                                    Next Week <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>

                            <div class="row">
                                @foreach ($dayOrder as $dayName)
                                @php
                                $dayDate = $weekDays[$dayName];
                                $isToday = $dayDate->isToday();
                                @endphp

                                <div class="col-md-4 mb-4">
                                    <div class="card card-day shadow {{ $isToday ? 'border-primary' : '' }}">
                                        <div class="card-header-day d-flex justify-content-between align-items-center {{ $isToday ? 'is-today' : '' }}">
                                            <h5 class="fw-bold mb-0 text-uppercase" style="{{ $isToday ? 'color: white !important;' : '' }}">
                                                <i class="fas fa-calendar-day mr-2"></i> {{ $dayName }}
                                            </h5>
                                            <span class="badge badge-dark">{{ $dayDate->format('d M') }}</span>
                                        </div>

                                        <div class="card-body p-3">
                                            @if (isset($schedules[$dayName]))
                                            @php
                                            usort($schedules[$dayName], function($a, $b) {
                                            return strtotime($a->course_time) - strtotime($b->course_time);
                                            });
                                            @endphp

                                            @foreach ($schedules[$dayName] as $schedule)
                                            @php
                                            $isAssist = $schedule->role === 'assist';
                                            // Logika deteksi kelas Private (sesuaikan string 'private' dengan data di DB Anda)
                                            $isPrivate = (strpos(strtolower($schedule->class), 'private') !== false);
                                            @endphp

                                            {{-- ITEM JADWAL --}}
                                            <div class="schedule-item {{ $isAssist ? 'assist' : '' }} mb-2">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="time-badge {{ $isAssist ? 'assist' : '' }}">{{ Carbon::parse($schedule->course_time)->format('H:i') }}</span>
                                                    <small class="text-right d-flex align-items-center">
                                                        @if ($isAssist)
                                                        <span class="role-badge assist mr-2"><i class="fas fa-handshake"></i> Assist</span>
                                                        @endif
                                                        <span class="text-muted text-sm">
                                                            <i class="fas fa-users mr-1"></i> {{ $schedule->total_students }}
                                                        </span>
                                                    </small>
                                                </div>
                                                <div class="text-sm">
                                                    <strong class="text-dark">{{ $schedule->class }}</strong>
                                                </div>
                                                @if($isPrivate)
                                                <ul class="list-unstyled mb-0">
                                                    @if(!empty($schedule->student_list))
                                                    @foreach($schedule->student_list as $student)
                                                    <li class="mb-1 fw-bold" style="color: #a2b440;">
                                                        {{ trim($student) }}
                                                    </li>
                                                    @endforeach
                                                    @else
                                                    <li class="text-muted small italic">No student names.</li>
                                                    @endif
                                                </ul>

                                                @endif
                                                <div class="text-xs mt-1 {{ $isAssist ? 'text-secondary' : 'text-dark' }}" style="{{ $isAssist ? 'color: var(--assist-color) !important;' : 'font-weight: 500;' }}">
                                                    <span>{{ $isAssist ? str_replace('Assist: ', 'Main: ', $schedule->teacher_name) : 'Teacher: '.$schedule->teacher_name }}</span>
                                                </div>
                                            </div>
                                            @endforeach
                                            @else
                                            <div class="text-center p-4 text-muted border border-dashed rounded" style="border-style: dashed !important; border-color: #dee2e6 !important;">
                                                <i class="far fa-smile-wink fa-2x mb-2"></i>
                                                <p class="mb-0">No classes scheduled.</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($loop->iteration % 3 == 0) <div class="w-100"></div> @endif
                                @endforeach
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection