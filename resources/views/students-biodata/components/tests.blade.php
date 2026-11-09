{{-- Test history. Data: $data['scoresByClass'], $data['testItems'], $data['total'] --}}
@if ($data['total'] == 0)
@include('students-biodata.components.empty', [
'icon' => 'fa-folder-open',
'title' => 'No test records found',
'text' => "This student doesn't have any test scores yet.",
])
@else
@foreach ($data['scoresByClass'] as $className => $scores)
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span class="font-weight-bold text-dark">
            <i class="fas fa-layer-group text-info mr-2"></i>{{ $className }}
        </span>
        <span class="badge badge-count badge-secondary">{{ $scores->count() }} test(s)</span>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach ($scores as $item)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="border rounded p-3 h-100" style="background:#fafafa;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong class="text-dark">{{ $item->test_name }}</strong>
                        <span class="badge {{ $item->grade_badge }}" style="font-size: 12px;">
                            Average &middot; {{ $item->average_score }}
                        </span>
                    </div>
                    <div class="text-muted small mb-2">
                        <i class="fas fa-chalkboard-teacher mr-1"></i>{{ $item->teacher_name ?? '-' }}
                        <span class="mx-1">&middot;</span>
                        <i class="fas fa-calendar-alt mr-1"></i>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M Y') : '-' }}
                    </div>
                    <div class="d-flex flex-wrap">
                        @foreach ($data['testItems'] as $testItem)
                        <span class="badge badge-light border mr-1 mb-1" style="font-size: 11px;">
                            {{ $testItem->name }}: {{ $item->details->get($testItem->id)->score ?? '-' }}
                        </span>
                        @endforeach
                    </div>
                    @if ($item->comment)
                    <div class="mt-2 small text-dark" style="border-top:1px dashed #ddd; padding-top:6px;">
                        <i class="fas fa-comment-dots text-muted mr-1"></i>{{ $item->comment }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach
@endif
