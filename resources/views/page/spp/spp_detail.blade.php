@extends('template.master')
@section('title', 'SPP | Detail')
@section('header')
    <link rel="stylesheet" href="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
@endsection
@section('konten')

    @php
        function encrypt_path($data) {
            $encrypt_key = 'perdamain';
            $iv = '1234567891011121';
            return url('doc/' . urlencode(base64_encode(openssl_encrypt($data, 'AES-128-CTR', $encrypt_key, 0, $iv))));
        }
    @endphp

    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Detail SPP</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- DETAIL SPP -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Detail SPP</h3>
                            </div>
                            <input type="hidden" id="formspp" value="{{ $formspp }}">
                            <div class="panel-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Jenis SPP</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['sppb_jenis']))
                                            <p>{{ $sppb['sppb_jenis'] }} </p>
                                        @else
                                            <p>{{ $sppn['sppn_jenis'] }} </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Dokumen SPP</label>
                                    <div class="col-sm-10">
                                        @if (isset($dok_kabag))
                                            <a href="{{ encrypt_path('dokumen/' . $dok_kabag) }}"
                                                target="_blank">{{ $dok_kabag }}</a><br></br>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Sumber Dana</label>
                                    <div class="col-sm-10">
                                        @if (isset($spp->nama_sumber_dana))
                                            <p>{{ $spp->nama_sumber_dana }}
                                            <p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DETAIL SPPB -->
                        <div class="panel" id="panel_sppb" style="display: none">
                            <div class="panel-heading">
                                SPPb
                            </div>
                            <div class="panel-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No SPPb</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['sppb_no']))
                                            <p>{{ $sppb['sppb_no'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if (isset($sppb['sppb_kwitansi']) && $sppb['sppb_kwitansi'] != null)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Kwitansi</label>
                                        <div class="col-sm-10">
                                            <p>{{ $sppb['sppb_kwitansi'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($sppb['sppb_referensi']) && $sppb['sppb_referensi'] != null)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Referensi</label>
                                        <div class="col-sm-10">
                                            <p>{{ $sppb['sppb_referensi'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($sppb['sppb_au_53']) && $sppb['sppb_au_53'] != null)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">AU.53</label>
                                        <div class="col-sm-10">
                                            <p>{{ $sppb['sppb_au_53'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Berita Acara</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['sppb_berita_acara']))
                                            <p>{{ $sppb['sppb_berita_acara'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Faktur Pajak</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb[1][0]->faktur_pajak_nomor))
                                            @for ($i = 0; $i < count($sppb[1]); $i++)
                                                <p>{{ $sppb[1][$i]->faktur_pajak_nomor }}</p>
                                            @endfor
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No Kontrak</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['sppb_sp_opl']))
                                            <p>{{ $sppb['sppb_sp_opl'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bagian</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['master_bagian_nama']))
                                            <p>{{ $sppb['master_bagian_nama'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Tanggal</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['sppb_tanggal']))
                                            <p>{{ date('d-m-Y', strtotime($sppb['sppb_tanggal'])) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Catatan</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppb['sppb_catatan']))
                                            <p>{{ $sppb['sppb_catatan'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if (isset($sppb['sppb_jenis']) && $sppb['sppb_jenis'] == 'vendor')
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Kontrak Perjanjian / Dokumen Sejenis</label>
                                        <div class="col-sm-10">
                                            @if (isset($sppb['sppb_kontrak_perjanjian']))
                                                <a name="kontrak_perjanjian"
                                                    href="{{ encrypt_path('dokumen/kontrakperjanjian/' . $sppb['sppb_kontrak_perjanjian']) }}"
                                                    target="_blank">{{ $sppb['sppb_kontrak_perjanjian'] }}</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Invoice / Nota Pembayaran</label>
                                        <div class="col-sm-10">
                                            @if (isset($sppb['sppb_invoice']))
                                                <a href="{{ encrypt_path('dokumen/invoice/' . $sppb['sppb_invoice']) }}"
                                                    target="_blank">{{ $sppb['sppb_invoice'] }}</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">E-Faktur</label>
                                        <div class="col-sm-10">
                                            @if (isset($sppb['sppb_efaktur']))
                                                <a href="{{ encrypt_path('dokumen/efaktur/' . $sppb['sppb_efaktur']) }}"
                                                    target="_blank">{{ $sppb['sppb_efaktur'] }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Dokumen Pendukung</label>
                                    <div class="col-sm-10">
                                        @if (isset($dokpensppb))
                                            @foreach ($dokpensppb as $d)
                                                <a href="{{ encrypt_path('dokumen/' . $d->dokumen_pendukung_sppb_nama) }}"
                                                    target="_blank">{{ $d->dokumen_pendukung_sppb_nama }}</a><br></br>
                                            @endforeach
                                        @else
                                            <p> - </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Isi SPPb</label>
                                    <br></br>
                                    <div class="col-sm-12">
                                        <table class="table table-bordered striped" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="display:none;">id</th>
                                                    <th rowspan="2">No</th>
                                                    <th colspan="2">Kode Rekening</th>
                                                    <th rowspan="2">Uraian</th>
                                                    <th rowspan="2">Jumlah (Rp.)</th>
                                                </tr>
                                                <tr>
                                                    <th> SAP </th>
                                                    <th> CC/PC </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($sppb[0]))
                                                    @for ($i = 0; $i < count($sppb[0]); $i++)
                                                        <tr>
                                                            <th style="display:none;">{{ $sppb[0][$i]['sppb_isi_id'] }}
                                                            </th>
                                                            <th> {{ $i + 1 }}</th>
                                                            @if ($sppb[0][$i]['master_customer_id'] != null)
                                                                <td rowspan="{{ count($sppb[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppb[0][$i]['master_customer_kode_sap'] }}</td>
                                                            @elseif ($sppb[0][$i]['master_gl_id'] != null)
                                                                <td rowspan="{{ count($sppb[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppb[0][$i]['master_gl_kode'] }}</td>
                                                            @else
                                                                <td rowspan="{{ count($sppb[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppb[0][$i]['master_rekening_kode_sap'] }}</td>
                                                            @endif

                                                            {{-- @if ($sppb[0][$i]['master_gl_id'] == null)
                                            <td rowspan="{{count($sppb[0][$i][0])}}" style="text-align:center">{{$sppb[0][$i]['master_rekening_kode_sap']}}</td>
                                            @else
                                            <td rowspan="{{count($sppb[0][$i][0])}}" style="text-align:center">{{$sppb[0][$i]['master_gl_kode']}}</td>

                                            @endif --}}
                                                            <th>{{ $sppb[0][$i]['master_cost_center_kode'] }}{{ $sppb[0][$i]['master_profit_center_kode'] }}
                                                            </th>
                                                            <th style="text-align:left">{!! $sppb[0][$i][0][0]->sppb_uraian_uraian !!}</th>
                                                            <th>{{ number_format($sppb[0][$i][0][0]->sppb_uraian_nominal) }}
                                                            </th>

                                                            @for ($a = 1; $a < count($sppb[0][$i][0]); $a++)
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th style="text-align:left">{!! $sppb[0][$i][0][$a]->sppb_uraian_uraian !!}</th>
                                                            <th>{{ number_format($sppb[0][$i][0][$a]->sppb_uraian_nominal) }}
                                                            </th>
                                                        </tr>
                                                    @endfor
                                                    </tr>
                                                @endfor
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END DETAIL SPPB -->

                        <!-- DETAIL SPPN -->
                        <div class="panel" id="panel_sppn" style="display: none">
                            <div class="panel-heading">
                                SPPn
                            </div>
                            <div class="panel-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No SPPn</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppn['sppn_no']))
                                            <p>{{ $sppn['sppn_no'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if (isset($sppn['sppn_kwitansi']) && $sppn['sppn_kwitansi'] != null)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Kwitansi</label>
                                        <div class="col-sm-10">
                                            <p>{{ $sppn['sppn_kwitansi'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($sppn['sppn_referensi']) && $sppn['sppn_referensi'] != null)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Referensi</label>
                                        <div class="col-sm-10">
                                            <p>{{ $sppn['sppn_referensi'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($sppn['sppn_ba_au_53']) && $sppn['sppn_ba_au_53'] != null)
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">BA/AU.58</label>
                                        <div class="col-sm-10">
                                            <p>{{ $sppn['sppn_ba_au_53'] }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Faktur Pajak</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppn[1][0]->faktur_pajak_nomor))
                                            @for ($i = 0; $i < count($sppn[1]); $i++)
                                                <p>{{ $sppn[1][$i]->faktur_pajak_nomor }}</p>
                                            @endfor
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No Kontrak</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppn['sppn_sp_opl']))
                                            <p>{{ $sppn['sppn_sp_opl'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bagian</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppn['master_bagian_nama']))
                                            <p>{{ $sppn['master_bagian_nama'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Tanggal</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppn['sppn_tanggal']))
                                            <p>
                                            <p>{{ date('d-m-Y', strtotime($sppn['sppn_tanggal'])) }}</p>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Catatan</label>
                                    <div class="col-sm-10">
                                        @if (isset($sppn['sppn_catatan']))
                                            <p>{{ $sppn['sppn_catatan'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Dokumen Pendukung</label>
                                    <div class="col-sm-10">
                                        @if (isset($dokpensppn))
                                            @foreach ($dokpensppn as $d)
                                                <a href="{{ encrypt_path('dokumen/' . $d->dokumen_pendukung_sppn_nama) }}"
                                                    target="_blank">{{ $d->dokumen_pendukung_sppn_nama }}</a><br></br>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Isi SPPn</label>
                                    <br></br>
                                    <div class="col-sm-12">
                                        <table class="table table-bordered striped" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th style="display:none;">id</th>
                                                    <th rowspan="2">No</th>
                                                    <th colspan="4">Kode Rekening</th>
                                                    <th rowspan="2">Uraian</th>
                                                    <th rowspan="2">Jumlah (Rp.)</th>
                                                </tr>
                                                <tr>
                                                    <th> KBB </th>
                                                    <th> SAP </th>
                                                    <th> CC/PC </th>
                                                    <th> CF </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($sppn[0]))
                                                    @for ($i = 0; $i < count($sppn[0]); $i++)
                                                        <tr>
                                                            <th style="display:none;">{{ $sppn[0][$i]['sppn_isi_id'] }}
                                                            </th>
                                                            <th> {{ $i + 1 }}</th>

                                                            <th style="text-align:center;">
                                                                {{ $sppn[0][$i]['master_kode_kbb'] }}</th>
                                                            {{-- @if ($sppn[0][$i]['master_gl_id'] == null)
                                                                <td rowspan="{{ count($sppn[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_rekening_kode_sap'] }}</td>
                                                            @else
                                                                <td rowspan="{{ count($sppn[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_gl_kode'] }}</td>
                                                            @endif --}}
                                                            @if ($sppn[0][$i]['master_customer_id'] != null)
                                                                <td rowspan="{{ count($sppn[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_customer_kode_sap'] }}</td>
                                                            @elseif ($sppn[0][$i]['master_gl_id'] != null)
                                                                <td rowspan="{{ count($sppn[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_gl_kode'] }}</td>
                                                            @else
                                                                <td rowspan="{{ count($sppn[0][$i][0]) }}"
                                                                    style="text-align:center">
                                                                    {{ $sppn[0][$i]['master_rekening_kode_sap'] }}</td>
                                                            @endif
                                                            <th>{{ $sppn[0][$i]['master_cost_center_kode'] }}{{ $sppn[0][$i]['master_profit_center_kode'] }}
                                                            </th>
                                                            <th>{{ $sppn[0][$i]['master_cash_flow_kode'] }}</th>
                                                            <th style="text-align:left">{!! $sppn[0][$i][0][0]->sppn_uraian_uraian !!}</th>
                                                            <th>{{ number_format($sppn[0][$i][0][0]->sppn_uraian_nominal) }}
                                                            </th>

                                                            @for ($a = 1; $a < count($sppn[0][$i][0]); $a++)
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th style="text-align:left">{!! $sppn[0][$i][0][$a]->sppn_uraian_uraian !!}</th>
                                                            <th>{{ number_format($sppn[0][$i][0][$a]->sppn_uraian_nominal) }}
                                                            </th>
                                                        </tr>
                                                    @endfor
                                                    </tr>
                                                @endfor
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- END DETAIL SPPN -->

                        <!-- DOKUMEN TAMBAHAN -->
                        <div class="panel" id="panel_dokumen_tambahan">
                            <div class="panel-heading">
                                <h3 class="panel-title">Dokumen Tambahan</h3>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Dokumen</th>
                                            <th>User</th>
                                            <th>Waktu</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i=1 @endphp
                                        @foreach ($doktam as $d)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>
                                                    <a href="{{ encrypt_path('dokumen/' . $d->dokumen_tambahan_nama) }}"
                                                        target="_blank">{{ $d->dokumen_tambahan_nama }}</a>
                                                </td>
                                                <td>{{ $d->master_user_name }}</td>
                                                <td>{{ date('d-m-Y H:i:s', strtotime($d->dokumen_tambahan_waktu)) }}</td>
                                                <td style="text-align:center">
                                                    <button type="button" class="btn btn-danger btn-sm" title="hapus"
                                                        onclick="hapus_dokumen_tambahan('{{ $d->dokumen_tambahan_id }}','{{ $d->dokumen_tambahan_nama }}')">x</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END DOKUMEN TAMBAHAN -->
                        <!-- DOKUMEN BUKTI KAS -->
                        <div class="panel" id="panel_dokumen_bukti_kas">
                            <div class="panel-heading">
                                <h3 class="panel-title">Dokumen Pembayaran</h3>
                            </div>
                            <div class="panel-body">
                                @if (isset($sppb_bayar->sppb_bayar_bukti))
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Bukti Transfer</label>
                                        <div class="col-sm-10">

                                            <a href="{{ encrypt_path('dokumen/' . $sppb_bayar->sppb_bayar_bukti) }}"
                                                target="_blank">{{ $sppb_bayar->sppb_bayar_bukti }}</a>

                                        </div>
                                    </div>
                                @endif
                                @if (isset($sppn_terima->sppn_terima_bukti))
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Bukti Terima</label>
                                        <div class="col-sm-10">

                                            <a href="{{ encrypt_path('dokumen/' . $sppn_terima->sppn_terima_bukti) }}"
                                                target="_blank">{{ $sppn_terima->sppn_terima_bukti }}</a>

                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                        <div class="panel" id="panel_dokumen_bukti_kas">
                            <div class="panel-heading">
                                <h3 class="panel-title">Dokumen Bukti Kas</h3>
                            </div>
                            <div class="panel-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bukti Kas</label>
                                    <div class="col-sm-10">
                                        @if (isset($spp->spp_bukti_kas_bank))
                                            <a href="{{ encrypt_path('dokumen/' . $spp->spp_bukti_kas_bank) }}"
                                                target="_blank">{{ $spp->spp_bukti_kas_bank }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END DOKUMEN BUKTI KAS -->
                        <!-- NOMOR DOKUMEM -->
                        <div class="panel" id="panel_dokumen_bukti_kas">
                            <div class="panel-heading">
                                <h3 class="panel-title">Nomor Dokumen</h3>
                            </div>
                            <div class="panel-body">
                                <div class="form-group row">
                                    <!-- <label class="col-sm-2 col-form-label">Nomor Dokumen</label> -->
                                    <div class="col-sm-10">
                                        @if (isset($spp->spp_no_dokumen))
                                            <h1>{{ $spp->spp_no_dokumen }}</h1>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END NOMOR DOKUMEN -->

                        <?php
                        $hakakses = Session::get('hak_akses');
                        $level = Session::get('level');
                        ?>
                        <!-- FORM SUBMIT -->
                        <div class="panel" id="panel_submit">
                            <div class="panel-body">
                                @if ($hakakses == 2)
                                    @if ($spp->spp_status_proses == 0)
                                        <center>
                                            <br>
                                            <!-- <button type="button" class="btn btn-success" onclick="upload_kirim({{ $spp->spp_id }})" title="Kirim" ><i class="fa fa-check"> Kirim</i></button> -->
                                            <button type="button" class="btn btn-primary"
                                                onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();"
                                                title="Cetak"><i class="fa fa-print"> Cetak</i></button>
                                        </center>
                                    @else
                                        <center>
                                            <br>
                                            <button type="button" class="btn btn-primary"
                                                onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();"
                                                title="Cetak"><i class="fa fa-print"> Cetak</i></button>
                                        </center>
                                    @endif
                                @elseif($hakakses == 4)
                                    @if ($spp->spp_status_proses == 1)
                                        <center>
                                            <br>
                                            <button type="button" class="btn btn-success"
                                                onclick="terima({{ $spp->spp_id }})" title="Terima"><i
                                                    class="fa fa-check"> Terima</i></button>
                                        </center>
                                    @elseif($spp->spp_status_proses == 2)
                                        <div id="respon">
                                            <center>
                                                <br>
                                                <!-- <button type="button" class="btn btn-success" onclick="kirim({{ $spp->spp_id }})" title="Kirim" ><i class="fa fa-send"> Kirim</i></button> -->
                                                <button type="button" class="btn btn-warning"
                                                    onclick="revisi({{ $spp->spp_id }})" title="Revisi"><i
                                                        class="fa fa-pencil"> Revisi</i></button>
                                            </center>
                                        </div>
                                    @else
                                        <center>
                                            <br>
                                            <!-- <button type="button" class="btn btn-primary" onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"> Cetak</i></button> -->
                                        </center>
                                    @endif
                                @elseif($hakakses == 5)
                                    @if ($spp->spp_status_proses == 3)
                                        <center>
                                            <br>
                                            <button type="button" class="btn btn-success"
                                                onclick="terima({{ $spp->spp_id }})" title="Terima"><i
                                                    class="fa fa-check"> Terima</i></button>
                                        </center>
                                    @elseif($spp->spp_status_proses == 4)
                                        <div id="respon">
                                            <center>
                                                <br>
                                                <!-- <button type="button" class="btn btn-success" onclick="kirim({{ $spp->spp_id }})" title="Kirim" ><i class="fa fa-send"> Kirim</i></button> -->
                                                <button type="button" class="btn btn-warning"
                                                    onclick="revisi({{ $spp->spp_id }})" title="Revisi"><i
                                                        class="fa fa-pencil"> Revisi</i></button>
                                            </center>
                                        </div>
                                    @else
                                        <center>
                                            <br>
                                            <!-- <button type="button" class="btn btn-primary" onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"> Cetak</i></button> -->
                                        </center>
                                    @endif
                                @elseif($hakakses == 6)
                                    @if ($spp->spp_status_proses == 5)
                                        <center>
                                            <br>
                                            <button type="button" class="btn btn-success"
                                                onclick="terima({{ $spp->spp_id }})" title="Terima"><i
                                                    class="fa fa-check"> Terima</i></button>
                                        </center>
                                    @elseif($spp->spp_status_proses == 6)
                                        <div id="respon">
                                            <center>
                                                <br>
                                                <!-- <button type="button" class="btn btn-success" onclick="kirim({{ $spp->spp_id }})" title="Kirim" ><i class="fa fa-send"> Kirim</i></button> -->
                                                <button type="button" class="btn btn-warning"
                                                    onclick="revisi({{ $spp->spp_id }})" title="Revisi"><i
                                                        class="fa fa-pencil"> Revisi</i></button>
                                            </center>
                                        </div>
                                    @else
                                        <center>
                                            <br>
                                            <!-- <button type="button" class="btn btn-primary" onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"> Cetak</i></button> -->
                                        </center>
                                    @endif
                                @elseif($hakakses == 7)
                                    @if ($spp->spp_status_proses == 7)
                                        <center>
                                            <br>
                                            <button type="button" class="btn btn-success"
                                                onclick="terima({{ $spp->spp_id }})" title="Terima"><i
                                                    class="fa fa-check"> Terima</i></button>
                                        </center>
                                    @elseif($spp->spp_status_proses == 8)
                                        <div id="respon">
                                            <center>
                                                <br>
                                                <!-- <button type="button" class="btn btn-success" onclick="kirim({{ $spp->spp_id }})" title="Kirim" ><i class="fa fa-send"> Kirim</i></button> -->
                                                <button type="button" class="btn btn-warning"
                                                    onclick="revisi({{ $spp->spp_id }})" title="Revisi"><i
                                                        class="fa fa-pencil"> Revisi</i></button>
                                            </center>
                                        </div>
                                    @else
                                        <center>
                                            <br>
                                            <!-- <button type="button" class="btn btn-primary" onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"> Cetak</i></button> -->
                                        </center>
                                    @endif
                                @elseif($hakakses == 8)
                                    @if ($spp->spp_status_proses == 9)
                                        <center>
                                            <br>
                                            <button type="button" class="btn btn-success"
                                                onclick="terima({{ $spp->spp_id }})" title="Terima"><i
                                                    class="fa fa-check"> Terima</i></button>
                                        </center>
                                    @elseif($spp->spp_status_proses == 10)
                                        <div id="respon">
                                            <center>
                                                <br>
                                                <!-- <button type="button" class="btn btn-success" onclick="kirim({{ $spp->spp_id }})" title="Kirim" ><i class="fa fa-send"> Kirim</i></button> -->
                                                <button type="button" class="btn btn-warning"
                                                    onclick="revisi({{ $spp->spp_id }})" title="Revisi"><i
                                                        class="fa fa-pencil"> Revisi</i></button>
                                            </center>
                                        </div>
                                    @else
                                        <center>
                                            <br>
                                            <!-- <button type="button" class="btn btn-primary" onclick="window.open('{{ url('spp_keuangan/cetak/' . $spp->spp_id) }}').print();" title="Cetak"><i class="fa fa-print"> Cetak</i></button> -->
                                        </center>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <!-- END FORM SUBMIT -->
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN -->


        {{-- Modal KIRIM --}}
        <div id="modal_kirim" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="" id="form-kirim" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Kirim SPP</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Upload File SPP yang sudah di TTD Kepala Bagian :</label><br>
                                <input type="file" id="spp_kabag" name="spp_kabag" class="file"
                                    accept="application/pdf, image/*" placeholder="SPP tanda tangan Kabag"
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="pisanae" class="btn btn-success">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- End Modal KIRIM --}}


        {{-- Modal REVISI --}}
        <div id="modal_revisi" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="{{ url('spp') }}" method="get">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Revisi SPP</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Masukkan Keterangan Revisi SPP :</label><br>
                                <textarea class="form-control" name="" placeholder="Keterangan Revisi" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- End Modal REVISI --}}

        <!-- Javascript -->
        <script type="text/javascript">
            $(document).ready(function() {
                $(".file").fileinput({
                    allowedFileTypes: ["image", "pdf"],
                    browseClass: "btn btn-primary btn-block",
                    showCaption: true,
                    showRemove: false,
                    showUpload: false,
                    showPreview: false,
                });
            });
            $(document).ready(function() {
                var formspp = $("#formspp").val();

                if (formspp == 2) {
                    $('#panel_sppn').show();

                } else if (formspp == 1) {
                    $('#panel_sppb').show();

                } else {
                    $('#panel_sppb').show();
                    $('#panel_sppn').show();

                }

            });

            function revisi(id) {
                $("#modal_revisi").modal('show');
                $("#form-revisi").attr('action', '{{ url('') }}/spp/revisi/' + id);
            }

            function upload_kirim(id) {
                $("#modal_kirim").modal('show');
                $("#form-kirim").attr('action', '{{ url('') }}/spp/upload/' + id);
                $("#pisanae").click(function() {
                    if (document.getElementById("spp_kabag").files.length == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Pilih file dulu!'
                        })
                    } else {
                        $("#modal_kirim").modal('hide');
                        $("#form-kirim").attr('action', '{{ url('') }}/spp/upload/' + id);
                        let timerInterval
                        Swal.fire({
                            title: 'Loading !',
                            allowOutsideClick: false,
                            timer: 1000000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal.DismissReason.timer) {
                                console.log('I was closed by the timer')
                            }
                        })
                    }

                });
            }

            function terima(id) {
                Swal.fire({
                    title: 'Apakah Anda Yakin sudah menerima SPP?',
                    text: "Terima Data SPP!",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#41B314',
                    cancelButtonColor: '#F9354C',
                    confirmButtonText: 'Terima SPP'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('') }}/spp/accept/` + id;
                        Swal.fire(
                            'Terima SPP',
                            'SPP berhasil anda Terima.',
                            'success'
                        )
                    }
                })
            }
            <?php
            $level = Session::get('level');
            ?>

            function kirim(id) {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Kirim Data SPP!",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#41B314',
                    cancelButtonColor: '#F9354C',
                    confirmButtonText: 'Kirim SPP'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('') }}/spp/send/` + id;
                        Swal.fire(
                            'Kirim SPP',
                            'SPP berhasil anda Kirim.',
                            'success',
                        )
                        $("#respon").hide();
                    }
                })
            }

            $('#rekening_sppb').click(function(event) {
                $('#modal_rekening_sppb').modal('show');
            });

            $('#rekening_sppn').click(function(event) {
                $('#modal_rekening_sppn').modal('show');
            });

            function pilih_rekening_sppb(kbb, sap, keterangan) {
                $('#rekening_sppb').val(kbb + ' / ' + sap + ' (' + keterangan + ')');
                $('#modal_rekening_sppb').modal('hide');
            }

            function pilih_rekening_sppn(kbb, sap, keterangan) {
                $('#rekening_sppn').val(kbb + ' / ' + sap + ' (' + keterangan + ')');
                $('#modal_rekening_sppn').modal('hide');
            }

            function hapus_dokumen_tambahan(id, nama) {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Menghapus Dokumen " + nama + " !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('') }}/dokumen_tambahan/hapus/` + id;
                        Swal.fire(
                            'Hapus!',
                            'Dokumen telah berhasil dihapus.',
                            'success'
                        )
                    }
                })
            }
        </script>
        <!-- End Javascript -->

    @endsection
    @section('footer')
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
        <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
    @endsection
