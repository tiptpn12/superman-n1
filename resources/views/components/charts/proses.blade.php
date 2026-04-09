{{-- <div class="container-fluid">
    <!-- OVERVIEW -->
    <div class="panel panel-headline">
        <div class="panel-heading">
            <h3 class="panel-title" id="title_bar_chart_proses">Pemrosesan SPPb/SPPn</h3>
        </div> --}}
        <div class="panel-body">
            <div class="row">
                <div class="col-12">
                    <div class="bar-proses">
                        <canvas id="proses" class="w-100" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{--
    </div>
    <!-- END OVERVIEW -->
</div> --}}

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    const proses = new Chart(document.getElementById('proses'), {
        type: 'bar',
        data: {
            labels: ['Operator', 'Asisten Akuntansi Perpajakan', 'Asisten Pajak', 'Asisten Verifikasi', 
                    'Asisten Anggaran', 'Asisten Miro', 'Asisten Kas dan Bank', 'Asisten Pembayaran'
            ],
            datasets: [{
                label: 'Jumlah SPP',
                data: [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',     // Operator (pink)
                    'rgba(255, 159, 64, 0.7)',     // Asisten Akuntansi Perpajakan (oranye)
                    'rgba(255, 205, 86, 0.7)',     // Asisten Pajak (kuning)
                    'rgba(64, 224, 208, 0.7)',     // Asisten Verifikasi (turquoise)
                    'rgba(75, 192, 192, 0.7)',     // Asisten Anggaran (tosca)
                    'rgba(54, 162, 235, 0.7)',     // Asisten Miro (biru muda)
                    'rgba(153, 102, 255, 0.7)',    // Asisten Kas dan Bank (ungu)
                    'rgba(201, 203, 207, 0.7)'     // Asisten Pembayaran (abu-abu)
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(64, 224, 208)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Jumlah Proses SPP per Posisi'
                },
                legend: {
                    display: false
                },
                datalabels: {
                    anchor: 'end', // Position of the label relative to the bar
                    align: 'end', // Alignment of the label
                    offset: 5,
                    color: 'black', // Color of the label
                    font: {
                        weight: 'light',
                    }
                }
            },
        },
        plugins: [ChartDataLabels]
    });

    $(document).ready(function () {
        $.ajax({
            type: 'GET',
            url: '{{ route('getTotalProses') }}',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (data) {
                // console.log(data);
                // proses.data.label = "test"
                proses.data.datasets[0].data = [
                    data.data.proses_divisi,
                    data.data.proses_akuntansi,
                    data.data.proses_perpajakan,
                    data.data.proses_verifikasi,
                    data.data.proses_anggaran,
                    data.data.proses_miro,
                    data.data.proses_kas_bank,
                    data.data.proses_pembayaran
                ];
                proses.update();
            }
        });
    });
</script>
