<style>
    .col-pie {
        width: 450px;
        height: 450px;
    }

    .pie-spp {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }

    @media (max-width < 425px) {
        .col-pie {
            flex-direction: column;
            width: 100%;
            height: 100%;
        }
    }

    @media (max-width: 320px) {
        .col-pie {
            flex-direction: column;
            width: 100%;
            height: 100%;
        }
    }
</style>

<div class="container-fluid">

    <!-- OVERVIEW -->
    <div class="panel panel-headline">
        <div class="panel-heading">
            <h3 class="panel-title text-center" style="font-weight: bold;" id="title_pie_chart_spp">DASHBOARD STATUS SPPB /
                SPPN</h3>
        </div>
        <div class="panel-body" style="width: 100%">
            <div style="display: flex; justify-content:center; margin: 0px 0px 40px 0px">
                <input type="text" class="pie_spp_start_month" name="start">
                <div style="margin: 0px 15px 0px 15px">Hingga</div>
                <input type="text" class="pie_spp_end_month" name="end">
            </div>
            <div class="pie-spp">
                <div class="col-pie">
                    <canvas id="pie_sppb"></canvas>
                </div>
                <div class="col-pie">
                    <canvas id="pie_sppn"></canvas>
                </div>
            </div>
        </div>

        @include('components.charts.proses')
    </div>
    <!-- END OVERVIEW -->

</div>
<!-- END MAIN CONTENT -->

<script>
    var startDate = '';
    var endDate = '';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    const pie_sppb = new Chart(document.getElementById('pie_sppb'), {
        type: 'pie',
        data: {
            labels: ['Terbayar', 'Belum Terbayar'],
            datasets: [{
                label: 'SPPb',
                data: [0, 0],
                backgroundColor: ['#00BFFF', '#FF6347'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'SPPb Terbayar & Belum Terbayar'
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

    const pie_sppn = new Chart(document.getElementById('pie_sppn'), {
        type: 'pie',
        data: {
            labels: ['Selesai', 'Belum Selesai'],
            datasets: [{
                label: 'SPPn',
                data: [0, 0],
                backgroundColor: ['#00BFFF', '#FF6347'],
            }, ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'SPPn Selesai & Belum Selesai'
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

    function refreshPieChartSpp(data_sppb, data_sppn, company_nama) {
        pie_sppb.data.datasets[0].data = data_sppb;
        pie_sppn.data.datasets[0].data = data_sppn;

        pie_sppb.options.plugins.title.text =
            `SPPb Terbayar & Belum Terbayar${company_nama != ''? ' ' + company_nama : ''}`;
        pie_sppn.options.plugins.title.text =
            `SPPn Selesai & Belum Selesai${company_nama != ''? ' ' + company_nama : ''}`;

        pie_sppb.update();
        pie_sppn.update();
    }

    function getPieSPPData(start_month, end_month) {
        $.ajax({
            type: 'POST',
            url: '{{ route('piechart_spp') }}',
            data: {
                'start_month': start_month,
                'end_month': end_month,
            },
            success: function(response) {
                refreshPieChartSpp([response.sppb_terbayar, response.sppb_belum_terbayar], [response
                    .sppn_terselesaikan, response.sppn_belum_terselesaikan
                ], response.region_nama);
                // define start month and end month
                if (startDate == '' && endDate == '') {
                    $('.pie_spp_start_month').first().val(response.start).trigger('change');
                    $('.pie_spp_end_month').first().val(response.end).trigger('change');
                }
            }
        })
    }

    function refreshStartMonth(endMonth) {
        $('.pie_spp_start_month').datepicker('destroy');
        $('.pie_spp_start_month').datepicker({
            format: 'MM yyyy',
            maxViewMode: 'years',
            minViewMode: 'months',
            endDate: endMonth != undefined ? new Date(endMonth) : null,
            autoclose: true,
        });
    }

    function refreshEndMonth(startMonth) {
        $('.pie_spp_end_month').datepicker('destroy');
        $('.pie_spp_end_month').datepicker({
            format: 'MM yyyy',
            maxViewMode: 'years',
            minViewMode: 'months',
            autoclose: true,
            startDate: startMonth != undefined ? new Date(startMonth) : null,
        });
    }

    $(document).ready(function() {
        var now = `${(new Date()).toLocaleString('en', {month: 'long'})} ${(new Date()).getFullYear()}`;
        getPieSPPData(now, now)

        $('.pie_spp_start_month').on('change', function() {
            if (startDate != '') {
                refreshEndMonth($(this).val());

                startDate = $(this).val();

                getPieSPPData(startDate, endDate);
            } else {
                refreshEndMonth($(this).val());

                startDate = $(this).val();
            }
        });

        $('.pie_spp_end_month').on('change', function() {
            if (endDate != '') {
                refreshStartMonth($(this).val());

                endDate = $(this).val();

                getPieSPPData(startDate, endDate);
            } else {
                refreshStartMonth($(this).val());

                endDate = $(this).val();
            }
        });

        $('.pie_spp_start_month').on('hide', function() {
            if ($('.pie_spp_start_month').first().val() == undefined || $('.pie_spp_start_month')
                .first().val() == '') {
                $('.pie_spp_start_month').first().val(startDate).trigger('change');
            }
        })

        $('.pie_spp_end_month').on('hide', function() {
            if ($('.pie_spp_end_month').first().val() == undefined || $('.pie_spp_end_month').first()
                .val() == '') {
                $('.pie_spp_end_month').first().val(endDate).trigger('change');
            }
        })
    });
</script>
