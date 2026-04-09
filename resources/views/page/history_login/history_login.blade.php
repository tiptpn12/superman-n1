@extends('template.master')
@section('title', 'Main')
@section('active','active')
@section('konten')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<style>
.btn-corner {
  position: absolute;
  top: 27px;
  right: 75px;
}
</style>
<!-- MAIN -->
<div class="main">
  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="page-title">History Login</h3>
      <div class="row">
        <div class="col-md-12">
          <div class="panel" id="panel-tabel">
            <div class="panel-heading">
              <h3 class="panel-title">Advanced Search</h3>
            </div>
            <div class="panel-body">
              <div class="col-md-12">
                <form action="{{route('search_history_login')}}" method="post" enctype="multipart\form-data">
                {{csrf_field()}}
                <div class="form-group row col-md-6">
                    <div class="col-md-10">
                        <label>Username</label>
                    </div>
                    <div id="username">
                        <div class="col-md-12">
                            <select class="js-select2" id="username" onchange="pilih(this)" name="username[]" multiple="multiple" style="border-color:light-grey">
                              <option value="semua">Semua User</option>
                            @foreach($user as $u)    
                              <option value="{{$u->master_user_id}}">{{$u->master_user_name}}</option>
                            @endforeach  
                          </select>
                        </div>
                        <!-- <div class="col-md-1">
                            <button type="button" class="btn btn-info btn-xs" id="tambah_username_0" onclick="tambah_username(0)">+</button>
                          </div> -->
                    </div>
                </div>
                <div class="form-group row col-md-4">
                <div class="col-md-10">
                  <label>Rentang Waktu</label>
                  </div>
                  <div class="col-md-10">
                      <input type="text" class="form-control date-range" name="rentang_waktu" >
                  </div>
                  </div>
                  <div class="form-group row col-md-2" style="vertical-align:bottom">
                          <button type="submit" class="btn btn-primary btn-corner">Search</button>
                </div>
                </form>
              </div>
            </div>
          </div>
          <!-- TABLE -->
          <div class="panel" id="panel-tabel">
            <div class="panel-heading">
              <h3 class="panel-title">Tabel History Login</h3>
            </div>
            <div class="panel-body">
              <table class="table table-bordered table-striped nowrap" style="width: 100%">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Username</th>
                    <th>Bagian</th>
                    <th>Login</th>
                    <th>Detail</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($history_login as $key => $value)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $value->master_user_name }}</td>
                      <td>{{ $value->master_bagian_nama }}</td>
                      <td>{{date('d-m-Y H:i:s',strtotime($value->history_login_waktu))}}</td>
                      <td style="text-align:center">
                        <button type="button" class="btn btn-info btn-sm" id="detail_login" onclick="detail_login({{json_encode($value)}})" ><i class="fa fa-eye" aria-hidden="true"></i></button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <!-- END TABLE -->
        </div>
      </div>
    </div>
  </div>
  <!-- END MAIN CONTENT -->
</div>
{{-- Start Modal Detail Login --}}
<div id="modal_detail_login" class="modal fade" role="dialog">
	<div class="modal-dialog modal-default">
		<!-- Modal content-->
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Detail login</h4>
				</div>
				<div class="modal-body">
					<div class="row">
            <div class="col-sm-6">
              <label for="">IP Adress</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_ip"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">Hostname</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_hostname"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">City</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_city"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">Region</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_region"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">Country</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_country"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">Location</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_location"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">Browser</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_browser"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="">Operating System</label>
            </div>
            <div class="col-sm-6">
              <span id="detail_os"></span>
            </div>
          </div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- End Modal Detail login --}}
<!-- END MAIN -->
<script>
  
  // var index_tambah =0;
  // var index_show = 0;
  // function hapus_username(index){
  //   index_show = index-1;
    
  //   $('#tambah_username_'+index_show).show();
  //   $('#hapus_username_'+index_show).show();
  //   $('#username_'+index).remove();
  //   $('#hapus_username_'+index).remove();
  //   $('#tambah_username_'+index).remove();
  // }
  // function tambah_username(index_username){
  //   index_username++;
  //   index_tambah = index_username-1;
  //   $('#tambah_username_'+index_tambah).hide();
  //   $('#hapus_username_'+index_tambah).hide();
  //   $('#username').append(`<div class="col-md-10">
  //                           <select class="form-control" id="username_${index_username}" name="username[${index_username}]">
  //                           <option value="" disabled selected>--Pilih Username--</option>
  //                           @foreach($user as $u)    
  //                             <option value="{{$u->master_user_id}}">{{$u->master_user_name}}</option>
  //                           @endforeach  
  //                         </select>
  //                       </div>
  //                       <div class="col-md-1">
  //                           <button type="button" class="btn btn-info btn-xs" id="tambah_username_${index_username}" onclick="tambah_username(${index_username})">+</button>
  //                           </div>
  //                           <div class="col-md-1">
  //                           <button type="button" class="btn btn-danger btn-xs" id="hapus_username_${index_username}" onclick="hapus_username(${index_username})">x</button>
  //                           </div>`);
  // }
  $(".js-select2").select2({
    width : "100%",
			closeOnSelect : false,
			placeholder : "Pilih Username",
			allowHtml: true,
			allowClear: true,
			tags: true // создает новые опции на лету
		});

	$('.icons_select2').select2({
		width: "100%",
		templateSelection: iformat,
		templateResult: iformat,
		allowHtml: true,
		placeholder: "Click to select an option",
		dropdownParent: $( '.select-icon' ),//обавили класс
		allowClear: true,
		multiple: false
	});

  $("#username").select(function(){
    window.alert('a');
    if($(this).val() == "semua"){
      $("#username option[value!='semua']").attr("disabled","disabled");
    }
  });
  
  function pilih(sel){
    // window.alert(sel.value);
    if(sel.value == "semua"){
      $("#username option[value!='semua']").attr("selected","selected");
      $("#username option[value!='semua']").attr("disabled","disabled");
    }

  }

  function detail_login(data){
    $("#modal_detail_login").modal('show');
    document.getElementById('detail_ip').innerHTML = data.detail_login_ip;
    document.getElementById('detail_hostname').innerHTML = data.detail_login_hostname;
    document.getElementById('detail_city').innerHTML = data.detail_login_city;
    document.getElementById('detail_region').innerHTML = data.detail_login_region;
    document.getElementById('detail_country').innerHTML = data.detail_login_country;
    document.getElementById('detail_location').innerHTML = data.detail_login_loc;
    document.getElementById('detail_browser').innerHTML = data.detail_login_browser;
    document.getElementById('detail_os').innerHTML = data.detail_login_os;
  }

</script>



@endsection