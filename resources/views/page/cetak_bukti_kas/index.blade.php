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

        .dropbtn:hover,
        .dropbtn:focus {
            background-color: #3e8e41;
        }

        #myInput {
            box-sizing: border-box;
            width: 565px;
            font-size: 14px;
            padding: 8px 14px 20px 12px 45px;
            border: none;
            border-bottom: 1px solid #ddd;
        }

        #myInput:focus {
            outline: 3px solid #ddd;
        }

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

        .dropdown a:hover {
            background-color: #ddd;
        }

        .show {
            display: block;
        }
    </style>

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Master Cetak Bukti Kas</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tabel Master Cetak Bukti Kas</h3>
                            </div>
                            <div class="panel-body">
                                @if (in_array($hakAkses, [44, 45]))
                                    <button type="button" class="btn btn-primary" onclick="tambah()"
                                        style="margin-bottom: 15px">Tambah Data</button>
                                @endif
                                <table class="table table-bordered table-striped nowrap" style="width: 100%"
                                    id="cetakBuktiKasTable">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Perusahaan</th>
                                            <th>Sub Bagian Pembuat</th>
                                            {{-- <th>Nama Pembuat</th> --}}
                                            <th>Sub Bagian Pemeriksa</th>
                                            <th>Nama Pemeriksa Sub Bagian</th>
                                            <th>Bagian Pemeriksa</th>
                                            <th>Nama Pemeriksa Bagian</th>
                                            <th>Yang Menyetujui</th>
                                            <th>Nama Menyetujui</th>
                                            <th>Dari Bank</th>
                                            <th>Lebih Dari 5 Miliar</th>
                                            <th>Lebih Dari 25 Juta</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
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
                <form id="form-tambah">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tambah Data Master Cetak Bukti Kas</h4>
                    </div>
                    <div class="modal-body">
                        {{-- Flash Error --}}
                        <div id="flash-error" class="alert alert-danger" style="display: none;">
                            <ul id="flash-error-list"></ul>
                        </div>

                        <ul class="nav nav-tabs" id="tambah_tab" role="tablist">
                            <li class="nav-item active">
                                <a class="nav-link" id="tab-penandatangan-tab" data-toggle="tab" href="#tab-penandatangan"
                                    role="tab">Penandatangan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-lainnya-tab" data-toggle="tab" href="#tab-lainnya"
                                    role="tab">Lainnya</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="tab-penandatangan" role="tabpanel"
                                aria-labelledby="tab-penandatangan-tab">
                                <div class="form-group">
                                    <label for="company">
                                        Perusahaan
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="company" id="company" required>
                                        <option value="" disabled selected>Pilih Perusahaan</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->company_id }}">{{ $company->company_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="company_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="sub_bagian_pembuat">
                                        Sub Bagian Pembuat
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="sub_bagian_pembuat" name="sub_bagian_pembuat"
                                        class="form-control" placeholder="Contoh: Sub Bagian Keuangan" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="sub_bagian_pembuat_error"></small>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="nama_pembuat">Nama Pembuat</label>
                                    <input type="text" id="nama_pembuat" name="nama_pembuat" class="form-control"
                                        placeholder="Contoh: John Doe" autocomplete="off">
                                    <small class="text-danger" id="nama_pembuat_error"></small>
                                </div> --}}
                                <div class="form-group">
                                    <label for="sub_bagian_pemeriksa">
                                        Sub Bagian Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="sub_bagian_pemeriksa" name="sub_bagian_pemeriksa"
                                        class="form-control" placeholder="Contoh: Kepala Sub Bagian Keuangan"
                                        autocomplete="off" required>
                                    <small class="text-danger" id="sub_bagian_pemeriksa_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="nama_pemeriksa">
                                        Nama Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="nama_pemeriksa" name="nama_pemeriksa" class="form-control"
                                        placeholder="Contoh: Jane Doe" autocomplete="off" required>
                                    <small class="text-danger" id="nama_pemeriksa_error"></small>

                                </div>
                                <div class="form-group">
                                    <label for="bagian_pemeriksa">
                                        Bagian Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="bagian_pemeriksa" name="bagian_pemeriksa"
                                        class="form-control" placeholder="Contoh: Kepala Bagian Akuntansi dan Keuangan"
                                        autocomplete="off" required>
                                    <small class="text-danger" id="bagian_pemeriksa_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="nama_bagian_pemeriksa">
                                        Nama Bagian Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="nama_bagian_pemeriksa" name="nama_bagian_pemeriksa"
                                        class="form-control" placeholder="Contoh: James Smith" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="nama_bagian_pemeriksa_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="yang_menyetujui">
                                        Yang Menyetujui
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="yang_menyetujui" name="yang_menyetujui"
                                        class="form-control" placeholder="Contoh: Region Head" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="yang_menyetujui_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="nama_yang_menyetujui">
                                        Nama Penyetuju
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="nama_yang_menyetujui" name="nama_yang_menyetujui"
                                        class="form-control" placeholder="Contoh: Robert Brown" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="nama_yang_menyetujui_error"></small>

                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-lainnya" role="tabpanel"
                                aria-labelledby="tab-lainnya-tab">
                                <div class="form-group">
                                    <label for="is_bank">
                                        Apakah digunakan kas atau bank?
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="is_bank" id="is_bank" required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="0">Kas</option>
                                        <option value="1">Bank</option>
                                    </select>
                                    <small class="text-danger" id="is_bank_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="lebih_dari_5_m">
                                        Apakah digunakan untuk dana yang lebih dari 5 Miliar?
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="lebih_dari_5_m" id="lebih_dari_5_m" required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <small class="text-danger" id="lebih_dari_5_m_error"></small>

                                </div>
                                <div class="form-group">
                                    <label for="lebih_dari_25_jt">
                                        Apakah digunakan untuk dana yang lebih dari 25 Juta?
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="lebih_dari_25_jt" id="lebih_dari_25_jt" required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <small class="text-danger" id="lebih_dari_25_jt_error"></small>
                                </div>
                            </div>
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
                <form id="form-ubah">
                    @csrf
                    <input type="hidden" id="ubah_id" name="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Ubah Data Master Cetak Bukti Kas</h4>
                    </div>
                    <div class="modal-body">
                        {{-- Flash Error --}}
                        <div id="flash-ubah-error" class="alert alert-danger" style="display: none;">
                            <ul id="flash-ubah-error-list"></ul>
                        </div>

                        <ul class="nav nav-tabs" id="ubah_tab" role="tablist">
                            <li class="nav-item active">
                                <a class="nav-link" id="tab-ubah-penandatangan-tab" data-toggle="tab"
                                    href="#tab-ubah-penandatangan" role="tab">Penandatangan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-ubah-lainnya-tab" data-toggle="tab" href="#tab-ubah-lainnya"
                                    role="tab">Lainnya</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="tab-ubah-penandatangan" role="tabpanel"
                                aria-labelledby="tab-ubah-penandatangan-tab">
                                <div class="form-group">
                                    <label for="ubah_company_id">
                                        Perusahaan
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="ubah_company_id" id="ubah_company_id" required>
                                        <option disabled selected>Pilih Perusahaan</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->company_id }}">{{ $company->company_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="ubah_company_id_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_sub_bagian_pembuat">
                                        Sub Bagian Pembuat
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_sub_bagian_pembuat" name="ubah_sub_bagian_pembuat"
                                        class="form-control" placeholder="Contoh: Sub Bagian Keuangan" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="ubah_sub_bagian_pembuat_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_nama_pembuat">Nama Pembuat</label>
                                    <input type="text" id="ubah_nama_pembuat" name="ubah_nama_pembuat"
                                        class="form-control" placeholder="Contoh: John Doe" autocomplete="off">
                                    <small class="text-danger" id="ubah_nama_pembuat_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_sub_bagian_pemeriksa">
                                        Sub Bagian Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_sub_bagian_pemeriksa" name="ubah_sub_bagian_pemeriksa"
                                        class="form-control" placeholder="Contoh: Kepala Sub Bagian Keuangan"
                                        autocomplete="off" required>
                                    <small class="text-danger" id="ubah_sub_bagian_pemeriksa_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_nama_pemeriksa">
                                        Nama Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_nama_pemeriksa" name="ubah_nama_pemeriksa"
                                        class="form-control" placeholder="Contoh: Jane Doe" autocomplete="off" required>
                                    <small class="text-danger" id="ubah_nama_pemeriksa_error"></small>

                                </div>
                                <div class="form-group">
                                    <label for="ubah_bagian_pemeriksa">
                                        Bagian Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_bagian_pemeriksa" name="ubah_bagian_pemeriksa"
                                        class="form-control" placeholder="Contoh: Kepala Bagian Akuntansi dan Keuangan"
                                        autocomplete="off" required>
                                    <small class="text-danger" id="ubah_bagian_pemeriksa_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_nama_bagian_pemeriksa">
                                        Nama Bagian Pemeriksa
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_nama_bagian_pemeriksa"
                                        name="ubah_nama_bagian_pemeriksa" class="form-control"
                                        placeholder="Contoh: James Smith" autocomplete="off" required>
                                    <small class="text-danger" id="ubah_nama_bagian_pemeriksa_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_yang_menyetujui">
                                        Yang Menyetujui
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_yang_menyetujui" name="ubah_yang_menyetujui"
                                        class="form-control" placeholder="Contoh: Region Head" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="ubah_yang_menyetujui_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_nama_yang_menyetujui">
                                        Nama Penyetuju
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ubah_nama_yang_menyetujui" name="ubah_nama_yang_menyetujui"
                                        class="form-control" placeholder="Contoh: Robert Brown" autocomplete="off"
                                        required>
                                    <small class="text-danger" id="ubah_nama_yang_menyetujui_error"></small>

                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-ubah-lainnya" role="tabpanel"
                                aria-labelledby="tab-ubah-lainnya-tab">
                                <div class="form-group">
                                    <label for="ubah_is_bank">
                                        Apakah digunakan kas atau bank?
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="ubah_is_bank" id="ubah_is_bank" required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="0">Kas</option>
                                        <option value="1">Bank</option>
                                    </select>
                                    <small class="text-danger" id="ubah_is_bank_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="ubah_lebih_dari_5_m">
                                        Apakah digunakan untuk dana yang lebih dari 5 Miliar?
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="ubah_lebih_dari_5_m" id="ubah_lebih_dari_5_m"
                                        required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <small class="text-danger" id="ubah_lebih_dari_5_m_error"></small>

                                </div>
                                <div class="form-group">
                                    <label for="ubah_lebih_dari_25_jt">
                                        Apakah digunakan untuk dana yang lebih dari 25 Juta?
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="ubah_lebih_dari_25_jt" id="ubah_lebih_dari_25_jt"
                                        required>
                                        <option value="" disabled selected>Pilih salah satu</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <small class="text-danger" id="ubah_lebih_dari_25_jt_error"></small>
                                </div>
                            </div>
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

    <script>
        function tambah() {
            $("#modal_tambah").modal('show');
        }

        function edit(id) {
            $.ajax({
                url: "{{ route('admin.cetakbuktikas.getDataById', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    var data = response.data;
                    if (response.success) {
                        $('#ubah_id').val(data.id);
                        $('#ubah_company_id').val(data.company_id);
                        $('#ubah_sub_bagian_pembuat').val(data.dibuat_sub_bagian);
                        $('#ubah_nama_pembuat').val(data.dibuat_sub_bagian_nama);
                        $('#ubah_sub_bagian_pemeriksa').val(data.diperiksa_oleh_sub_bagian);
                        $('#ubah_nama_pemeriksa').val(data.diperiksa_oleh_sub_bagian_nama);
                        $('#ubah_bagian_pemeriksa').val(data.diperiksa_oleh_bagian);
                        $('#ubah_nama_bagian_pemeriksa').val(data.diperiksa_oleh_bagian_nama);
                        $('#ubah_yang_menyetujui').val(data.disetujui_oleh);
                        $('#ubah_nama_yang_menyetujui').val(data.disetujui_oleh_nama);
                        $('#ubah_is_bank').val(data.is_bank);
                        $('#ubah_lebih_dari_5_m').val(data.lebih_dari_5_m);
                        $('#ubah_lebih_dari_25_jt').val(data.lebih_dari_25_jt);
                        $('#modal_ubah').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data gagal ditambahkan',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Data gagal dimuat',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
        }

        function destroy(id) {
            Swal.fire({
                title: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.cetakbuktikas.destroy', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                fetchData();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil dihapus',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                console.error(response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data gagal dihapus',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal dihapus',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    })
                }
            })
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fetchData() {
            $.ajax({
                url: "{{ route('admin.cetakbuktikas.getdata') }}",
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var table = $('#cetakBuktiKasTable').DataTable();
                    table.clear();
                    $.each(data, function(index, item) {
                        table.row.add([
                            index + 1,
                            item.company.company_nama,
                            item.dibuat_sub_bagian,
                            // item.dibuat_sub_bagian_nama,
                            item.diperiksa_oleh_sub_bagian,
                            item.diperiksa_oleh_sub_bagian_nama,
                            item.diperiksa_oleh_bagian,
                            item.diperiksa_oleh_bagian_nama,
                            item.disetujui_oleh,
                            item.disetujui_oleh_nama,
                            item.is_bank == 1 ? 'Ya' : 'Tidak',
                            item.lebih_dari_5_m == 1 ? 'Ya' : 'Tidak',
                            item.lebih_dari_25_jt == 1 ? 'Ya' : 'Tidak',
                            '<button type="button" class="btn btn-warning btn-sm" onclick="edit(' +
                            item.id + ')" title="Edit Data">' +
                            '<i class="fa fa-pencil" aria-hidden="true"></i> Edit' +
                            '</button> ' +
                            '<button  type="button" class="btn btn-danger btn-sm" onclick="destroy(' +
                            item.id + ')" title="Hapus Data">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i> Hapus' +
                            '</button>'
                        ]).draw();
                    });
                }
            });
        }

        function destroy(id) {
            Swal.fire({
                title: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.cetakbuktikas.destroy', ':id') }}".replace(':id',
                            id),
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                fetchData();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil dihapus',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                console.error(response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data gagal dihapus',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal dihapus',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    })
                }
            })
        }

        $(document).ready(function() {
            fetchData();

            function toggleRequiredAttributes() {
                if ($('#tab-penandatangan').hasClass('active')) {
                    // Remove required attributes from tab "Lainnya"
                    $('#tab-lainnya').find('input, select').prop('required', false);
                    // Add required attributes to tab "Penandatangan"
                    $('#tab-penandatangan').find('input, select').prop('required', true);
                } else {
                    // Add required attributes to tab "Lainnya"
                    $('#tab-lainnya').find('input, select').prop('required', true);
                    // Remove required attributes from tab "Penandatangan"
                    $('#tab-penandatangan').find('input, select').prop('required', false);
                }
            }

            // Initial call to ensure the correct tab has required attributes
            toggleRequiredAttributes();

            $('#tambah_tab a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                toggleRequiredAttributes();
            });

            $('#form-tambah').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.cetakbuktikas.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#form-tambah').trigger('reset');
                            $('#modal_tambah').modal('hide');
                            fetchData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil ditambahkan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal ditambahkan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr) {
                        $('small.text-danger').text('');
                        $('#flash-error').hide();
                        $('#flash-error-list').empty();

                        var errors = xhr.responseJSON.errors;

                        if (errors) {
                            $.each(errors, function(key, value) {
                                $('#' + key + '_error').text(value[0]);
                            });
                        } else {
                            console.log(xhr.responseJSON);
                            $('#flash-error').show();
                            $('#flash-error-list').append(
                                "<li>Gagal menyimpan data, silakan coba lagi.</li>"
                            );
                        }
                    }
                })
            })

            $('#form-ubah').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var id = $('#ubah_id').val();
                $.ajax({
                    url: "{{ route('admin.cetakbuktikas.update', ':id') }}".replace(':id', id),
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#form-ubah').trigger('reset');
                            $('#modal_ubah').modal('hide');
                            fetchData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil diubah',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            console.error(response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal diubah',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr) {
                        $('small.text-danger').text('');
                        $('#flash-error').hide();
                        $('#flash-error-list').empty();

                        var errors = xhr.responseJSON.errors;

                        if (errors) {
                            $.each(errors, function(key, value) {
                                $('#' + key + '_error').text(value[0]);
                            });
                        } else {
                            console.log(xhr.responseJSON);
                            $('#flash-error').show();
                            $('#flash-error-list').append(
                                "<li>Gagal menyimpan data, silakan coba lagi.</li>"
                            );
                        }
                    }
                })
            })
        })
    </script>
@endsection
