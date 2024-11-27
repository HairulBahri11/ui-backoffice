<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Receipt example</title>
    <style>
        * {
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        .content-title {
            font-size: 12pt !important;
            font-weight: bolder;
        }

        .p2 {
            font-size: 8pt !important;
            font-weight: 500;
            font-family: Arial, sans-serif !important;
        }

        .centered {
            text-align: center;
            align-content: center;
            font-family: Arial, sans-serif !important;
        }

        .foot {
            font-size: 6pt !important;
            font-style: italic;
        }

        .subtitle {
            margin-block-start: 0px;
            margin-block-end: 0px;
            font-family: Arial, sans-serif !important;
        }


        hr {
            border: none;
            border-top: 2px dotted #000;
            color: #fff;
            background-color: #fff;
            height: 1px;
            width: 100%;
        }

        .not-a-flexbox {
            display: table;
            width: 100%;
        }

        .box {
            display: table-cell;
            text-align: left;
        }

        .box2 {
            display: table-cell;
            text-align: right;
        }

        .box>span {
            display: inline-block;
            margin: 0 10px;
            font-size: 8pt !important;
        }

        .box2>span {
            display: inline-block;
            margin: 0 10px;
            font-size: 8pt !important;
        }

        @page {
            margin: 4px;
        }

        body {
            margin: 0px;
        }
    </style>
</head>

<body>
    <div class="ticket">

        <p class="centered content-title">U&I
            <br>ENGLISH COURSE
        </p>
        <p class="centered p2">Sutorejo Prima Utara PDD 18-19<br> Surabaya
            <br> 031-58204040/58207070
        </p>
        <hr>
        <p class="centered p2 subtitle">
            {{$detail->unique_code}} <br>
            {{$detail->date}} </p>
        <hr>
        <p class="centered p2">INVOICE</p>

        @php
        $total = 0;
        @endphp
        @foreach ($data as $item)
        <div style="margin-bottom: 8px">
            <div class="not-a-flexbox">
                <div class="box">
                    <span>ID</span>
                </div>

                <div class="box2">
                    <span>{{$item->student_id}}</span>
                </div>
            </div>
            <div class="not-a-flexbox">
                <div class="box">
                    <span>NAME</span>
                </div>

                <div class="box2">
                    <span>{{substr($item->name,0,15)}}</span>
                </div>
            </div>
            <div class="not-a-flexbox">
                <div class="box">
                    <span>LEVEL</span>
                </div>

                <div class="box2">
                    <span>{{$item->program}}</span>
                </div>
            </div>
            {{-- <div class="not-a-flexbox">
                <div class="box">
                    <span>METHOD</span>
                </div>

                <div class="box2">
                    <span>{{$item->method}}</span>
                </div>
            </div>
            <div class="not-a-flexbox">
                <div class="box">
                    <span>BANK</span>
                </div>

                <div class="box2">
                    <span>{{$item->bank}}</span>
                </div>
            </div>
            <div class="not-a-flexbox">
                <div class="box">
                    <span>NUMBER</span>
                </div>

                <div class="box2">
                    <span>{{$item->number}}</span>
                </div>
            </div> --}}
            <div class="not-a-flexbox">
                <div class="box">
                    <span>PAYMENT</span>
                </div>

                <div class="box2">
                    <span>{{$item->payment}}</span>
                </div>
            </div>
            <div class="not-a-flexbox">
                <div class="box">

                </div>

                <div class="box2">
                    <span>Rp. {{
                        number_format($item->price,0,',','.').",00"}}</span>
                </div>
            </div>
        </div>
        @php
        $total += $item->price;
        @endphp
        @endforeach

        <div class="not-a-flexbox" style="margin-top: 8px;">
            <div class="box">
                <span style="font-weight: bolder;">Total</span>
            </div>

            <div class="box2">
                <span style="font-weight: bolder;">Rp. {{
                    number_format($detail->amount,0,',','.').",00"}}</span>
            </div>
        </div>
        {{-- <div class="not-a-flexbox" style="margin-top: 8px;">
            <div class="box">
                <span style="font-weight: bolder;">Total</span>
            </div>

            <div class="box2">
                <span style="font-weight: bolder;">Rp. 250.000,0</span>
            </div>
        </div> --}}

        <p class="centered foot">Thank You
        </p>
    </div>

</body>

</html>