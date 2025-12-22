@extends('template.app')

@section('content')
    <style>
        /* Custom CSS for a Clean, Modern Look (Senada) */
        :root {
            --main-color: #01c293; /* Green Accent (Utama) */
            --main-hover-bg: #e5f7f2; /* Lightest Main Green */
            --assist-color: #94233fff; /* Darker Teal/Senada untuk Assist */
            --assist-bg: #faf0f0ff; /* Very Light Cyan/Teal Background */
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
        
        /* Highlight border for today */
        .card-day.border-primary {
            border-color: var(--main-color) !important;
            box-shadow: 0 0 10px rgba(1, 194, 147, 0.2);
        }

        /* --- SCHEDULE ITEMS --- */
        .schedule-item {
            border-left: 4px solid var(--main-color); /* Main: Green accent line */
            background-color: #f7fcfb; 
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: background-color 0.1s;
        }
        .schedule-item:hover {
            background-color: var(--main-hover-bg);
        }
        
        .schedule-item.assist {
            border-left: 4px solid var(--assist-color); /* Assist: Darker Teal accent line */
            background-color: var(--assist-bg); /* Very light, senada background */
        }
        /* Keep hover minimal for assist to avoid color conflict */
        /* .schedule-item.assist:hover {
            background-color: var(--assist-bg);
        } */

        /* --- BADGES --- */
        .time-badge {
            background-color: var(--main-color);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .time-badge.assist {
            background-color: var(--assist-color); /* Assist time badge */
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

        /* Warna untuk header 'Today' */
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
        {{-- Header with Schedule Title (Tetap menggunakan main color) --}}
        <div class="panel-header bg-primary-gradient" style="background:var(--main-color) !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Teaching Schedule Calendar</h2>
                        {{-- Pastikan teacher_name tampil, gunakan currentTeacherId jika tidak ada data --}}
                        @php
                            $teacherName = optional($data->first())->teacher_name ?? 
                                ($currentTeacherId ? 'Teacher ID: ' . $currentTeacherId : 'Schedule Overview');
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

                                // 1. Tentukan Minggu Saat Ini (Ambil dari Controller, bukan request)
                                try {
                                    // $startOfWeekDateString dilewatkan dari controller
                                    $currentDate = Carbon::parse($startOfWeekDate)->startOfWeek(Carbon::MONDAY);
                                } catch (\Exception $e) {
                                    $currentDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
                                }
                                
                                // 2. Hitung Tanggal Navigasi
                                $prevWeek = $currentDate->copy()->subWeek()->format('Y-m-d');
                                $nextWeek = $currentDate->copy()->addWeek()->format('Y-m-d');
                                
                                // 3. Tentukan rentang tanggal yang ditampilkan (Senin hingga Sabtu)
                                $weekDays = [];
                                $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                for ($i = 0; $i < count($dayOrder); $i++) {
                                    $date = $currentDate->copy()->addDays($i);
                                    $weekDays[$dayOrder[$i]] = $date;
                                }

                                // 4. Grouping jadwal (Logika ini sudah benar)
                                $schedules = [];
                                foreach ($data as $item) {
                                    if ($item->day1_name) {
                                        $schedules[$item->day1_name][] = $item;
                                    }
                                    // Hanya assist yang memiliki day2_name=null, jadi cek if ($item->day2_name) tidak akan menambah assist dua kali
                                    if ($item->day2_name && $item->day2_name !== $item->day1_name) {
                                        $schedules[$item->day2_name][] = $item;
                                    }
                                }

                               
                               
                                // Query string untuk teacher_id
                                $teacherQueryString = $currentTeacherId ? '&teacher_id=' . $currentTeacherId : ''; 
                                
                            @endphp

                            {{-- NAVIGASI MINGGUAN --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded" style="background:#f7fcfb;">
                                {{-- Tombol Previous Week --}}
                                <a href="{{ url()->current() }}?start_date={{ $prevWeek }}{{ $teacherQueryString }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-chevron-left"></i> Previous Week
                                </a>
                                {{-- Rentang Tanggal --}}
                                <h5 class="fw-bold mb-0 text-center">
                                    {{ $weekDays['Monday']->isoFormat('D MMM') }} - {{ $weekDays['Saturday']->isoFormat('D MMM YYYY') }}
                                </h5>
                                {{-- Tombol Next Week --}}
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

                                    {{-- Column for each Day --}}
                                    <div class="col-md-4 mb-4">
                                        <div class="card card-day shadow {{ $isToday ? 'border-primary' : '' }}">
                                            {{-- Header Hari --}}
                                            <div class="card-header-day d-flex justify-content-between align-items-center {{ $isToday ? 'is-today' : '' }}">
                                                <h5 class="fw-bold mb-0 text-uppercase" style="{{ $isToday ? 'color: white !important;' : '' }}">
                                                    <i class="fas fa-calendar-day mr-2"></i> {{ $dayName }}
                                                </h5>
                                                {{-- TANGGAL SPESIFIK --}}
                                                <span class="badge badge-dark">
                                                    {{ $dayDate->format('d M') }} 
                                                </span>
                                            </div>

                                            <div class="card-body p-3">
                                                @if (isset($schedules[$dayName]))
                                                    
                                                    @php
                                                        // Sort schedules by time before displaying
                                                        usort($schedules[$dayName], function($a, $b) {
                                                            return strtotime($a->course_time) - strtotime($b->course_time);
                                                        });
                                                    @endphp

                                                    @foreach ($schedules[$dayName] as $schedule)
                                                        @php
                                                            $isAssist = $schedule->role === 'assist';
                                                        @endphp
                                                        
                                                        {{-- SCHEDULE ITEM --}}
                                                        <div class="schedule-item mb-3 {{ $isAssist ? 'assist' : '' }}">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                
                                                                {{-- TIME BADGE --}}
                                                                <span class="time-badge {{ $isAssist ? 'assist' : '' }}">{{ Carbon::parse($schedule->course_time)->format('H:i') }}</span>
                                                                
                                                                {{-- STUDENTS COUNT & OPTIONAL ASSIST BADGE --}}
                                                                <small class="text-right d-flex align-items-center">
                                                                    
                                                                    @if ($isAssist)
                                                                        <span class="role-badge assist mr-2">
                                                                            <i class="fas fa-handshake"></i> Assist
                                                                        </span>
                                                                    @endif
                                                                   
                                                                    <span class="text-muted text-sm">
                                                                        <i class="fas fa-user-graduate mr-1"></i> {{ $schedule->total_students }}
                                                                    </span>
                                                                    
                                                                </small>
                                                            </div>
                                                            
                                                            {{-- PROGRAM NAME --}}
                                                            <div class="text-sm">
                                                                <strong class="text-dark">{{ $schedule->class }}</strong>
                                                            </div>
                                                            
                                                            {{-- ASSIST DETAILS / MAIN TEACHER NAME --}}
                                                            <div class="text-xs mt-1 {{ $isAssist ? 'text-secondary' : 'text-dark' }}" style="{{ $isAssist ? 'color: var(--assist-color) !important;' : 'font-weight: 500;' }}">
                                                                {{-- Tampilkan Guru Utama untuk Main, atau Guru Utama yang di-assist --}}
                                                                @if ($isAssist)
                                                                    <span>{{ str_replace('Assist: ', 'Main: ', $schedule->teacher_name) }}</span>
                                                                @else
                                                                    <span>Teacher: {{ $schedule->teacher_name }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    {{-- Empty State --}}
                                                    <div class="text-center p-4 text-muted border border-dashed rounded" style="border-style: dashed !important; border-color: #dee2e6 !important;">
                                                        <i class="far fa-smile-wink fa-2x mb-2"></i>
                                                        <p class="mb-0">No classes scheduled.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Insert row break after every 3 columns --}}
                                    @if ($loop->iteration % 3 == 0)
                                        <div class="w-100"></div> 
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection