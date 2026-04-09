<!DOCTYPE html>
<html>

<head>
    <title>Cetak Bukti Kas dan Bank</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/linearicons/style.css">
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/chartist/css/chartist-custom.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/main.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/timeline.css">
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <!-- ICONS -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('') }}assets/img/ptpn1.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('') }}assets/img/ptpn1g.png">
    <!-- JAVASCRIPT -->
    <script src="{{ asset('') }}assets/vendor/jquery/jquery.min.js"></script>
    <style type="text/css">
        @media print {

            m m .col-sm-1,
            .col-sm-2,
            .col-sm-3,
            .col-sm-4,
            .col-sm-5,
            .col-sm-6,
            .col-sm-7,
            .col-sm-8,
            .col-sm-9,
            .col-sm-10,
            .col-sm-11,
            .col-sm-12 {
                float: left;
            }

            .col-sm-12 {
                width: 100%;
            }

            .col-sm-11 {
                width: 91.66666667%;
            }

            .col-sm-10 {
                width: 83.33333333%;
            }

            .col-sm-9 {
                width: 75%;
            }

            .col-sm-8 {
                width: 66.66666667%;
            }

            .col-sm-7 {
                width: 58.33333333%;
            }

            .col-sm-6 {
                width: 50%;
            }

            .col-sm-5 {
                width: 41.66666667%;
            }

            .col-sm-4 {
                width: 33.33333333%;
            }

            .col-sm-3 {
                width: 25%;
            }

            .col-sm-2 {
                width: 16.66666667%;
            }

            .col-sm-1 {
                width: 8.33333333%;
            }

            @page {
                size: A4 landscape;
                margin: 0mm !important;
            }

            .text-underline {
                text-decoration: underline !important;
            }

            .panels {
                transform-origin: top;
                transform: scale(0.9, 0.9);
                margin-bottom: 0px !important;
            }

            table.table-bordered.sppb {
                border: 1px solid blue !important;
                color: blue !important;
            }

            table.table-bordered.sppb>thead>tr>th {
                border: 1px solid blue !important;
                color: blue !important;
            }

            table.table-bordered.sppb>tbody>tr>td {
                border: 1px solid blue !important;
                color: blue !important;
            }

            table.table-bordered.sppb>tfoot>tr>th {
                border: 1px solid blue !important;
                color: blue !important;
                padding-bottom: 0mm !important;

            }

            table.table-bordered.sppb>tfoot>tr>th.terbilang {
                border: 1px solid blue !important;
                color: blue !important;
                padding-bottom: 0mm !important;
                border-right-style: hidden !important;

            }

            table.table-bordered.sppn {
                border: 1px solid red !important;
                color: red !important;
            }

            table.table-bordered.sppn>thead>tr>th {
                border: 1px solid red !important;
                color: red !important;
            }

            table.table-bordered.sppn>tbody>tr>td {
                border: 1px solid red !important;
                color: red !important;
            }

            table.table-bordered.sppn>tfoot>tr>th {
                border: 1px solid red !important;
                color: red !important;
            }

            table.table-bordered.sppn>tfoot>tr>th.terbilang {
                border: 1px solid red !important;
                color: red !important;
                border-right-style: hidden !important;
            }

            .ml-3 {
                margin-left: 12px !important;
            }

            .footer-sppb {
                color: blue !important;
            }

            .uraian-sppb * {
                color: blue !important;
            }

            .footer-sppn {
                color: red !important;
            }

            .uraian-sppn * {
                color: red !important;
            }





        }

        .mb-0 {
            margin-bottom: 0mm !important;
        }

        .my-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }

        .flex {
            display: flex !important;
        }

        .flex-column {
            flex-direction: column !important;
            justify-content: center !important;
        }

        .align-center {
            vertical-align: middle !important;
        }

        .custom-table {
            border: 0px solid #000 !important;
            padding: 10px;
        }

        .custom-table td {
            border: 0px solid #000 !important;
        }

        .custom-table .label {
            text-align: right;
            font-weight: bold;

        }

        .justify-center {
            text-align: center !important;
            justify-content: center !important;
            gap: 30px;
        }
    </style>

    <input type="hidden" id="formspp" value="">
</head>

<body onload="window.print();">
    <div class="wrapper">
        <div class="container">
            <div class="row">
                @php
                    function convertTanggal($tanggal_cetak)
                    {
                        try {
                            return Carbon\Carbon::createFromFormat('Y-m-d', $tanggal_cetak);
                        } catch (Exception $e) {
                            return Carbon\Carbon::now();
                        }
                    }

                    $tanggal_cetak_sppb = $tanggal_cetak_sppb ?? Carbon\Carbon::now()->format('Y-m-d');
                    $tanggal_cetak_sppn = $tanggal_cetak_sppn ?? Carbon\Carbon::now()->format('Y-m-d');

                    $tanggal_sppb = convertTanggal($tanggal_cetak_sppb);
                    $tahun_sppb = $tanggal_sppb->year;
                    $month_sppb = $tanggal_sppb->month;
                    $day_sppb = $tanggal_sppb->day;

                    $bulanromawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

                    $bulan_sppb = $bulanromawi[$month_sppb] ?? '-';

                    $buktisppb = null;
                    if (isset($sppb['sppb_metode_pembayaran'])) {
                        if ($sppb['sppb_metode_pembayaran'] == 'bank') {
                            $buktisppb = 'HO' . '/' . 'B.';
                            // $buktisppb =
                            //     'B.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' .
                            //     'HO' .
                            //     '/' .
                            //     'Pb' .
                            //     '/' .
                            //     $day_sppb .
                            //     '/' .
                            //     $bulan_sppb .
                            //     '/' .
                            //     $tahun_sppb;
                        } elseif ($sppb['sppb_metode_pembayaran'] == 'tidak_transfer') {
                            $buktisppb = 'HO' . '/' . 'K.';
                            // $buktisppb =
                            //     'K.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' . $day_sppb . '/' . $bulan_sppb . '/' . $tahun_sppb;
                        }
                    }

                    $tanggal_sppn = convertTanggal($tanggal_cetak_sppn);
                    $tahun_sppn = $tanggal_sppn->year;
                    $month_sppn = $tanggal_sppn->month;
                    $day_sppn = $tanggal_sppn->day;

                    $bulan_sppn = $bulanromawi[$month_sppn] ?? '-';
                    $buktisppn = null;
                    if (isset($sppb['sppb_metode_pembayaran'])) {
                        if ($sppb['sppb_metode_pembayaran'] == 'bank') {
                            $buktisppn = 'HO' . '/' . 'B.';
                            // $buktisppn =
                            //     'B.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' .
                            //     'HO' .
                            //     '/' .
                            //     'Pn' .
                            //     '/' .
                            //     $day_sppn .
                            //     '/' .
                            //     $bulan_sppn .
                            //     '/' .
                            //     $tahun_sppn;
                        } elseif ($sppb['sppb_metode_pembayaran'] == 'tidak_transfer') {
                            $buktisppn = 'HO' . '/' . 'K.';
                            // $buktisppn =
                            //     'K.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' . $day_sppn . '/' . $bulan_sppn . '/' . $tahun_sppn;
                        }
                    } elseif (isset($sppn['sppn_metode_pembayaran'])) {
                        $buktisppn = 'HO' . '/' . 'B.';
                        // $buktisppn =
                        //     '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' .
                        //     'HO' .
                        //     '/' .
                        //     'Pn' .
                        //     '/' .
                        //     $day_sppn .
                        //     '/' .
                        //     $bulan_sppn .
                        //     '/' .
                        //     $tahun_sppn;
                        // if ($sppn['sppn_metode_pembayaran'] == 'bank') {
                        //     $buktisppn =
                        //         'B.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' .
                        //         'HO' .
                        //         '/' .
                        //         'Pn' .
                        //         '/' .
                        //         $day_sppn .
                        //         '/' .
                        //         $bulan_sppn .
                        //         '/' .
                        //         $tahun_sppn;
                        // } elseif ($sppn['sppn_metode_pembayaran'] == 'tidak_transfer') {
                        //     $buktisppn =
                        //         'K.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/' . $day_sppn . '/' . $bulan_sppn . '/' . $tahun_sppn;
                        // }
                    }
                @endphp

                @if (isset($sppb))

                    <div class="panels" id="panel-pengeluaran">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered sppb mb-0">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center">
                                                <div class="logo text-center"><img
                                                        src="{{ asset('') }}assets/img/ptpn1.png"
                                                        alt="Klorofil Logo" style="height: 80px;"></div>
                                                @php
                                                    $company_arr = explode('-', $company);
                                                @endphp
                                                <div class=" flex flex-column">
                                                    <span>{{ $company_arr[0] }}</span>
                                                    <span>{{ $company_arr[1] }}</span>
                                                </div>

                                                @if ($company_id == 1)
                                                    <span style="color:black !important">JAKARTA</span>
                                                @elseif($company_id == 2)
                                                    <span style="color:black !important">SURABAYA</span>
                                                @endif
                                            </th>
                                            @if (isset($databuktikassppb))
                                                <th style="text-align:center; vertical-align:middle" class="col-sm-4">
                                                    BUKTI PENGELUARAN <br>
                                                    {{-- No. Rek
                                                <span style="color:black !important">{{$databuktikassppb->master_rekening_kode_sap}} </span> --}}
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <br>
                                                    <div class="flex justify-center">
                                                        <div class="flex flex-column">
                                                            <input type="checkbox" value="kas" name="kas"
                                                                id="kas"
                                                                {{ $sppb['sppb_metode_pembayaran'] == 'tidak_transfer' ? 'checked' : '' }}>
                                                            <label for="kas">Kas</label>
                                                        </div>

                                                        <div class="flex flex-column">
                                                            <input type="checkbox" value="bank" name="bank"
                                                                id="bank" class=""
                                                                {{ $sppb['sppb_metode_pembayaran'] == 'bank' ? 'checked' : '' }}>
                                                            <label for="bank">Bank</label>
                                                        </div>

                                                    </div>
                                                </th>

                                                <th class="p-0 align-center">
                                                    <table class="custom-table">
                                                        <tr>
                                                            <td><b>No. Bukti</b></td>
                                                            <td>: {!! $buktisppb !!}</td>
                                                        </tr>
                                                        @if (isset($sppb['sppb_metode_pembayaran']))
                                                            @if ($sppb['sppb_metode_pembayaran'] == 'bank')
                                                                <tr>
                                                                    <td><b>Tgl. Posting B</b></td>
                                                                    <td>: </td>
                                                                </tr>
                                                            @elseif ($sppb['sppb_metode_pembayaran'] == 'tidak_transfer')
                                                                <tr>
                                                                    <td><b>Tgl. Posting K</b></td>
                                                                    <td>: </td>
                                                                </tr>
                                                            @endif
                                                        @endif
                                                        <tr>
                                                            <td><b>No. Doc. SAP</b></td>
                                                            <td>: </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Referensi</b></td>
                                                            <td>: {{ $sppb['sppb_no'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>GL Account</b></td>
                                                            <td>: {{ $databuktikassppb->master_gl_kode }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Deskripsi</b></td>
                                                            <td>: {{ $databuktikassppb->master_gl_keterangan }}
                                                            </td>
                                                        </tr>
                                                    </table>



                                                </th>
                                            @else
                                                <th style="text-align:center; vertical-align:middle ;font-size:11px">
                                                    BUKTI PENERIMAAN KAS SPPN <br> No. Rek ..................
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp No. Cek/Giro .................</th>

                                                <th style="text-align:center; vertical-align:middle;"> No.
                                                    {{ $nomor }}th>
                                            @endif
                                        </tr>
                                    </thead>
                                </table>
                                <table class="table table-bordered sppb my-auto ">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center"> Akun</th>
                                            <th style="text-align:center"> Vendor/Customer</th>
                                            <th style="text-align:center"> Deskripsi Akun </th>
                                            <th style="text-align:center"> Uraian </th>
                                            <th style="text-align:center"> Cost Center </th>
                                            <th style="text-align:center"> Profit Center </th>
                                            <th colspan="2" style="text-align:center"> Jumlah (Rp.) </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $count = count($sppb_isi);
                                        @endphp

                                        @if ($count <= 3)
                                            @foreach ($sppb_isi as $isi)
                                                <tr>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $isi->master_gl_kode }}</td>
                                                    @if ($sppb['sppb_data_metpen'] == 'input_data')
                                                        <td style="text-align:center;color:black !important">
                                                            {{ $nama_karyawan_sppb[0]->karyawan_nama }}</td>
                                                    @else
                                                        <td style="text-align:center;color:black !important">
                                                            {{ $sppb['master_vendor_nama'] }}
                                                        </td>
                                                    @endif
                                                    <td style="text-align:left;color:black !important">
                                                        {{ $isi->master_gl_keterangan }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {!! $isi->sppb_uraian_uraian !!}
                                                    </td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $isi->master_cost_center_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $isi->master_profit_unit }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ number_format($isi->sppb_nominal_akhir) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @for($i = 0; $i < 3; $i++)
                                            <tr>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $sppb_isi[$i]->master_gl_kode }}</td>
                                                    @if ($sppb['sppb_data_metpen'] == 'input_data')
                                                        <td style="text-align:center;color:black !important">
                                                            {{ $nama_karyawan_sppb[0]->karyawan_nama }}</td>
                                                    @else
                                                        <td style="text-align:center;color:black !important">
                                                            {{ $sppb['master_vendor_nama'] }}
                                                        </td>
                                                    @endif
                                                    <td style="text-align:left;color:black !important">
                                                        {{ $sppb_isi[$i]->master_gl_keterangan }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {!! $sppb_isi[$i]->sppb_uraian_uraian !!}
                                                    </td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $sppb_isi[$i]->master_cost_center_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $sppb_isi[$i]->master_profit_center_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ number_format($sppb_isi[$i]->sppb_nominal_akhir) }}</td>
                                                </tr>
                                            @endfor

                                            @php
                                                $jumlah_non_tiga = 0;
                                                for ($j = 3; $j < $count; $j++) {
                                                    $jumlah_non_tiga += $sppb_isi[$j]->sppb_nominal_akhir;
                                                }
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align:center">
                                                    Rincian Terlampir
                                                    <br><br>
                                                    Sesuai {{ $sppb['sppb_no'] }}
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align:center;">{{ number_format($jumlah_non_tiga) }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="6" style="text-align:center;color:black !important"> JUMLAH
                                            </td>
                                            <td style="text-align:center;color:black !important">
                                                {{ number_format($sppb['sppb_total']) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <th colspan="" class="terbilang"> No. Cek/Giro : </th>
                                        <th colspan="6"><span
                                                style="color:black !important; text-align:left">{{ $databuktikassppb->cek_giro }}</span>
                                        </th>
                                    </tfoot>
                                    <tfoot>
                                        <th colspan="" class="terbilang"> Terbilang : </th>
                                        <th colspan="6"
                                            style="text-align:center; width:500px ; font-style:italic; font-weight:bold; color:black !important; text-transform:capitalize">
                                            {{ Terbilang::angka($sppb['sppb_total']) }} rupiah </th>
                                    </tfoot>
                                </table>
                                <table class="table table-bordered sppb mb-0">
                                    {{-- @dd($company_id); --}}
                                    @if($company_jenis == "REG")
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1" style="text-align:center">Dibuat</th>
                                            <th class="col-sm-4" style="text-align:center; vertical-align:middle"
                                                colspan=2>
                                                Diperiksa</th>
                                            <th class="col-sm-2" style="text-align:center; vertical-align:middle">
                                                Disetujui</th>
                                            <th class="col-sm-1" style="text-align:center; vertical-align:middle">
                                                Diterima</th>
                                            <!-- <th rowspan="2" style="text-align:center; vertical-align:middle"> Penyetor </th> -->
                                            <!-- <th colspan="2" style="text-align:center">Telah menerima jumlah tersebut diatas</th> -->
                                            <!-- <th colspan="2" style="text-align:center"> Dibukukan </th> -->

                                        </tr>
                                            
                                    </thead>
                                    <tbody style="text-align: center;">
                                        <td class="col-sm-1" style="height: 200px;">
                                            <br><br><br><br><br><br>
                                            <span>{{ $data_penandatangan_sppb->dibuat_sub_bagian }}</span>
                                        </td>
                                        <td class="col-sm-2" style="height: 200px;">
                                            <br><br><br><br><br><br>
                                            <span
                                                style="text-decoration: underline;">{{ $data_penandatangan_sppb->diperiksa_oleh_sub_bagian_nama }}</span>
                                            <!-- <span class="text-underline"> Nugraha Akbar</span> -->
                                            <br>
                                            <span>{{ $data_penandatangan_sppb->diperiksa_oleh_sub_bagian }}</span>
                                            <!-- <span>Kasubdiv Perbendaharaan dan Anggaran</span> -->
                                        </td>
                                        <td class="col-sm-2" style="height: 200px;">
                                            <br><br><br><br><br><br>
                                            <span
                                                style="text-decoration: underline;">{{ $data_penandatangan_sppb->diperiksa_oleh_bagian_nama }}</span>
                                            <br>
                                            <span>{{ $data_penandatangan_sppb->diperiksa_oleh_bagian }}</span>
                                        </td>
                                        <td class="col-sm-2" style="height: 200px;">
                                            <br><br><br><br><br><br>
                                            <span
                                                style="text-decoration: underline;">{{ $data_penandatangan_sppb->disetujui_oleh_nama }}</span><br>
                                            <span>{{ $data_penandatangan_sppb->disetujui_oleh }}</span>
                                        </td>
                                        <td class="col-sm-1" style="height: 200px;"> </td>
                                        <!-- <td style="height: 80px;"> </td> -->
                                        <!-- <td style="height: 80px;"> </td> -->
                                        <!-- <td style="height: 80px;"> </td> -->
                                        <!-- <td style="height: 80px;"> </td> -->
                                        <!-- <td style="height: 80px;"> </td> -->
                                        <!-- <td style="height: 80px;"> </td> -->

                                    </tbody>
                                    @else
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1" style="text-align:center">Dibuat</th>
                                            <th class="col-sm-4" style="text-align:center; vertical-align:middle;width:25%;">Diperiksa</th>
                                            <th class="col-sm-2" style="text-align:center; vertical-align:middle;width:25%;">Disetujui</th>
                                            <th class="col-sm-1" style="text-align:center; vertical-align:middle;width:25%;">Diterima</th>
                                            <!-- <th rowspan="2" style="text-align:center; vertical-align:middle"> Penyetor </th> -->
                                            <!-- <th colspan="2" style="text-align:center">Telah menerima jumlah tersebut diatas</th> -->
                                            <!-- <th colspan="2" style="text-align:center"> Dibukukan </th> -->

                                        </tr>
                                            
                                    </thead>
                                        <tbody style="text-align: center;">
                                            <td class="col-sm-1" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span>{{ $data_penandatangan_sppb->dibuat_sub_bagian }}</span>
                                            </td>
                                            <td class="col-sm-2" style="height: 200px;">
                                            <br><br><br><br><br><br>
                                                <span>{{ $data_penandatangan_sppb->diperiksa_oleh_sub_bagian_nama }}</span>
                                                <!-- <span class="text-underline"> Nugraha Akbar</span> -->
                                                <br>
                                                <span>{{ $data_penandatangan_sppb->diperiksa_oleh_sub_bagian }}</span>
                                                <!-- <span>Kasubdiv Perbendaharaan dan Anggaran</span> -->
                                            </td>
                                            <td class="col-sm-2" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span
                                                    style="text-decoration: underline;">{{ $data_penandatangan_sppb->disetujui_oleh_nama }}</span><br>
                                                <span>{{ $data_penandatangan_sppb->disetujui_oleh }}</span>
                                            </td>
                                            <td class="col-sm-1" style="height: 200px;"> </td>
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                        </tbody>
                                    @endif
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                            </div>
                        </div>
                    </div>
                @endif

                @if (isset($sppb) && isset($sppn))
                    <p id="afterp" style="page-break-after: always;">&nbsp;</p>
                    <p id="beforep" style="page-break-before: always;">&nbsp;</p>
                @endif
                @if (isset($sppn))

                    <div class="panels" id="panel-penerimaan">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered sppn mb-0">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center">
                                                <div class="logo text-center"><img
                                                        src="{{ asset('') }}assets/img/ptpn1.png"
                                                        alt="Klorofil Logo" style="height: 80px;"></div>
                                                @php
                                                    $company_arr = explode('-', $company);
                                                @endphp
                                                <div class=" flex flex-column">
                                                    <span>{{ $company_arr[0] }}</span>
                                                    <span>{{ $company_arr[1] }}</span>
                                                </div>

                                                @if ($company_id == 1)
                                                    <span style="color:black !important">JAKARTA</span>
                                                @elseif($company_id == 2)
                                                    <span style="color:black !important">SURABAYA</span>
                                                @endif
                                            </th>
                                            @if (isset($databuktikassppn))
                                                <th style="text-align:center; vertical-align:middle" class="col-sm-4">
                                                    BUKTI PENERIMAAN <br>
                                                    {{-- No. Rek
                                            <span style="color:black !important">{{$databuktikassppb->master_rekening_kode_sap}} </span> --}}
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <br>
                                                    <div class="flex justify-center">
                                                        <div class="flex flex-column">
                                                            <input type="checkbox" value="kas" name="kas"
                                                                id="kas"
                                                                {{ (isset($sppb) && $sppb['sppb_metode_pembayaran'] == 'tidak_transfer') || (!isset($sppb) && $sppn['sppn_metode_pembayaran'] == 'tidak_transfer') ? 'checked' : '' }}>
                                                            <label for="kas">Kas</label>
                                                        </div>

                                                        <div class="flex flex-column">
                                                            <input type="checkbox" value="bank" name="bank"
                                                                id="bank" class=""
                                                                {{ (isset($sppb) && $sppb['sppb_metode_pembayaran'] == 'bank') || (!isset($sppb) && $sppn['sppn_metode_pembayaran'] == 'bank') || (!isset($sppb) && $sppn['sppn_metode_pembayaran'] == '') ? 'checked' : '' }}>
                                                            <label for="bank">Bank</label>
                                                        </div>
                                                    </div>

                                                </th>
                                            @else
                                                <th style="text-align:center; vertical-align:middle ;font-size:11px">
                                                    BUKTI PENERIMAAN KAS/BANK <br> No. Rek ..................
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp No. Cek/Giro .................</th>

                                                <th style="text-align:center; vertical-align:middle;"> No.
                                                    {{ $nomor }} </th>
                                            @endif
                                            <th class="p-0 align-center">
                                                <table class="custom-table">
                                                    <tr>
                                                        <td><b>No. Bukti</b></td>
                                                        <td>: {!! $buktisppn !!}</td>
                                                    </tr>
                                                    @if (isset($sppb['sppb_metode_pembayaran']))
                                                        @if ($sppb['sppb_metode_pembayaran'] == 'bank')
                                                            <tr>
                                                                <td><b>Tgl. Posting B</b></td>
                                                                <td>: </td>
                                                            </tr>
                                                        @elseif ($sppb['sppb_metode_pembayaran'] == 'tidak_transfer')
                                                            <tr>
                                                                <td><b>Tgl. Posting K</b></td>
                                                                <td>: </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td><b>Tgl. Posting</b></td>
                                                                <td>: </td>
                                                            </tr>
                                                        @endif
                                                    @elseif (isset($sppn['sppn_metode_pembayaran']))
                                                        @if ($sppn['sppn_metode_pembayaran'] == 'bank')
                                                            <tr>
                                                                <td><b>Tgl. Posting B</b></td>
                                                                <td>: </td>
                                                            </tr>
                                                        @elseif ($sppn['sppn_metode_pembayaran'] == 'tidak_transfer')
                                                            <tr>
                                                                <td><b>Tgl. Posting K</b></td>
                                                                <td>: </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td><b>Tgl. Posting B</b></td>
                                                                <td>: </td>
                                                            </tr>
                                                        @endif
                                                    @else
                                                        <tr>
                                                            <td><b>Tgl. Posting B</b></td>
                                                            <td>: </td>
                                                        </tr>
                                                    @endif
                                                    {{-- <tr>
                                                        <td><b>No. Doc. SAP</b></td>
                                                        <td>: {{ $spp->spp_no_dokumen }}</td>
                                                    </tr> --}}

                                                    <tr>
                                                        <td><b>No. Doc. SAP</b></td>
                                                        <td>: </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Referensi</b></td>
                                                        <td>: {{ $sppn['sppn_no'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>GL Account</b></td>
                                                        <td>: {{ $databuktikassppn->master_gl_kode }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Deskripsi</b></td>
                                                        <td>: {{ $databuktikassppn->master_gl_keterangan }}</td>
                                                    </tr>
                                                </table>
                                            </th>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                                <table class="table table-bordered sppn my-auto ">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center"> Akun</th>
                                            <th style="text-align:center"> Vendor/Customer</th>
                                            <th style="text-align:center"> Deskripsi Akun </th>
                                            <th style="text-align:center"> Uraian </th>
                                            <th style="text-align:center"> Cost Center </th>
                                            <th style="text-align:center"> Profit Center </th>
                                            <th colspan="2" style="text-align:center"> Jumlah (Rp.) </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @dd($sppb_isi) --}}
                                        @php
                                            $count = count($sppn_isi);
                                        @endphp

                                        @if ($count <= 3)
                                            @foreach ($sppn_isi as $isi)
                                                <tr>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $isi->master_gl_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $nama_karyawan_sppn[0]->karyawan_nama }}</td>
                                                    <td style="text-align:left;color:black !important">
                                                        {{ $isi->master_gl_keterangan }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {!! $isi->sppn_uraian_uraian !!}

                                                    </td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $isi->master_cost_center_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $isi->master_profit_unit }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ number_format($isi->sppn_nominal_akhir) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                        @for($i = 0; $i < 3; $i++)
                                            <tr>
                                            <td style="text-align:center;color:black !important">
                                                        {{ $sppn_isi[$i]->master_gl_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $nama_karyawan_sppn[0]->karyawan_nama }}</td>
                                                    <td style="text-align:left;color:black !important">
                                                        {{ $sppn_isi[$i]->master_gl_keterangan }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {!! $sppn_isi[$i]->sppn_uraian_uraian !!}

                                                    </td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $sppn_isi[$i]->master_cost_center_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ $sppn_isi[$i]->master_profit_center_kode }}</td>
                                                    <td style="text-align:center;color:black !important">
                                                        {{ number_format($sppn_isi[$i]->sppn_nominal_akhir) }}</td>
                                                </tr>
                                            @endfor

                                            @php
                                                $jumlah_non_tiga_pn = 0;
                                                for ($j = 4; $j < $count; $j++) {
                                                    $jumlah_non_tiga_pn += $sppn_isi[$j]->sppn_nominal_akhir;
                                                }
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align:center">
                                                    Rincian Terlampir
                                                    <br><br>
                                                    Sesuai {{ $sppb['sppb_no'] }}
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td>{{ number_format($jumlah_non_tiga_pn) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="6" style="text-align:center;color:black !important">
                                                JUMLAH</td>
                                            <td style="text-align:center;color:black !important">
                                                {{ number_format($sppn['sppn_jumlah']) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <th colspan="" class="terbilang"> No. Cek/Giro : </th>
                                        <th colspan="6"><span
                                                style="color:black !important; text-align:left">{{ $databuktikassppn->cek_giro }}</span>
                                        </th>
                                    </tfoot>
                                    <tfoot>
                                        <th colspan="" class="terbilang"> Terbilang : </th>
                                        <th colspan="6"
                                            style="text-align:center; width:500px ; font-style:italic; font-weight:bold; color:black !important; text-transform:capitalize">
                                            {{ Terbilang::angka($sppn['sppn_jumlah']) }} rupiah </th>
                                    </tfoot>
                                </table>
                                <table class="table table-bordered sppn mb-0">
                                    @if($company_jenis == "REG")
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1" style="text-align:center">Dibuat</th>
                                            <th class="col-sm-4" style="text-align:center; vertical-align:middle"
                                                colspan=2>
                                                Diperiksa</th>
                                            <th class="col-sm-2" style="text-align:center; vertical-align:middle">
                                                Disetujui</th>
                                            <th class="col-sm-1" style="text-align:center; vertical-align:middle">
                                                Diterima</th>
                                            <!-- <th rowspan="2" style="text-align:center; vertical-align:middle"> Penyetor </th> -->
                                            <!-- <th colspan="2" style="text-align:center">Telah menerima jumlah tersebut diatas</th> -->
                                            <!-- <th colspan="2" style="text-align:center"> Dibukukan </th> -->

                                        </tr>
                                    </thead>
                                        <tbody style="text-align: center;">
                                            <td class="col-sm-1" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span>{{ $data_penandatangan_sppn->dibuat_sub_bagian }}</span>
                                            </td>
                                            <td class="col-sm-2" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span
                                                    style="text-decoration: underline;">{{ $data_penandatangan_sppn->diperiksa_oleh_sub_bagian_nama }}</span>
                                                <!-- <span class="text-underline"> Nugraha Akbar</span> -->
                                                <br>
                                                <span>{{ $data_penandatangan_sppn->diperiksa_oleh_sub_bagian }}</span>
                                                <!-- <span>Kasubdiv Perbendaharaan dan Anggaran</span> -->
                                            </td>
                                            <td class="col-sm-2" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span
                                                    style="text-decoration: underline;">{{ $data_penandatangan_sppn->diperiksa_oleh_bagian_nama }}</span>
                                                <br>
                                                <span>{{ $data_penandatangan_sppn->diperiksa_oleh_bagian }}</span>
                                            </td>
                                            <td class="col-sm-2" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span
                                                    style="text-decoration: underline;">{{ $data_penandatangan_sppn->disetujui_oleh_nama }}</span><br>
                                                <span>{{ $data_penandatangan_sppn->disetujui_oleh }}</span>
                                            </td>
                                            <td class="col-sm-1" style="height: 200px;"> </td>
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->

                                        </tbody>
                                    @else
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1" style="text-align:center;width: 25%;">Dibuat</th>
                                            <th class="col-sm-4" style="text-align:center; vertical-align:middle;width: 25%;">Diperiksa</th>
                                            <th class="col-sm-2" style="text-align:center; vertical-align:middle;width: 25%;">Disetujui</th>
                                            <th class="col-sm-1" style="text-align:center; vertical-align:middle;width: 25%;">Diterima</th>
                                            <!-- <th rowspan="2" style="text-align:center; vertical-align:middle"> Penyetor </th> -->
                                            <!-- <th colspan="2" style="text-align:center">Telah menerima jumlah tersebut diatas</th> -->
                                            <!-- <th colspan="2" style="text-align:center"> Dibukukan </th> -->

                                        </tr>
                                    </thead>
                                        <tbody style="text-align: center;">
                                            <td class="col-sm-1" style="height: 200px;">
                                                <br><br><br><br><br><br>
                                                <span>{{ $data_penandatangan_sppn->dibuat_sub_bagian }}</span>
                                            </td>
                                            <td class="col-sm-2" style="height: 200px;width:100px">
                                                <br><br><br><br><br><br>
                                                <span
                                                    style="text-decoration: underline;">{{ $data_penandatangan_sppn->diperiksa_oleh_sub_bagian_nama }}</span>
                                                <!-- <span class="text-underline"> Nugraha Akbar</span> -->
                                                <br>
                                                <span>{{ $data_penandatangan_sppn->diperiksa_oleh_sub_bagian }}</span>
                                                <!-- <span>Kasubdiv Perbendaharaan dan Anggaran</span> -->
                                            </td>
                                            <td class="col-sm-2" style="height: 200px; width:100px">
                                                <br><br><br><br><br><br>
                                                <span
                                                    style="text-decoration: underline;">{{ $data_penandatangan_sppn->disetujui_oleh_nama }}</span><br>
                                                <span>{{ $data_penandatangan_sppn->disetujui_oleh }}</span>
                                            </td>
                                            <td class="col-sm-1" style="height: 200px;"> </td>
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->
                                            <!-- <td style="height: 80px;"> </td> -->

                                        </tbody>
                                    @endif
                                    
                                    <tfoot>
                                    </tfoot>
                                </table>



                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                <h6 class="footer-sppn">PTPN GROUP</h6>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    var data = @json($data_penandatangan_sppb);
    console_log(data);
    $("#afterp").hide();
    $("#beforep").hide();
    if ({{ $formspp }} == 1) {
        $("#panel-penerimaan").hide();
    } else if ({{ $formspp }} == 2) {
        $("#panel-pengeluaran").hide();
    } else {
        $("#afterp").show();
        $("#beforep").show();

    }
</script>
<script src="{{ asset('') }}assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('') }}assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="{{ asset('') }}assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
<script src="{{ asset('') }}assets/vendor/chartist/js/chartist.min.js"></script>
<script src="{{ asset('') }}assets/vendor/mask/jquery.mask.min.js"></script>
<script src="{{ asset('') }}assets/scripts/klorofil-common.js"></script>

</html>
