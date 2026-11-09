{{-- Biodata + riwayat registrasi + barcode. Data: $student, $data (lihat StudentBiodataController::biodata) --}}
@php
$fmt = fn($date) => $date && !str_starts_with($date, '0000') ?\Carbon\Carbon::parse($date)->format('d M Y') : '-';
$rows = [
'Student ID' => $student->id,
'Full Name' => ucwords($student->name),
'Birthday' => $student->birthday ?: '-',
'Phone' => trim($student->phone) ?: '-',
'Address' => $student->address ?: '-',
'School' => $student->school ?: '-',
'Grade' => $student->grade ?: '-',
'Class' => $student->class_name ?? '-',
'Schedule' => $data['schedule'] ?: '-',
'Teacher' => $student->teacher_name ?? '-',
'Learning Mode' => $student->is_online ? 'Online' : 'Offline',
'Point Balance' => number_format($student->total_point ?? 0),
];
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-id-card text-primary mr-2"></i>Biodata</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($rows as $label => $value)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">{{ $label }}</small>
                        <span class="font-weight-bold text-dark">{{ $value }}</span>
                    </div>
                    @endforeach
                    <div class="col-md-12 mb-1">
                        <small class="text-muted d-block">Parent / Guardian</small>
                        @forelse ($data['parents'] as $parent)
                        <span class="font-weight-bold text-dark">{{ ucwords($parent->name) }}</span>
                        <span class="text-muted">&middot; {{ $parent->no_hp ?: '-' }}</span><br>
                        @empty
                        <span class="text-muted">-</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-barcode text-primary mr-2"></i>Barcode</h4>
            </div>
            <div class="card-body text-center">
                @if ($data['barcode']['image'])
                <img src="{{ $data['barcode']['image'] }}" alt="Barcode {{ $student->id }}" class="img-fluid"
                    style="max-width:100%;">
                <div class="font-weight-bold mt-2">{{ $student->id }}</div>
                @else
                <span class="text-muted">Barcode could not be generated.</span>
                @endif

                @if ($data['barcode']['hasFile'])
                <div class="mt-3">
                    <a href="{{ url('barcode-download/' . $student->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-download mr-1"></i>Download
                    </a>
                    <a href="{{ url('barcode-print/' . $student->id) }}" target="_blank"
                        class="btn btn-sm btn-primary">
                        <i class="fas fa-print mr-1"></i>Print
                    </a>
                </div>
                @else
                <small class="text-muted d-block mt-3">
                    Barcode image file not generated yet. Download &amp; print become available after
                    <a href="{{ url('barcode-student') }}">Student's Barcode</a> is updated.
                </small>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="fas fa-clipboard-check text-primary mr-2"></i>Registration History</h4>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Registration Date</small>
                <span class="font-weight-bold text-dark">{{ $fmt($student->entrydate) }}</span>
            </div>

            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Branch</small>
                <span class="font-weight-bold text-dark">{{ $data['profile']->branch_name ?? '-' }}</span>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Heard About Us From</small>
                <span class="font-weight-bold text-dark">{{ $student->know ?: '-' }}</span>
            </div>

        </div>

        <h6 class="font-weight-bold text-dark mt-2">Registration Payments</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-head-bg-info data-table"
                data-empty="No registration payment recorded.">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Receipt</th>
                        <th class="text-center">Method</th>
                        <th class="text-center">Received By</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['registrationPayments'] as $pay)
                    <tr>
                        <td class="text-center" data-order="{{ $pay->paydate }}">{{ $fmt($pay->paydate) }}</td>
                        <td class="text-center">#{{ $pay->receipt }}</td>
                        <td class="text-center">{{ $pay->method ?: '-' }}</td>
                        <td>{{ $pay->username ?: '-' }}</td>
                        <td class="text-right" data-order="{{ $pay->amount }}">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>