@extends('template.app')

@section('content')
    <style>
        /* Custom CSS for a Clean, Modern Look */
        .card-day {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e9ecef; 
            transition: transform 0.2s, box-shadow 0.2s;
            background: #ffffff; 
        }
        .card-day:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }
        .card-header-day {
            background-color: #f8f9fa; 
            border-bottom: 2px solid #01c293; /* Accent color border */
            padding: 10px 15px;
            color: #343a40;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        /* Highlight border for today */
        .card-day.border-primary {
            border-color: #01c293 !important;
            box-shadow: 0 0 10px rgba(1, 194, 147, 0.3);
        }
        .schedule-item {
            border-left: 4px solid #01c293; /* Green accent line */
            background-color: #f7fcfb; 
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .time-badge {
            background-color: #01c293;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }
    </style>

    <div class="content">
        {{-- Header with Schedule Title --}}
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Teaching Schedule Calendar</h2>
                        {{-- Displaying the logged-in Teacher's Name --}}
                        <h5 class="text-white op-7 mb-2">{{ $data->first()->teacher_name ?? 'Your Schedule' }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-inner mt--5">
            {{-- SweetAlert Section --}}
            @if (session('status'))
                <script>
                    swal("Success", "{{ session('status') }}!", {
                        icon: "success",
                        buttons: { confirm: { className: 'btn btn-success' } },
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    swal("Failed", "{{ session('status') }}!", {
                        icon: "danger",
                        buttons: { confirm: { className: 'btn btn-danger' } },
                    });
                </script>
            @endif
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-0">Weekly Schedule Overview</h4>
                        </div>
                        <div class="card-body">

                            @php
                                use Carbon\Carbon;

                                // 1. Tentukan Minggu Saat Ini (Asumsikan $startOfWeekDate dilewatkan dari Controller)
                                // Jika $startOfWeekDate tidak ada, gunakan awal minggu ini (Senin)
                                try {
                                    $currentDate = isset($startOfWeekDate) ? Carbon::parse($startOfWeekDate)->startOfWeek(Carbon::MONDAY) : Carbon::now()->startOfWeek(Carbon::MONDAY);
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

                                // 4. Grouping jadwal (LOGIKA LAMA)
                                $schedules = [];
                                foreach ($data as $item) {
                                    if ($item->day1_name) {
                                        $schedules[$item->day1_name][] = $item;
                                    }
                                    if ($item->day2_name && $item->day2_name !== $item->day1_name) {
                                        $schedules[$item->day2_name][] = $item;
                                    }
                                }

                                // Ambil ID guru saat ini (untuk dilewatkan ke navigasi)
                                $currentTeacherId = $data->first()->teacher_id ?? request('teacher_id'); 
                                
                            @endphp

                            {{-- NAVIGASI MINGGUAN --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded" style="background:#f7fcfb;">
                                {{-- Tombol Previous Week --}}
                                <a href="{{ url()->current() }}?start_date={{ $prevWeek }}{{ $currentTeacherId ? '&teacher_id=' . $currentTeacherId : '' }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-chevron-left"></i> Previous Week
                                </a>
                                {{-- Rentang Tanggal --}}
                                <h5 class="fw-bold mb-0 text-center">
                                    {{ $weekDays['Monday']->isoFormat('D MMM') }} - {{ $weekDays['Saturday']->isoFormat('D MMM YYYY') }}
                                </h5>
                                {{-- Tombol Next Week --}}
                                <a href="{{ url()->current() }}?start_date={{ $nextWeek }}{{ $currentTeacherId ? '&teacher_id=' . $currentTeacherId : '' }}" class="btn btn-outline-primary btn-sm">
                                    Next Week <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>

                            <div class="row">
                                @foreach ($dayOrder as $dayName)
                                    @php
                                        // Ambil objek Carbon untuk hari ini
                                        $dayDate = $weekDays[$dayName];
                                        // Cek apakah hari ini adalah hari ini (untuk highlight)
                                        $isToday = $dayDate->isToday();
                                    @endphp

                                    {{-- Column for each Day --}}
                                    <div class="col-md-4 mb-4">
                                        {{-- Tambahkan class 'border-primary' jika hari ini --}}
                                        <div class="card card-day shadow {{ $isToday ? 'border-primary' : '' }}">
                                            <div class="card-header-day d-flex justify-content-between align-items-center {{ $isToday ? 'bg-primary text-white' : '' }}" style="{{ $isToday ? 'background-color: #01c293 !important; border-bottom: 2px solid #ffffff !important;' : '' }}">
                                                <h5 class="fw-bold mb-0 text-uppercase" style="{{ $isToday ? 'color: white !important;' : '' }}">
                                                    <i class="fas fa-calendar-day mr-2"></i> {{ $dayName }}
                                                </h5>
                                                {{-- TANGGAL SPESIFIK --}}
                                                <span class="badge badge-secondary {{ $isToday ? 'badge-light' : 'badge-primary' }}" style="{{ $isToday ? 'background: white !important; color: #01c293 !important;' : 'background: #01c293 !important; color: white !important;' }}">
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
                                                        <div class="schedule-item mb-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <span class="time-badge">{{ $schedule->course_time }}</span>
                                                                <small class="text-muted text-right">
                                                                    <i class="fas fa-user-graduate mr-1"></i> {{ $schedule->total_entri_duplikat }} Students
                                                                </small>
                                                            </div>
                                                            <div class="text-sm">
                                                                Program: <strong class="text-dark">{{ $schedule->class }}</strong>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center p-4 text-muted border-dashed border-gray">
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