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
                  <tr >
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No. </th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Tanggal</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Kode Cash Flow</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No. Bukti Kas</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Uraian</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No SPPb</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No SPPn</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No Rekg Kas/Bank</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No Rekg KBB</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">No Rekg SAP</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Profit/Cost Center</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPb</th>
                    <th style="font-weight:bold; text-align:center; border:1px solid black">Jumlah SPPn</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $value)
                  <tr>
                    <td style="text-align:center; border:1px solid black">{{$key+1}}</td>
                    <td style="text-align:center; border:1px solid black">{{$value->tanggal}}</td>
                    
                    @if(isset($sppbisi[$key]))
                    <td style="text-align:center; border:1px solid black">{{$sppbisi[$key]->master_cash_flow_kode}}</td>
                    @elseif(isset($sppnisi[$key]))
                    <td style="text-align:center; border:1px solid black">{{$sppnisi[$key]->master_cash_flow_kode}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black"></td>
                    @endif

                    
                    @if(isset($sppb_bayar[$key]) && $sppb_bayar[$key] !== null)
                    <td style="text-align:center; border:1px solid black">{{$sppb_bayar[$key]->sppb_bayar_nomor_bukti_kas}}</td>
                    @elseif(isset($sppn_terima[$key]) && $sppn_terima[$key]!== null)
                    <td style="text-align:center; border:1px solid black">{{$sppn_terima[$key]->sppn_terima_nomor_bukti_kas}}</td>
                    @else
                    <td style="text-align:center; border:1px solid black"></td>
                    @endif
                    @if(isset($value->sppb_uraian2))
                    <td style="border:1px solid black">{{strip_tags($value->sppb_uraian2)}}</td>
                    @elseif(isset($value->sppn_uraian2))
                    <td style="border:1px solid black">{{strip_tags($value->sppn_uraian2)}}</td>
                    @endif
                    @if(isset($value->sppb_no))
                    <td style="text-align:center; border:1px solid black">{{$value->sppb_no}}</td>
                    <td style="text-align:center; border:1px solid black"></td>

                    @elseif(isset($value->sppn_no))
                    <td style="text-align:center; border:1px solid black"></td>

                    <td style="text-align:center; border:1px solid black">{{$value->sppn_no}}</td>

                    @endif
                    
                    @if(isset($sppbisi[$key]))
                    <td style="text-align:center; border:1px solid black">{{$sppbisi[$key]->master_kode_kbb}}</td>
                    <td style="text-align:center; border:1px solid black">{{$sppbisi[$key]->master_kode_kbb}}</td>
                    @if(isset($sppb_sppbisi[$key]->master_gl_id) && $sppb_sppbisi[$key]->master_gl_id !== null)
                        <td style="text-align:center; border:1px solid black">{{$sppb_sppbisi[$key]->master_gl_kode}}</td>
                      @else
                      <td style="text-align:center; border:1px solid black">{{$sppbisi[$key]->master_rekening_kode_sap}}</td>
                      @endif                       
                    <td style="text-align:center; border:1px solid black">{{$sppbisi[$key]->master_cost_center_kode}}</td>
                    @elseif(isset($sppnisi[$key]))
                    <td style="text-align:center; border:1px solid black">{{$sppnisi[$key]->master_kode_kbb}}</td>
                    <td style="text-align:center; border:1px solid black">{{$sppnisi[$key]->master_kode_kbb}}</td>
                      @if(isset($sppn_sppnisi[$key]->master_gl_id) && $sppn_sppnisi[$key]->master_gl_id !== null)
                        <td style="text-align:center; border:1px solid black" > {{$sppn_sppnisi[$key]->master_gl_kode}}</td>
                      @else
                        <td style="text-align:center; border:1px solid black">{{$sppnisi[$key]->master_rekening_kode_sap}}</td>
                      @endif                    
                    <td style="text-align:center; border:1px solid black">{{$sppnisi[$key]->master_profit_center_kode}}{{$sppnisi[$key]->master_cost_center_kode}}</td>
                    @endif
                    @if(isset($value->sppb_total))
                    <td style="text-align:right; border:1px solid black" data-nominal_spp="{{$value->sppb_total}}" class="row-nominal_spp">Rp. {{number_format($value->sppb_total)}}</td>
                    <td style="text-align:center; border:1px solid black"></td>

                    @elseif(isset($value->sppn_jumlah))
                    <td style="text-align:center; border:1px solid black"></td>

                    <td style="text-align:right; border:1px solid black" data-nominal_spp="{{$value->sppn_jumlah}}" class="row-nominal_spp">Rp. {{number_format($value->sppn_jumlah)}}</td>
                    @endif
                  </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>            
                    <td colspan="11" style="border:1px solid black" ><center>Total</center></td>
                    <td colspan="2" style="border:1px solid black">Rp. {{number_format($sum_spp)}}</td>
                  </tr>
                </tfoot>
              </table>