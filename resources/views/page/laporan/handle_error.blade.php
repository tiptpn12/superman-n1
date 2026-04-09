@extends('template.master')
@section('title', 'SPP | Laporan')
@section('yoro','active')
@section('konten')
<div class="main">
  <!-- MAIN CONTENT -->
 
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="page-title">Laporan</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
                
                <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
                <script>
                    $(document).ready(function(){
                        window.setInterval(function () {
                            var sisawaktu = $("#waktu").html();
                            sisawaktu = eval(sisawaktu);
                            if (sisawaktu == 0) {
                                location.href = "{{ url('laporan') }}";
                            } else {
                                $("#waktu").html(sisawaktu - 1);
                            }
                        }, 1000);
                    });
                </script>
                <style type="text/css">
                    body {
                        font-size:12pt;
                        font-family:verdana;
                    } 
                    #waktu {
                        font-size:25pt;
                        color:red;
                    }
                </style>
                <center style="padding:100px">
                <h3>Nomor Bukti Kas ada yang belum terisi </h3>
                <h4>Anda akan diarahkan ke halaman awal dalam waktu <span id="waktu">10</span> detik</h4>
                <a href="{{ url('laporan') }}"><button class="btn btn-success" >Kembali Sekarang</button></a>
                </center>
          </div>
          <!-- END TABLE -->
        </div>
      </div>
    </div>
  </div>
  <!-- END MAIN CONTENT -->
</div>
		

@endsection