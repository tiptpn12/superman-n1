@extends('template.master')
@section('title', 'Semua Notifikasi')

@section('open')
active
@endsection
@section('konten')
<?php
$hakakses = Session::get('hak_akses');
$bagian = Session::get('bagian');
$level = Session::get('level');
?>
<!-- MAIN -->
<style>
  .preloader
{
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('{{asset('')}}assets/Ajux_loader.gif') 50% 50% no-repeat rgb(249,249,249);
  background-size: 200px 200px;
}
</style>
<script>
  $(window).load(function()
  {
        $("#preloaders").fadeOut(1000);
    });
</script>
<div id="preloaders" class="preloader"></div>

<div class="main">
  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="container-fluid">
        <h1>Semua Notifikasi</h1>
        @foreach ($listNotifikasi as $notif)
            <div id="div-list-notifikasi">
                <div class="alert alert-success" role="alert">
                    <p>Notifikasi dibuat pada {{ $notif->created_at }}</p>
                </div>
            </div>
        @endforeach
    </div>
  </div>
</div>
@endsection
