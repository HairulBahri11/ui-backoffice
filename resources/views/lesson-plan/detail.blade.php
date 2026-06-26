@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Lesson Plan Details</h2>
                    <h5 class="text-white op-7">This is the details of the lesson plan</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="{{ route('lesson-plan.index') }}" class="btn btn-white btn-border btn-round">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
    $day = [
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
    ];
    @endphp

    <div class="page-inner mt--5">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-profile">

                    <div class="card-body pt-3">
                        <div class="user-profile text-center">

                            <!-- profile pake avatar icon -->
                            <div class="avatar avatar-xl">
                                <span class="avatar-title rounded-circle border border-white bg-success text-white" style="font-size: 2.5rem;">
                                    <i class="fas fa-pen-alt "></i>
                                </span>
                            </div>

                            <div class="desc text-left  pt-3">
                                <p class="mb-1"><strong><i class="fas fa-chalkboard-teacher text-success mr-2"></i> Teacher:</strong></p>
                                <p class="text-muted pl-4">{{ $item->teacher->name ?? $item->teacher_id }}</p>

                                <p class="mb-1"><strong><i class="fas fa-school text-info mr-2"></i> Class:</strong></p>
                                <p class="text-muted pl-4">{{ $item->price->program ?? $item->class_id }} ({{ $day[$item->day1] ?? $item->level }} {{ $day[$item->day2] ?? $item->level }})</p>

                                <p class="mb-1"><strong><i class="far fa-clock text-warning mr-2"></i> Course Time:</strong></p>
                                <p class="text-muted pl-4">{{ $item->course_time }}</p>

                                <p class="mb-1"><strong><i class="fas fa-users text-primary mr-2"></i> Total Students:</strong></p>
                                <p class="text-muted pl-4">{{ $totalStudents }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-primary text-center text-white border-top text-small" style="font-size: 0.85rem;">
                        Created On: {{now()->parse($item->created_at)->format('d M Y H:i:s')}}
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="card-title">Lesson Plan Content</h4>
                    </div>
                    <div class="card-body">


                        <div class="accordion accordion-secondary">

                            <div class="row">
                                <div class="col-md-6">

                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <div class="span-title fw-bold text-dark">
                                                <i class="fas fa-book-open text-primary mr-2"></i> Topic
                                            </div>
                                        </div>
                                        <div class="card-body" style="background:#fafafa;">
                                            {!! nl2br(e($item->topic ?? 'Tidak ada data konten belajar.')) !!}
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <div class="span-title fw-bold text-dark">
                                                <i class="fas fa-images text-danger mr-2"></i> Flashcards
                                            </div>
                                        </div>
                                        <div class="card-body" style="background:#fafafa;">
                                            {!! nl2br(e($item->flashcards ?? 'Tidak ada data flashcard.')) !!}
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <div class="span-title fw-bold text-dark">
                                                <i class="fas fa-dumbbell text-info mr-2"></i> Exercise
                                            </div>
                                        </div>
                                        <div class="card-body" style="background:#fafafa;">
                                            {!! nl2br(e($item->exercise ?? 'Tidak ada data latihan.')) !!}
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <div class="span-title fw-bold text-dark">
                                                <i class="fas fa-chalkboard text-success mr-2"></i> Activity
                                            </div>
                                        </div>
                                        <div class="card-body" style="background:#fafafa;">
                                            {!! nl2br(e($item->activity ?? 'Tidak ada data aktivitas.')) !!}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection