    {{-- <h3>Student Name : {{ $student->name }}</h3>
    @php
        $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
    @endphp

    <img
        src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($student->id, $generatorPNG::TYPE_CODE_128)) }}">
    <img src="{{ url('storage/barcode') . $student->id . '.jpg' }}" alt=""> --}}
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Print Barcode</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
            .body-print {
                width: 100mm;
                height: 60mm;
                font-family: 'Poppins', sans-serif;
                padding: 40px;
            }

            .print {
                margin-left: 18.897637795px;
                margin-right: 18.897637795px;
                margin-top: 75.590551181px;
                margin-bottom: 75.590551181px;
                width: 210mm;
                height: 297mm;
            }

            .grid-container {
                display: grid;
                grid-template-columns: auto auto;
                grid-gap: 50px;
                /* background-color: #2196F3; */
                padding: 10px;
            }

            .grid-item {
                /* background-color: rgba(255, 255, 255, 0.8); */
                /* border: 1px solid rgba(0, 0, 0, 0.8); */
                padding: 5px;
                font-size: 30px;
                text-align: center;
            }

            @page {
                size: A4;
                margin: 0;
            }

            @media print {
                .print {
                    width: 210mm;
                    height: 297mm;
                }

                /* ... the rest of the rules ... */
            }
        </style>
    </head>

    <body>
        @php
            $countStudent = count($student);
            $pembulatan = round($countStudent / 6) + 2;
        @endphp
        @for ($i = 1, $ii = 1; $i < $pembulatan; $i++, $ii++)
            {{-- {{ $pembulatan }} --}}
            @php
                $students = app\Models\Students::leftJoin('price', 'price.id', 'student.priceid')
                    ->select('student.id as id', 'student.name as name', 'price.program as program')
                    ->where('status', 'ACTIVE')
                    ->skip($i * 6 + 42 * 21 + 12 * 20)
                    ->take(6)
                    ->get();
            @endphp
            {{-- {{ $i * 6 + 42 }} --}}
            <div class="print">
                <div class="grid-container">
                    @foreach ($students as $item)
                        <div class="grid-item">
                            <div class="body-print">
                                <p style="margin: 2px !important;text-align: center;font-size: 20px;font-weight: 800;">
                                    {{ $item->name }}
                                </p>
                                {{-- <p style="margin:2px !important;text-align:center;font-size: 18px;font-weight: 600;">
                            {{ $item->program }}
                        </p> --}}
                                <img src="{{ url('storage/barcode/' . $item->id . '.jpg') }}" width="226px">
                                <p style="margin:2px !important;text-align:center;font-size: 16px;font-weight: 600;">
                                    {{ $item->id }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endfor
        {{-- <div style="width:500px;height:100px;border:1px solid #000;">This is a rectangle!</div> --}}
        {{-- <div style="width:500px;height:100px;border:1px solid #000;">This is a rectangle!</div> --}}
    </body>

    </html>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        // window.print();
    </script>
