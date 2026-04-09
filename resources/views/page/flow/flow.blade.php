@extends('template.master')
@section('title', 'Main')
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

#myInput:focus {outline: 3px solid #ddd;}
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
<!-- MAIN -->
<div class="main">
  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="page-title">Flow</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
            <div class="panel-heading">
              <h3 class="panel-title">Tabel Master Flow</h3>
            </div>
            <div class="panel-body">
              <button type="button" class="btn btn-primary" onclick="tambah()" style="margin-bottom: 15px">Tambah Data</button>
              <table class="table table-bordered table-striped nowrap" style="width: 100%">
                <thead>
                  <tr>
                    <th>No. </th>
                    <th>Nama Flow</th>
                    <th>Urutan Flow</th>
                    <th>Keterangan</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($flow as $key => $value)
                    <tr>
                      <td>{{ ($key+1) }}</td>
                      <td>{{ $value->flow_nama }}</td>
                      <td>
                        @foreach ($value->flow as $key => $value2)
                          {{ $value2->master_hak_akses_nama }},
                          
                        @endforeach
                        
                      </td>
                      <td>{{ $value->flow_keterangan }}</td>
                      <td>{{ $value->company_nama }}</td>
                      <td>{{ $value->flow_status==1 ? "Aktif" : "Tidak Aktif" }}</td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="ubah({{ json_encode($value) }})" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapus({{ $value->flow_id }}, {{ $value->flow_status }})" title="Hapus Data" ><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
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
<!-- END MAIN -->

<!-- Modal -->

{{-- Modal Tambah Data --}}
<div id="modal_tambah" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{url('')}}/flow/tambah" method="post" id="formABC">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Tambah Flow</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Flow</label>
            <input type="text" id="tambah_nama" name="nama_flow" class="form-control" placeholder="Nama Flow" autocomplete="off" required>
          </div>
          <div id="isi_flow">
            <div class="form-group" id="tambah_flow_1">
              <label>Flow</label></br>
              <select class="form-control" id="flow" name="tahapan[1]" required style="width: 88%;display:initial">
              <option value="" selected disabled>-- Pilih Urutan Flow --</option>
                @foreach ($hakakses as $key => $value)
                  <option value="{{ $value->master_hak_akses_id }}">{{ $value->master_hak_akses_nama }}</option>
                @endforeach  
              </select>
              <button type="button" class="btn btn-info btn-sm" onclick="tambah_flow()">+</button>
              <input class="stop[1]" type="checkbox" name="stop[1]" onclick="stop()" value="NULL">
            </div>
          </div>
          <div id="isi_company">
            <div class="form-group" id="tambah_company_1">
              <label>Company</label>
              <select class="form-control" id="company" name="company[1]" required style="width: 90%;display:initial">
                <option value="" selected disabled>-- Pilih Company --</option>
                @foreach ($company as $key => $value)
                  <option value="{{ $value->company_id }}">{{ $value->company_nama }}</option>
                @endforeach
              </select>
              <button type="button" class="btn btn-info btn-sm" onclick="tambah_company()">+</button>  
            </div>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <input type="text" id="tambah_keterangan" name="keterangan" class="form-control" placeholder="Keterangan" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnSubmit">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal Tambah Data --}}

{{-- Modal Ubah Data --}}
<div id="modal_ubah" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{url('')}}/flow/update" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="ubah_id" name="id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Ubah Flow</h4>
        </div>
        <div class="modal-body">
           <div class="form-group">
            <label>Nama Flow</label>
            <input type="text" id="ubah_nama" name="nama_flow" class="form-control" placeholder="Nama Flow" autocomplete="off" required>
          </div>
          <div id="ubah_flow">
            <div class="form-group" id="ubah_flow_1">            
              <label>Flow</label></br>
              <select class="form-control" id="ubah_flow" name="tahapan[1]" required style="width: 90%;display:initial">
                @foreach ($hakakses as $key => $value)
                  <option value="{{ $value->master_hak_akses_id }}">{{ $value->master_hak_akses_nama }}</option>
                @endforeach  
              </select>
              <button type="button" class="btn btn-info btn-sm" onclick="tambah_ubah_flow(1)">+</button>    
            </div>
          </div>
          <div class="form-group">
            <label>Company</label>
            <select class="form-control" id="ubah_company" name="company" required>
              <option value="" selected disabled>-- Pilih Company --</option>
              @foreach ($company as $key => $value)
                <option value="{{ $value->company_id }}">{{ $value->company_nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <input type="text" id="ubah_keterangan" name="keterangan" class="form-control" placeholder="Keterangan" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal Ubah Data --}}


<!-- End Modal -->

<script type="text/javascript">
 var index_flow = 1;
 var sub_index_flow = [];
 sub_index_flow[index_flow] = index_flow;
 var index_company = 1;
 var sub_index_company = [];
 sub_index_company[index_company] = index_company;
  
  function pilih(sel){
    // window.alert(sel.value);
    if(sel.value == "semua"){
      $("#username option[value!='semua']").attr("selected","selected");
      $("#username option[value!='semua']").attr("disabled","disabled");
    }

  }
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
  function kabagselect(kabag){
    $('#myInput').val(kabag);
    document.getElementById("myDropdown").classList.toggle("show",false);
  }
  function ubahkabagselect(kabag){
    $('#ubah_kabag').val(kabag);
    document.getElementById("myDropdownubah").classList.toggle("show",false);
  }
  
  function tambah()
  {
    $("#modal_tambah").modal('show');
  }

  function ubah(data)
  {
    console.log($('#ubah_flow'));
    $("#ubah_flow").empty();
    $("#ubah_flow").append('<label>Flow</label>');
    index_flow = 0;
    console.log(data);
    urutan = data.flow;
    console.log(urutan);
    urutan.forEach(element => {
      console.log(element);
      //select that had name tahapan[]

      tambah_ubah_flow();
      $('#ubah_flow_'+index_flow).find('select[name="tahapan['+index_flow+']"]').val(element.flow_detail_urutan);
      $('#ubah_flow_'+index_flow).find('input[name="stop['+index_flow+']"]').prop('checked', element.flow_revisi_stop == 1 ? true : false);
    })
    $("#ubah_id").val(data.flow_id);
    $("#ubah_nama").val(data.flow_nama);
    $("#ubah_keterangan").val(data.flow_keterangan);
    $("#ubah_company").val(data.company_id);
    $("#ubah_flow").val(data.flow_urutan);
    $("#modal_ubah").modal('show');
  }

  function hapus(id, status){
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Menghapus Data Bagian!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Hapus Data!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `{{url('')}}/flow/destroy/${id}/${status}`;
      }
    })
  }
  function tambah_flow(){
		index_flow++;
		// index = index_sppb-1;
		sub_index_flow[index_flow] = 1;
		$('#isi_flow').append(`<div class="form-group" id="tambah_flow_${index_flow}">
            <select class="form-control" id="flow" name="tahapan[${index_flow}]" required style="width: 80%;display:initial">
            <option value="semua">-- Pilih Urutan Flow --</option>
              @foreach ($hakakses as $key => $value)
                <option value="{{ $value->master_hak_akses_id }}">{{ $value->master_hak_akses_nama }}</option>
              @endforeach  
            </select>
            <button type="button" class="btn btn-danger btn-sm" onclick="hapus_flow(${index_flow})">x</button>
            <button type="button" class="btn btn-info btn-sm" onclick="tambah_flow()">+</button>
            <input class="stop${index_flow}" type="checkbox" name="stop[${index_flow}]" onclick="stop(${index_flow})" value="NULL">
          </div>`);
	}
  function tambah_ubah_flow(){
		index_flow++;
		// index = index_sppb-1;
		sub_index_flow[index_flow] = 1;
		$('#ubah_flow').append(`<div class="form-group" id="ubah_flow_${index_flow}">
            <select class="form-control" id="ubah_flow" name="tahapan[${index_flow}]" required style="width: 80%;display:initial">
            <option value="semua">-- Pilih Urutan Flow --</option>
              @foreach ($hakakses as $key => $value)
                <option value="{{ $value->master_hak_akses_id }}">{{ $value->master_hak_akses_nama }}</option>
              @endforeach  
            </select>
            <button type="button" class="btn btn-danger btn-sm" onclick="hapus_ubah_flow(${index_flow})">x</button>
            <button type="button" class="btn btn-info btn-sm" onclick="tambah_ubah_flow()">+</button>
            <input class="stop${index_flow}" type="checkbox" name="stop[${index_flow}]" onclick="stop(${index_flow})" value="NULL">
          </div>`);
	}
  function hapus_flow(isi){
		$("#tambah_flow_"+isi).remove();
	}
  function hapus_ubah_flow(isi){
		$("#ubah_flow_"+isi).remove();
	}
  function tambah_company(){
		index_company++;
		// index = index_sppb-1;
		sub_index_company[index_company] = 1;
		$('#isi_company').append(`<div class="form-group" id="tambah_company_${index_company}">
              <select class="form-control" id="company" name="company[${index_company}]" required style="width: 80%;display:initial">
                <option value="" selected disabled>-- Pilih Company --</option>
                @foreach ($company as $key => $value)
                  <option value="{{ $value->company_id }}">{{ $value->company_nama }}</option>
                @endforeach
              </select>
              <button type="button" class="btn btn-danger btn-sm" onclick="hapus_company(${index_company})">x</button>
              <button type="button" class="btn btn-info btn-sm" onclick="tambah_company()">+</button>  
            </div>`);
	}
  function hapus_company(isi){
		$("#tambah_company_"+isi).remove();
	}


 function stop(isian){
  $(".stop"+isian).change(function () {
      if (this.checked) {
        $(".stop"+isian).val("1");
      $(':checkbox:not(:checked)').prop('disabled', true);
   } 
   else
   {
    $(".stop"+isian).removeAttr("disabled");
   }
 });
 }
 $("#formABC").submit(function (e) {

$(':checkbox:not(:checked)').prop('disabled', false);
var d = $(this).data(); 
    $(':checkbox').prop('checked', !d.checked); 
    d.checked = !d.checked; 

return true;

});

</script>
@endsection