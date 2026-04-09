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
            /* background-image: url('searchicon.png');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      background-position: 14px 12px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      background-repeat: no-repeat; */
            width: 565px;
            font-size: 14px;
            padding: 8px 14px 20px 12px 45px;
            border: none;
            border-bottom: 1px solid #ddd;
        }

        #myInput:focus {
            outline: 3px solid #ddd;
        }

        /* #ubah_kabag {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            box-sizing: border-box;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            /* background-image: url('searchicon.png');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      background-position: 14px 12px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      background-repeat: no-repeat; */
        width: 565px;
        font-size: 14px;
        padding: 8px 14px 20px 12px 45px;
        border: none;
        border-bottom: 1px solid #ddd;
        }

        */
        /* #ubah_kabag:focus {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            outline: 3px solid #ddd;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */

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
    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Master Cetak SPP</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tabel Master Cetak SPP</h3>
                            </div>
                            <div class="panel-body">
                                @if ($hakakses == 1 || $hakakses == 44 || $hakakses == 45)
                                    <button type="button" class="btn btn-primary" onclick="tambah()"
                                        style="margin-bottom: 15px">Tambah Data</button>
                                @endif
                                <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Perusahaan</th>
                                            <th>Pemeriksa Pertama</th>
                                            {{-- <th>Nama Pemeriksa Pertama</th> --}}
                                            <th>Pemeriksa Kedua</th>
                                            {{-- <th>Nama Pemeriksa Kedua</th> --}}
                                            <th>Pemeriksa Ketiga</th>
                                            {{-- <th>Nama Pemeriksa Ketiga</th> --}}
                                            <th>Yang Menyetujui</th>
                                            {{-- <th>Nama Penyetuju</th> --}}
                                            <th>Tujuan Kepada</th>
                                            <th>Tujuan Kepada SEVP</th>
                                            <th>Keterangan</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cetak_spp as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->company_nama }}</td>
                                                <td>{{ $value->diperiksa_oleh_1 }}</td>
                                                {{-- <td>{{ $value->diperiksa_oleh_1_nama }}</td> --}}
                                                <td>{{ $value->diperiksa_oleh_2 }}</td>
                                                {{-- <td>{{ $value->diperiksa_oleh_2_nama }}</td> --}}
                                                <td>{{ $value->diperiksa_oleh_3 }}</td>
                                                {{-- <td>{{ $value->diperiksa_oleh_3_nama }}</td> --}}
                                                <td>{{ $value->disetujui_oleh }}</td>
                                                {{-- <td>{{ $value->disetujui_oleh_nama }}</td> --}}
                                                <td>{{ $value->tujuan_kepada }}</td>
                                                <td>{{ $value->tujuan_kepada_sevp }}</td>
                                                <td>{{ $value->keterangan }}</td>
                                                <td>{{ $value->status == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="ubah({{ json_encode($value) }})" title="Edit Data"><i
                                                            class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="hapus({{ $value->id }}, {{ $value->status }})"
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
                <form action="{{ url('') }}/cetak_spp/store" id="form_tambah" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tambah Data Master Cetak SPP</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Perusahaan <span class="text-danger">*</label>
                            <select class="form-control" name="company" id="company_select">
                                {{-- <option value="" disabled selected>Pilih Perusahaan</span>
                                </option>
                                @foreach ($data_company as $c)
                                    <option value="{{ $c->company_id }}">{{ $c->company_nama }}</option>
                                @endforeach --}}
                                @if ($hakakses == 45)
                                    @foreach ($data_company as $c)
                                        <option value="{{ $c->company_id }}"
                                            {{ $companyId == $c->company_id ? 'selected' : '' }}>
                                            {{ $c->company_nama }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled selected>Pilih Perusahaan</option>
                                    @foreach ($data_company as $c)
                                        <option value="{{ $c->company_id }}">
                                            {{ $c->company_nama }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pemeriksa Pertama <span class="text-danger">*</label>
                            <input type="text" id="tambah_diperiksa_oleh_1" name="diperiksa_oleh_1" class="form-control"
                                placeholder="Contoh : Kepala Sub Bagian Anggaran" autocomplete="off" required>
                        </div>
                        {{-- <div class="form-group">
                            <label>Nama Pemeriksa Pertama <span class="text-danger">*</label>
                            <input type="text" id="tambah_diperiksa_oleh_1_nama" name="diperiksa_oleh_1_nama"
                                class="form-control" placeholder="Nama Pemeriksa Pertama" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label>Pemeriksa Kedua <span class="text-danger">*</label>
                            <input type="text" id="tambah_diperiksa_oleh_2" name="diperiksa_oleh_2" class="form-control"
                                placeholder="Contoh : Kepala Sub Bagian Akuntansi" autocomplete="off" required>
                        </div>
                        {{-- <div class="form-group">
                            <label>Nama Pemeriksa Kedua <span class="text-danger">*</label>
                            <input type="text" id="tambah_diperiksa_oleh_2_nama" name="diperiksa_oleh_2_nama"
                                class="form-control" placeholder="Nama Pemeriksa Kedua" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label>Pemeriksa Ketiga <span class="text-danger">*</label>
                            <input type="text" id="tambah_diperiksa_oleh_3" name="diperiksa_oleh_3" class="form-control"
                                placeholder="Contoh : Kepala Sub Bagian Pajak" autocomplete="off" required>
                        </div>
                        {{-- <div class="form-group">
                            <label>Nama Pemeriksa Ketiga <span class="text-danger">*</label>
                            <input type="text" id="tambah_diperiksa_oleh_3_nama" name="diperiksa_oleh_3_nama"
                                class="form-control" placeholder="Nama Pemeriksa Ketiga" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label>Yang Menyetujui <span class="text-danger">*</label>
                            <input type="text" id="tambah_disetujui_oleh" name="disetujui_oleh" class="form-control"
                                placeholder="Contoh : Kepala Bagian Keuangan dan Akuntansi" autocomplete="off" required>
                        </div>
                        {{-- <div class="form-group">
                            <label>Nama Penyetuju <span class="text-danger">*</label>
                            <input type="text" id="tambah_disetujui_oleh_nama" name="disetujui_oleh_nama"
                                class="form-control" placeholder="Nama Penyetuju" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label>Tujuan Kepada <span class="text-danger">*</label>
                            <input type="text" id="tambah_tujuan_kepada" name="tujuan_kepada" class="form-control"
                                placeholder="Contoh : Kepala Bagian Akuntansi dan Keuangan" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Tujuan Kepada SEVP<span class="text-danger">*</label>
                            <input type="text" id="tambah_tujuan_kepada_sevp" name="tujuan_kepada_sevp"
                                class="form-control" placeholder="Contoh : SEVP Business Support Regional 5"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan <span class="text-danger">*</label>
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
                <form id="form_ubah" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" id="ubah_id" name="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Ubah Master Data Cetak SPP</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Perusahaan <span class="text-danger">*</label>
                            <select class="form-control" name="company" id="ubah_company_select">
                                <option value="" disabled selected>Pilih Perusahaan</option>
                                @foreach ($data_company as $c)
                                    <option value="{{ $c->company_id }}">{{ $c->company_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pemeriksa Pertama <span class="text-danger">*</label>
                            <input type="text" id="ubah_diperiksa_oleh_1" name="diperiksa_oleh_1"
                                class="form-control" placeholder="Pemeriksa Pertama" autocomplete="off" required>
                            {{-- <span id="alert_username">Contoh : Kepala Sub Bagian Anggaran</span> --}}
                        </div>

                        {{-- <div class="form-group">
                            <label>Nama Pemeriksa Pertama <span class="text-danger">*</label>
                            <input type="text" id="ubah_diperiksa_oleh_1_nama" name="diperiksa_oleh_1_nama"
                                class="form-control" placeholder="Nama Pemeriksa Pertama" autocomplete="off" required>
                        </div> --}}

                        <div class="form-group">
                            <label>Pemeriksa Kedua <span class="text-danger">*</label>
                            <input type="text" id="ubah_diperiksa_oleh_2" name="diperiksa_oleh_2"
                                class="form-control" placeholder="Pemeriksa Kedua" autocomplete="off" required>
                        </div>

                        {{-- <div class="form-group">
                            <label>Nama Pemeriksa Kedua <span class="text-danger">*</label>
                            <input type="text" id="ubah_diperiksa_oleh_2_nama" name="diperiksa_oleh_2_nama"
                                class="form-control" placeholder="Nama Pemeriksa Kedua" autocomplete="off" required>
                        </div> --}}

                        <div class="form-group">
                            <label>Pemeriksa Ketiga <span class="text-danger">*</label>
                            <input type="text" id="ubah_diperiksa_oleh_3" name="diperiksa_oleh_3"
                                class="form-control" placeholder="Pemeriksa Ketiga" autocomplete="off" required>
                        </div>

                        {{-- <div class="form-group">
                            <label>Nama Pemeriksa Ketiga <span class="text-danger">*</label>
                            <input type="text" id="ubah_diperiksa_oleh_3_nama" name="diperiksa_oleh_3_nama"
                                class="form-control" placeholder="Nama Pemeriksa Ketiga" autocomplete="off" required>
                        </div> --}}

                        <div class="form-group">
                            <label>Yang Menyetujui <span class="text-danger">*</label>
                            <input type="text" id="ubah_disetujui_oleh" name="disetujui_oleh" class="form-control"
                                placeholder="Yang Menyetujui" autocomplete="off" required>
                        </div>

                        {{-- <div class="form-group">
                            <label>Nama Penyetuju <span class="text-danger">*</label>
                            <input type="text" id="ubah_disetujui_oleh_nama" name="disetujui_oleh_nama"
                                class="form-control" placeholder="Nama Penyetuju" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label>Tujuan Kepada <span class="text-danger">*</label>
                            <input type="text" id="ubah_tujuan_kepada" name="tujuan_kepada" class="form-control"
                                placeholder="Tujuan Kepada" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Tujuan Kepada SEVP <span class="text-danger">*</label>
                            <input type="text" id="ubah_tujuan_kepada_sevp" name="tujuan_kepada_sevp"
                                class="form-control" placeholder="Tujuan Kepada SEVP" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan <span class="text-danger">*</label>
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


    <!-- End Modal -->

    <script type="text/javascript">
        function myFunction() {
            document.getElementById("myDropdown").classList.toggle("show", true);
        }

        // function myFunctionubah() {
        //     document.getElementById("myDropdownubah").classList.toggle("show", true);
        // }



        function tambah() {
            $("#modal_tambah").modal('show');
        }

        function ubah(data) {
            $('#ubah_id').val(data.id);
            $('#ubah_company_select').val(data.company_id);
            $('#ubah_diperiksa_oleh_1').val(data.diperiksa_oleh_1);
            // $('#ubah_diperiksa_oleh_1_nama').val(data.diperiksa_oleh_1_nama);
            $('#ubah_diperiksa_oleh_2').val(data.diperiksa_oleh_2);
            // $('#ubah_diperiksa_oleh_2_nama').val(data.diperiksa_oleh_2_nama);
            $('#ubah_diperiksa_oleh_3').val(data.diperiksa_oleh_3);
            // $('#ubah_diperiksa_oleh_3_nama').val(data.diperiksa_oleh_3_nama);
            $('#ubah_disetujui_oleh').val(data.disetujui_oleh);
            // $('#ubah_disetujui_oleh_nama').val(data.disetujui_oleh_nama);
            $('#ubah_tujuan_kepada').val(data.tujuan_kepada);
            $('#ubah_tujuan_kepada_sevp').val(data.tujuan_kepada_sevp);
            $('#ubah_keterangan').val(data.keterangan);
            $("#modal_ubah").modal('show');
        }
        $('#form_ubah').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ url('') }}/cetak_spp/update",
                type: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil diubah!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                        $("#modal_ubah").modal('hide');
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Data gagal diubah. Silakan coba lagi!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        $('#form_tambah').on('submit', function(e) {
            e.preventDefault();

            // Buat objek FormData dari form yang di-submit
            var formData = new FormData(this);

            $.ajax({
                url: "{{ url('') }}/cetak_spp/store",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil ditambahkan!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                        $("#modal_tambah").modal('hide');
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Data gagal ditambahkan. Silakan coba lagi!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });


        if ($('#ubah_company').data('readonly') == true) {
            $('#ubah_company').on('mousedown', function(event) {
                event.preventDefault();
                // this.blur();
                // window.focus();
            });
        }

        function hapus(id, status) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Mengubah Status Data Cetak SPP!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ubah Status!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `{{ url('') }}/cetak_spp/destroy/${id}/${status}`;
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Status berhasil diubah!',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            })
        }
    </script>

@endsection
