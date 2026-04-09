@extends('template.master')
@section('title', 'Main')
@section('konten')

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
      <h3 class="page-title">GL Detail</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
            <div class="panel-heading">
              <h3 class="panel-title">Tabel GL Detail</h3>
            </div>
            <div class="panel-body">
              <button type="button" class="btn btn-primary" onclick="tambah()" style="margin-bottom: 15px">Tambah Data</button>
              <table class="table table-bordered table-striped nowrap" style="width: 100%">
                <thead>
                  <tr>
                    <th>No. </th>
                    <th>Bagian</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($gl_detail as $key => $value)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $value->master_bagian_nama }}</td>
                      <td>{{ $value->master_gl_detail_budget }}</td>
                      <td>{{ $value->master_gl_detail_status == 1 ? "Aktif" : "Tidak Aktif" }}</td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="ubah({{ json_encode($value) }})" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapus({{ $value->id_gl_detail }}, {{ $value->master_gl_detail_status }})" title="Hapus Data" ><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
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
      <form action="{{url('')}}/gl_detail/store" method="post">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Tambah Data GL Detail</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Bagian/Kebun</label>
                <select class="form-control" id="tambah_id_bagian" name="id_bagian" readonly>
                  <option value="" disabled>-- Pilih Bagian --</option>
                  @foreach($bagian as $b)
                  <option value="{{$b->master_bagian_id}}" >{{$b->master_bagian_nama}}</option>
                  @endforeach
                </select>
          <div class="form-group">
            <label>Budget</label>
            <input type="text" id="tambah_budget" name="budget" class="form-control" placeholder="Budget" autocomplete="off" required>
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
      <form action="{{url('')}}/gl_detail/update" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="ubah_id" name="id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Ubah Data GL Detail</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Bagian / Kebun</label>
              <select class="form-control" id="ubah_id_bagian" name="id_bagian" required>
                  <option value="{{$b->master_bagian_id}}" disabled>-- Pilih Bagian --</option>
                  @foreach($bagian as $b)
                  <option value="{{$b->master_bagian_id}}" >{{$b->master_bagian_nama}}</option>
                  @endforeach
                </select>
          </div>
          <div class="form-group">
            <label>Budget</label>
            <input type="text" id="ubah_budget" name="Budget" class="form-control" placeholder="Budget" autocomplete="off" required>
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
        var tanpa_rupiah = document.getElementById('tambah_budget');
        tanpa_rupiah.addEventListener('keyup', function(e)
        {
            tanpa_rupiah.value = formatRupiah(this.value);
        });
        var tanpa_rupiah1 = document.getElementById('ubah_budget');
        tanpa_rupiah1.addEventListener('keyup', function(e)
        {
            tanpa_rupiah1.value = formatRupiah(this.value);
        });

       function formatRupiah(angka, prefix)
          {
              var number_string = angka.replace(/[^,\d]/g, '').toString(),
                  split    = number_string.split(','),
                  sisa     = split[0].length % 3,
                  rupiah     = split[0].substr(0, sisa),
                  ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
                  
              if (ribuan) {
                  separator = sisa ? '.' : '';
                  rupiah += separator + ribuan.join('.');
              }
              
              rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
              return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
          }
  function tambah()
  {
    $("#modal_tambah").modal('show');

  }

  function ubah(data)
  {
    $("#ubah_id").val(data.id_gl_detail);
    $("#ubah_id_bagian").val(data.master_bagian_nama);
    $("#ubah_budget").val(data.master_gl_detail_budget);
    $("#modal_ubah").modal('show');
  }

  function hapus(id, status){
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Menghapus Data Customer!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Hapus Data!'
    }).then((result) => {
      if (result.isConfirmed) {
         window.location.href = `{{url('')}}/customer/destroy/${id}/${status}`;
      }
    })
  }
</script>

@endsection