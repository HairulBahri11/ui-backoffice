@extends('template.app')

@section('content')
<div class="content">
    <div class="panel-header" style="background:#01c293 !important">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Book Collection</h2>
                    <h5 class="text-white op-7 mb-2">List of book collections awaiting distribution to certification and failed-promoted students.</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="page-inner mt--5">
        @if (session('status'))
        <script>
            swal("Success", "{{ session('status') }}!", {
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
                        <span class="badge badge-warning">{{ count($data) }} Students Pending</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Category</th>
                                        <th>Level / Program</th>
                                        <th>Teacher</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ ucwords($item->student_name) }}</strong>
                                        </td>
                                        <td>
                                            @if($item->combined_categories)
                                            @foreach(explode(', ', $item->combined_categories) as $cat)
                                            <span class="badge badge-primary">{{ $cat }}</span>
                                            @endforeach
                                            @else
                                            <span class="text-muted" style="font-size: 0.85rem; font-style: italic;">
                                                <i class="fas fa-exclamation-circle mr-1"></i> No book selected
                                            </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $item->program.' - '. $item->day_one_name. ' - '.$item->day_two_name.' - '.$item->course_time }}</td>
                                        <td>{{ ucwords($item->teacher_name) }}</td>
                                        <td>
                                            @if($item->payment_status == 'READY TO TAKE')
                                            <span class="badge badge-success" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                                                <i class="fas fa-check-circle mr-1"></i> Ready to Take (PAID)
                                            </span>
                                            @else
                                            <span class="badge badge-danger" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                                                <i class="fas fa-times-circle mr-1"></i> Unpaid
                                            </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{--
                                                Silakan sesuaikan kondisi di bawah ini dengan kolom penanda di database Anda.
                                                Contoh di bawah memeriksa jika properti status bernilai 'TAKEN' atau 'SUDAH DIAMBIL'
                                            --}}
                                            @if(isset($item->collection_status) && ($item->collection_status == 'TAKEN' || $item->collection_status == 'SUDAH DIAMBIL') || (isset($item->is_taken) && $item->is_taken == 1) || (isset($item->is_book_taken) && $item->is_book_taken == 1))
                                            <span class="badge badge-count" style="background: #e0e0e0; color: #424242; font-weight: bold; padding: 5px 10px;">
                                                Already Collected
                                            </span>
                                            @else
                                            {{-- Button hanya muncul jika barang BELUM pernah diambil --}}
                                            <form action="{{ url('/book-collection/take') }}" method="POST" class="form-distribute">
                                                @csrf
                                                <input type="hidden" name="item_ids" value="{{ $item->combined_ids }}">
                                                <input type="hidden" name="studentid" value="{{ $item->studentid }}">

                                                @if($item->payment_status == 'READY TO TAKE')
                                                <button type="button" class="btn btn-sm btn-success btn-round btn-confirm-take" data-name="{{ $item->student_name }}" data-paid="true">
                                                    <i class="fas fa-check-double mr-1"></i> Mark as Taken
                                                </button>
                                                @else

                                                <button type="button" class="btn btn-sm btn-success btn-round btn-confirm-take" data-name="{{ $item->student_name }}" data-paid="false">
                                                    <i class="fas fa-check-double mr-1"></i> Mark as Taken
                                                </button>
                                                @endif
                                            </form>
                                            @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmButtons = document.querySelectorAll('.btn-confirm-take');

        confirmButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.form-distribute');
                const studentName = this.getAttribute('data-name');
                const isPaid = this.getAttribute('data-paid') === 'true';

                let alertText = "Are you sure you want to mark the items as taken for " + studentName + "?";
                let alertIcon = "warning";

                if (!isPaid) {
                    alertText = "Warning: " + studentName + " has NOT paid yet. Are you sure you want to allow them to take the books anyway?";
                }

                swal({
                    title: "Confirm Distribution?",
                    text: alertText,
                    icon: alertIcon,
                    buttons: {
                        cancel: {
                            visible: true,
                            text: "Cancel",
                            className: 'btn btn-danger'
                        },
                        confirm: {
                            text: "Yes, Confirm",
                            className: 'btn btn-success'
                        }
                    },
                    dangerMode: !isPaid,
                }).then((willSubmit) => {
                    if (willSubmit) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection