<!--   Core JS Files   -->

<script src="{{ url('/') }}/assets/js/core/popper.min.js"></script>
<script src="{{ url('/') }}/assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="{{ url('/') }}/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="{{ url('/') }}/assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="{{ url('/') }}/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>


<!-- Chart JS -->
<script src="{{ url('/') }}/assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="{{ url('/') }}/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="{{ url('/') }}/assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="{{ url('/') }}/assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Bootstrap Notify -->
{{-- <script src="{{url('/')}}/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script> --}}

<!-- jQuery Vector Maps -->
<script src="{{ url('/') }}/assets/js/plugin/jqvmap/jquery.vmap.min.js"></script>
<script src="{{ url('/') }}/assets/js/plugin/jqvmap/maps/jquery.vmap.world.js"></script>

<!-- Atlantis JS -->
<script src="{{ url('/') }}/assets/js/atlantis.min.js"></script>
<script src="{{ url('/') }}/assets/dropify/dist/js/dropify.min.js"></script>
<!-- Datatables -->
<script src="{{ url('/') }}/assets/js/plugin/datatables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Atlantis DEMO methods, don't include it in your project! -->
<script src="{{ url('/') }}/assets/js/setting-demo.js"></script>
<script src="{{ url('/') }}/assets/js/demo.js"></script>
<style>
    .select2-hidden-accessible {
        border: 0 !important;
        clip: rect(0 0 0 0) !important;
        height: 1px !important;
        margin: -1px !important;
        overflow: hidden !important;
        padding: 0 !important;
        position: absolute !important;
        width: 1px !important
    }

    .select2-container--default .select2-selection--single,
    .select2-selection .select2-selection--single {
        border: 1px solid #d2d6de;
        border-radius: 0;
        padding: 6px 12px;
        height: 40px
    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px
    }

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 28px;
        user-select: none;
        -webkit-user-select: none
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: 10px
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        padding-right: 0;
        height: auto;
        margin-top: -3px
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #444;
        line-height: 28px
    }

    .select2-container--default .select2-selection--single,
    .select2-selection .select2-selection--single {
        border: 1px solid #d2d6de;
        border-radius: 0 !important;
        padding: 6px 12px;
        height: 40px !important
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px;
        position: absolute;
        top: 6px !important;
        right: 1px;
        width: 20px
    }
</style>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            closeOnSelect: true
        });
    });
</script>
<script>
    Circles.create({
        id: 'circles-1',
        radius: 45,
        value: 60,
        maxValue: 100,
        width: 7,
        text: 5,
        colors: ['#f1f1f1', '#FF9E27'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    Circles.create({
        id: 'circles-2',
        radius: 45,
        value: 70,
        maxValue: 100,
        width: 7,
        text: 36,
        colors: ['#f1f1f1', '#2BB930'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    Circles.create({
        id: 'circles-3',
        radius: 45,
        value: 40,
        maxValue: 100,
        width: 7,
        text: 12,
        colors: ['#f1f1f1', '#F25961'],
        duration: 400,
        wrpClass: 'circles-wrp',
        textClass: 'circles-text',
        styleWrapper: true,
        styleText: true
    })

    // var totalIncomeChart = document.getElementById('totalIncomeChart').getContext('2d');

    // var mytotalIncomeChart = new Chart(totalIncomeChart, {
    //     type: 'bar',
    //     data: {
    //         labels: ["S", "M", "T", "W", "T", "F", "S", "S", "M", "T"],
    //         datasets : [{
    //             label: "Total Income",
    //             backgroundColor: '#ff9e27',
    //             borderColor: 'rgb(23, 125, 255)',
    //             data: [6, 4, 9, 5, 4, 6, 4, 3, 8, 10],
    //         }],
    //     },
    //     options: {
    //         responsive: true,
    //         maintainAspectRatio: false,
    //         legend: {
    //             display: false,
    //         },
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     display: false //this will remove only the label
    //                 },
    //                 gridLines : {
    //                     drawBorder: false,
    //                     display : false
    //                 }
    //             }],
    //             xAxes : [ {
    //                 gridLines : {
    //                     drawBorder: false,
    //                     display : false
    //                 }
    //             }]
    //         },
    //     }
    // });
</script>

<script>
    $(document).ready(function() {
        $('#basic-datatables').DataTable({
            responsive: true
        });

    });
</script>
