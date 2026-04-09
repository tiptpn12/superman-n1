<style>
    .panel-title {
        /* text-align: center; */
        font-weight: bold !important;
    }

    .panel-body {
        padding: 50px;
    }

    .col-bar {
        width: 100%;
        height: 500px;
    }

    .col-bar:nth-child(2) {
        margin-top: 100px;
    }

    .pie-bar {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }

    .month-filter {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }

    #bar_divisi,
    #bar_regional {
        margin-top: 20px;
    }

    @media (max-width < 425px) {
        .col-bar {
            flex-direction: column;
            width: 100%;
            height: 100%;
        }

        .col-bar:nth-child(2) {
            margin-top: 20px;
        }
    }

    @media (max-width: 320px) {
        .col-bar {
            flex-direction: column;
            width: 100%;
            height: 100%;
        }

        .col-bar:nth-child(2) {
            margin-top: 10px;
        }
    }
</style>

<div class="container-fluid">

    <!-- OVERVIEW -->
    {{-- <div class="panel panel-headline"> --}}
    {{-- <div class="panel-heading">
            <h3 class="panel-title" style="text-align: center;" id="title_bar_chart_spp">DASHBOARD RECEIPT DAN PAYMENT</h3>
        </div> --}}
    <div class="panel-body" style="width: 100%; padding:50px;">
        <div class="bar-spp">
            <div class="col-bar">
                <div class="month-filter" id="filter-divisi">
                    <div class="input-filter">
                        <input type="month" name="bulan-mulai">
                    </div>
                    <span>Hingga</span>
                    <div class="input-filter">
                        <input type="month" name="bulan-akhir">
                    </div>
                </div>
                <canvas id="bar_divisi"></canvas>
            </div>
            <div class="col-bar">
                <div class="month-filter" id="filter-regional">
                    <div class="input-filter">
                        <input type="month" name="bulan-mulai">
                    </div>
                    <span>Hingga</span>
                    <div class="input-filter">
                        <input type="month" name="bulan-akhir">
                    </div>
                </div>
                <canvas id="bar_regional"></canvas>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    <!-- END OVERVIEW -->

</div>
<!-- END MAIN CONTENT -->

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    const filter_divisi = document.querySelector('#filter-divisi')
    const filter_regional = document.querySelector('#filter-regional')

    const tanggal_awal_divisi = filter_divisi.querySelectorAll('input')[0]
    const tanggal_akhir_divisi = filter_divisi.querySelectorAll('input')[1]

    const tanggal_awal_regional = filter_regional.querySelectorAll('input')[0]
    const tanggal_akhir_regional = filter_regional.querySelectorAll('input')[1]

    function refreshBarChartSpp(barchart, title, filter_element, data) {
        const labels = data.results.map(item => !item.company ? item.master_bagian_kode : (item.company.split('-')[1] ?
            item.company.split('-')[1] : item.company));
        const total_sppb = data.results.map(item => parseInt(item.total_sppb))
        const total_sppn = data.results.map(item => parseInt(item.total_sppn))

        const tanggal_awal_element = filter_element.querySelectorAll('input')[0]
        const tanggal_akhir_element = filter_element.querySelectorAll('input')[1]

        tanggal_awal_element.value = (data.tanggal_awal)
        tanggal_akhir_element.value = (data.tanggal_akhir)

        barchart.options.plugins.title.text = title
        barchart.data.labels = labels

        barchart.data.datasets[0].data = total_sppn
        barchart.data.datasets[1].data = total_sppb

        const allValuesZeroSppb = barchart.data.datasets[0].data.every(value => value === 0);
        const allValuesZeroSppn = barchart.data.datasets[1].data.every(value => value === 0);

        // Update chart options based on the check
        barchart.options.scales.y.display = !(allValuesZeroSppb && allValuesZeroSppn);

        barchart.update()
    }

    function fetchFilteredData(tanggal_awal_ele, tanggal_akhir_ele, filter_element, barchart, title, url) {
        const tanggal_awal = tanggal_awal_ele.value
        const tanggal_akhir = tanggal_akhir_ele.value

        console.log([tanggal_awal, tanggal_akhir]);

        if ((tanggal_awal && tanggal_akhir) && tanggal_awal > tanggal_akhir) {
            console.error('Invalid date range');
            return
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                'tanggal_awal': tanggal_awal,
                'tanggal_akhir': tanggal_akhir
            },
            success: function(response) {
                console.log(response);
                refreshBarChartSpp(barchart, title, filter_element, response)
            }
        })
    }


    const bar_chart_divisi = new Chart(document.getElementById('bar_divisi'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                    label: 'Penerimaan',
                    backgroundColor: 'rgb(91, 155, 213)', // Adjusted transparency for better distinction
                    borderWidth: 1,
                    data: []
                },
                {
                    label: 'Pengeluaran',
                    backgroundColor: 'rgb(237, 125, 49)', // Adjusted transparency for better distinction
                    borderWidth: 1,
                    data: []
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    beginAtZero: true,
                    // display : false,
                    ticks: {
                        callback: function(value) {
                            return value / 1000000
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    title: 'Receipt & Payment Head Office (Dalam Juta)'
                },
                legend: {
                    position: 'top'
                },
                datalabels: {
                    anchor: 'end', // Position of the label relative to the bar
                    align: 'end', // Alignment of the label
                    offset: 5,
                    formatter: (value, context) => {
                        if (!value || value === 0) {
                            return null
                        }
                        formattedValue = value / 1000000
                        return formattedValue.toFixed(2).toLocaleString().replaceAll(',',
                            '.'); // Format the label with commas
                    },
                    color: 'black', // Color of the label
                    font: {
                        weight: 'light',
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    })

    const bar_chart_regional = new Chart(document.getElementById('bar_regional'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                    label: 'Penerimaan',
                    backgroundColor: 'rgb(91, 155, 213)', // Adjusted transparency for better distinction
                    borderWidth: 1,
                    data: []
                },
                {
                    label: 'Pengeluaran',
                    backgroundColor: 'rgb(237, 125, 49)', // Adjusted transparency for better distinction
                    borderWidth: 1,
                    data: []
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value / 1000000000
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    title: 'Receipt & Payment HO & Reg (Dalam Milliar)'
                },
                legend: {
                    position: 'top'
                },
                datalabels: {
                    anchor: 'end', // Position of the label relative to the bar
                    align: 'end', // Alignment of the label
                    offset: 5,
                    formatter: (value, context) => {
                        if (!value || value === 0) {
                            return null
                        }
                        let formattedValue = value / 1000000000
                        return formattedValue.toLocaleString().replaceAll(',',
                            '.'); // Format the label with commas
                    },
                    color: 'black', // Color of the label
                    font: {
                        weight: 'light',
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    })


    $(document).ready(() => {
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        currentMonthDate = `${year}-${month}`
        console.log(currentMonthDate);

        const reqSppDivisi = $.ajax({
            url: '{{ route('barchart.divisi') }}',
            method: 'POST',
            data: {
                'tanggal_awal': currentMonthDate,
                'tanggal_akhir': currentMonthDate
            }
        })
        const reqSppRegional = $.ajax({
            url: '{{ route('barchart.regional') }}',
            method: 'POST',
            data: {
                'tanggal_awal': currentMonthDate,
                'tanggal_akhir': currentMonthDate
            }
        })

        Promise.all([reqSppDivisi, reqSppRegional])
            .then((responses) => {
                console.log(responses);

                const [responseSppDivisi, responseSppRegional] = responses
                refreshBarChartSpp(bar_chart_divisi, 'Receipt & Payment (Dalam Juta)', filter_divisi,
                    responseSppDivisi)
                refreshBarChartSpp(bar_chart_regional, 'Receipt & Payment (Dalam Milliar)', filter_regional,
                    responseSppRegional)
            })
    })

    $(tanggal_awal_divisi).on('change', () => {
        fetchFilteredData(tanggal_awal_divisi, tanggal_akhir_divisi, filter_divisi, bar_chart_divisi,
            'Receipt & Payment Head Office (Dalam Juta)', '{{ route('barchart.divisi') }}')
    })
    $(tanggal_akhir_divisi).on('change', () => {
        fetchFilteredData(tanggal_awal_divisi, tanggal_akhir_divisi, filter_divisi, bar_chart_divisi,
            'Receipt & Payment Head Office (Dalam Juta)', '{{ route('barchart.divisi') }}')
    })
    $(tanggal_awal_regional).on('change', () => {
        fetchFilteredData(tanggal_awal_regional, tanggal_akhir_regional, filter_regional, bar_chart_regional,
            'Receipt & Payment HO & Reg (Dalam Milliar)', '{{ route('barchart.regional') }}')
    })
    $(tanggal_akhir_regional).on('change', () => {
        fetchFilteredData(tanggal_awal_regional, tanggal_akhir_regional, filter_regional, bar_chart_regional,
            'Receipt & Payment Head Office (Dalam Juta)', '{{ route('barchart.regional') }}')
    })
</script>
