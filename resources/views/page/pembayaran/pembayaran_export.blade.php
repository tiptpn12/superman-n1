<table>
    <tr>
        <td style="text-align:center; font-weight:bold" colspan="8">DATA REKAP PEMBAYARAN</td>
    </tr>
    <tr>
    </tr>
</table>

<table style="border-style: dashed">
    <thead>
        <tr>
            <th style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Bagian</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Tanggal SPP</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Nomor SPPb</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Jumlah</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Nomor SPPn</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Jumlah</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Status</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Tujuan Transfer</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">Metode Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        {{-- @php
            dd($getdata);
        @endphp --}}
        @foreach ($getdata as $key => $value)
            <tr>
                <td style="text-align:center; border:1px solid black">{{ $key + 1 }}</td>
                <td style="text-align:center; border:1px solid black">{{ $value->master_bagian_nama }}</td>
                <td style="text-align:center; border:1px solid black">{{ $value->spp_tanggal }}</td>
                <td style="text-align:center; border:1px solid black">{{ $value->sppb_no }}</td>
                <td style="text-align:center; border:1px solid black">{{ strip_tags($value->sppb_uraian2) }}</td>
                <td style="text-align:center; border:1px solid black">Rp.{{ number_format($value->sppb_total) }}</td>
                <td style="text-align:center; border:1px solid black">{{ $value->sppn_no }}</td>
                <td style="text-align:center; border:1px solid black">{{ strip_tags($value->sppn_uraian2) }}</td>
                <td style="text-align:center; border:1px solid black">Rp.{{ number_format($value->sppn_jumlah) }}</td>
                @if ($value->spp_status_bayar == 0)
                    <td style="text-align:center; border:1px solid black">Belum Dibayar</td>
                @elseif($value->spp_status_bayar == 1)
                    <td style="text-align:center; border:1px solid black">Sudah Dibayar</td>
                @endif
                <td style="text-align:center; border:1px solid black">{{ $value->karyawan_nama }}</td>
                @if ($value->sppb_metode_pembayaran == 'bank')
                    <td style="text-align:center; border:1px solid black">Bank</td>
                @elseif($value->sppb_metode_pembayaran == 'tidak_transfer' || $value->sppb_metode_pembayaran == 'kas')
                    <td style="text-align:center; border:1px solid black">Kas</td>
                @else
                    <td style="text-align:center; border:1px solid black"><span>&nbsp;</span></td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" style="font-weight:bold; text-align:center; border:1px solid black">
                <center>Jumlah</center>
            </th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">
                Rp.{{ number_format($totalNominalSppb) }}</th>
            <th colspan="2" style="font-weight:bold; text-align:center; border:1px solid black">
                <center>Jumlah</center>
            </th>
            <th style="font-weight:bold; text-align:center; border:1px solid black">
                Rp.{{ number_format($totalNominalSppn) }}</th>
            <th style="font-weight:bold; text-align:center; border:1px solid black"></th>
            <th style="font-weight:bold; text-align:center; border:1px solid black"></th>
            <th style="font-weight:bold; text-align:center; border:1px solid black"></th>
        </tr>
    </tfoot>
</table>
