@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Print Out</h2>
                    <h5 class="text-white op-7-5">Document generation and printing system menu</h5>
                </div>

                @if(Auth::guard('teacher')->check() == true)
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="{{ url('/print-out/create') }}" class="btn btn-secondary btn-round">Add Data</a>
                </div>
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Documents Ready to Print</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Teacher</th>
                                        <th>Class & Schedule</th>
                                        <th>Notes</th>
                                        <th>Total Files</th>
                                        <th>Created On</th>
                                        <th class="text-center" style="width: 15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    $day = [
                                    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
                                    4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'
                                    ];
                                    @endphp

                                    {{-- Menggunakan data baru hasil mapping dari model/controller utama --}}
                                    @foreach ($printOut as $item)
                                    @php
                                    // Mengumpulkan semua relasi berkas dokumen ke dalam array format JSON untuk Modal Preview
                                    $filesData = [];

                                    // Menyesuaikan pemanggilan relasi baru (Contoh: $item->documents atau $item->printoutDetails)
                                    // Pastikan nama relasi di bawah ini disesuaikan dengan nama Method HasMany di Model PrintOut baru Anda
                                    if($item->documentPrintouts) {
                                    foreach($item->documentPrintouts as $doc) {
                                    $filesData[] = [
                                    'id' => $doc->id,
                                    'link' => asset($doc->file_link)
                                    ];
                                    }
                                    }
                                    @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->teacher->name ?? $item->teacher_id }}</td>
                                        <td>
                                            {{ $item->price->program ?? $item->class ?? $item->class_id }} <br>
                                            <small class="text-muted">
                                                {{ $day[$item->day1_id] ?? ' - ' }} &
                                                {{ $day[$item->day2_id] ?? ' - ' }}
                                                ({{ $item->course_time }})
                                            </small>
                                        </td>
                                        <td class="text-muted">{{ $item->note ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ count($filesData) }} Files</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y h:i A') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex flex-row justify-content-center align-items-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-primary mr-1 btn-open-print-modal"
                                                    data-files="{{ json_encode($filesData) }}"
                                                    title="View & Download Files">
                                                    <i class="fas fa-folder-open mr-1"></i> Files
                                                </button>

                                                <form action="{{ route('print-out.destroy', $item->id) }}" method="POST" class="form-inline" onsubmit="return confirm('Are you sure you want to delete this print request and all files?')">
                                                    @method('delete')
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
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

{{-- Bagian Modal & Script JavaScript tetap dipertahankan karena sudah dinamis membaca object data-files --}}
<div class="modal fade" id="printPreviewModal" tabindex="-1" role="dialog" aria-labelledby="printPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="printPreviewModalLabel">
                    <i class="fas fa-file-alt mr-2"></i> Document Preview & Download Menu
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card shadow-sm mb-0">
                            <div class="card-header bg-white font-weight-bold">File List</div>
                            <div class="list-group list-group-flush" id="modal-file-list" style="max-height: 380px; overflow-y: auto;">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-block btn-lg font-weight-bold shadow-sm" id="btn-download-all">
                                <i class="fas fa-download mr-2"></i> Download All Files
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card shadow-sm h-100 mb-0">
                            <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                                <span>Live Preview</span>
                                <button id="btn-download-current" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download mr-1"></i> Download This File
                                </button>
                            </div>
                            <div class="card-body p-0 text-center d-flex align-items-center justify-content-center bg-dark" style="min-height: 440px;">
                                <img id="preview-image" src="" class="img-fluid d-none" style="max-height: 440px; object-fit: contain;">
                                <iframe id="preview-pdf" src="" class="w-100 h-100 border-0 d-none" style="min-height: 440px;"></iframe>
                                <div id="preview-fallback" class="text-white p-4 d-none">
                                    <i class="fas fa-file-word fa-4x mb-3 text-info"></i>
                                    <h5>Preview not supported for DOCX</h5>
                                    <p class="small text-muted">Browser cannot render Microsoft Word files directly.</p>
                                    <button id="download-fallback-btn" class="btn btn-info btn-sm mt-2">
                                        <i class="fas fa-download mr-1"></i> Download to View
                                    </button>
                                </div>
                                <div id="preview-empty" class="text-muted p-4">
                                    <h5>Select a file from the list to preview</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-border btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentActiveLink = "";
        let allGroupFiles = [];

        document.querySelectorAll('.btn-open-print-modal').forEach(button => {
            button.addEventListener('click', function() {
                const files = JSON.parse(this.getAttribute('data-files'));
                allGroupFiles = files;

                const fileListContainer = document.getElementById('modal-file-list');
                fileListContainer.innerHTML = '';
                resetPreviewArea();

                if (files.length === 0) {
                    fileListContainer.innerHTML = '<div class="p-3 text-muted text-center">No files found.</div>';
                    $('#printPreviewModal').modal('show');
                    return;
                }

                files.forEach((file, index) => {
                    const fileName = file.link.split('/').pop().replace(/^\d+_\w+_/, '');

                    const itemLink = document.createElement('a');
                    itemLink.href = "#";
                    itemLink.className = "list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 file-item-row";
                    itemLink.setAttribute('data-link', file.link);

                    let iconClass = "fa-file text-secondary";
                    if (/\.(gif|jpg|jpeg|tiff|png)$/i.test(file.link)) iconClass = "fa-file-image text-warning";
                    if (/\.(pdf)$/i.test(file.link)) iconClass = "fa-file-pdf text-danger";
                    if (/\.(docx|doc)$/i.test(file.link)) iconClass = "fa-file-word text-primary";

                    itemLink.innerHTML = `
                        <div class="text-truncate mr-2" style="max-width: 80%;">
                            <i class="fas ${iconClass} mr-2"></i>
                            <span class="small font-weight-bold text-dark text-break">${fileName}</span>
                        </div>
                        <button type="button" class="btn btn-xs btn-light border hover-download" data-url="${file.link}" data-name="${fileName}" title="Download file ini">
                            <i class="fas fa-download text-secondary"></i>
                        </button>
                    `;

                    itemLink.addEventListener('click', function(e) {
                        if (e.target.closest('.hover-download')) return;
                        e.preventDefault();
                        document.querySelectorAll('.file-item-row').forEach(el => el.classList.remove('active'));
                        this.classList.add('active');
                        setFilePreview(file.link);
                    });

                    fileListContainer.appendChild(itemLink);

                    if (index === 0) {
                        itemLink.click();
                    }
                });

                document.querySelectorAll('.hover-download').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const url = this.getAttribute('data-url');
                        const name = this.getAttribute('data-name');
                        downloadSingleFile(url, name);
                    });
                });

                $('#printPreviewModal').modal('show');
            });
        });

        function setFilePreview(url) {
            resetPreviewArea();
            currentActiveLink = url;

            const imgPreview = document.getElementById('preview-image');
            const pdfPreview = document.getElementById('preview-pdf');
            const fallbackPreview = document.getElementById('preview-fallback');

            if (/\.(gif|jpg|jpeg|tiff|png)$/i.test(url)) {
                imgPreview.src = url;
                imgPreview.classList.remove('d-none');
            } else if (/\.(pdf)$/i.test(url)) {
                pdfPreview.src = url;
                pdfPreview.classList.remove('d-none');
            } else {
                fallbackPreview.classList.remove('d-none');
            }
        }

        function resetPreviewArea() {
            document.getElementById('preview-empty').classList.add('d-none');
            document.getElementById('preview-image').classList.add('d-none');
            document.getElementById('preview-pdf').classList.add('d-none');
            document.getElementById('preview-fallback').classList.add('d-none');
            document.getElementById('preview-image').src = "";
            document.getElementById('preview-pdf').src = "";
        }

        document.getElementById('btn-download-current').addEventListener('click', function(e) {
            e.preventDefault();
            if (!currentActiveLink) return;
            const name = currentActiveLink.split('/').pop().replace(/^\d+_\w+_/, '');
            downloadSingleFile(currentActiveLink, name);
        });

        document.getElementById('download-fallback-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if (!currentActiveLink) return;
            const name = currentActiveLink.split('/').pop().replace(/^\d+_\w+_/, '');
            downloadSingleFile(currentActiveLink, name);
        });

        document.getElementById('btn-download-all').addEventListener('click', function() {
            if (allGroupFiles.length === 0) return;

            swal({
                title: "Download All Files?",
                text: "Sistem akan mendownload seluruh (" + allGroupFiles.length + ") file dari transaksi ini secara otomatis bertahap.",
                icon: "info",
                buttons: {
                    cancel: "Batal",
                    confirm: {
                        text: "Ya, Download",
                        className: "btn btn-success"
                    }
                },
            }).then((willDownload) => {
                if (willDownload) {
                    allGroupFiles.forEach((file, index) => {
                        const fileName = file.link.split('/').pop().replace(/^\d+_\w+_/, '');
                        setTimeout(() => {
                            downloadSingleFile(file.link, fileName);
                        }, index * 300);
                    });
                }
            });
        });

        function downloadSingleFile(url, name) {
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', name);
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });
</script>
@endsection