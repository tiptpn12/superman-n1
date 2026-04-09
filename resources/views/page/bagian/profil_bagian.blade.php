@extends('template.master')
@section('title', 'SPP | Profil Kepala Bagian')
@section('kabag','active')
@section('header')
<link rel="stylesheet" href="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css">
@endsection
@section('konten')
<style>
.dropbtn {
  background-color: #4CAF50;
  color: white;
  padding: 8px;
  font-size: 10px;
  border: none;
  cursor: pointer;
}

.dropbtn:hover, .dropbtn:focus {
  background-color: #3e8e41;
}

#myInput {
 
  /* background-image: url('searchicon.png');
  background-position: 14px 12px;
  background-repeat: no-repeat; */
  width : 200px;
  font-size: 12px;
  padding: 0px;
  border: 1px;
  text-align : right;
}

#myInput:focus {outline: 1px solid #ddd;}
#ubah_kabag {
  box-sizing: border-box;
  /* background-image: url('searchicon.png');
  background-position: 14px 12px;
  background-repeat: no-repeat; */
  width : 565px;
  font-size: 14px;
  padding: 8px 14px 20px 12px 45px;
  border: none;
  border-bottom: 1px solid #ddd;
}

#ubah_kabag:focus {outline: 3px solid #ddd;}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 280px;
  overflow: auto;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {background-color: #ddd;}

.show {display: block;}
</style>
<div class="main">
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <!-- FORM SPP -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title" style="text-align:center; font-weight:bold">Profil Kepala Bagian</h3>
                        </div>
                        <div class="panel-body">
                            <div id="panel_profil">
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Nama Lengkap</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                    {{ $bagian->master_bagian_kepala_bagian}}
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Nomor Induk Karyawan</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                    @foreach($kepala_bagian as $k)
                                    {{ $k->karyawan_nik}}
                                    @endforeach
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Nama Bagian</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                    {{$bagian->master_bagian_nama}}
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Kode Bagian</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                    {{$bagian->master_bagian_kode}}
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="text-center">
                                    <button type="button" class="btn btn-warning" onclick="edit()">Edit</button>
                                    </div>
                                </div>
                            </div>
                            <div id="panel_edit_profil" style="display:none">
                            <form action="{{url('')}}/bagian/update_kabag" method="post" enctype="multipart\form-data">
                            {{ csrf_field() }}
                                <div class="form-group row col-sm-12">
                                <input type="hidden" id="ubah_id" name="id" value="{{$bagian->master_bagian_id}}">
                                    <div class="col-sm-6">
                                        <label>Nama Lengkap</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                        <input type="text" onkeypress="myFunction()" onkeyup="filterFunction()" onblur="blur()" id="myInput" name="kepala_bagian" class="form-control" value="{{ $bagian->master_bagian_kepala_bagian}}">
                                        <div id="myDropdown" class="dropdown-content">
                                            @foreach($karyawan as $k)
                                            <a value="{{$k->karyawan_id}}" onclick="kabagselect('{{$k->karyawan_nama}}','{{$k->karyawan_nik}}')">{{$k->karyawan_nama}}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Nomor Induk Karyawan</label>
                                    </div>
                                    <div class="col-sm-6">
                                    @foreach($kepala_bagian as $k)
                                        <input type="text" id="karyawan_nik" class="form-control" style="text-align:right" value=" {{ $k->karyawan_nik}}" readonly>
                                    @endforeach
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Nama Bagian</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                    {{$bagian->master_bagian_nama}}
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="col-sm-6">
                                        <label>Kode Bagian</label>
                                    </div>
                                    <div class="col-sm-6" style="text-align:right">
                                    {{$bagian->master_bagian_kode}}
                                    </div>
                                </div>
                                <div class="form-group row col-sm-12">
                                    <div class="text-center">
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show",true);
  }
  function myFunctionubah() {
    document.getElementById("myDropdownubah").classList.toggle("show",true);
  }

  function filterFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        a[i].style.display = "";
      } else {
        a[i].style.display = "none";
      }
    }
  }
  function filterFunctionubah() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("ubah_kabag");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdownubah");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        a[i].style.display = "";
      } else {
        a[i].style.display = "none";
      }
    }
  }
  function kabagselect(kabag,nik){
    $('#myInput').val(kabag);
    $('#karyawan_nik').val(nik);
    document.getElementById("myDropdown").classList.toggle("show",false);
  }

  function blur(){
    document.getElementById("myDropdown").classList.toggle("show",false);
  }
  function ubahkabagselect(kabag){
    $('#ubah_kabag').val(kabag);
    document.getElementById("myDropdownubah").classList.toggle("show",false);
  }

function edit(){
    $("#panel_profil").hide();
    $("#panel_edit_profil").show();
}
  
</script>
@endsection

@section('footer')
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
<script src="{{asset('')}}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
<script src="{{asset('')}}assets/vendor/ckeditor/ckeditor5-build-inline/build/ckeditor.js"></script>
@endsection