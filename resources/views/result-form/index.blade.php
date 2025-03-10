@extends('template.app')
<style>
    .staff-comment {
        width: 100% !important;
        min-height: 120px !important;
        resize: vertical;
        font-size: 14px;
        padding: 8px;
    }

    table.dataTable tbody td {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Poor Result Scores</h2>
                        {{-- <h5 class="text-white op-7 mb-2">Free Bootstrap 4 Admin Dashboard</h5> --}}
                    </div>
                    <div class="ml-md-auto py-2 py-md-0">
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
            @if (session('error'))
                <script>
                    swal("Failed", "{{ session('error') }}!", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        },
                    });
                </script>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Data Students</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="20%">Student Info</th>
                                            <th>Test</th>
                                            <th width="10%">Date</th>
                                            <th>Last Update</th>
                                            <th width="5%">Avg Score</th>
                                            <th width="5%">Grade</th>
                                            @php
                                                $count_resultnya = '';
                                                if (Auth::guard('staff')->check() == true) {
                                                    if ($count_result > 0) {
                                                        $count_resultnya =
                                                            '<span class="badge badge-warning">' .
                                                            $count_result .
                                                            '</span>';
                                                    }
                                                }
                                            @endphp
                                            <th width="15%">Teacher's Comment </th>

                                            <th class="no-print" width="25%">Staff's Comment</th>
                                            @if (Auth::guard('staff')->check() == true)
                                                <th class="no-print">{!! $count_resultnya !!} Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resultnya as $item)
                                            <?php $get_grade = Helper::getGrade($item->average_score); ?>

                                            <tr>
                                                <!-- Student Info -->
                                                <td>
                                                    <strong>{{ $item->name }}</strong> <br>
                                                    <small>ID: {{ $item->student_id }} | {{ $item->class }} -
                                                        {{ $item->teacher_name ?? '-' }}</small>
                                                </td>

                                                <td>{{ $item->test_name }}</td>
                                                <td>{{ date('d M Y', strtotime($item->date)) }}</td>
                                                <td>{{ date('d M Y', strtotime($item->updated_at)) }}</td>
                                                <td class="text-center">{{ $item->average_score }}</td>

                                                <!-- Grade -->
                                                <td class="text-center">
                                                    <span
                                                        class="badge 
                                                        @if ($get_grade == 'A') bg-success 
                                                        @elseif($get_grade == 'B') bg-primary 
                                                        @elseif($get_grade == 'C') bg-warning text-dark 
                                                        @elseif($get_grade == 'D') bg-danger 
                                                        @else bg-dark @endif
                                                        text-white p-2 rounded">
                                                        {{ $get_grade }}
                                                    </span>
                                                </td>

                                                <!-- Teacher Comment -->
                                                <td>
                                                    @if ($item->comment)
                                                        <div class=" p-2">

                                                            <span class="text-dark">{{ $item->comment }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No Comment</span>
                                                    @endif
                                                </td>

                                                <form action="{{ route('result-form.update', $item->id) }}" method="POST">
                                                    @csrf

                                                    <!-- Staff Comment -->
                                                    <td class="text-center">
                                                        @if (Auth::guard('staff')->check() == true && $item->staff_comment == null)
                                                            <textarea name="staff_comment" class="form-control border-primary shadow-sm"
                                                                style="width: 100%; min-height: 120px; resize: vertical;" placeholder="Write your comment here..."></textarea>
                                                        @else
                                                            <div class="p-2">
                                                                @if ($item->staff_comment)
                                                                    <span
                                                                        class="text-dark">{{ $item->staff_comment }}</span>
                                                                @else
                                                                    <span class="text-muted">No Comment</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <!-- Action -->
                                                    @if (Auth::guard('staff')->check() == true)
                                                        <td class="text-center no-print">
                                                            @if ($item->staff_comment)
                                                                <span class="badge bg-secondary">Followed Up</span>
                                                            @else
                                                                <button class="btn btn-sm btn-primary" type="submit">
                                                                    <i class="fas fa-check"></i> Follow Up
                                                                </button>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </form>
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


@push('js')
    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- DataTables JS -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#basic-datatables')) {
                $('#basic-datatables').DataTable().destroy();
            }

            $('#basic-datatables').DataTable({
                "ordering": false, // Menonaktifkan sorting
            });

            // let table = $('#basic-datatables').DataTable({
            //     "ordering": false,
            //     "dom": 'Bfrtip',
            //     "buttons": [{
            //         extend: 'print',
            //         text: '🖨 Print Data',
            //         title: `<div style="text-align:center;">
        //                          <img src="{{ asset('assets/img/logoui.png') }}" width="80px" style="margin-bottom:10px;">
        //                          <h2>Data Students</h2>
        //                          <h5>{{ date('d M Y') }}</h5>
        //                      </div>`,
            //         exportOptions: {
            //             columns: ':not(.no-print)' // Jangan cetak kolom "Action"
            //         }
            //     }]
            // });
        });
    </script>
@endpush
