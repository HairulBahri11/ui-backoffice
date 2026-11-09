{{-- Active / inactive history. Data: $data['events'], inactiveCount, activeCount; $student --}}
@php
$fmt = fn($date) => $date && !str_starts_with($date, '0000') ? \Carbon\Carbon::parse($date)->format('d M Y') : '-';
$isActive = strtoupper((string) $student->status) === 'ACTIVE';
$badges = [
'registered' => ['badge-primary', 'Registered'],
'inactive' => ['badge-secondary', 'Inactive'],
'active' => ['badge-success', 'Active'],
];
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="card-title mb-0"><i class="fas fa-exchange-alt text-primary mr-2"></i>Active / Inactive History</h4>
        <span>
            <small class="text-muted mr-2">
                Inactive {{ $data['inactiveCount'] }}x &middot; Reactivated {{ $data['activeCount'] }}x
            </small>
            <span class="badge {{ $isActive ? 'badge-success' : 'badge-secondary' }}" style="font-size: 13px;">
                Current status &middot; {{ $isActive ? 'ACTIVE' : 'INACTIVE' }}
            </span>
        </span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-head-bg-info data-table">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Class</th>
                        <th class="text-center">Reason</th>
                        <th class="text-center">Explanation</th>
                        <th class="text-center">Teacher / Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['events'] as $event)
                    <tr>
                        <td class="text-center" data-order="{{ $event->date }}">{{ $fmt($event->date) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $badges[$event->type][0] }}">{{ $badges[$event->type][1] }}</span>
                        </td>
                        <td>{{ $event->program ?: '-' }}</td>
                        <td>{{ $event->reason ? ucfirst($event->reason) : '-' }}</td>
                        <td>{{ $event->explanation ?: '-' }}</td>
                        <td>{{ $event->person ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
