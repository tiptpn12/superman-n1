<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak SPP - Preview Presisi</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            font-family: 'Source Sans Pro', 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .no-print {
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .btn-pdf {
            background: #d9534f;
            color: white;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        #pdf-container {
            width: 18cm;
            margin: 0 auto;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .company-name {
            font-weight: bold;
            font-size: 13px;
            text-align: left;
        }

        .logo-cell {
            text-align: right;
        }

        .logo-img {
            height: 55px;
        }

        /* RECIPIENT */
        .recipient-outer {
            width: 100%;
            margin-bottom: 20px;
        }

        .recipient-box {
            float: right;
            width: 40%;
            font-size: 11px;
        }

        /* TITLE */
        .document-title {
            text-align: center;
            margin: 30px 0 20px 0;
            clear: both;
        }

        .document-title h4 {
            margin: 0;
            text-decoration: underline;
            font-weight: bold;
            font-size: 15px;
            text-transform: uppercase;
        }

        /* REFERENCE BOX */
        .ref-table {
            width: 100%;
            border: 1.5px solid #000;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .ref-table td {
            border: 0.5px solid #000;
            padding: 8px;
            vertical-align: top;
            width: 50%;
        }

        /* MAIN TABLE */
        .main-table {
            width: 100%;
            border: 1.5px solid #000;
            border-collapse: collapse;
        }

        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .main-table thead th {
            text-align: center;
            font-weight: bold;
        }

        .col-code { text-align: center; font-size: 10px; }
        .col-amount { text-align: right; font-weight: bold; }

        .uraian-content {
            min-height: 80px;
        }

        .label-bold {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        /* TERBILANG */
        .terbilang-row td {
            padding: 8px;
            font-weight: bold;
        }

        .terbilang-text {
            font-style: italic;
            text-transform: uppercase;
            text-align: center;
        }

        /* FOOTER */
        .footer-note {
            font-size: 11px;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .footer-signatures {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .paraf-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            page-break-inside: avoid;
        }

        .paraf-table th, .paraf-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        {{-- Tombol Download PDF (html2pdf.js) disembunyikan karena library tidak mampu handle page-break pada tabel panjang --}}
        {{-- <button onclick="generatePDF()" class="btn-pdf">DOWNLOAD PDF (HASIL PRESISI)</button> --}}
        <button onclick="window.print()" class="btn-pdf" style="background: #337ab7;">CETAK / SIMPAN PDF (Ctrl+P)</button>
    </div>

    @php
        $regional = [151 => 1, 157 => 2, 166 => 3, 175 => 4, 178 => 5, 190 => 7, 196 => 8];
        
        $bagian_id = null;
        if (isset($sppb) && isset($sppb['master_bagian_id'])) {
            $bagian_id = $sppb['master_bagian_id'];
        } elseif (isset($sppn) && isset($sppn['master_bagian_id'])) {
            $bagian_id = $sppn['master_bagian_id'];
        }
        
        $perusahaan_sess = Session::get('company');
        if ($bagian_id) {
            $bagian_db = DB::table('master_bagian')->where('master_bagian_id', $bagian_id)->first();
            if ($bagian_db && isset($bagian_db->company_id)) {
                $perusahaan_sess = $bagian_db->company_id;
            }
        }
    @endphp

    <div id="pdf-container">
        
        {{-- SECTION SPPb --}}
        @if (isset($sppb))
            <div class="spp-wrapper">
                <table class="header-table">
                    <tr>
                        <td class="company-name">
                            {{ $company }}<br>
                            <span style="font-weight: normal; font-size: 11px;">
                                Nomor : {{ $sppb['sppb_no'] }}<br>
                                Tanggal : {{ date('d-m-Y', strtotime($sppb['sppb_tanggal'])) }}
                            </span>
                        </td>
                        <td class="logo-cell">
                            <img src="{{ asset('assets/img/ptpn1.png') }}" class="logo-img">
                        </td>
                    </tr>
                </table>

                <div class="recipient-outer clearfix">
                    <div class="recipient-box">
                        <strong>Kepada Yth.</strong><br>
                        @if (in_array($sppb['master_bagian_id'], [151, 157, 166, 175, 178, 190, 196]))
                            @if ($sppb['master_bagian_id'] == 190)
                                Business Support Head Regional {{ $regional[$sppb['master_bagian_id']] }}
                            @else
                                SEVP Business Support Regional {{ $regional[$sppb['master_bagian_id']] }}
                            @endif
                        @elseif(isset($company_jenis) && $company_jenis == 'UNIT')
                            @php
                                $parts = explode('-', $company);
                                $company_tampil = count($parts) >= 3 ? trim($parts[2]) : (count($parts) == 2 ? trim($parts[1]) : trim($company));
                                $company_tampil = ucfirst(strtolower($company_tampil));
                            @endphp
                            Manajer Kebun {{ $company_tampil }}
                        @elseif ($perusahaan_sess == 5)
                            @if ($sppb['master_bagian_id'] != 126)
                                Kepala Divisi Perbendaharaan Anggaran dan Keuangan<br>
                            @else
                                Direktur Keuangan dan Manajemen Risiko<br>
                            @endif
                            PT Perkebunan Nusantara I
                        @else
                            @foreach ($kotak_cetak as $kotak)
                                {{ $kotak->tujuan_kepada }}
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="document-title">
                    <h4>SURAT PERMINTAAN PEMBAYARAN (SPPb)</h4>
                </div>

                <span style="display:block; margin-bottom: 8px;">Dengan ini dimohon bantuannya untuk dibayarkan tagihan sebagai berikut :</span>

                <table class="ref-table">
                    <tr>
                        <td>
                            Nama Vendor/Karyawan : {{ $sppb['sppb_kwitansi'] }}<br>
                            BA / AU 58 No : -
                        </td>
                        <td>
                            Nomor Faktur Pajak : {{ $sppb[1][0]->faktur_pajak_nomor ?? '-' }}<br>
                            Nomor SPPb : -
                        </td>
                    </tr>
                </table>

                <table class="main-table">
                    <thead>
                        <tr>
                            <th colspan="4">KODE</th>
                            <th rowspan="2">URAIAN</th>
                            <th rowspan="2" width="120">Jumlah<br>Rp.</th>
                        </tr>
                        <tr>
                            <th width="70">SAP</th>
                            <th width="80">CC/PC</th>
                            <th width="40">CF</th>
                            <th width="50">RF Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($sppb[0]))
                            @php
                                $sppb_calculated_total = 0;
                                foreach ($sppb[0] as $item) {
                                    if (isset($item[0])) {
                                        foreach ($item[0] as $urai) {
                                            $sppb_calculated_total += $urai->sppb_nominal_akhir;
                                        }
                                    }
                                }
                            @endphp
                            @foreach ($sppb[0] as $item)
                                <tr>
                                    <td class="col-code">
                                        {{ $item['master_customer_kode_sap'] ?? ($item['master_gl_kode'] ?? $item['master_rekening_kode_sap']) }}
                                    </td>
                                    <td class="col-code">{{ $item['master_cost_center_kode'] }}{{ $item['master_profit_center_kode'] }}</td>
                                    <td class="col-code">{{ $item['master_cash_flow_kode'] }}</td>
                                    <td class="col-code">{{ $item['master_cash_flow_key'] }}</td>
                                    <td class="uraian-content">
                                        {!! str_replace('</p>', '</n>', $item[0][0]->sppb_uraian_uraian) !!}
                                        
                                        <span class="label-bold">Nominal</span>
                                        Nominal DPP : {{ number_format($item[0][0]->sppb_uraian_nominal) }}<br>
                                        
                                        @if ($sppb['sppb_catatan'] !== null)
                                            <span class="label-bold">Catatan :</span>
                                            {{ $sppb['sppb_catatan'] }}
                                        @endif
                                    </td>
                                    <td class="col-amount">
                                        {{ number_format($item[0][0]->sppb_nominal_akhir) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr class="terbilang-row">
                            <td colspan="5">
                                <strong>TERBILANG:</strong> &nbsp;&nbsp;&nbsp;
                                <span class="terbilang-text">{{ Terbilang::angka($sppb_calculated_total) }} RUPIAH</span>
                            </td>
                            <td class="col-amount">{{ number_format($sppb_calculated_total) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer-note">
                    Dokumen-dokumen syarat pembayaran kami lampirkan dan kami bertanggung jawab atas kebenarannya
                </div>

                <div class="footer-signatures clearfix" style="margin-bottom: 10px;">
                    <div style="float:left; width: 50%;">
                        {!! QrCode::size(75)->generate('https://superman.ptpn1.co.id/spp/validasi/' . $id) !!}
                    </div>
                    <div style="float:right; width: 50%; text-align:center;">
                        <span>{{ $sppb['master_bagian_nama'] }}</span>
                        <br><br><br><br>
                        <span style="text-decoration: underline; font-weight: bold; text-transform: uppercase;">
                            {{ $sppb['master_bagian_kepala_bagian'] }}
                        </span>

                        @if(isset($company_jenis) && $company_jenis == "UNIT")
                            <div style="margin-top: 25px;">
                                <table class="paraf-table">
                                    <thead>
                                        <tr><th colspan="3" style="font-size: 10px;">Paraf dan Tanggal Pengecekan Berkas</th></tr>
                                        <tr style="font-size: 10px;">
                                            <th width="33%">COA / MIRO</th>
                                            <th width="33%">Pajak</th>
                                            <th width="34%">Verifikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td height="50"></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                @if(isset($company_jenis) && $company_jenis != "UNIT")
                    <div style="margin-top: 15px; clear: both;">
                        <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                            <tr>
                                @if ($flowid != 25)
                                    <td style="width: 24.5%; vertical-align: top;">
                                        <table class="paraf-table" style="width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr><th style="font-size: 10px; padding: 5px; text-align: center;">Diperiksa Oleh :</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; text-align: center; height: 115px;">
                                                        @foreach ($kotak_cetak as $kotak)
                                                            {{ $kotak->diperiksa_oleh_1 }}
                                                            <br><br><br><br><br><br>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width: 2%;"></td>
                                @endif
                                <td style="width: {{ $flowid == 25 ? '100%' : '73.5%' }}; vertical-align: top;">
                                    <table class="paraf-table" style="width: 100%; table-layout: fixed;">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="width: 66.6%; font-size: 10px; padding: 5px; border-right: 1px solid #000; text-align: center;">Diperiksa Oleh :</th>
                                                <th style="width: 33.4%; font-size: 10px; padding: 5px; text-align: center;">Disetujui Oleh :</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($kotak_cetak as $kotak)
                                                <tr>
                                                    <td style="width: 33.3%; font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; border-right: 1px solid #000; text-align: center; height: 115px;">
                                                        {{ $kotak->diperiksa_oleh_2 }}
                                                        <br><br><br><br><br><br>
                                                    </td>
                                                    <td style="width: 33.3%; font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; border-right: 1px solid #000; text-align: center; height: 115px;">
                                                        {{ $kotak->diperiksa_oleh_3 }}
                                                        <br><br><br><br><br><br>
                                                    </td>
                                                    <td style="width: 33.4%; font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; text-align: center; height: 115px;">
                                                        {{ $kotak->disetujui_oleh }}
                                                        <br><br><br><br><br><br>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                @elseif(!isset($company_jenis) || ($company_jenis != "UNIT"))
                    @if ($flowid != 25)
                        <div style="margin-top: 15px; clear: both;">
                            <table class="paraf-table" style="width: 30%;">
                                <thead>
                                    <tr><th style="font-size: 10px; padding: 5px; text-align: center;">Diperiksa Oleh :</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; text-align: center;">
                                            @foreach ($kotak_cetak as $kotak)
                                                {{ $kotak->diperiksa_oleh_1 }}
                                                <br><br><br><br><br><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- PAGE BREAK --}}
        @if (isset($sppb) && isset($sppn))
            <div style="page-break-after: always; height: 1px;"></div>
        @endif

        {{-- SECTION SPPn --}}
        @if (isset($sppn))
            <div class="spp-wrapper">
                <table class="header-table">
                    <tr>
                        <td class="company-name">
                            {{ $company }}<br>
                            <span style="font-weight: normal; font-size: 11px;">
                                Nomor : {{ $sppn['sppn_no'] }}<br>
                                Tanggal : {{ date('d-m-Y', strtotime($sppn['sppn_tanggal'])) }}
                            </span>
                        </td>
                        <td class="logo-cell">
                            <img src="{{ asset('assets/img/ptpn1.png') }}" class="logo-img">
                        </td>
                    </tr>
                </table>

                <div class="recipient-outer clearfix">
                    <div class="recipient-box">
                        <strong>Kepada Yth.</strong><br>
                        @if (in_array($sppn['master_bagian_id'], [151, 157, 166, 175, 178, 190, 196]))
                            @if ($sppn['master_bagian_id'] == 190)
                                Business Support Head Regional {{ $regional[$sppn['master_bagian_id']] }}
                            @else
                                SEVP Business Support Regional {{ $regional[$sppn['master_bagian_id']] }}
                            @endif
                        @elseif(isset($company_jenis) && $company_jenis == 'UNIT')
                            @php
                                $parts = explode('-', $company);
                                $company_tampil = count($parts) >= 3 ? trim($parts[2]) : (count($parts) == 2 ? trim($parts[1]) : trim($company));
                                $company_tampil = ucfirst(strtolower($company_tampil));
                            @endphp
                            Manajer {{ $company_tampil }}
                        @elseif ($perusahaan_sess == 5)
                            @if ($sppn['master_bagian_id'] != 126)
                                Kepala Divisi Perbendaharaan Anggaran dan Keuangan<br>
                            @else
                                Direktur Keuangan dan Manajemen Risiko<br>
                            @endif
                            PT Perkebunan Nusantara I
                        @else
                            @foreach ($kotak_cetak as $kotak)
                                {{ $kotak->tujuan_kepada }}
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="document-title">
                    <h4>SURAT PERMINTAAN PENERIMAAN (SPPn)</h4>
                </div>

                <span style="display:block; margin-bottom: 8px;">Dengan ini dimohon bantuannya untuk dibayarkan tagihan sebagai berikut :</span>

                <table class="ref-table">
                    <tr>
                        <td>
                            Kwitansi dari : {{ $sppn['sppn_kwitansi'] }}<br>
                            BA / AU 58 No : {{ $sppn['sppn_ba_au_53'] }}
                        </td>
                        <td>
                            Nomor Faktur Pajak : {{ $sppn[1][0]->faktur_pajak_nomor ?? '-' }}<br>
                            Nomor SPPb : {{ isset($sppb) ? $sppb['sppb_no'] : '-' }}
                        </td>
                    </tr>
                </table>

                <table class="main-table">
                    <thead>
                        <tr>
                            <th colspan="4">KODE</th>
                            <th rowspan="2">URAIAN</th>
                            <th rowspan="2" width="120">Jumlah<br>Rp.</th>
                        </tr>
                        <tr>
                            <th width="70">SAP</th>
                            <th width="80">CC/PC</th>
                            <th width="40">CF</th>
                            <th width="50">RF Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($sppn[0]))
                            @php
                                $sppn_calculated_total = 0;
                                foreach ($sppn[0] as $item) {
                                    if (isset($item[0])) {
                                        foreach ($item[0] as $urai) {
                                            $sppn_calculated_total += $urai->sppn_nominal_akhir;
                                        }
                                    }
                                }
                            @endphp
                            @foreach ($sppn[0] as $item)
                                <tr>
                                    <td class="col-code">
                                        {{ $item['master_customer_kode_sap'] ?? ($item['master_gl_kode'] ?? $item['master_rekening_kode_sap']) }}
                                    </td>
                                    <td class="col-code">{{ $item['master_cost_center_kode'] }}{{ $item['master_profit_center_kode'] }}</td>
                                    <td class="col-code">{{ $item['master_cash_flow_kode'] }}</td>
                                    <td class="col-code">{{ $item['master_cash_flow_key'] }}</td>
                                    <td class="uraian-content">
                                        {!! str_replace('</p>', '</n>', $item[0][0]->sppn_uraian_uraian) !!}
                                        
                                        <span class="label-bold">Nominal</span>
                                        Nominal DPP : {{ number_format($item[0][0]->sppn_uraian_nominal) }}<br>
                                        
                                        @if ($sppn['sppn_catatan'] !== null)
                                            <span class="label-bold">Catatan :</span>
                                            {{ $sppn['sppn_catatan'] }}
                                        @endif
                                    </td>
                                    <td class="col-amount">
                                        {{ number_format($item[0][0]->sppn_nominal_akhir) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr class="terbilang-row">
                            <td colspan="5">
                                <strong>TERBILANG:</strong> &nbsp;&nbsp;&nbsp;
                                <span class="terbilang-text">{{ Terbilang::angka($sppn_calculated_total) }} RUPIAH</span>
                            </td>
                            <td class="col-amount">{{ number_format($sppn_calculated_total) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer-note">
                    Dokumen-dokumen syarat pembayaran kami lampirkan dan kami bertanggung jawab atas kebenarannya
                </div>

                <div class="footer-signatures clearfix" style="margin-bottom: 10px;">
                    <div style="float:left; width: 50%;">
                        {!! QrCode::size(75)->generate('https://superman.ptpn1.co.id/spp/validasi/' . $id) !!}
                    </div>
                    <div style="float:right; width: 50%; text-align:center;">
                        <span>{{ $sppn['master_bagian_nama'] }}</span>
                        <br><br><br><br>
                        <span style="text-decoration: underline; font-weight: bold; text-transform: uppercase;">
                            {{ $sppn['master_bagian_kepala_bagian'] }}
                        </span>

                        @if(isset($company_jenis) && $company_jenis == "UNIT")
                            <div style="margin-top: 25px;">
                                <table class="paraf-table">
                                    <thead>
                                        <tr><th colspan="3" style="font-size: 10px;">Paraf dan Tanggal Pengecekan Berkas</th></tr>
                                        <tr style="font-size: 10px;">
                                            <th width="33%">COA / MIRO</th>
                                            <th width="33%">Pajak</th>
                                            <th width="34%">Verifikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td height="50"></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                @if(isset($company_jenis) && $company_jenis != "UNIT")
                    <div style="margin-top: 15px; clear: both;">
                        <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                            <tr>
                                @if ($flowid != 25)
                                    <td style="width: 24.5%; vertical-align: top;">
                                        <table class="paraf-table" style="width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr><th style="font-size: 10px; padding: 5px; text-align: center;">Diperiksa Oleh :</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; text-align: center; height: 115px;">
                                                        @foreach ($kotak_cetak as $kotak)
                                                            {{ $kotak->diperiksa_oleh_1 }}
                                                            <br><br><br><br><br><br>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width: 2%;"></td>
                                @endif
                                <td style="width: {{ $flowid == 25 ? '100%' : '73.5%' }}; vertical-align: top;">
                                    <table class="paraf-table" style="width: 100%; table-layout: fixed;">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="width: 66.6%; font-size: 10px; padding: 5px; border-right: 1px solid #000; text-align: center;">Diperiksa Oleh :</th>
                                                <th style="width: 33.4%; font-size: 10px; padding: 5px; text-align: center;">Disetujui Oleh :</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($kotak_cetak as $kotak)
                                                <tr>
                                                    <td style="width: 33.3%; font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; border-right: 1px solid #000; text-align: center; height: 115px;">
                                                        {{ $kotak->diperiksa_oleh_2 }}
                                                        <br><br><br><br><br><br>
                                                    </td>
                                                    <td style="width: 33.3%; font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; border-right: 1px solid #000; text-align: center; height: 115px;">
                                                        {{ $kotak->diperiksa_oleh_3 }}
                                                        <br><br><br><br><br><br>
                                                    </td>
                                                    <td style="width: 33.4%; font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; text-align: center; height: 115px;">
                                                        {{ $kotak->disetujui_oleh }}
                                                        <br><br><br><br><br><br>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                @elseif(!isset($company_jenis) || ($company_jenis != "UNIT"))
                    @if ($flowid != 25)
                        <div style="margin-top: 15px; clear: both;">
                            <table class="paraf-table" style="width: 30%;">
                                <thead>
                                    <tr><th style="font-size: 10px; padding: 5px; text-align: center;">Diperiksa Oleh :</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-size: 10px; vertical-align: top; padding: 5px; font-weight: bold; text-align: center;">
                                            @foreach ($kotak_cetak as $kotak)
                                                {{ $kotak->diperiksa_oleh_1 }}
                                                <br><br><br><br><br><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            </div>
        @endif

    </div>

    {{-- html2pdf.js dinonaktifkan karena tidak mampu handle page-break pada tabel multi-halaman.
         User menggunakan Ctrl+P (Print dialog browser) untuk cetak/simpan ke PDF. --}}
    {{--
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function generatePDF() {
            const element = document.getElementById('pdf-container');
            const options = {
                margin: [10, 10, 10, 10],
                filename: 'SPP-Export.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['avoid-all', 'css'], avoid: ['.main-table tr', '.footer-signatures', '.paraf-table', '.footer-note'] }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>
    --}}
</body>
</html>
