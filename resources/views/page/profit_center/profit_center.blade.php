@extends('template.master')
@section('title', 'Main')
@section('konten')

    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Profit Center</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tabel Profit Center</h3>
                            </div>
                            <div class="panel-body">
                                <button type="button" class="btn btn-primary" onclick="tambah()"
                                    style="margin-bottom: 15px">Tambah Data</button>
                                <button type="button" class="btn btn-success" onclick="importData()"
                                    style="margin-bottom: 15px">Import Data</button>
                                <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Kode Profit Center</th>
                                            <th>Keterangan Unit</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($profit_center as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->master_profit_center_kode }}</td>
                                                <td>{{ $value->master_profit_unit }}</td>
                                                <td>{{ $value->master_profit_center_status == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="ubah({{ json_encode($value) }})" title="Edit Data"><i
                                                            class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="hapus({{ $value->master_profit_center_id }}, {{ $value->master_profit_center_status }})"
                                                        title="Hapus Data"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                                        Ubah Status</button>
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
                <form action="{{ url('') }}/profit_center/store" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Tambah Data Profit Center</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Profit Center</label>
                            <input type="text" id="tambah_kode" name="kode" class="form-control"
                                placeholder="Kode Profit Center" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Unit</label>
                            <input type="text" id="tambah_keterangan" name="keterangan" class="form-control"
                                placeholder="Keterangan" autocomplete="off" required>
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
                <form action="{{ url('') }}/profit_center/update" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" id="ubah_id" name="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Ubah Data Profit Center</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Profit Center</label>
                            <input type="text" id="ubah_kode" name="kode" class="form-control"
                                placeholder="Profit Center" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Unit</label>
                            <input type="text" id="ubah_keterangan" name="keterangan" class="form-control"
                                placeholder="Keterangan" autocomplete="off" required>
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

    {{-- Start Modal Upload File Excel --}}
    <div id="modal_import" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('profitcenter.import') }}" id="form_upload_excel" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Upload Data Profit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" id="upload_data">
                            <label>Upload data profit:</label><br>
                            <p>Silakan unggah data yang ingin diimpor menggunakan file Excel.
                                Pastikan file Anda memiliki ekstensi <strong>.xls</strong> atau <strong>.xlsx</strong>.</p>
                            <span style="color: red;">*Format Excel diperlukan</span>
                            <input type="file" class="form-control" name="file" accept=".xls,.xlsx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_data" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Modal Upload File Excel --}}

    <!-- End Modal -->

    <script type="text/javascript">
        function tambah() {
            $("#modal_tambah").modal('show');
        }

        function importData() {
            $('#modal_import').modal('show');
        }

        function ubah(data) {
            $("#ubah_id").val(data.master_profit_center_id);
            $("#ubah_kode").val(data.master_profit_center_kode);
            $("#ubah_keterangan").val(data.master_profit_unit);
            $("#modal_ubah").modal('show');
        }

        function hapus(id, status) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Menghapus Data Profit Center!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus Data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `{{ url('') }}/profit_center/destroy/${id}/${status}`;
                }
            })
        }
    </script>

@endsection
