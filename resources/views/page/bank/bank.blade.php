@extends('template.master')
@section('title', 'Main')
@section('konten')

<!-- MAIN -->
<div class="main">
  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="page-title">Bank</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
            <div class="panel-heading">
              <h3 class="panel-title">Tabel Bank</h3>
            </div>
            <div class="panel-body">
              <button type="button" class="btn btn-primary" onclick="tambah()" style="margin-bottom: 15px">Tambah Data</button>
              <table class="table table-bordered table-striped nowrap" style="width: 100%">
                <thead>
                  <tr>
                    <th>No. </th>
                    <th>No Rekening</th>
                    <th>Nama Pemilik</th>
                    <th>Bank</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($bank as $key => $value)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $value->master_bank_no_rekening }}</td>
                      <td>{{ $value->master_bank_atas_nama }}</td>
                      <td>{{ $value->master_bank_nama }}</td>
                      <td>{{ $value->master_bank_status == 1 ? "Aktif" : "Tidak Aktif" }}</td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="ubah({{ json_encode($value) }})" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapus({{ $value->master_bank_id }}, {{ $value->master_bank_status }})" title="Hapus Data" ><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
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
      <form action="{{url('')}}/bank/store" method="post">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Tambah Data Bank</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>No Rekening</label>
            <input type="number" id="tambah_no_rekening" name="no_rekening" class="form-control" placeholder="No Rekening" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label>Nama atas_nama</label>
            <input type="text" id="tambah_atas_nama" name="atas_nama" class="form-control" placeholder="Nama atas_nama" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label>Nama Bank</label>
            <input type="text" id="tambah_nama" name="nama" class="form-control" placeholder="Nama Bank" autocomplete="off" required>
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
      <form action="{{url('')}}/bank/update" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="ubah_id" name="id">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Ubah Data Bank</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>No Rekening</label>
            <input type="number" id="ubah_no_rekening" name="no_rekening" class="form-control" placeholder="No Rekening" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label>Nama atas_nama</label>
            <input type="text" id="ubah_atas_nama" name="atas_nama" class="form-control" placeholder="Nama atas_nama" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label>Nama Bank</label>
            <input type="text" id="ubah_nama" name="nama" class="form-control" placeholder="Nama Bank" autocomplete="off" required>
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

  function tambah()
  {
    $("#modal_tambah").modal('show');
  }

  function ubah(data)
  {
    $("#ubah_id").val(data.master_bank_id);
    $("#ubah_no_rekening").val(data.master_bank_no_rekening);
    $("#ubah_atas_nama").val(data.master_bank_atas_nama);
    $("#ubah_nama").val(data.master_bank_nama);
    $("#modal_ubah").modal('show');
  }

  function hapus(id, status){
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Menghapus Data Bank!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Hapus Data!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `{{url('')}}/bank/destroy/${id}/${status}`;
      }
    })
  }
</script>

@endsection