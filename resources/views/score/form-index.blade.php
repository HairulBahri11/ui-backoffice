@extends('template.app')

@section('content')
    <style>
        .table th {
            font-size: 14px;

            padding: 0 25px !important;
            height: 35px;
            vertical-align: middle !important;
        }

        .table td {
            height: 35px !important;
            padding: 8px 16px !important;
        }
    </style>
    <div class="content">
        <div class="page-inner py-5 panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div class="">
                    <h2 class="text-white pb-2 fw-bold">Score</h2>
                    <ul class="breadcrumbs">
                        <li class="nav-home text-white">
                            <a href="#">
                                <i class="flaticon-home text-white"></i>
                            </a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">Score</a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">List Student {{ $test->name }}</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="page-inner mt--5">
            @if (session('status'))
                <script>
                    swal("Gagal!", "{{ session('status') }}!", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        },
                    });
                </script>
            @endif
            @if (session('success'))
                <script>
                    swal("Success!", "{{ session('success') }}!", {
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
                        <div class="card-header">
                            <h4 class="card-title">List Student</h4>
                        </div>
                        <div class="card-body">

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Name</th>
                                                    @foreach ($testItem as $itemTest)
                                                        <th class="text-center">{{ $itemTest->name }}</th>
                                                    @endforeach
                                                    <th class="text-center">Average</th>
                                                    <th class="text-center">Grade</th>
                                                    <th class="text-center">Comment For Student</th>
                                                    <th class="text-center">Date Test</th>
                                                    <th class="text-center">Date Time</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @foreach ($students as $itemSKey => $itemSValue)
                                                    @php
                                                        $average = DB::table('student_scores')
                                                            ->where('student_id', $itemSValue->id)
                                                            ->where('price_id', $itemSValue->priceid)
                                                            ->where('test_id', Request::get('test'))
                                                            ->orderBy('id', 'ASC')
                                                            ->limit(1)
                                                            ->first();
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ ucwords($itemSValue->name) }}</td>
                                                        @foreach ($testItem as $itemTestKey => $itemTestValue)
                                                            @php
                                                                $detail = null;
                                                                if ($average != null) {
                                                                    $detail = DB::table('student_score_details');
                                                                    $detail = $detail->where('student_score_id', $average->id);
                                                                    $detail = $detail->where('test_item_id', $itemTestValue->id)->first();
                                                                }
                                                            @endphp
                                                            <td>{{ $detail != null ? $detail->score : '-' }}</td>
                                                        @endforeach
                                                        <td>{{ $average != null ? $average->average_score : '-' }}
                                                        </td>
                                                        <td>{{ $average != null ? Helper::getGrade($average->average_score) : '' }}
                                                        </td>
                                                        <td>{{ $average != null ? $average->comment : '-' }}
                                                        </td>
                                                        <td>{{ $average != null ? $average->date : '-' }}
                                                        </td>
                                                        <td>{{ $average != null ? $average->created_at : '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($average != null)
                                                                <a href="{{ url('score/create?type=edit&class=') . Request::get('class') . '&student=' . $itemSValue->id . '&test=' . Request::get('test') . '&id_test=' . $average->id . '&date=' . $average->date . '&day1=' . Request::get('day1') . '&day2=' . Request::get('day2') . '&teacher=' . Request::get('teacher') . '&time=' . Request::get('time') }}"
                                                                    class="btn btn-sm btn-primary">Edit</a>
                                                            @else
                                                                <a href="{{ url('score/create?type=create&class=') . Request::get('class') . '&student=' . $itemSValue->id . '&test=' . Request::get('test') . '&day1=' . Request::get('day1') . '&day2=' . Request::get('day2') . '&teacher=' . Request::get('teacher') . '&time=' . Request::get('time') }}"
                                                                    class="btn btn-sm btn-primary">Create</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
