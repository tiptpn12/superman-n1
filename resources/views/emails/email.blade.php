@component('mail::message')
# Pemberitahuan SPP masuk

@if($sppb_nomor != null && $sppn_nomor != null)
Ada SPP masuk dengan nomor {{$sppb_nomor}} dan nomor {{$sppn_nomor}} dikirim dari Divisi {{$divisi}}
@elseif($sppb_nomor != null && $sppn_nomor == NULL)
Ada SPP masuk dengan nomor {{$sppb_nomor}} dikirim dari Divisi {{$divisi}}
@elseif($sppn_nomor != null && $sppb_nomor == NULL)
Ada SPP masuk dengan nomor {{$sppn_nomor}} dikirim dari Divisi {{$divisi}}
@endif
@component('mail::button', ['url' => 'https://superman-coba.ptpn12.com/sppd'])
Proses SPP
@endcomponent

PTPN GROUP
@endcomponent
