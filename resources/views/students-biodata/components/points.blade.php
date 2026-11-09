{{-- Points history. Data: $data['history'], balance --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-">
        <h4 class="card-title"><i class="fas fa-star text-warning mr-2"></i>Points History</h4>

        <div class="group text-right">
            <small class="text-muted d-block">Current Balance</small>
            <h3 class="mb-0 font-weight-bold text-primary">{{ number_format($data['balance'] ?? 0) }}</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-head-bg-info data-table"
                data-empty="No points history found." data-page-length="25">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Points</th>
                        <th class="text-center">Balance Before</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['history'] as $no => $item)
                    <tr>
                        <td class="text-center">{{ $no + 1 }}</td>
                        <td class="text-center" data-order="{{ $item->date }}">
                            {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M Y') : '-' }}
                        </td>
                        <td>{{ $item->keterangan ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->type == 'in' ? 'badge-success' : 'badge-danger' }}">
                                {{ $item->type == 'in' ? 'In' : 'Out' }}
                            </span>
                        </td>
                        <td class="text-right font-weight-bold {{ $item->type == 'in' ? 'text-success' : 'text-danger' }}"
                            data-order="{{ $item->type == 'in' ? $item->total_point : -$item->total_point }}">
                            {{ $item->type == 'in' ? '+' : '-' }}{{ number_format($item->total_point) }}
                        </td>
                        <td class="text-right" data-order="{{ $item->balance_before }}">{{ number_format($item->balance_before) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>