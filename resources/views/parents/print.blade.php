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
        <title>Print {{ $id }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">
        <style>
            .body-print {
                width: 226.77165354px;
                height: 377.95275591px;
                font-family: 'Poppins', sans-serif;
            }
        </style>
    </head>

    <body>
        <div class="body-print">
            <p style="margin: 2px !important;text-align: center;font-size: 20px;font-weight: 800;">{{ $student->name }}
            </p>
            {{-- <p style="margin:2px !important;text-align:center;font-size: 18px;font-weight: 600;">{{ $student->program }}
            </p> --}}
            <!--<img src="{{ url('storage/barcode/' . $id . '.jpg') }}" width="225px">-->
            <img src="{{ url('barcode/' . $id . '.jpg') }}" width="225px">
            <p style="margin:2px !important;text-align:center;font-size: 16px;font-weight: 600;">{{ $id }}</p>
        </div>
    </body>

    </html>
    <script>
        // window.print();
    </script>
