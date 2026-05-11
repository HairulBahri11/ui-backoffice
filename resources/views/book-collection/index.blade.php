@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Book Collection</h2>
                    <h5 class="text-white op-7 mb-2">List of Book Collection that need to be distributed.</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="page-inner mt--5">
        @if (session('status'))
        <script>
            swal("Berhasil", "{{ session('status') }}!", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                },
            });
        </script>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Pending Distribution</h4>
                        <span class="badge badge-warning">{{ count($data) }} Items Pending</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Category</th>
                                        <th>Level/Program</th>
                                        <th>Teacher</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                    <tr>
                                        <td><strong>{{ ucwords($item->student_name) }}</strong></td>
                                        <td>
                                            {{-- Pecah kembali kategori untuk dibuatkan badge individual --}}
                                            @foreach(explode(', ', $item->combined_categories) as $cat)
                                            <span class="badge badge-primary">{{ $cat }}</span>
                                            @endforeach
                                        </td>
                                        <td class="fw-bold">{{ $item->program.' - '. $item->day_one_name. ' - '.$item->day_two_name.' - '.$item->course_time }}</td>
                                        <td>{{ ucwords($item->teacher_name) }}</td>
                                        <!-- <td>
                                            {{-- Mengatasi masalah tanggal 1970 --}}
                                            @if($item->monthpay && $item->monthpay != '1970-01-01')
                                            {{ date('d M Y', strtotime($item->monthpay)) }}
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td> -->
                                        <td>
                                            <span class="badge badge-count" style="background: #fff3cd; color: #856404; border: 1px solid #ffeeba;">
                                                <i class="fas fa-clock mr-1"></i> Waiting for Pick-up
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ url('/book-collection/take') }}" method="POST">
                                                @csrf
                                                {{-- Input hidden berisi semua ID yang akan di-update --}}
                                                <input type="hidden" name="item_ids" value="{{ $item->combined_ids }}">
                                                <button type="submit" class="btn btn-sm btn-success btn-round"
                                                    onclick="return confirm('Konfirmasi pengambilan semua item untuk {{ $item->student_name }}?')">
                                                    <i class="fas fa-check-double mr-1"></i> Mark as Taken
                                                </button>
                                            </form>
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
@endsection