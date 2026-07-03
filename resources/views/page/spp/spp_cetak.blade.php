<!DOCTYPE html>
<html>

<head>
    <title> SPP | Cetak SPP </title>
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
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('') }}assets/img/ptpn3.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('') }}assets/img/ptpn3.png">
    <!-- JAVASCRIPT -->
    <script src="{{ asset('') }}assets/vendor/jquery/jquery.min.js"></script>
    <style type="text/css">
        @media print {

            .col-sm-1,
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
                font-size: 12px;
            }

            .col-sm-12 {
                width: 100%;
                font-size: 12px;
            }

            .col-sm-11 {
                width: 91.66666667%;
                font-size: 12px;
            }

            .col-sm-10 {
                width: 83.33333333%;
                font-size: 12px;
            }

            .col-sm-9 {
                width: 75%;
                font-size: 12px;
            }

            .col-sm-8 {
                width: 66.66666667%;
                font-size: 12px;
            }

            .col-sm-7 {
                width: 58.33333333%;
                font-size: 12px;
            }

            .col-sm-6 {
                width: 50%;
                font-size: 12px;
            }

            .col-sm-5 {
                width: 41.66666667%;
                font-size: 12px;
            }

            .col-sm-4 {
                width: 33.33333333%;
                font-size: 12px;
            }

            .col-sm-3 {
                width: 25%;
                font-size: 12px;
            }

            .col-sm-2 {
                width: 16.66666667%;
                font-size: 12px;
            }

            .col-sm-1 {
                width: 8.33333333%;
                font-size: 12px;
            }

            .panel {
                transform-origin: top;
                transform: scale(1.077, 0.89);
                font-size: 12px;

            }

            table.table-bordered {
                border: 1px solid black !important;
                margin-top: 20px;
                font-size: 12px;
            }

            table.table-bordered>thead>tr>th {
                border: 1px solid black !important;
                font-size: 12px;
            }

            table.table-bordered>tbody>tr>td {
                border: 1px solid black !important;
                border-bottom-style: hidden !important;
                padding-left: 5px !important;
                padding-top: 3px !important;
                padding-bottom: 1px !important;
                font-size: 12px;
            }

            table.table-bordered>tbody>tr>th {
                border: 1px solid black !important;
                padding-left: 5px !important;
                padding-top: 3px !important;
                padding-bottom: 1px !important;
                font-size: 12px;
            }

            table.table-bordered>tfoot>tr>td {
                border: 1px solid black !important;
                font-size: 12px;
            }

            table.table-bordered>tfoot>tr>td.terbilang {
                border: 1px solid black !important;
                padding-left: 2px !important;
                border-right-style: hidden !important;
                font-size: 12px;
            }

            .logo {
                margin-top: 10px;
            }


        }
    </style>
    <?php
    $bagian_id = null;
    if (isset($sppb) && isset($sppb['master_bagian_id'])) {
        $bagian_id = $sppb['master_bagian_id'];
    } elseif (isset($sppn) && isset($sppn['master_bagian_id'])) {
        $bagian_id = $sppn['master_bagian_id'];
    }
    
    $perusahaan = Session::get('company');
    if ($bagian_id) {
        $bagian_db = DB::table('master_bagian')->where('master_bagian_id', $bagian_id)->first();
        if ($bagian_db && isset($bagian_db->company_id)) {
            $perusahaan = $bagian_db->company_id;
        }
    }
    ?>



</head>
<input type="hidden" id="formspp" value="{{ $formspp }}">

<body onload="window.print();">

    @php
        $regional = [
            151 => 1,
            157 => 2,
            166 => 3,
            175 => 4,
            178 => 5,
            190 => 7,
            196 => 8,
        ];
    @endphp
    {{-- 
    <div style="padding: 10px; background: #f5f5f5; border-bottom: 1px solid #ddd;" class="no-print">
        <a href="javascript:void(0)" onclick="generatePDF()" style="background: #d9534f; color: white; padding: 5px 15px; border-radius: 4px; text-decoration: none; font-weight: bold;">
            <i class="fa fa-file-pdf-o"></i> Download PDF (html2pdf)
        </a>
        <small style="margin-left: 10px; color: #666;">*Gunakan link ini jika hasil cetak biasa terpotong</small>
    </div>
    --}}
    <div class="wrapper">
        <div class="container">

            <div class="row" id="pdf">

                <div class="col-sm-13">

                    @if (isset($sppb))
                        <div class="panel" id="panel_sppb">
                            <div class="panel-body">

                                <table>

                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <h5 style="text-align:left; font-weight:bold;">
                                                            {{ $company }}
                                                        </h5>
                                                        @if(isset($company_jenis) && $company_jenis == 'UNIT')
                                                            <br>
                                                        @endif
                                                        Nomor : {{ $sppb['sppb_no'] }}
                                                        <br>
                                                        Tanggal : {{ date('d-m-Y', strtotime($sppb['sppb_tanggal'])) }}

                                                    </div>
                                                    <div class="col-sm-2"></div>
                                                    <div class="col-sm-2">
                                                        <img src="{{ asset('assets/img/ptpn1.png') }}" class="logo"
                                                            width="auto" height="60">
                                                    </div>
                                                </div>
                                                <!-- info row -->
                                                <div class="row invoice-info">
                                                    <div class="col-sm-7 invoice-col">

                                                    </div>
                                                    <!-- /.col -->

                                                    <!-- /.col -->
                                                    @if (in_array($sppb['master_bagian_id'], [151, 157, 166, 175, 178, 190, 196]))
                                                        <div class="col-sm-5 invoice-col">
                                                            <div style="text-align:left">
                                                                <b>Kepada Yth.</b>
                                                                <br>
                                                                @if ($sppb['master_bagian_id'] == 190)
                                                                    <b>Business Support Head Regional
                                                                        {{ $regional[$sppb['master_bagian_id']] }}</b>
                                                                @else
                                                                    <b>SEVP Business Support Regional
                                                                        {{ $regional[$sppb['master_bagian_id']] }}</b>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @elseif(isset($company_jenis) && $company_jenis == 'UNIT')
                                                        <div class="col-sm-5 invoice-col">
                                                            <div style="text-align:left">
                                                                <b>Kepada Yth.</b>
                                                                <br>
                                                                @php
                                                                    $parts = explode('-', $company);
                                                                    $company_tampil = count($parts) >= 3 ? trim($parts[2]) : (count($parts) == 2 ? trim($parts[1]) : trim($company));
                                                                    $company_tampil = ucfirst(strtolower($company_tampil));
                                                                @endphp
                                                                <b>Manajer Kebun {{ $company_tampil }}</b>
                                                            </div>
                                                        </div>
                                                    @elseif ($perusahaan == 5)
                                                        @if ($sppb['master_bagian_id'] != 126)
                                                            <div class="col-sm-5 invoice-col">
                                                                <div style="text-align:left">
                                                                    <b>Kepada Yth.</b>
                                                                    <br>
                                                                    <b>Kepala Divisi Perbendaharaan Anggaran dan
                                                                        Keuangan</b>
                                                                    <br>
                                                                    <b>PT Perkebunan Nusantara I</b>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="col-sm-5 invoice-col">
                                                                <div style="text-align:left">
                                                                    <b>Kepada Yth.</b>
                                                                    <br>
                                                                    <b>Direktur Keuangan dan Manajemen Risiko</b>
                                                                    <br>
                                                                    <b>PT Perkebunan Nusantara I</b>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="col-sm-5 invoice-col">
                                                            <div style="text-align:left">
                                                                <b>Kepada Yth.</b>
                                                                <br>
                                                                @foreach ($kotak_cetak as $kotak)
                                                                    <b> {{ $kotak->tujuan_kepada }}</b>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <!-- /.col -->
                                                </div>
                                                <!-- /.row -->
                                                <div class="row">
                                                    <center>
                                                        <br><br>
                                                        <h4 style="font-weight:bold; text-decoration:underline;">SURAT
                                                            PERMINTAAN
                                                            PEMBAYARAN (SPPb)</h4>
                                                        <br>
                                                    </center>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table class="table table-bordered table-responsive"
                                                    style="width:99.92%">
                                                    <thead>
                                                        <tr>
                                                            <th
                                                                style="vertical-align:middle; text-align:left; width:49.96%">

                                                                Nama Vendor/Karyawan
                                                                : {{ $sppb['sppb_kwitansi'] }}

                                                            </th>

                                                            <th style="vertical-align:middle; width:49.96%">
                                                                 Nomor Faktur Pajak
                                                                 &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                                 &nbsp; &nbsp;
                                                                 &nbsp; &nbsp; &nbsp;
                                                                 : {{ $sppb[1][0]->faktur_pajak_nomor ?? '-' }}
                                                                 <br>
                                                                 @if (isset($sppb[1]) && count($sppb[1]) > 1)
                                                                     @for ($i = 1; $i < count($sppb[1]); $i++)
                                                                         &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                                         &nbsp;
                                                                         &nbsp;
                                                                         &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
                                                                         &nbsp;
                                                                         &nbsp;
                                                                         &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                                         &nbsp;
                                                                         &nbsp;
                                                                         &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                                         &nbsp;
                                                                         &nbsp;
                                                                         &nbsp; &nbsp; &nbsp;
                                                                         : {{ $sppb[1][$i]->faktur_pajak_nomor }}
                                                                         <br>
                                                                     @endfor
                                                                 @endif
                                                                 Nomor SP/OPL/SPK/Perjanjian
                                                                 : {{ $sppb['sppb_sp_opl'] }}
                                                             </th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                                Dengan ini dimohon bantuannya untuk dibayarkan tagihan sebagai
                                                berikut :
                                            </td>
                                        </tr>
                                        <tr style="page-break-inside: auto">
                                            <td>
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <table class="table table-bordered" style="width:99.92%">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="4" style="text-align:center"> KODE
                                                                    </th>
                                                                    <th rowspan="2"
                                                                        style="text-align:center; vertical-align:middle">
                                                                        URAIAN </th>
                                                                    <th rowspan="3"
                                                                        style="text-align:center; vertical-align:middle">
                                                                        Jumlah <br> Rp.</th>
                                                                </tr>

                                                                <tr>

                                                                    <th style="text-align:center">SAP</th>
                                                                    <th style="text-align:center">CC/PC</th>
                                                                    <th style="text-align:center">CF</th>
                                                                    <th style="text-align:center">RF Key</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody style="height:200px;">
                                                                 @if (isset($sppb[0]))
                                                                     @php
                                                                         $sppb_calculated_total = 0;
                                                                         for ($i = 0; $i < count($sppb[0]); $i++) {
                                                                             if (isset($sppb[0][$i][0])) {
                                                                                 for ($a = 0; $a < count($sppb[0][$i][0]); $a++) {
                                                                                     $sppb_calculated_total += $sppb[0][$i][0][$a]->sppb_nominal_akhir;
                                                                                 }
                                                                             }
                                                                         }
                                                                     @endphp
                                                                     @for ($i = 0; $i < count($sppb[0]); $i++)
                                                                        <tr style="page-break-inside:auto">

                                                                            {{-- @if ($sppb[0][$i]['master_gl_id'] == null)
                                                                                <td style="text-align:center">
                                                                                    {{ $sppb[0][$i]['master_rekening_kode_sap'] }}
                                                                                </td>
                                                                            @else
                                                                                <td style="text-align:center">
                                                                                    {{ $sppb[0][$i]['master_gl_kode'] }}
                                                                                </td>
                                                                            @endif --}}
                                                                            @if ($sppb[0][$i]['master_customer_id'] != null)
                                                                                <td style="text-align:center">
                                                                                    {{ $sppb[0][$i]['master_customer_kode_sap'] }}
                                                                                </td>
                                                                            @elseif ($sppb[0][$i]['master_gl_id'] != null)
                                                                                <td style="text-align:center">
                                                                                    {{ $sppb[0][$i]['master_gl_kode'] }}
                                                                                </td>
                                                                            @else
                                                                                <td style="text-align:center">
                                                                                    {{ $sppb[0][$i]['master_rekening_kode_sap'] }}
                                                                                </td>
                                                                            @endif

                                                                            <td style="text-align:center">
                                                                                {{ $sppb[0][$i]['master_cost_center_kode'] }}{{ $sppb[0][$i]['master_profit_center_kode'] }}
                                                                            </td>
                                                                            <td style="text-align:center">
                                                                                {{ $sppb[0][$i]['master_cash_flow_kode'] }}
                                                                            </td>
                                                                            <td style="text-align:center">
                                                                                {{ $sppb[0][$i]['master_cash_flow_key'] }}
                                                                            </td>
                                                                            <td><?php $j = str_replace('</p>', '</n>', $sppb[0][$i][0][0]->sppb_uraian_uraian); ?>
                                                                                {!! $j !!} <br> <br>
                                                                                <b> Nominal </b> <br>
                                                                                Nominal DPP :
                                                                                {{ number_format($sppb[0][$i][0][0]->sppb_uraian_nominal) }}
                                                                                <br>
                                                                                @if ($sppb[0][$i][0][0]->sppb_tanpa_pajak == null)
                                                                                    Nominal PPh :
                                                                                    {{ number_format($sppb[0][$i][0][0]->sppb_uraian_pph) }}
                                                                                    <br>
                                                                                    Nominal PPn :
                                                                                    {{ number_format($sppb[0][$i][0][0]->sppb_nominal_pajak) }}
                                                                                    <br>
                                                                                    Jumlah DPP + PPN :
                                                                                    {{ number_format($sppb[0][$i][0][0]->sppb_dpp_ppn) }}
                                                                                    <br>
                                                                                    Jumlah Potongan :
                                                                                    {{ number_format($sppb[0][$i][0][0]->sppb_potongan) }}
                                                                                    <br>
                                                                                @else
                                                                                @endif
                                                                                <br>

                                                                            </td>

                                                                            <td style="text-align:right">
                                                                                {{ number_format($sppb[0][$i][0][0]->sppb_nominal_akhir) }}
                                                                            </td>
                                                                        </tr>
                                                                        @if (count($sppb[0][$i][0]) > 1)
                                                                            <tr style="page-break-inside: auto">
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td>
                                                                                    @for ($a = 1; $a < count($sppb[0][$i][0]); $a++)
                                                                                        <?php $b = str_replace('</p>', '</n>', $sppb[0][$i][0][$a]->sppb_uraian_uraian); ?>
                                                                                        {!! $b !!} <br>
                                                                                        <br>
                                                                                        <b> Nominal </b> <br>
                                                                                        Nominal DPP :
                                                                                        {{ number_format($sppb[0][$i][0][$a]->sppb_uraian_nominal) }}
                                                                                        <br>
                                                                                        @if ($sppb[0][$i][0][$a]->sppb_tanpa_pajak == null)
                                                                                            Nominal PPh :
                                                                                            {{ number_format($sppb[0][$i][0][$a]->sppb_uraian_pph) }}
                                                                                            <br>
                                                                                            Nominal PPn :
                                                                                            {{ number_format($sppb[0][$i][0][$a]->sppb_nominal_pajak) }}
                                                                                            <br>
                                                                                            Jumlah DPP + PPN :
                                                                                            {{ number_format($sppb[0][$i][0][$a]->sppb_dpp_ppn) }}
                                                                                            <br>
                                                                                            Jumlah Potongan :
                                                                                            {{ number_format($sppb[0][$i][0][$a]->sppb_potongan) }}
                                                                                            <br>
                                                                                        @else
                                                                                        @endif
                                                                                    @endfor

                                                                                </td>
                                                                                <td style="text-align:right">
                                                                                    @for ($a = 1; $a < count($sppb[0][$i][0]); $a++)
                                                                                        {{ number_format($sppb[0][$i][0][$a]->sppb_nominal_akhir) }}</br>
                                                                                    @endfor
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @endfor
                                                                @endif
                                                                <tr>

                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>

                                                                    @if ($sppb['sppb_catatan'] !== null)
                                                                        <br>
                                                                        <td><b> Catatan : </b> <br>
                                                                            {{ $sppb['sppb_catatan'] }} </td>
                                                                    @else
                                                                        <td></td>
                                                                    @endif
                                                                    <td></td>
                                                                </tr>
                                                                <tr>

                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>

                                                                    @if ($sppb['sppb_metode_pembayaran'] == 'kas')
                                                                        <th><b> Dibayarkan dengan cash <br>
                                                                                @if ($sppb['sppb_jenis'] == 'karyawan')
                                                                                    @foreach ($karyawan_sppb as $k)
                                                                                        a/n. :
                                                                                        {{ $k->karyawan_nama }}<br>
                                                                                    @endforeach

                                                                                @endif
                                                                        </th>
                                                                    @elseif($sppb['sppb_metode_pembayaran'] == 'bank')
                                                                        <th>
                                                                            @if ($sppb['sppb_jenis'] == 'karyawan')
                                                                                @foreach ($karyawan_sppb as $k)
                                                                                    {{ $k->karyawan_nama_bank }} <br>
                                                                                    A/C : {{ $k->karyawan_no_rek }}
                                                                                    <br>
                                                                                    a/n. : {{ $k->karyawan_nama }} <br>
                                                                                @endforeach
                                                                            @else
                                                                                @if ($sppb['sppb_data_metpen'] == 'input_data')
                                                                                    {{ $karyawan_sppb[0]->karyawan_nama_bank }}
                                                                                    <br>
                                                                                    A/C :
                                                                                    {{ $karyawan_sppb[0]->karyawan_no_rek }}
                                                                                    <br>
                                                                                    a/n. :
                                                                                    {{ $karyawan_sppb[0]->karyawan_nama }}
                                                                                    <br>
                                                                                @elseif($sppb['sppb_data_metpen'] == 'lampirkan_data')
                                                                                    TERLAMPIR <br>
                                                                                    A/C : TERLAMPIR <br>
                                                                                    a/n. : TERLAMPIR <br>
                                                                                @else
                                                                                    {{ $sppb['master_vendor_nama_bank'] }}
                                                                                    <br>
                                                                                    A/C :
                                                                                    {{ $sppb['master_vendor_rekening'] }}
                                                                                    <br>
                                                                                    a/n. :
                                                                                    {{ $sppb['master_vendor_nama'] }}
                                                                                    <br>
                                                                                @endif
                                                                            @endif
                                                                        </th>
                                                                    @elseif($sppb['sppb_metode_pembayaran'] == 'skbdn')
                                                                        <th></th>
                                                                    @else
                                                                        <th></th>
                                                                    @endif
                                                                    <th></th>
                                                                </tr>
                                                                <tr
                                                                     style="border-bottom-style: solid !important; border-bottom-width: 1px">
                                                                     <td style="text-align:left;border-bottom-style: solid !important;"
                                                                         class="terbilang">
                                                                         <b>TERBILANG:
                                                                     </td>
                                                                     <td colspan="4"
                                                                         style="text-transform:uppercase; text-align:center;border-bottom-style: solid !important;">
                                                                         <b style="font-style:italic">
                                                                             {{ Terbilang::angka($sppb_calculated_total) }}
                                                                             rupiah </b>
                                                                     </td>
                                                                     <td
                                                                         style="text-align:right;border-bottom-style: solid !important;">
                                                                         <b>{{ number_format($sppb_calculated_total) }}
                                                                     </td>
                                                                 </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <br>
                                            </td>
                                        </tr>
                                        <tr style="page-break-inside: auto;">
                                            <td>
                                                <!-- Table row -->
                                                <div class="" style="">
                                                    <div class="col-sm-12">
                                                        Dokumen-dokumen syarat pembayaran kami lampirkan dan kami
                                                        bertanggung jawab
                                                        atas kebenarannya
                                                    </div>
                                                    <div class="col-sm-6">

                                                        <br><br>
                                                        {!! QrCode::size(50)->generate('https://superman.ptpn1.co.id/spp/validasi/' . $id) !!}

                                                    </div>
                                                    <div class="col-sm-6">
                                                        <br>
                                                        <p style="text-align:center;">
                                                            {{ $sppb['master_bagian_nama'] }}
                                                        </p>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <p style="text-align:center; text-decoration:underline;">
                                                            {{ $sppb['master_bagian_kepala_bagian'] }}</p>
                                                    </div>
                                                    &nbsp;
                                                    <div class="row" style="page-break-inside: avoid;">
                                                        @if($company_jenis != "UNIT")
                                                        @if ($flowid == 25)
                                                            <div class="col-sm-4">
                                                                <table style="border-collapse: collapse; width: 100%;">

                                                                </table>
                                                            </div>
                                                        
                                                        @else
                                                        <div class="col-sm-4">
                                                                <table style="border-collapse: collapse; width: 100%;">
                                                                    <tr>
                                                                        <th
                                                                            style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                            Diperiksa Oleh : </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th
                                                                            style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                            @foreach ($kotak_cetak as $kotak)
                                                                                {{ $kotak->diperiksa_oleh_1 }}
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                            @endforeach
                                                                        </th>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            @endif
                                                        <div class="col-sm-8">
                                                            <table style="border-collapse: collapse; width: 100%;">
                                                                <th
                                                                    style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center; width: 68.70%;">
                                                                    Diperiksa Oleh : </th>
                                                                <th
                                                                    style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                    Disetujui Oleh : </th>
                                                            </table>
                                                            @foreach ($kotak_cetak as $kotak)
                                                                <table style="border-collapse: collapse; width: 100%;">
                                                                    <th
                                                                        style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                        {{ $kotak->diperiksa_oleh_2 }}
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                    </th>
                                                                    <th
                                                                        style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                        {{ $kotak->diperiksa_oleh_3 }}
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                    </th>
                                                                    <th
                                                                        style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                        {{ $kotak->disetujui_oleh }}
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                        <br>
                                                                    </th>
                                                                </table>
                                                            @endforeach
                                                        </div>
                                                        @elseif($company_jenis == "UNIT")
                                                        <table class="table table-bordered" style="width:50%; float:right;text-align: center;">
                                                            <thead>
                                                                <tr>
                                                                <th class="title-th" colspan="3" style="text-align: center;">Paraf dan Tanggal Pengecekan Berkas</th>
                                                                </tr>

                                                                <!-- Baris header kolom -->
                                                                <tr>
                                                                <th class="col-coa" style="text-align: center;">COA / MIRO</th>
                                                                <th class="col-pajak" style="text-align: center;">Pajak</th>
                                                                <th class="col-verif" style="text-align: center;">Verifikasi</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <!-- Baris kosong untuk isi/paraf -->
                                                                <tr>
                                                                    <td class="signature-cell"><br><br><br></td>
                                                                    <td class="signature-cell"><br><br><br></td>
                                                                    <td class="signature-cell"><br><br><br></td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                        @else
                                                            <div class="col-sm-4">
                                                                <table style="border-collapse: collapse; width: 100%;">
                                                                    <tr>
                                                                        <th
                                                                            style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                            Diperiksa Oleh : </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th
                                                                            style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                            @foreach ($kotak_cetak as $kotak)
                                                                                {{ $kotak->diperiksa_oleh_1 }}
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                            @endforeach
                                                                        </th>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if (isset($sppb) && isset($sppn))
                        <p style="page-break-after: always;">&nbsp;</p>
                        <p style="page-break-before: always;">&nbsp;</p>
                    @endif

                    @if (isset($sppn))
                        <div class="panel" id="panel_sppn">
                            <div class="panel-body">
                                <!-- title row -->

                                <div class="row">
                                    <div class="col-sm-8">
                                        <h5 style="text-align:left; font-weight:bold;">
                                            {{ $company }}
                                        </h5>
                                        @if(isset($company_jenis) && $company_jenis == 'UNIT')
                                            <br>
                                        @endif
                                        Nomor : {{ $sppn['sppn_no'] }}
                                        <br>
                                        Tanggal : {{ date('d-m-Y', strtotime($sppn['sppn_tanggal'])) }}

                                    </div>
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-2">
                                        <img src="{{ asset('assets/img/ptpn1.png') }}" class="logo" width="auto"
                                            height="60">
                                    </div>
                                </div>

                                <!-- info row -->
                                <div class="row invoice-info">
                                    <div class="col-sm-7 invoice-col">

                                    </div>
                                    <!-- /.col -->

                                    <!-- /.col -->
                                    @if (in_array($sppn['master_bagian_id'], [151, 157, 166, 175, 178, 190, 196]))
                                        <div class="col-sm-5 invoice-col">
                                            <div style="text-align:left">
                                                <b>Kepada Yth.</b>
                                                <br>
                                                @if ($sppn['master_bagian_id'] == 190)
                                                    <b>Business Support Head
                                                        {{ $regional[$sppn['master_bagian_id']] }}</b>
                                                @else
                                                    <b>SEVP Business Support Regional
                                                        {{ $regional[$sppn['master_bagian_id']] }}</b>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(isset($company_jenis) && $company_jenis == 'UNIT')
                                        <div class="col-sm-5 invoice-col">
                                            <div style="text-align:left">
                                                <b>Kepada Yth.</b>
                                                <br>
                                                @php
                                                    $parts = explode('-', $company);
                                                    $company_tampil = count($parts) >= 3 ? trim($parts[2]) : (count($parts) == 2 ? trim($parts[1]) : trim($company));
                                                    $company_tampil = ucfirst(strtolower($company_tampil));
                                                @endphp
                                                <b>Manajer {{ $company_tampil }}</b>
                                            </div>
                                        </div>
                                    @elseif ($perusahaan == 5)
                                        @if ($sppn['master_bagian_id'] != 126)
                                            <div class="col-sm-5 invoice-col">
                                                <div style="text-align:left">
                                                    <b>Kepada Yth.</b>
                                                    <br>
                                                    <b>Kepala Divisi Perbendaharaan Anggaran dan Keuangan</b>
                                                    <br>
                                                    <b>PT Perkebunan Nusantara I</b>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-sm-5 invoice-col">
                                                <div style="text-align:left">
                                                    <b>Kepada Yth.</b>
                                                    <br>
                                                    <b>Direktur Keuangan dan Manajemen Risiko</b>
                                                    <br>
                                                    <b>PT Perkebunan Nusantara I</b>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="col-sm-5 invoice-col">
                                            <div style="text-align:left">
                                                <b>Kepada Yth.</b>
                                                <br>
                                                @foreach ($kotak_cetak as $kotak)
                                                    <b> {{ $kotak->tujuan_kepada }}</b>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                                <div class="row">
                                    <center>
                                        <br><br>
                                        <h4 style="font-weight:bold; text-decoration:underline;">SURAT PERMINTAAN
                                            PENERIMAAN (SPPn)</h4>
                                        <br>
                                    </center>
                                </div>
                                <!-- Table row -->
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        Dengan ini dimohon bantuannya untuk dibayarkan tagihan sebagai berikut :

                                        <table class="table table-bordered" style="width:99.92%">
                                            <thead>
                                                <tr>
                                                    <th style="vertical-align:middle; text-align:left; width:49.96%">
                                                        Kwitansi dari &nbsp;: {{ $sppn['sppn_kwitansi'] }}
                                                        <br>
                                                        BA / AU 58 No : {{ $sppn['sppn_ba_au_53'] }}
                                                    </th>
                                                    <th style="vertical-align:middle; text-align:left; width:49.96%">
                                                         Nomor Faktur Pajak : {{ $sppn[1][0]->faktur_pajak_nomor ?? '-' }}
                                                         <br>
                                                         @if (isset($sppn[1]) && count($sppn[1]) > 1)
                                                             @for ($i = 1; $i < count($sppn[1]); $i++)
                                                                 &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                                 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
                                                                 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                                 : {{ $sppn[1][$i]->faktur_pajak_nomor }}
                                                                 <br>
                                                             @endfor
                                                         @endif
                                                         @if (isset($sppb['sppb_no']))
                                                             Nomor SPPb &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                             &nbsp;: {{ $sppb['sppb_no'] }}
                                                         @else
                                                             Nomor SPPb &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                             &nbsp;: -
                                                         @endif
                                                     </th>
                                                    </th>

                                                </tr>
                                            </thead>
                                        </table>
                                        <table class="table table-bordered" style="width:99.92%">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align:center"> KODE </th>
                                                    <th rowspan="2"
                                                        style="text-align:center; vertical-align:middle">
                                                        URAIAN </th>
                                                    <th rowspan="3"
                                                        style="text-align:center; vertical-align:middle">
                                                        Jumlah <br>
                                                        Rp.</th>
                                                </tr>
                                                <tr>
                                                    <!-- <th style="text-align:center">KBB</th> -->
                                                    <th style="text-align:center">SAP</th>
                                                    <th style="text-align:center">CC/PC</th>
                                                    <th style="text-align:center">CF</th>
                                                    <th style="text-align:center">RF Key</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @if (isset($sppn[0]))
                                                     @php
                                                         $sppn_calculated_total = 0;
                                                         for ($i = 0; $i < count($sppn[0]); $i++) {
                                                             if (isset($sppn[0][$i][0])) {
                                                                 for ($a = 0; $a < count($sppn[0][$i][0]); $a++) {
                                                                     $sppn_calculated_total += $sppn[0][$i][0][$a]->sppn_nominal_akhir;
                                                                 }
                                                             }
                                                         }
                                                     @endphp
                                                     @for ($i = 0; $i < count($sppn[0]); $i++)
                                                        <tr style="position:relative;padding:0px;">
                                                            <!-- <td style="text-align:center">{{ $sppn[0][$i]['master_kode_kbb'] }}</td> -->
                                                            {{-- @if ($sppn[0][$i]['master_gl_id'] == null)
                                                                <td style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_rekening_kode_sap'] }}</td>
                                                            @else
                                                                <td style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_gl_kode'] }}</td>
                                                            @endif --}}
                                                            @if ($sppn[0][$i]['master_customer_id'] != null)
                                                                <td style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_customer_kode_sap'] }}
                                                                </td>
                                                            @elseif ($sppn[0][$i]['master_gl_id'] != null)
                                                                <td style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_gl_kode'] }}
                                                                </td>
                                                            @else
                                                                <td style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_rekening_kode_sap'] }}
                                                                </td>
                                                            @endif
                                                            <td style="text-align:center">
                                                                {{ $sppn[0][$i]['master_cost_center_kode'] }}{{ $sppn[0][$i]['master_profit_center_kode'] }}
                                                            </td>
                                                            <td style="text-align:center">
                                                                {{ $sppn[0][$i]['master_cash_flow_kode'] }}</td>
                                                            <td style="text-align:center">
                                                                {{ $sppn[0][$i]['master_cash_flow_key'] }}</td>
                                                            <td>
                                                                <?php $b = str_replace('</p>', '</n>', $sppn[0][$i][0][0]->sppn_uraian_uraian); ?>
                                                                {!! $b !!} <br><br>
                                                                <b> Nominal </b> <br>
                                                                Nominal DPP :
                                                                {{ number_format($sppn[0][$i][0][0]->sppn_uraian_nominal) }}
                                                                <br>
                                                                @if ($sppn[0][$i][0][0]->sppn_tanpa_pajak == null)
                                                                    Nominal PPh :
                                                                    {{ number_format($sppn[0][$i][0][0]->sppn_uraian_pph) }}
                                                                    <br>
                                                                    Nominal PPn :
                                                                    {{ number_format($sppn[0][$i][0][0]->sppn_nominal_pajak) }}
                                                                    <br>
                                                                    Jumlah DPP + PPN :
                                                                    {{ number_format($sppn[0][$i][0][0]->sppn_dpp_ppn) }}
                                                                    <br>
                                                                    Jumlah Potongan :
                                                                    {{ number_format($sppn[0][$i][0][0]->sppn_potongan) }}
                                                                    <br>
                                                                @else
                                                                @endif
                                                                <br>
                                                            </td>
                                                            <td style="text-align:right">
                                                                {{ number_format($sppn[0][$i][0][0]->sppn_nominal_akhir) }}</br>
                                                            </td>
                                                        </tr>
                                                        @if (count($sppn[0][$i][0]) > 1)
                                                            <tr>
                                                                <!-- <td></td> -->
                                                                <td></td>
                                                                <td></td>
                                                                <th></th>
                                                                <th></th>
                                                                <!-- <td></td> -->
                                                                <td>
                                                                    @for ($a = 1; $a < count($sppn[0][$i][0]); $a++)
                                                                        <?php $b = str_replace('</p>', '</n>', $sppn[0][$i][0][$a]->sppn_uraian_uraian); ?> {!! $b !!} <br>
                                                                        <br>
                                                                        <b> Nominal </b> <br>
                                                                        Nominal DPP :
                                                                        {{ number_format($sppn[0][$i][0][$a]->sppn_uraian_nominal) }}
                                                                        <br>
                                                                        @if ($sppn[0][$i][0][$a]->sppn_tanpa_pajak == null)
                                                                            Nominal PPh :
                                                                            {{ number_format($sppn[0][$i][0][$a]->sppn_uraian_pph) }}
                                                                            <br>
                                                                            Nominal PPn :
                                                                            {{ number_format($sppn[0][$i][0][$a]->sppn_nominal_pajak) }}
                                                                            <br>
                                                                            Jumlah DPP + PPN :
                                                                            {{ number_format($sppn[0][$i][0][$a]->sppn_dpp_ppn) }}
                                                                            <br>
                                                                            Jumlah Potongan :
                                                                            {{ number_format($sppn[0][$i][0][$a]->sppn_potongan) }}
                                                                            <br>
                                                                        @else
                                                                        @endif
                                                                    @endfor
                                                                </td>
                                                                <td style="text-align:right">
                                                                    @for ($a = 1; $a < count($sppn[0][$i][0]); $a++)
                                                                        {{ number_format($sppn[0][$i][0][$a]->sppn_nominal_akhir) }}</br>
                                                                    @endfor
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endfor
                                                @endif
                                                <tr>
                                                    <!-- <td></td> -->
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <!-- <td></td> -->
                                                    @if ($sppn['sppn_catatan'] !== null)
                                                        <br>
                                                        <td><b> Catatan : </b> <br> {{ $sppn['sppn_catatan'] }} </td>
                                                    @else
                                                        <td></td>
                                                    @endif
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                 <tr>
                                                     <td style="text-align:left;" class="terbilang"><b>TERBILANG:</td>
                                                     <td colspan="4"
                                                         style="text-transform:uppercase; text-align:center"> <b
                                                             style="font-style:italic">
                                                             {{ Terbilang::angka($sppn_calculated_total) }} rupiah </b>
                                                     </td>
                                                     <td style="text-align:right;">
                                                         <b>{{ number_format($sppn_calculated_total) }}
                                                     </td>
                                                 </tr>
                                            </tfoot>
                                        </table>
                                        Dokumen-dokumen syarat pembayaran kami lampirkan dan kami bertanggung jawab atas
                                        kebenarannya
                                    </div>

                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                                <div class="col-sm-6">
                                    <br><br>
                                    {!! QrCode::size(50)->generate('https://superman.ptpn1.co.id/spp/validasi/' . $id) !!}

                                </div>
                                <div class="col-sm-6">
                                    <br>
                                    <p style="text-align:center;">{{ $sppn['master_bagian_nama'] }} </p>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <p style="text-align:center; text-decoration:underline;">
                                        {{ $sppn['master_bagian_kepala_bagian'] }}</p>

                                </div>
                                &nbsp;

                                <div class="row">
                                    @if($company_jenis != "UNIT")
                                                        @if ($flowid == 25)
                                                            <div class="col-sm-4">
                                                                <table style="border-collapse: collapse; width: 100%;">

                                                                </table>
                                                            </div>
                                                        
                                                        @else
                                                        <div class="col-sm-4">
                                                                <table style="border-collapse: collapse; width: 100%;">
                                                                    <tr>
                                                                        <th
                                                                            style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                            Diperiksa Oleh : </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th
                                                                            style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                                            @foreach ($kotak_cetak as $kotak)
                                                                                {{ $kotak->diperiksa_oleh_1 }}
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                                <br>
                                                                            @endforeach
                                                                        </th>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            @endif
                                    <div class="col-sm-8">
                                        <table style="border-collapse: collapse; width: 100%;">
                                            <th
                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center; width: 68.70%;">
                                                Diperiksa Oleh : </th>
                                            <th
                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                Disetujui Oleh : </th>
                                        </table>
                                        @foreach ($kotak_cetak as $kotak)
                                            <table style="border-collapse: collapse; width: 100%;">
                                                <th
                                                    style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">

                                                    {{ $kotak->diperiksa_oleh_2 }}
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                </th>
                                                <th
                                                    style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                    {{ $kotak->diperiksa_oleh_3 }}
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                </th>
                                                <th
                                                    style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                    {{ $kotak->disetujui_oleh }}
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                </th>
                                            </table>
                                        @endforeach
                                    </div>
                                    @elseif($company_jenis == "UNIT")
                                    <table class="table table-bordered" style="width:50%; margin-left:350px;">
                                        <thead>
                                            <tr>
                                            <th class="title-th" colspan="3" style="text-align: center;">Paraf dan Tanggal Pengecekan Berkas</th>
                                            </tr>

                                            <!-- Baris header kolom -->
                                            <tr>
                                            <th class="col-coa" style="text-align: center;">COA / MIRO</th>
                                            <th class="col-pajak" style="text-align: center;">Pajak</th>
                                            <th class="col-verif" style="text-align: center;">Verifikasi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <!-- Baris kosong untuk isi/paraf -->
                                            <tr>
                                                <td class="signature-cell"><br><br><br></td>
                                                <td class="signature-cell"><br><br><br></td>
                                                <td class="signature-cell"><br><br><br></td>
                                            </tr>
                                        </tbody>
                                        </table>
                                    @else
                                        <div class="col-sm-4">
                                            <table style="border-collapse: collapse; width: 100%;">
                                                <tr>
                                                    <th
                                                        style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                        Diperiksa Oleh : </th>
                                                </tr>
                                                <tr>
                                                    <th
                                                        style="border: 1px solid #dddddd; text-align: left; padding: 8px; text-align: center;">
                                                        @foreach ($kotak_cetak as $kotak)
                                                            {{ $kotak->diperiksa_oleh_1 }}
                                                            <br>
                                                            <br>
                                                            <br>
                                                            <br>
                                                            <br>
                                                            <br>
                                                        @endforeach
                                                    </th>
                                                </tr>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
    integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    function generatePDF() {
        const element = document.getElementById('pdf');
        const options = {
            margin: [10, 10, 10, 10], // top, left, buttom, right
            filename: 'SPP-{{ isset($sppb) ? $sppb['sppb_no'] : (isset($sppn) ? $sppn['sppn_no'] : "Document") }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2, 
                useCORS: true,
                logging: false,
                letterRendering: true
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait' 
            },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Tambahkan class khusus saat proses generate jika diperlukan
        // element.classList.add('generating-pdf');

        html2pdf().set(options).from(element).save().then(() => {
            // element.classList.remove('generating-pdf');
        });
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
