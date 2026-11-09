{{-- Payment history. Data: $data['history'], $data['total'], $data['bills'] --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="card-title mb-0"><i class="fas fa-money-bill-wave text-success mr-2"></i>Payment History</h4>
        <span class="badge badge-count badge-success" style="font-size: 13px;">
            Total paid &middot; Rp {{ number_format($data['total'], 0, ',', '.') }}
        </span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover table-head-bg-info data-table"
                data-empty="No payment history found.">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Receipt</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Month</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Method</th>
                        <th class="text-center">Received By</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['history'] as $item)
                    @php $cancelled = strtoupper(trim($item->category)) === 'CANCEL'; @endphp
                    <tr class="{{ $cancelled ? 'text-muted' : '' }}">
                        <td class="text-center" data-order="{{ $item->paydate }}">
                            {{ $item->paydate ? \Carbon\Carbon::parse($item->paydate)->format('d M Y') : '-' }}
                        </td>
                        <td class="text-center">#{{ $item->paymentid }}</td>
                        <td>
                            @if ($cancelled)
                            <span class="badge badge-danger">CANCEL</span>
                            @else
                            {{ $item->category ?: '-' }}
                            @endif
                        </td>
                        <td class="text-center" data-order="{{ $item->monthpay && !str_starts_with($item->monthpay, '0000') ? $item->monthpay : '' }}">
                            {{ $item->monthpay && !str_starts_with($item->monthpay, '0000') ? \Carbon\Carbon::parse($item->monthpay)->format('M Y') : '-' }}
                        </td>
                        <td>{{ $item->explanation ?: ($item->other_detail ?: '-') }}</td>
                        <td class="text-center">{{ $item->method ?: '-' }}{{ $item->bank ? ' (' . $item->bank . ')' : '' }}</td>
                        <td>{{ $item->username ?: '-' }}</td>
                        <td class="text-right {{ $cancelled ? 'text-muted' : '' }}" data-order="{{ $item->amount }}"
                            style="{{ $cancelled ? 'text-decoration: line-through;' : '' }}">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>