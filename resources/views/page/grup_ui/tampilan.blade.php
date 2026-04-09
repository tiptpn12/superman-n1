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
      <h3 class="page-title">Grup UI</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
            <div class="panel-heading">
              <h3 class="panel-title">Tabel Master Grup UI</h3>
            </div>
            <div class="panel-body">
              <button type="button" class="btn btn-primary" onclick="tambah()" style="margin-bottom: 15px">Tambah Data</button>
              <table class="table table-bordered table-striped nowrap" style="width: 100%">
                <thead>
                  <tr>
                    <th>No. </th>
                    <th>Nama Grup</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($grup_ui as $key => $value)
                    <tr>
                      <td>{{ ($key+1) }}</td>
                      <td>{{ $value->grup_nama }}</td>
                      <td>{{ $value->grup_keterangan }}</td>
                      <td>{{ $value->grup_status==1 ? "Aktif" : "Tidak Aktif" }}</td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="ubah({{ json_encode($value) }})" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapus({{ $value->grup_id }}, {{ $value->grup_status }})" title="Hapus Data" ><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
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
      <form action="{{url('')}}/tampilan/tambah" method="post">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Tambah Grup UI</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Grup</label>
            <input type="text" id="tambah_nama" name="grup" class="form-control" placeholder="Nama Grup" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <input type="text" id="tambah_keterangan" name="keterangan" class="form-control" placeholder="Keterangan" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
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
      <form action="{{url('')}}/tampilan/update" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="ubah_id" name="id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Ubah Data Grup UI</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Grup</label>
            <input type="text" id="ubah_nama" name="grup" class="form-control" placeholder="Nama Grup" autocomplete="off" required>
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
    $("#ubah_id").val(data.grup_id);
    $("#ubah_nama").val(data.grup_nama);
    $("#ubah_keterangan").val(data.grup_keterangan);
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
        window.location.href = `{{url('')}}/tampilan/destroy/${id}/${status}`;
      }
    })
  }
</script>

@endsection