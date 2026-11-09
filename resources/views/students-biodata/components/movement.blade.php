{{-- Class movement. Data: $data['moves'] (lihat StudentBiodataController::movement) --}}
@php
$fmt = fn($date) => $date && !str_starts_with($date, '0000') ? \Carbon\Carbon::parse($date)->format('d M Y') : '-';
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="card-title mb-0"><i class="fas fa-random text-primary mr-2"></i>Class Movement</h4>
        <span class="badge badge-count badge-secondary">{{ $data['moves']->count() }} movement(s)</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-head-bg-info data-table"
                data-empty="No class movement recorded yet. Only movements made after this feature was added are listed.">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">From</th>
                        <th class="text-center">To</th>
                        <th class="text-center">Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['moves'] as $move)
                    <tr>
                        <td class="text-center" data-order="{{ $move->created_at }}">{{ $fmt($move->created_at) }}</td>
                        <td class="text-center">
                            @if ($move->type == 'jump_class')
                            <span class="badge badge-primary">Jump Class</span>
                            @elseif ($move->type == 'update_class')
                            <span class="badge badge-warning">Update Class</span>
                            @else
                            <span class="badge badge-info">Transfer Class</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $move->from_class ?? '-' }}</strong>
                            <span class="text-muted d-block small">{{ $move->from_schedule ?: '-' }} &middot; {{ $move->from_teacher ?? '-' }}</span>
                        </td>
                        <td>
                            <strong>{{ $move->to_class ?? '-' }}</strong>
                            <span class="text-muted d-block small">{{ $move->to_schedule ?: '-' }} &middot; {{ $move->to_teacher ?? '-' }}</span>
                        </td>
                        <td>{{ $move->user_name ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
