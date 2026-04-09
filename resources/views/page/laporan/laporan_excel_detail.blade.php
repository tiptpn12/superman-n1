
<table>
  <tr>
    <td style="text-align:center; font-weight:bold" colspan="13">DATA REKAP PEMBAYARAN/PENERIMAAN </td>
  </tr>
  <tr>
  @if($rentang_waktu == 'semua')
          @if(isset($data[0]))
          <td style="text-align:center; font-weight:bold" colspan="13">RENTANG TANGGAL {{$data[0]->tanggal}} - {{$data[count($data)-1]->tanggal}}</td>
          @endif
          @else
          <td style="text-align:center; font-weight:bold" colspan="13">RENTANG TANGGAL {{$rentang_waktu}}</td>
        @endif
  </tr> 
</table>

            <table style="border-style: dashed">
                <thead>
                  <tr>
                    <th rowspan="2" style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Tanggal</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode Cash Flow</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No. Bukti Kas</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">No SPPB/N</th>
                    <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(D)</th>
                    <th colspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Kode(K)</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Profit/Cost Center</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Nama Customer/Kebun/Biaya</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Status</th>
                    <th rowspan="2"style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPB/N</th>

                  </tr>
                  <tr >
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black" >KBB</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $value)
                <tr>
                    <td style="text-align:center; border:1px solid black">{{$key+1}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->tanggal}}</td>
                    
                    @if(isset($value))
                    <td style="text-align:center; border:1px solid black">{{$value->master_cash_flow_kode}}</td>
                    @elseif(isset($value))
                    <td style="text-align:center; border:1px solid black">{{$value->master_cash_flow_kode}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black"></td>
                    @endif

                    
                    @if(isset($value->sppb_bayar_nomor_bukti_kas) && $value->sppb_bayar_nomor_bukti_kas !== null)
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_bayar_nomor_bukti_kas}}</td>
                    @elseif(isset($value->sppn_terima_nomor_bukti_kas) && $value->sppn_terima_nomor_bukti_kas!== null)
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_terima_nomor_bukti_kas}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black"></td>
                    @endif

                   

                    @if(isset($value->sppb_no))
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_no}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_kode_sap_bayar}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_kode_kbb_bayar}}</td>
                    @elseif(isset($value->sppn_no))
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_no}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_kode_sap_terima}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->sppn_kode_kbb_terima}}</td>
                    @endif

                    
                    @if(isset($value->master_kode_kb))
                      @if(isset($value->master_gl_id) && $value->master_gl_id !== null)
                        <td style="text-align:center; border:1px solid black">{{$value->master_gl_kode}}</td>
                      @else
                        <td style="text-align:center; border:1px solid black">{{$value->master_rekening_kode_sap}}</td>
                      @endif          
                      @if(isset($value->master_kode_kbb))
                        <td style="text-align:center; border:1px solid black">{{$value->master_kode_kbb}}</td>
                        @endif          

                        <td style="text-align:center; border:1px solid black">{{$value->master_cost_center_kode}}</td>
                      
                    @elseif(isset($value))
                      @if(isset($value->master_gl_id) && $value->master_gl_id !== null)
                        <td style="text-align:center; border:1px solid black" > {{$value->master_gl_kode}}</td>
                      @else
                        <td style="text-align:center; border:1px solid black">{{$value->master_rekening_kode_sap  }}</td>
                      @endif    
                      @if(isset($value->master_kode_kbb))
                        <td style="text-align:center; border:1px solid black">{{$value->master_kode_kbb}}</td>
                        @endif  
                      <td style="text-align:center; border:1px solid black">{{$value->master_profit_center_kode}}{{$value->master_cost_center_kode}}</td>
                    @endif

                    @if($value->master_vendor_nama !== null)
                    <td style="border:1px solid black">{{$value->master_vendor_nama}}</td>
                    @else
                    <td style="border:1px solid black">{{$value->master_gl_keterangan}}</td>
                    @endif
                    
                    @if(isset($value->sppb_uraian2))
                    <td style="border:1px solid black">{{strip_tags($value->sppb_uraian2)}}</td>
                    @elseif(isset($value->sppn_uraian2))
                    <td style="border:1px solid black">{{strip_tags($value->sppn_uraian2)}}</td>
                    @endif
                    <td>{{$posisi_dinamis[$key]->master_hak_akses_nama}}</</td>

                    @if(isset($value->sppb_uraian_nominal))
                    <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppb_uraian_nominal)}}</td>

                    @elseif(isset($value->sppn_uraian_nominal))
                    <td style="text-align:right; border:1px solid black">Rp. {{number_format($value->sppn_uraian_nominal)}}</td>
                    @endif
                  </tr>
                @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <td style="text-align:right; border:1px solid black" colspan = "13">Total</td>
                    <td style="text-align:right; border:1px solid black">Rp.{{number_format($sum_spp)}}</td>
                  </tr>
                </tfoot>
              </table>
