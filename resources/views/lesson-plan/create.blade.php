@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-4">
            <h2 class="text-white pb-2 fw-bold">Lesson Plan Planner</h2>
        </div>
    </div>
    @if (session('error'))
    <script>
        swal("Gagal", "{{ session('error') }}!", {
            icon: "danger",
            buttons: {
                confirm: {
                    className: 'btn btn-danger'
                }
            },
        });
    </script>
    @endif

    <div class="page-inner mt--5">
        <form action="{{ route('lesson-plan.store') }}" method="POST" id="main-lesson-plan-form">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 form-group">
                                    <label for="select-day" class="fw-bold text-dark"><i class="fas fa-calendar-day text-success mr-1"></i> Choose Day:</label>
                                    <select class="form-control" id="select-day" name="selected_day" required>
                                        <option value="">-- Select Day --</option>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                    </select>
                                </div>

                                <div class="col-md-8 form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="fw-bold text-dark mb-0"><i class="fas fa-school text-info mr-1"></i> Available Classes:</label>
                                        <div id="bulk-select-actions" style="display: none;">
                                            <button type="button" class="btn btn-xs btn-outline-primary mr-1" id="btn-select-all">Select All</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-unselect-all">Unselect All</button>
                                        </div>
                                    </div>
                                    <div id="class-selection-wrapper" class="p-3 border rounded d-flex flex-wrap gap-2" style="background: #fafafa; min-height: 45px; max-height: 180px; overflow-y: auto;">
                                        <span class="text-muted italic">Please select a day first to load schedules...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="dynamic-forms-container">
            </div>

            <div class="row id-submit-container mb-5" id="submit-all-container" style="display: none;">
                <div class="col-md-12 text-right">
                    <a href="{{ route('lesson-plan.index') }}" class="btn btn-danger btn-round mr-2">Cancel</a>
                    <button type="submit" class="btn btn-success btn-round px-5 shadow">
                        <i class="fas fa-save mr-2"></i> Save All Lesson Plans
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .gap-2 {
        gap: 10px;
    }

    .class-card-checkbox {
        background: #fff;
        padding: 8px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .class-card-checkbox:hover:not(.disabled-card) {
        border-color: #01c293;
        background: #f4fbf9;
    }

    .disabled-card {
        background: #fbeeeefc !important;
        border-color: #f5c6cb !important;
        cursor: not-allowed !important;
        opacity: 0.85;
    }
</style>

<script>
    let availableClasses = [];

    // 1. Ambil data kelas saat Day berubah
    document.getElementById('select-day').addEventListener('change', function() {
        const day = this.value;
        const selectionWrapper = document.getElementById('class-selection-wrapper');
        const formsContainer = document.getElementById('dynamic-forms-container');
        const submitContainer = document.getElementById('submit-all-container');
        const bulkActions = document.getElementById('bulk-select-actions');

        selectionWrapper.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin mr-2"></i>Loading schedules...</span>';
        formsContainer.innerHTML = '';
        submitContainer.style.display = 'none';
        bulkActions.style.display = 'none';

        if (!day) {
            selectionWrapper.innerHTML = '<span class="text-muted">Please select a day first...</span>';
            return;
        }

        fetch(`/lesson-plan/get-classes?day=${day}`)
            .then(response => response.json())
            .then(data => {
                availableClasses = data;
                selectionWrapper.innerHTML = '';

                if (data.length === 0) {
                    selectionWrapper.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>No active classes found.</span>';
                    return;
                }

                bulkActions.style.display = 'block';

                data.forEach((item, index) => {
                    // Status true didapatkan dari kalkulasi 1 minggu setelah day2 di Controller
                    const isLockedByDay2Rule = item.already_filled_this_week === true;

                    selectionWrapper.innerHTML += `
                        <div class="custom-control custom-checkbox class-card-checkbox m-1 ${isLockedByDay2Rule ? 'disabled-card' : ''}">
                            <input type="checkbox" class="custom-control-input class-selector-checkbox" 
                                   value="${item.priceid}" id="chk-${index}" 
                                   data-index="${index}"
                                   data-time="${item.course_time}" 
                                   data-program="${item.program || 'No Program'}"
                                   data-students="${item.total_students}"
                                   data-day1="${item.day1_name || '-'}"
                                   data-day2="${item.day2_name || '-'}"
                                   data-day1_id="${item.day1 || '-'}"
                                   data-day2_id="${item.day2 || '-'}"
                                   ${isLockedByDay2Rule ? 'disabled' : ''}>
                            <label class="custom-control-label fw-bold ${isLockedByDay2Rule ? 'text-danger' : 'text-dark'}" for="chk-${index}" style="cursor:${isLockedByDay2Rule ? 'not-allowed' : 'pointer'}">
                                ${item.program || 'Class'}
                                <span class="badge ml-1" style="background:#e1f5fe; color:#0288d1; font-size:11px;">${item.course_time}</span>
                                ${isLockedByDay2Rule ? '<span class="badge ml-1" style="background-color: #dc3545; color: white; font-size:10px;"><i class="fas fa-lock mr-1"></i>Locked</span>' : ''}
                            </label>
                        </div>
                    `;
                });

                document.querySelectorAll('.class-selector-checkbox:not(:disabled)').forEach(checkbox => {
                    checkbox.addEventListener('change', handleClassSelection);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                selectionWrapper.innerHTML = '<span class="text-danger">Error loading data.</span>';
            });
    });

    // Aksi Tombol Select All & Unselect All (Hanya menargetkan checkbox aktif / bukan readonly)
    document.getElementById('btn-select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.class-selector-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                cb.checked = true;
                handleClassSelection.call(cb);
            }
        });
    });

    document.getElementById('btn-unselect-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.class-selector-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            if (cb.checked) {
                cb.checked = false;
                handleClassSelection.call(cb);
            }
        });
    });

    // 2. Render Form Card Menggunakan Array Index
    function handleClassSelection() {
        const formsContainer = document.getElementById('dynamic-forms-container');
        const submitContainer = document.getElementById('submit-all-container');

        const classId = this.value;
        const index = this.getAttribute('data-index');
        const courseTime = this.getAttribute('data-time');
        const program = this.getAttribute('data-program');
        const students = this.getAttribute('data-students');
        const day1 = this.getAttribute('data-day1');
        const day2 = this.getAttribute('data-day2');
        const day1Id = this.getAttribute('data-day1_id');
        const day2Id = this.getAttribute('data-day2_id');
        const formId = `form-card-${classId.replace(/[^a-zA-Z0-9]/g, '-')}`;

        if (this.checked) {
            if (document.getElementById(formId)) return;

            const cardHTML = `
                <div class="col-md-6 mb-4" id="${formId}">
                    <div class="card shadow-sm border-0" style="border-radius: 8px; overflow: hidden;">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #01c293, #00a881); padding: 15px 20px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title fw-bold text-white mb-0" style="font-size: 1.2rem;">${program}</h4>
                                </div>
                                <span class="badge badge-light text-success fw-bold px-2 py-1" style="font-size: 11px;">
                                    <i class="fas fa-users mr-1"></i> ${students} Students
                                </span>
                            </div>
                        </div>
                        <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between text-muted" style="font-size: 14px;">
                            <span><i class="far fa-clock text-warning"></i> <strong>Time:</strong> ${courseTime}</span>
                            <span><i class="far fa-calendar-alt text-info"></i> <strong>Schedule:</strong> ${day1} & ${day2}</span>
                        </div>
                        
                        <input type="hidden" name="plans[${index}][class_id]" value="${classId}">
                        
                        <div class="card-body py-3">
                            <div class="form-group px-0 py-1 mb-2">
                                <label class="mb-1 fw-bold text-dark">Topic <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="plans[${index}][topic]" required placeholder="What is the topic today?">
                            </div>
                            <div class="form-group px-0 py-1 mb-2">
                                <label class="mb-1 fw-bold text-dark">Flashcards</label>
                                <textarea class="form-control" name="plans[${index}][flashcards]" rows="2" placeholder="List vocabulary..."></textarea>
                            </div>
                            <div class="form-group px-0 py-1 mb-2">
                                <label class="mb-1 fw-bold text-dark">Exercise</label>
                                <textarea class="form-control" name="plans[${index}][exercise]" rows="2" placeholder="Task or quiz..."></textarea>
                            </div>
                            <div class="form-group px-0 py-1 mb-0">
                                <label class="mb-1 fw-bold text-dark">Activity</label>
                                <textarea class="form-control" name="plans[${index}][activity]" rows="2" placeholder="Games or practice steps..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="plans[${index}][day1]" value="${day1Id}">
                <input type="hidden" name="plans[${index}][day2]" value="${day2Id}">
                <input type="hidden" name="plans[${index}][course_time]" value="${courseTime}">
            `;
            formsContainer.insertAdjacentHTML('beforeend', cardHTML);
        } else {
            const existingCard = document.getElementById(formId);
            if (existingCard) existingCard.remove();
        }

        const totalChecked = document.querySelectorAll('.class-selector-checkbox:checked').length;
        submitContainer.style.display = totalChecked > 0 ? 'block' : 'none';
    }
</script>
@endsection