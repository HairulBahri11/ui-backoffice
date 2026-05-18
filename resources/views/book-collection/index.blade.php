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
                                            @if($item->payment_status == 'READY TO TAKE' && $item->combined_categories)
                                            @foreach(explode(', ', $item->combined_categories) as $cat)
                                            <span class="badge badge-primary">{{ $cat }}</span>
                                            @endforeach
                                            @else
                                            <span class="text-muted" style="font-size: 0.85rem; font-style: italic;">
                                                <i class="fas fa-exclamation-circle mr-1"></i> No book selected / unpaid
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
                                            @if($item->payment_status == 'READY TO TAKE')
                                            <form action="{{ url('/book-collection/take') }}" method="POST" class="form-distribute">
                                                @csrf
                                                <input type="hidden" name="item_ids" value="{{ $item->combined_ids }}">
                                                <button type="button" class="btn btn-sm btn-success btn-round btn-confirm-take" data-name="{{ $item->student_name }}">
                                                    <i class="fas fa-check-double mr-1"></i> Mark as Taken
                                                </button>
                                            </form>
                                            @else
                                            <button type="button" class="btn btn-sm btn-secondary btn-round" disabled title="Student has not settled the book payment">
                                                <i class="fas fa-ban mr-1"></i> Locked
                                            </button>
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

{{-- JavaScript Section for SweetAlert Confirmation handling --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmButtons = document.querySelectorAll('.btn-confirm-take');

        confirmButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.form-distribute');
                const studentName = this.getAttribute('data-name');

                swal({
                    title: "Confirm Distribution?",
                    text: "Are you sure you want to mark the items as taken for " + studentName + "?",
                    icon: "warning",
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
                    dangerMode: false,
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