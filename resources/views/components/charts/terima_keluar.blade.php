<div class="container-fluid">

    <!-- OVERVIEW -->
    <div class="panel panel-headline">
        <div class="panel-heading">
            <h3 class="panel-title text-center" style="font-weight: bold;" id="receipt_payment_title">
                DASHBOARD RECEIPT AND PAYMENT
            </h3>
        </div>
        <div class="panel-body">
            <div style="display: flex; justify-content:center; margin: 0px 0px 40px 0px">
                <input type="text" class="tk_start_month" name="tk_start">
                <div style="margin: 0px 15px 0px 15px">Hingga</div>
                <input type="text" class="tk_end_month" name="tk_end">
            </div>
            <div class="terima_keluar_chart">
                <canvas id="terima_keluar" class="w-100" height="400"></canvas>
            </div>
        </div>
        {{-- @include('components.charts.proses') --}}
        @include('components.charts.bar_chart_spp')
    </div>
    <!-- END OVERVIEW -->

</div>

<script>
    var startDateTk = '';
    var endDateTk = '';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    const pie_terima_keluar = new Chart(document.getElementById('terima_keluar'), {
        type: 'pie',
        data: {
            labels: ['Penerimaan', 'Pengeluaran'],
            datasets: [{
                label: 'SPP',
                data: [0, 0],
                backgroundColor: ['#00BFFF', '#FF6347'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Jumlah Total Penerimaan & Pengeluaran'
                },
                datalabels: {
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data.reduce((acc, val) => acc + val,
                            0);
                        const percentage = total != 0 ? ((value / total) * 100).toFixed(2) + '%' : '';
                        return percentage;
                    },
                    color: '#000000',
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            }
        },
        plugins: [ChartDataLabels],
    });

    function getDataTerimaKeluar(start_month, end_month) {
        $.ajax({
            type: 'POST',
            url: '{{ route('getKeluarTerima') }}',
            data: {
                'start_month': start_month,
                'end_month': end_month,
            },
            success: function(response) {
                console.log(response.data);
                pie_terima_keluar.data.datasets[0].data = [
                    response.data.total_penerimaan,
                    response.data.total_pembayaran
                ];
                pie_terima_keluar.options.plugins.title.text =
                    `Jumlah Total Penerimaan & Pengeluaran ${response.data.nama_region != '' ? response.data.nama_region : ''}`;
                pie_terima_keluar.update();

                // define start month and end month
                if (startDateTk == '' && endDateTk == '') {
                    $('.tk_start_month').first().val(response.data.start_month).trigger('change');
                    $('.tk_end_month').first().val(response.data.end_month).trigger('change');
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    function refreshTkStartMonth(month) {
        $('.tk_start_month').datepicker('destroy');
        $('.tk_start_month').datepicker({
            format: 'MM yyyy',
            maxViewMode: 'years',
            minViewMode: 'months',
            endDate: month != undefined ? new Date(month) : null,
            autoclose: true,
        })

    }

    function refreshTkEndMonth(month) {
        $('.tk_end_month').datepicker('destroy');
        $('.tk_end_month').datepicker({
            format: 'MM yyyy',
            maxViewMode: 'years',
            minViewMode: 'months',
            autoclose: true,
            startDate: month != undefined ? new Date(month) : null,
        });
    }

    $(document).ready(function() {
        var now = `${(new Date()).toLocaleString('en', {month: 'long'})} ${(new Date()).getFullYear()}`;
        getDataTerimaKeluar(now, now)

        $('.tk_start_month').on('change', function() {
            if (startDateTk != '') {
                refreshTkEndMonth($(this).val());

                startDateTk = $(this).val();

                getDataTerimaKeluar(startDateTk, endDateTk);
            } else {
                refreshTkEndMonth($(this).val());

                startDateTk = $(this).val();
            }
        });

        $('.tk_end_month').on('change', function() {
            if (endDateTk != '') {
                refreshTkStartMonth($(this).val());

                endDateTk = $(this).val();

                getDataTerimaKeluar(startDateTk, endDateTk);

            } else {
                refreshTkStartMonth($(this).val());

                endDateTk = $(this).val();
            }

        });

        $('.tk_start_month').on('hide', function() {
            if ($('.tk_start_month').first().val() == undefined || $('.tk_start_month').first().val() ==
                '') {
                $('.tk_start_month').first().val(startDateTk).trigger('change');
            }
        })

        $('.tk_end_month').on('hide', function() {
            if ($('.tk_end_month').first().val() == undefined || $('.tk_end_month').first().val() ==
                '') {
                $('.tk_end_month').first().val(endDateTk).trigger('change');
            }
        })
    });
</script>
