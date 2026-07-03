@extends('template.master')
@section('title', 'Main')
@section('header')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

@endsection
@section('konten')

<!-- MAIN -->
<div class="main">
  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="page-title">Master RKAP</h3>
      <div class="row">
        <div class="col-md-12">
          <!-- TABLE -->
          <div class="panel">
            <div class="panel-heading">
              <h3 class="panel-title">Tabel RKAP</h3>
            </div>
            <div class="panel-body">
              <button type="button" class="btn btn-primary" onclick="tambah()" style="margin-bottom: 15px">Tambah Data</button>
              <table class="table table-bordered table-striped nowrap" style="width: 100%" id="table">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Kode GL</th>
                    <th>Divisi/Bagian</th>
                    <th>Nilai RKAP</th>
					          <th>Tahun</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- @foreach ($rkap as $key => $value)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $value->master_gl_kode }}</td>
                      <td>{{ $value->master_bagian_nama }}</td>
					  <td><strong>Rp.{{number_format($value->jumlah_budget)}}</strong></td>
					  <td>{{ $value->budget_tahun }}</td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="ubah({{ json_encode($value) }})" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapus({{ $value->master_hak_akses_id }}, {{ $value->master_hak_akses_status }})" title="Hapus Data" ><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
                      </td>
                    </tr>
                  @endforeach --}}
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
      <form action="{{url('')}}/rkap/store" method="post">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Tambah RKAP</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Kode GL</label>
            <select class="selectpicker form-control"  data-live-search="true" data-dropup-auto="false" id="tambah_gl" name="gl" required>
              <option value="" selected disabled>-- Pilih Kode GL --</option>
              @foreach ($gl as $key => $value)
                <option value="{{ $value->master_gl_id }}"> {{ $value->master_gl_kode }} - {{ $value->master_gl_keterangan }}</option>
              @endforeach
            </select>
          </div>
		  <div class="form-group">
            <label>Divisi/Bagian</label>
            <select class="form-control" id="tambah_divisi" name="divisi" required>
              <option value="" selected disabled>-- Pilih Divisi/Bagian --</option>
			  <option value="all">Semua Divisi/Bagian</option>
              @foreach ($bagian as $key => $value)
                <option value="{{ $value->master_bagian_id }}">{{ $value->master_bagian_nama }}</option>
              @endforeach
            </select>
          </div>
		  <div class="form-group">
            <label>Jumlah RKAP</label>
            <input type="text" id="tambah_rkap" name="rkap" class="form-control" placeholder="Nilai RKAP Tanpa Singkatan" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Tahun</label>
            <input type="text" id="tambah_tahun" name="tahun" class="form-control" placeholder="Tahun RKAP" autocomplete="off" value="{{ date('Y')}}">
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
      <form action="{{url('')}}/rkap/update" method="post">
        <input type="hidden" id="ubah_id" name="id">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Ubah RKAP</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Kode GL</label>
            <select class="selectpicker form-control"  data-live-search="true" data-dropup-auto="false"  id="ubah_gl" name="gl" required>
              <option value="" disabled>-- Pilih Kode GL --</option>
              @foreach ($gl as $key => $value)
                <option value="{{ $value->master_gl_id }}"> {{ $value->master_gl_kode }} - {{ $value->master_gl_keterangan }}</option>
              @endforeach
            </select>
          </div>
		  <div class="form-group">
            <label>Divisi/Bagian</label>
            <select class="form-control" id="ubah_divisi" name="divisi" required>
              <option value="" disabled>-- Pilih Divisi/Bagian --</option>
			  <option value="all">Semua Divisi/Bagian</option>
              @foreach ($bagian as $key => $value)
                <option value="{{ $value->master_bagian_id }}">{{ $value->master_bagian_nama }}</option>
              @endforeach
            </select>
          </div>
		  <div class="form-group">
            <label>Jumlah RKAP</label>
            <input type="text" id="ubah_rkap" name="rkap" class="form-control" placeholder="Nilai RKAP Tanpa Singkatan" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Tahun</label>
            <input type="text" id="ubah_tahun" name="tahun" class="form-control" placeholder="Tahun RKAP" autocomplete="off" value="{{ date('Y')}}">
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

{{-- End Modal Ubah Data --}}


<!-- End Modal -->

<script type="text/javascript">
  
  $(document).ready(function() {
    

    tabel = $('#table').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      autoWidth: true,
        ajax : {
            'url' : '{{ url('rkap/getdata') }}',
            'type' : 'get',
        },
        order: [[4, 'desc']],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'master_gl_kode', name: 'master_gl_kode' },
            { data: 'master_bagian_nama', name: 'master_bagian_nama' },
            { data: 'jumlah_budget', name: 'jumlah_budget', render: function(data) { return 'Rp. ' + (data ? data.toString() : '0').replace(/\B(?=(\d{3})+(?!\d))/g, "."); } },
            { data: 'budget_tahun', name: 'budget_tahun' },
            { data: 'budget_status', name: 'budget_status', render : function(data) { return data == 1 ? 'Aktif' : 'Tidak Aktif' } },
            { data: 'master_gl_kode', name: 'master_gl_kode', render: function(data, type, full, meta) {
                string_json = JSON.stringify(full).replace(/'/g, "&apos;");
                return `
                    <button type="button" class="btn btn-warning btn-sm" onclick='ubah(${string_json})' title="Edit Data"><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="hapus(${full.budget_id}, '${full.budget_status}')" title="Hapus Data"><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
                `;
            }
          }
        ]
    });
  })



  function tambah()
  {
    $("#modal_tambah").modal('show');
  }

  function ubah(data)
  
  {
    console.log(data);
    $("#ubah_id").val(data.budget_id);
    $("#ubah_gl").val(data.master_gl_id);
    $("#ubah_gl").selectpicker('refresh');
    $("#ubah_divisi").val(data.master_bagian_id);
    $("#ubah_rkap").val(data.jumlah_budget);
    $("#ubah_tahun").val(data.budget_tahun);
    $("#modal_ubah").modal('show');
  }

  function hapus(id, status)
  {
    Swal.fire({
      title: 'Apakah Anda Yakin?',
      text: "Menghapus Data Vendor!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Hapus Data!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `{{url('')}}/rkap/destroy/${id}/${status}`;
      }
    })
  }
</script>


@endsection
@section('footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
@endsection