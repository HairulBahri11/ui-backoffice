@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Lesson Plans</h2>
                </div>
                @if(Auth::guard('teacher')->check() == true)
                @if(now()->format('H:i') < '15:01' )
                    <div class="ml-md-auto py-2 py-md-0">
                    <a href="{{ url('/lesson-plan/create') }}" class="btn btn-secondary btn-round">Add Data</a>
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    @if (session('success'))
    <script>
        swal("Success", "{{ session('success') }}!", {
            buttons: {
                confirm: {
                    className: 'btn btn-success'
                }
            },
        });
    </script>
    @endif
    @if (session('error'))
    <script>
        swal("Error", "{{ session('error') }}!", {
            buttons: {
                confirm: {
                    className: 'btn btn-danger'
                }
            },
        });
    </script>
    @endif

    @php
    $dayOptions = [
    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
    4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'
    ];
    $currentDayOfWeek = now()->dayOfWeekIso;
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-filter"></i> Filter Data</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            @if(Auth::guard('teacher')->check() == true && Auth::guard('teacher')->id() != 21)
                            <div class="col-md-4 mt-2">
                                <label>Day</label>
                                <select name="day" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- All Days --</option>
                                    @foreach($dayOptions as $key => $name)
                                    <option value="{{ $key }}" {{ request('day', $currentDayOfWeek) == $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <div class="col-md-3 mt-2">
                                <label>Teacher</label>
                                <select name="teacher_id" class="form-control select2">
                                    <option value="">-- All Teachers --</option>
                                    @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Day</label>
                                <select name="day" class="form-control">
                                    <option value="">-- Choose Day --</option>
                                    @foreach($dayOptions as $key => $name)
                                    <option value="{{ $key }}" {{ request('day') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Created On</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Class</label>
                                <select name="class_id" class="form-control">
                                    <option value="">-- All Classes --</option>
                                    @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->program }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 text-right mt-3">
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-border btn-warning mr-2">Reset Filter</a>
                                <button type="submit" class="btn btn-sm btn-primary">Apply Filters</button>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lesson Plans Data</h4>

                    @if(Auth::guard('teacher')->check() == true)
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('lesson-plan.create') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus"></i> Add Lesson Plan
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Teacher</th>
                                    <th>Class</th>
                                    <th>Topic</th>
                                    <th>Created On</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($lessonPlans as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->teacher->name ?? $item->teacher_id }}</td>
                                    <td>
                                        {{ $item->price->program ?? $item->class ?? $item->class_id }}
                                        ({{ $dayOptions[$item->day1] ?? '' }}
                                        {{ $dayOptions[$item->day2] ?? '' }})
                                        {{ $item->course_time }}
                                    </td>
                                    <td>{{ $item->topic }}</td>
                                    <td>{{ now()->parse($item->created_at)->format('D d F Y H:i') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('lesson-plan.destroy', $item->id) }}" method="POST" class="form-inline d-flex justify-content-center">
                                            @method('delete')
                                            @csrf

                                            <a href="{{ route('lesson-plan.show', $item->id) }}" class="btn btn-xs btn-primary mr-1" title="Detail Data">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @php
                                            $isCreatedToday = \Carbon\Carbon::parse($item->created_at)->isToday();
                                            $isBeforeThreePm = now()->format('H:i') < '15:00' ;
                                                $isPastDays=\Carbon\Carbon::parse($item->created_at)->isPast() && !$isCreatedToday;

                                                $canEdit = ($isCreatedToday && $isBeforeThreePm) || $isPastDays;
                                                @endphp

                                                @if($canEdit)
                                                <a href="{{ url('/lesson-plan/' . $item->id . '/edit') }}" class="btn btn-xs btn-info mr-1" title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @else
                                                <button type="button" class="btn btn-xs btn-secondary mr-1" title="Editing Locked" disabled>
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                                @endif

                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <button type="submit" onclick="return confirm('Are you sure you want to delete this data ??')" class="btn btn-xs btn-danger" title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection