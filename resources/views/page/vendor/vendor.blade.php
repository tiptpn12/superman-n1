@extends('template.master')
@section('title', 'Main')
@section('konten')

    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Vendor</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tabel Vendor</h3>
                            </div>
                            <div class="panel-body">
                                <button type="button" class="btn btn-primary" onclick="tambah()"
                                    style="margin-bottom: 15px">Tambah Data</button>
                                <button type="button" class="btn btn-success" onclick="importExcel()"
                                    style="margin-bottom: 15px">Import Data</button>
                                <table class="table table-bordered table-striped nowrap" style="width: 100%" id="table">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Vendor</th>
                                            <th>Nama Bank</th>
                                            <th>No. Rekening</th>
                                            <th>Atas Nama</th>
                                            <th>Nama Company</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @foreach ($vendor as $key => $value)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $value->master_vendor_nama }}</td>
                      <td>{{ $value->master_vendor_nama_bank }}</td>
                      <td>{{ $value->master_vendor_rekening }}</td>
                      <td>{{ $value->master_vendor_atas_nama }}</td>
                      <td>{{ $value->master_vendor_status == 1 ? "Aktif" : "Tidak Aktif" }}</td>
                      <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="ubah({{ json_encode($value) }})" title="Edit Data" ><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                        <button t ype="button" class="btn btn-danger btn-sm" onclick="hapus({{ $value->master_vendor_id }}, {{ $value->master_vendor_status }})" title="Hapus Data" ><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
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
                <form action="{{ url('') }}/vendor/store" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Tambah Data Vendor</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Vendor</label>
                            <input type="text" id="tambah_nama" name="nama" class="form-control"
                                placeholder="Nama Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Bank</label>
                            <input type="text" id="tambah_nama_bank" name="nama_bank" class="form-control"
                                placeholder="Nama Bank Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>No. Rekening</label>
                            <input type="text" id="tambah_rekening" name="rekening_vendor" class="form-control"
                                placeholder="Nama Rekening Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Atas Nama</label>
                            <input type="text" id="tambah_atas_nama" name="atas_nama" class="form-control"
                                placeholder="Atas Nama Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <select class="form-control" id="tambah_company" name="company" required>
                                <option value="" selected disabled>-- Pilih Company --</option>
                                @foreach ($company as $key => $value)
                                    <option value="{{ $value->company_id }}"> {{ $value->company_nama }}</option>
                                @endforeach
                            </select>
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
                <form action="{{ url('') }}/vendor/update" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" id="ubah_id" name="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Ubah Data Vendor</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Vendor</label>
                            <input type="text" id="ubah_nama" name="nama" class="form-control"
                                placeholder="Nama Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Bank Vendor</label>
                            <input type="text" id="ubah_nama_bank" name="nama_bank" class="form-control"
                                placeholder="Nama Bank Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>No. Rekening Vendor</label>
                            <input type="text" id="ubah_rekening" name="rekening_vendor" class="form-control"
                                placeholder="No. Rekening Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Atas Nama Vendor</label>
                            <input type="text" id="ubah_atas_nama" name="atas_nama" class="form-control"
                                placeholder="Atas Nama Vendor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <select class="form-control" id="ubah_company" name="company" required>
                                <option value="" disabled>-- Pilih Company --</option>
                                @foreach ($company as $key => $value)
                                    <option value="{{ $value->company_id }}"> {{ $value->company_nama }}</option>
                                @endforeach
                            </select>
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
                <form action="{{ route('vendor.import') }}" id="form_upload_excel" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Upload Data Vendor</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" id="upload_bukti">
                            <label>Upload data Vendor:</label><br>
                            <p>Silakan unggah data yang ingin diimpor menggunakan file Excel.
                                Pastikan file Anda memiliki ekstensi <strong>.xls</strong> atau <strong>.xlsx</strong>.</p>
                            <span style="color: red;">*Format Excel diperlukan</span>
                            <input type="file" class="form-control" name="file" accept=".xls,.xlsx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_bukti" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Modal Upload File Excel --}}

    <!-- End Modal -->

    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                scrollX: true,
                autoWidth: true,
                ajax: {
                    'url': '{{ url('vendor/getdatatableall') }}',
                    'type': 'get',
                },
                order: [],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'master_vendor_nama',
                        name: 'master_vendor_nama'
                    },
                    {
                        data: 'master_vendor_nama_bank',
                        name: 'master_vendor_nama_bank'
                    },
                    {
                        data: 'master_vendor_rekening',
                        name: 'master_vendor_rekening'
                    },
                    {
                        data: 'master_vendor_atas_nama',
                        name: 'master_vendor_atas_nama'
                    },
                    {
                        data: 'company_nama',
                        name: 'company_nama'
                    },
                    {
                        data: 'master_vendor_status',
                        name: 'master_vendor_status',
                        render: function(data, type, full, meta) {
                            return data == 1 ? 'Aktif' : 'Tidak Aktif';
                        }
                    },
                    {
                        data: 'master_vendor_id',
                        name: 'master_vendor_id',
                        render: function(data, type, full, meta) {
                            string_json = JSON.stringify(full);
                            return `
                    <button type="button" class="btn btn-warning btn-sm" onclick='ubah(${string_json})' title="Edit Data"><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="hapus(${full.master_vendor_id}, '${full.master_vendor_status}')" title="Hapus Data"><i class="fa fa-trash-o" aria-hidden="true"></i> Ubah Status</button>
                `;
                        }
                    }
                ]
            });
        })

        function tambah() {
            $("#modal_tambah").modal('show');
        }

        function importExcel() {
            $("#modal_import").modal('show');
        }

        function ubah(data) {
            $("#ubah_id").val(data.master_vendor_id);
            $("#ubah_nama").val(data.master_vendor_nama);
            $("#ubah_nama_bank").val(data.master_vendor_nama_bank);
            $("#ubah_rekening").val(data.master_vendor_rekening);
            $("#ubah_atas_nama").val(data.master_vendor_atas_nama);
            $("#ubah_company").val(data.company_id);
            $("#modal_ubah").modal('show');
        }

        function hapus(id, status) {
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
                    window.location.href = `{{ url('') }}/vendor/destroy/${id}/${status}`;
                }
            })
        }
    </script>

@endsection
