{{-- Certification history. Data: $data['history'], $data['files']; $student --}}
@php
$fmt = fn($date) => $date && !str_starts_with($date, '0000') ?\Carbon\Carbon::parse($date)->format('d M Y') : '-';

@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="card-title mb-0"><i class="fas fa-certificate text-warning mr-2"></i>Certification History</h4>
        <span>
            @if ($student->is_certificate)
            <span class="badge badge-success">Certified{{ $student->date_certificate ? ' - ' . $fmt($student->date_certificate) : '' }}</span>
            @else
            <span class="badge badge-secondary">Not certified</span>
            @endif
        </span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-head-bg-info data-table"
                data-empty="No certification history found.">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Class</th>
                        <th class="text-center">Teacher</th>
                        <th class="text-center">Result</th>
                        <th class="text-center">Certificate Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['history'] as $no => $item)
                    <tr>
                        <td class="text-center">{{ $no + 1 }}</td>
                        <td>
                            <strong>{{ $item->class ?? '-' }}</strong>
                            @if ($item->schedule)
                            <span class="text-muted">&middot; {{ $item->schedule }}</span>
                            @endif
                        </td>
                        <td>{{ $item->teacher_name ?? '-' }}</td>
                        <td class="text-center">
                            @if ($item->status == 'passed')
                            <span class="badge badge-success">Passed</span>
                            @elseif ($item->status == 'failed')
                            <span class="badge badge-danger">Failed</span>
                            @else
                            <span class="badge badge-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center" data-order="{{ $item->date_certificate }}">{{ $fmt($item->date_certificate) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>