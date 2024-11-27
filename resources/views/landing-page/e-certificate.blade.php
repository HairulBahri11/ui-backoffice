<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ md5($id) }}</title>
</head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
    html {
        font-family: 'Poppins', sans-serif;
    }

    .a4 {
        height: 2480px;
        width: 3508px;
        page-break-after: auto;
    }

    .image {
        position: relative;
    }

    .nama {
        position: absolute;
        top: 310px;
        left: 490px;
        font-size: 28px;
    }

    .level {
        position: absolute;
        top: 364px;
        left: 491px;
        font-size: 28px;
    }

    .priceid {
        position: absolute;
        top: 76px;
        left: 165px;
        font-size: 28px;
    }

    .writing {
        position: absolute;
        top: 170px;
        left: 292px;
        font-size: 28px;
    }

    .speaking {
        position: absolute;
        top: 201px;
        left: 292px;
        font-size: 28px;
    }

    .reading {
        position: absolute;
        top: 289px;
        left: 292px;
        font-size: 28px;
    }

    .listening {
        position: absolute;
        top: 319px;
        left: 292px;
        font-size: 28px;
    }

    .grammar {
        position: absolute;
        top: 408px;
        left: 292px;
        font-size: 28px;
    }

    .vocabulary {
        position: absolute;
        top: 438px;
        left: 292px;
        font-size: 28px;
    }

    .average {
        position: absolute;
        top: 525px;
        left: 337px;
        font-size: 58px;
    }

    .principal {
        position: absolute;
        top: 526px;
        left: 202px;
        font-size: 28px;
    }

    .teacher {
        position: absolute;
        top: 526px;
        left: 684px;
        font-size: 28px;
    }

    .a4:last-child {}

    /* Pre */

    .nama-pre {
        position: absolute;
        top: 216px;
        left: 250px;
        font-size: 28px;
    }

    .level-pre {
        position: absolute;
        top: 128px;
        left: 337px;
        font-size: 28px;
    }

    .principal-pre {
        position: absolute;
        top: 652px;
        left: 278px;
        font-size: 14px;
    }

    .teacher-pre {
        position: absolute;
        top: 652px;
        left: 455px;
        font-size: 14px;
    }

    .date-pre {
        position: absolute;
        top: 652px;
        left: 168px;
        font-size: 14px;
    }

    .writing-pre {
        position: absolute;
        top: 334px;
        left: 288px;
        font-size: 20px;
    }

    .speaking-pre {
        position: absolute;
        top: 376px;
        left: 288px;
        font-size: 20px;
    }

    .reading-pre {
        position: absolute;
        top: 477px;
        left: 288px;
        font-size: 20px;
    }

    .listening-pre {
        position: absolute;
        top: 516px;
        left: 288px;
        font-size: 20px;
    }

    @media print {

        html,
        body {
            page-break-after: always !important;
        }
    }
</style>

<body>
    {{-- <div class="a4"> --}}
    @if ($student->priceid >= 1 && $student->priceid <= 2)
        @php
            $writing = (($score1 ? $writing1->score : $writing1) + ($score2 ? $writing2->score : $writing2) + ($score3 ? $writing3->score : $writing3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $speaking = (($score1 ? $speaking1->score : $speaking1) + ($score2 ? $speaking2->score : $speaking2) + ($score3 ? $speaking3->score : $speaking3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $reading = (($score1 ? $reading1->score : $reading1) + ($score2 ? $reading2->score : $reading2) + ($score3 ? $reading3->score : $reading3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $listening = (($score1 ? $listening1->score : $listening1) + ($score2 ? $listening2->score : $listening2) + ($score3 ? $listening3->score : $listening3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $average = ($average_score1 + $average_score2 + $average_score3) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
        @endphp
        <div class="image">
            <img src="{{ url('/') }}/assets/img/sertif-depan-pre-toodle.jpg" alt="" width="711px">
            <div class="nama-pre">
                {{ ucwords($student->name) }}
            </div>
            <div class="level-pre">
                {{ ucwords($class->level) }}
            </div>
            <div class="principal-pre">
                {{ ucwords('Lie Citro Dewi Ruslie') }}
            </div>
            <div class="teacher-pre">
                {{ ucwords($student->teacher->name) }}
            </div>
            <div class="date-pre">
                {{ ucwords($student->date_certificate) }}
            </div>
            <div class="writing-pre">
                {{ round($writing) . ' (' . Helper::getGrade($writing) . ')' }}
            </div>
            <div class="speaking-pre">
                {{ round($speaking) . ' (' . Helper::getGrade($speaking) . ')' }}
            </div>
            <div class="reading-pre">
                {{ round($reading) . ' (' . Helper::getGrade($reading) . ')' }}
            </div>
            <div class="listening-pre">
                {{ round($listening) . ' (' . Helper::getGrade($listening) . ')' }}
            </div>
        </div>
        <div class="image">
            <img src="{{ url('/') }}/assets/img/sertif-belakang-pre-toodle.jpg" alt="" width="711px">
        </div>
    @elseif ($student->priceid >= 3 && $student->priceid <= 6)
        @php
            $writing = (($score1 ? $writing1->score : $writing1) + ($score2 ? $writing2->score : $writing2) + ($score3 ? $writing3->score : $writing3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $speaking = (($score1 ? $speaking1->score : $speaking1) + ($score2 ? $speaking2->score : $speaking2) + ($score3 ? $speaking3->score : $speaking3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $reading = (($score1 ? $reading1->score : $reading1) + ($score2 ? $reading2->score : $reading2) + ($score3 ? $reading3->score : $reading3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $listening = (($score1 ? $listening1->score : $listening1) + ($score2 ? $listening2->score : $listening2) + ($score3 ? $listening3->score : $listening3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            $average = ($average_score1 + $average_score2 + $average_score3) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
        @endphp
        <div class="image">
            <img src="{{ url('/') }}/assets/img/sertif-depan-toodle.jpg" alt="" width="711px">
            <div class="nama-pre">
                {{ ucwords($student->name) }}
            </div>
            <div class="level-pre">
                {{ ucwords($class->level) }}
            </div>
            <div class="principal-pre">
                {{ ucwords('Lie Citro Dewi Ruslie') }}
            </div>
            <div class="teacher-pre">
                {{ ucwords($student->teacher->name) }}
            </div>
            <div class="date-pre">
                {{ ucwords($student->date_certificate) }}
            </div>
            <div class="writing-pre">
                {{ round($writing) . ' (' . Helper::getGrade($writing) . ')' }}
            </div>
            <div class="speaking-pre">
                {{ round($speaking) . ' (' . Helper::getGrade($speaking) . ')' }}
            </div>
            <div class="reading-pre">
                {{ round($reading) . ' (' . Helper::getGrade($reading) . ')' }}
            </div>
            <div class="listening-pre">
                {{ round($listening) . ' (' . Helper::getGrade($listening) . ')' }}
            </div>
        </div>
        <div class="image">
            <img src="{{ url('/') }}/assets/img/sertif-belakang-pre-toodle.jpg" alt="" width="711px">
        </div>
    @else
        <div class="image">
            <img src="{{ url('/') }}/assets/img/sertif-depan.jpg" alt="" width="1001px">
            <div class="nama">
                {{ ucwords($student->name) }}
            </div>
            <div class="level">
                {{ ucwords($class->level) }}
            </div>
            <div class="principal">
                {{ ucwords('Lie Citro Dewi Ruslie') }}
            </div>
            <div class="teacher">
                {{ ucwords($student->teacher->name) }}
            </div>
        </div>
        <div class="image">
            <img src="{{ url('/') }}/assets/img/sertif-belakang.jpg" alt="" width="1001px">
            <div class="priceid">
                {{ $student->date_certificate }}
            </div>
            @php
                $writing = (($score1 ? $writing1->score : $writing1) + ($score2 ? $writing2->score : $writing2) + ($score3 ? $writing3->score : $writing3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
                $speaking = (($score1 ? $speaking1->score : $speaking1) + ($score2 ? $speaking2->score : $speaking2) + ($score3 ? $speaking3->score : $speaking3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
                $reading = (($score1 ? $reading1->score : $reading1) + ($score2 ? $reading2->score : $reading2) + ($score3 ? $reading3->score : $reading3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
                $listening = (($score1 ? $listening1->score : $listening1) + ($score2 ? $listening2->score : $listening2) + ($score3 ? $listening3->score : $listening3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
                $grammar = (($score1 ? $grammar1->score : $grammar1) + ($score2 ? $grammar2->score : $grammar2) + ($score3 ? $grammar3->score : $grammar3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
                $vocabulary = (($score1 ? $vocabulary1->score : $vocabulary1) + ($score2 ? $vocabulary2->score : $vocabulary2) + ($score3 ? $vocabulary3->score : $vocabulary3)) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
                $average = ($average_score1 + $average_score2 + $average_score3) / (($score1 ? 1 : 0) + ($score2 ? 1 : 0) + ($score3 ? 1 : 0));
            @endphp
            <div class="writing">
                {{ round($writing) . ' (' . Helper::getGrade($writing) . ')' }}
            </div>
            <div class="speaking">
                {{ round($speaking) . ' (' . Helper::getGrade($speaking) . ')' }}
            </div>
            <div class="reading">
                {{ round($reading) . ' (' . Helper::getGrade($reading) . ')' }}
            </div>
            <div class="listening">
                {{ round($listening) . ' (' . Helper::getGrade($listening) . ')' }}
            </div>
            <div class="grammar">
                {{ round($grammar) . ' (' . Helper::getGrade($grammar) . ')' }}
            </div>
            <div class="vocabulary">
                {{ round($vocabulary) . ' (' . Helper::getGrade($vocabulary) . ')' }}
            </div>
            <div class="average">
                {{ round($average) . ' (' . Helper::getGrade($average) . ')' }}
            </div>
        </div>
    @endif
    {{-- </div> --}}
    <script>
        // window.print();
        // window.onafterprint = (event) => {

        //     window.close();
        // };
    </script>
</body>

</html>
