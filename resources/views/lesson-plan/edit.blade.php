@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Edit Lesson Plan</h2>
                    <h5 class="text-white op-7">Mengubah rincian konten rencana pembelajaran</h5>
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
        <form action="{{ route('lesson-plan.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-profile">
                        <div class="card-body pt-3">
                            <div class="user-profile text-center">
                                <div class="avatar avatar-xl">
                                    <span class="avatar-title rounded-circle border border-white bg-warning text-white" style="font-size: 2.5rem;">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                </div>

                                <div class="desc text-left pt-3">
                                    <p class="mb-1"><strong><i class="fas fa-chalkboard-teacher text-success mr-2"></i> Teacher:</strong></p>
                                    <p class="text-muted pl-4">{{ $item->teacher->name ?? $item->teacher_id }}</p>

                                    <p class="mb-1"><strong><i class="fas fa-school text-info mr-2"></i> Class:</strong></p>
                                    <p class="text-muted pl-4">
                                        {{ $item->price->program ?? $item->class_id }}
                                        ({{ $day[$item->day1] ?? $item->level }}
                                        {{ $day[$item->day2] ?? $item->level }}) - {{ $item->course_time }}
                                    </p>

                                    <p class="mb-1"><strong><i class="fas fa-users text-primary mr-2"></i> Total Students:</strong></p>
                                    <p class="text-muted pl-4">{{ $totalStudents }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-primary text-center text-white border-top text-small" style="font-size: 0.85rem;">
                            Created On: {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i:s') }}
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0">Edit Lesson Plan Content</h4>
                        </div>
                        <div class="card-body">

                            @php
                            // Memecah string topic_page (contoh: "5-8" menjadi ["5", "8"])
                            $topicPages = explode('-', $item->topic_page ?? '');
                            $topicStart = $topicPages[0] ?? '';
                            $topicEnd = $topicPages[1] ?? '';

                            // Memecah string flashcard_page (contoh: "1-25" menjadi ["1", "25"])
                            $flashcardPages = explode('-', $item->flashcard_page ?? '');
                            $flashcardStart = $flashcardPages[0] ?? '';
                            $flashcardEnd = $flashcardPages[1] ?? '';
                            @endphp

                            <!-- Section 1: Topic/Textbook -->
                            <div class="card mb-3 border">
                                <div class="card-header bg-light py-2">
                                    <label class="font-weight-bold text-dark m-0">
                                        <i class="fas fa-book-open text-primary mr-2"></i> Topic/Textbook <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="card-body p-2" style="background:#fafafa;">
                                    <!-- Range input untuk halaman/bab -->
                                    <div class="d-flex align-items-center mb-2">
                                        <input type="number" class="form-control text-center" name="topic_start" value="{{ old('topic_start', $topicStart) }}" style="width: 80px;" required>
                                        <span class="mx-2 font-weight-bold">—</span>
                                        <input type="number" class="form-control text-center" name="topic_end" value="{{ old('topic_end', $topicEnd) }}" style="width: 80px;" required>
                                    </div>
                                    <!-- Input teks utama untuk topik -->
                                    <input type="text" class="form-control" name="topic" value="{{ old('topic', $item->topic) }}" required placeholder="e.g., Bedouin People">
                                </div>
                            </div>

                            <!-- Section 2: Flashcards -->
                            <div class="card mb-3 border">
                                <div class="card-header bg-light py-2">
                                    <label class="font-weight-bold text-dark m-0">
                                        <i class="fas fa-images text-danger mr-2"></i> Flashcards
                                    </label>
                                </div>
                                <div class="card-body p-2" style="background:#fafafa;">
                                    <!-- Range input untuk nomor flashcard -->
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control text-center" name="flashcards_start" value="{{ old('flashcards_start', $flashcardStart) }}" style="width: 80px;">
                                        <span class="mx-2 font-weight-bold">—</span>
                                        <input type="number" class="form-control text-center" name="flashcards_end" value="{{ old('flashcards_end', $flashcardEnd) }}" style="width: 80px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Exercise/Supplement -->
                            <div class="card mb-3 border">
                                <div class="card-header bg-light py-2">
                                    <label class="font-weight-bold text-dark m-0">
                                        <i class="fas fa-dumbbell text-info mr-2"></i> Exercise/Supplement
                                    </label>
                                </div>
                                <div class="card-body p-2" style="background:#fafafa;">
                                    <textarea class="form-control" name="exercise" rows="3" placeholder="e.g., Booklet page 7">{{ old('exercise', $item->exercise) }}</textarea>
                                </div>
                            </div>

                            <!-- Section 4: Class Activity -->
                            <div class="card border">
                                <div class="card-header bg-light py-2">
                                    <label class="font-weight-bold text-dark m-0">
                                        <i class="fas fa-chalkboard text-success mr-2"></i> Class Activity
                                    </label>
                                </div>
                                <div class="card-body p-2" style="background:#fafafa;">
                                    <textarea class="form-control" name="activity" rows="3" placeholder="e.g., Fun quiz">{{ old('activity', $item->activity) }}</textarea>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-end">
                            <a href="{{ route('lesson-plan.index') }}" class="btn btn-secondary mr-2 btn-round">Cancel</a>
                            <button type="submit" class="btn btn-success btn-round px-4">
                                <i class="fas fa-save mr-1"></i> Update Lesson Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection