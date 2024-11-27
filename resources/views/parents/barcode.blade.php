@extends('template.app')

@section('content')
    <div class="content">
        <div class="panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Student’s Barcode</h2>
                        {{-- <h5 class="text-white op-7 mb-2">Free Bootstrap 4 Admin Dashboard</h5> --}}
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
            @if (session('error'))
                <script>
                    swal("Gagal", "{{ session('status') }}!", {
                        icon: "danger",
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
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">Data Student’s Barcode</h4>
                                </div>
                                <div class="col-md-6" style="text-align: end
                                ">
                                    <a href="{{ url('get-barcode-student') }}" class="btn btn-primary"
                                        onclick="return confirm('Are you sure to update the blank barcode?')">Update
                                        Barcode</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ ucwords($item->name) }}</td>
                                                <!--<td><img src="{{ url('storage/barcode/' . $item->barcode) }}">-->
                                                <td><img src="{{ url('barcode/' . $item->barcode) }}">

                                                </td>
                                                <td><a href="{{ url('barcode-download/' . $item->id) }}"
                                                        class="btn btn-sm btn-primary">Download</a>
                                                    <a href="{{ url('barcode-print/' . $item->id) }}" target="_blank"
                                                        class="btn btn-sm btn-success">Print</a>
                                                </td>
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
