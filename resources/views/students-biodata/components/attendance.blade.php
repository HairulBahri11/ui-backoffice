{{-- Class attendance. Data: $data['classes'], $data['summary'], $data['total'] --}}
@if ($data['total'] == 0)
@include('students-biodata.components.empty', [
'icon' => 'fa-folder-open',
'title' => 'No attendance records found',
'text' => "This student doesn't have any attendance records yet.",
])
@else
<div class="card mb-3">
    <div class="card-body d-flex flex-wrap align-items-center">
        <span class="font-weight-bold text-dark mr-3">{{ $data['total'] }} session(s)</span>
        <span class="badge badge-success mr-2">Present {{ $data['summary']['present'] }}</span>
        <span class="badge badge-info mr-2">Permission {{ $data['summary']['permission'] }}</span>
        <span class="badge badge-danger mr-2">Alpha {{ $data['summary']['alpha'] }}</span>
        <span class="badge badge-secondary">Pending {{ $data['summary']['pending'] }}</span>
    </div>
</div>

@foreach ($data['classes'] as $class)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="card-title mb-0"><i class="fas fa-layer-group text-info mr-2"></i>{{ $class['label'] }}</h4>
        <span class="badge badge-count badge-secondary">{{ $class['sessions']->count() }} session(s)</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-head-bg-info data-table">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Teacher</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Activity</th>
                        <th class="text-center">Book / Pages</th>
                        <th class="text-center">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($class['sessions'] as $no => $item)
                    <tr>
                        <td class="text-center">{{ $no + 1 }}</td>
                        <td class="text-center" data-order="{{ $item->date }}">
                            {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M Y') : '-' }}
                        </td>
                        <td>{{ $item->teacher_name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->status_badge }}">{{ $item->status_label }}</span>
                        </td>
                        <td>{{ $item->activity_class ?: '-' }}</td>
                        <td>
                            @php
                            $book = collect([
                            $item->topic_page ? 'Text Book: ' . $item->topic_page : null,
                            $item->excercise_book ? 'Exercise: ' . $item->excercise_book : null,
                            $item->flashcard_page ? 'Flashcard: ' . $item->flashcard_page : null,
                            ])->filter();
                            $comment = collect([
                            $item->comment_teacher ? 'Teacher: ' . $item->comment_teacher : null,
                            $item->comment_staff ? 'Staff: ' . $item->comment_staff : null,
                            ])->filter();
                            @endphp
                            @forelse ($book as $line)
                            {{ $line }}<br>
                            @empty
                            -
                            @endforelse
                        </td>
                        <td>
                            @forelse ($comment as $line)
                            {{ $line }}<br>
                            @empty
                            -
                            @endforelse
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
@endif
