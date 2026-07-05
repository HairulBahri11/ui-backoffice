@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Create Print Request</h2>
                    <h5 class="text-white op-7-5">Submit files and printing specifications seamlessly.</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="{{ url('/print-out') }}" class="btn btn-white btn-border btn-round">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&amp;times;</span>
            </button>
        </div>
        @endif

        <form action="{{ url('/print-out/store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="class_id" id="class_id">
            <input type="hidden" name="course_time" id="course_time">
            <input type="hidden" name="day1_id" id="day1_id">
            <input type="hidden" name="day2_id" id="day2_id">

            <div class="row">
                <div class="col-md-5">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <div class="card-title text-primary fw-bold">
                                <i class="fas fa-chalkboard-teacher mr-2"></i> Class &amp; File Upload
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="form-group pt-0">
                                <label for="schedule_select" class="font-weight-bold">Select Active Schedule <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg border-primary" id="schedule_select" required>
                                    <option value="">-- Choose Assigned Schedule --</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}"
                                        data-time="{{ $class->course_time }}"
                                        data-day1="{{ $class->day1_id }}"
                                        data-day2="{{ $class->day2_id }}">
                                        {{ $class->program_name }} | {{ $class->day1_name }} &amp; {{ $class->day2_name }} ({{ $class->course_time }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group pb-0">
                                <label class="font-weight-bold">Upload Document File <span class="text-danger">*</span></label>
                                <div class="border border-primary rounded p-4 text-center bg-light position-relative" style="border-style: dashed !important; border-width: 2px !important;">
                                    <i class="fas fa-cloud-upload-alt text-primary fa-3x mb-3"></i>
                                    <h5 class="font-weight-bold text-dark mb-1">Click to select files</h5>
                                    <p class="text-muted small mb-3">Supports PDF, DOCX(Max 5MB)</p>
                                    <input type="file" name="document_file" id="document_file" class="form-control-file position-absolute w-100 h-100" style="opacity: 0; top: 0; left:0; cursor: pointer;" required>
                                    <div id="file_name_preview" class="badge badge-success text-wrap p-2 d-none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <div class="card-title text-success fw-bold">
                                <i class="fas fa-file-alt mr-2"></i> Notes and details for printing
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group pt-0">
                                <label for="note" class="font-weight-bold">Notes / Special Instructions <span class="text-danger">*</span></label>
                                <textarea class="form-control border-success" id="note" name="note" rows="8" placeholder="Please write instructions here (e.g., Print double-sided, Page 1-5 only, Black &amp; White, number of copies...)" required>{{ old('note') }}</textarea>
                            </div>
                        </div>
                        <div class="card-action bg-light text-right">
                            <button type="submit" class="btn btn-success btn-lg px-4 mr-2">
                                <i class="fas fa-save mr-1"></i> Save Request
                            </button>
                            <a href="{{ url('/print-out') }}" class="btn btn-danger btn-lg px-4">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Automap selection attributes to hidden inputs
    document.getElementById('schedule_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value !== "") {
            document.getElementById('class_id').value = selectedOption.value;
            console.log('Selected Class ID:', selectedOption.value);
            document.getElementById('course_time').value = selectedOption.getAttribute('data-time');
            document.getElementById('day1_id').value = selectedOption.getAttribute('data-day1');
            document.getElementById('day2_id').value = selectedOption.getAttribute('data-day2');
        } else {
            document.getElementById('class_id').value = "";
            document.getElementById('course_time').value = "";
            document.getElementById('day1_id').value = "";
            document.getElementById('day2_id').value = "";
        }
    });

    // Real-time preview indicator when file selection shifts
    document.getElementById('document_file').addEventListener('change', function(e) {
        const previewDiv = document.getElementById('file_name_preview');
        if (e.target.files.length > 0) {
            previewDiv.innerHTML = '<i class="fas fa-file mr-1"></i> Selected: ' + e.target.files[0].name;
            previewDiv.classList.remove('d-none');
        } else {
            previewDiv.classList.add('d-none');
        }
    });
</script>
@endsection