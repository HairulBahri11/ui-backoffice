@extends('template.app')

@section('content')
    <div class="content">
        <div class="page-inner py-5 panel-header bg-primary-gradient" style="background:#01c293 !important">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div class="">
                    <h2 class="text-white pb-2 fw-bold">Last Score Student</h2>
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
                            <a href="#" class="text-white">Last Score Student</a>
                        </li>
                        <li class="separator text-white">
                            <i class="flaticon-right-arrow text-white"></i>
                        </li>
                        <li class="nav-item text-white">
                            <a href="#" class="text-white">{{ $class->program }}</a>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ $student->name }} Score</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-head-bg-info table-bordered-bd-info">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Skill</th>
                                                @if (Request::get('class') >= 43 && Request::get('class') <= 45)
                                                    <th>
                                                        Test 1
                                                        <br>
                                                        @if ($studentScore1 == 0)
                                                            <a href="{{ url('score/create-last') . '?type=create&class=' . $class->id . '&student=' . $student->id . '&test=1' }}"
                                                                class="btn btn-sm btn-success">Add Score</a>
                                                        @endif
                                                    </th>
                                                @elseif (Request::get('class') >= 22 && Request::get('class') <= 37)
                                                    <th>
                                                        Test 1
                                                        <br>
                                                        @if ($studentScore1 == 0)
                                                            <a href="{{ url('score/create-last') . '?type=create&class=' . $class->id . '&student=' . $student->id . '&test=1' }}"
                                                                class="btn btn-sm btn-success">Add Score</a>
                                                        @endif
                                                    </th>
                                                    <th>
                                                        Test 2
                                                        <br>
                                                        @if ($studentScore2 == 0)
                                                            <a href="{{ url('score/create-last') . '?type=create&class=' . $class->id . '&student=' . $student->id . '&test=2' }}"
                                                                class="btn btn-sm btn-success">Add Score</a>
                                                        @endif
                                                    </th>
                                                @else
                                                    <th>
                                                        Test 1
                                                        <br>
                                                        @if ($studentScore1 == 0)
                                                            <a href="{{ url('score/create-last') . '?type=create&class=' . $class->id . '&student=' . $student->id . '&test=1' }}"
                                                                class="btn btn-sm btn-success">Add Score</a>
                                                        @endif
                                                    </th>
                                                    <th>
                                                        Test 2
                                                        <br>
                                                        @if ($studentScore2 == 0)
                                                            <a href="{{ url('score/create-last') . '?type=create&class=' . $class->id . '&student=' . $student->id . '&test=2' }}"
                                                                class="btn btn-sm btn-success">Add Score</a>
                                                        @endif
                                                    </th>
                                                    <th>
                                                        Test 3
                                                        <br>
                                                        @if ($studentScore3 == 0)
                                                            <a href="{{ url('score/create-last') . '?type=create&class=' . $class->id . '&student=' . $student->id . '&test=3' }}"
                                                                class="btn btn-sm btn-success">Add Score</a>
                                                        @endif
                                                    </th>
                                                @endif
                                                <th>Average</th>
                                                <th>Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total_test1 = 0;
                                                $total_test2 = 0;
                                                $total_test3 = 0;
                                                $total_test = 0;
                                            @endphp
                                            @foreach ($testItem as $item)
                                                @php
                                                    $score1 = DB::table('student_scores')
                                                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                                        ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                                        ->where('student_id', $student->id)
                                                        ->where('price_id', $class->id)
                                                        ->where('test_id', 1)
                                                        ->where('student_score_details.test_item_id', $item->id)
                                                        ->first();
                                                    $score2 = DB::table('student_scores')
                                                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                                        ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                                        ->where('student_id', $student->id)
                                                        ->where('price_id', $class->id)
                                                        ->where('test_id', 2)
                                                        ->where('student_score_details.test_item_id', $item->id)
                                                        ->first();
                                                    $score3 = DB::table('student_scores')
                                                        ->join('student_score_details', 'student_score_details.student_score_id', 'student_scores.id')
                                                        ->select('student_scores.*', 'student_score_details.score as score_test', 'student_score_details.test_item_id')
                                                        ->where('student_id', $student->id)
                                                        ->where('price_id', $class->id)
                                                        ->where('test_id', 3)
                                                        ->where('student_score_details.test_item_id', $item->id)
                                                        ->first();
                                                    $score_test1 = $score1 ? $score1->score_test : 0;
                                                    $score_test2 = $score2 ? $score2->score_test : 0;
                                                    $score_test3 = $score3 ? $score3->score_test : 0;
                                                    $bagi1 = $score1 ? 1 : 0;
                                                    $bagi2 = $score2 ? 1 : 0;
                                                    $bagi3 = $score3 ? 1 : 0;
                                                    $bagiSemua = $bagi1 + $bagi2 + $bagi3;
                                                    $score_test = round(($score_test1 + $score_test2 + $score_test3) / ($bagiSemua != 0 ? $bagiSemua : 1));
                                                    $total_test1 += $score_test1;
                                                    $total_test2 += $score_test2;
                                                    $total_test3 += $score_test3;
                                                    $total_test += $score_test;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    @if (Request::get('class') >= 43 && Request::get('class') <= 45)
                                                        <td>{{ $score_test1 }}</td>
                                                    @elseif(Request::get('class') >= 22 && Request::get('class') <= 37)
                                                        <td>{{ $score_test1 }}</td>
                                                        <td>{{ $score_test2 }}</td>
                                                    @else
                                                        <td>{{ $score_test1 }}</td>
                                                        <td>{{ $score_test2 }}</td>
                                                        <td>{{ $score_test3 }}</td>
                                                    @endif
                                                    <td>{{ $score_test }}</td>
                                                    <td>{{ Helper::getGrade($score_test) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                @if (Request::get('class') >= 43 && Request::get('class') <= 45)
                                                    <td colspan="2">Total</td>
                                                    <td>{{ round($total_test1 / count($testItem)) }}</td>
                                                    <td>{{ round($total_test / count($testItem)) }}</td>
                                                    <td>{{ Helper::getGrade(round($total_test / count($testItem))) }}</td>
                                                @elseif(Request::get('class') >= 22 && Request::get('class') <= 37)
                                                    <td colspan="2">Total</td>
                                                    <td>{{ round($total_test1 / count($testItem)) }}</td>
                                                    <td>{{ round($total_test2 / count($testItem)) }}</td>
                                                    <td>{{ round($total_test / count($testItem)) }}</td>
                                                    <td>{{ Helper::getGrade(round($total_test / count($testItem))) }}</td>
                                                @else
                                                    <td colspan="2">Total</td>
                                                    <td>{{ round($total_test1 / count($testItem)) }}</td>
                                                    <td>{{ round($total_test2 / count($testItem)) }}</td>
                                                    <td>{{ round($total_test3 / count($testItem)) }}</td>
                                                    <td>{{ round($total_test / count($testItem)) }}</td>
                                                    <td>{{ Helper::getGrade(round($total_test / count($testItem))) }}</td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
