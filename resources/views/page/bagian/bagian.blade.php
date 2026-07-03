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
                <h3 class="page-title">Bagian</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tabel Bagian</h3>
                            </div>
                            <div class="panel-body">
                                @if ($hakakses != 45)
                                    <button type="button" class="btn btn-primary" onclick="tambah()"
                                        style="margin-bottom: 15px">Tambah Data</button>
                                @endif

                                @if ($hakakses == 45)
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#tab_regional" aria-controls="tab_regional" role="tab" data-toggle="tab">Bagian Regional</a></li>
                                        <li role="presentation"><a href="#tab_unit" aria-controls="tab_unit" role="tab" data-toggle="tab">Unit / Kebun</a></li>
                                    </ul>

                                    <div class="tab-content" style="padding-top: 15px;">
                                        <div role="tabpanel" class="tab-pane active" id="tab_regional">
                                            <div class="table-responsive">
                                            <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>No. </th>
                                                        <th>Nama Bagian</th>
                                                        <th>Perusahaan</th>
                                                        <th>Kode Bagian</th>
                                                        <th>Nama Kepala Bagian </th>
                                                        <th>Jabatan</th>
                                                        <th>Keterangan</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($bagian_regional as $key => $value)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $value->master_bagian_nama }}</td>
                                                            <td>{{ $value->company_nama }}</td>
                                                            <td>{{ $value->master_bagian_kode }}</td>
                                                            <td>{{ $value->master_bagian_kepala_bagian }} </td>
                                                            <td>{{ $value->master_bagian_jabatan }} </td>
                                                            <td>{{ $value->master_bagian_keterangan }}</td>
                                                            <td>{{ $value->master_bagian_status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-warning btn-sm"
                                                                    onclick="ubah({{ json_encode($value) }})" title="Edit Data"><i
                                                                        class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="hapus({{ $value->master_bagian_id }}, {{ $value->master_bagian_status }})"
                                                                    title="Hapus Data"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                                                    Ubah Status</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="tab_unit">
                                            <div class="table-responsive">
                                            <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>No. </th>
                                                        <th>Nama Bagian</th>
                                                        <th>Perusahaan</th>
                                                        <th>Kode Bagian</th>
                                                        <th>Nama Kepala Bagian </th>
                                                        <th>Jabatan</th>
                                                        <th>Keterangan</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($bagian_unit as $key => $value)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $value->master_bagian_nama }}</td>
                                                            <td>{{ $value->company_nama }}</td>
                                                            <td>{{ $value->master_bagian_kode }}</td>
                                                            <td>{{ $value->master_bagian_kepala_bagian }} </td>
                                                            <td>{{ $value->master_bagian_jabatan }} </td>
                                                            <td>{{ $value->master_bagian_keterangan }}</td>
                                                            <td>{{ $value->master_bagian_status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-warning btn-sm"
                                                                    onclick="ubah({{ json_encode($value) }})" title="Edit Data"><i
                                                                        class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="hapus({{ $value->master_bagian_id }}, {{ $value->master_bagian_status }})"
                                                                    title="Hapus Data"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                                                    Ubah Status</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                <div class="table-responsive">
                                <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Nama Bagian</th>
                                            <th>Perusahaan</th>
                                            <th>Kode Bagian</th>
                                            <th>Nama Kepala Bagian </th>
                                            <th>Jabatan</th>
                                            <th>Keterangan</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bagian as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->master_bagian_nama }}</td>
                                                <td>{{ $value->company_nama }}</td>
                                                <td>{{ $value->master_bagian_kode }}</td>
                                                <td>{{ $value->master_bagian_kepala_bagian }} </td>
                                                <td>{{ $value->master_bagian_jabatan }} </td>
                                                <td>{{ $value->master_bagian_keterangan }}</td>
                                                <td>{{ $value->master_bagian_status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="ubah({{ json_encode($value) }})" title="Edit Data"><i
                                                            class="fa fa-pencil" aria-hidden="true"></i> Ubah Data</button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="hapus({{ $value->master_bagian_id }}, {{ $value->master_bagian_status }})"
                                                        title="Hapus Data"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                                        Ubah Status</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                                @endif
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
                <form action="{{ url('') }}/bagian/store" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Tambah Data Bagian</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Bagian</label>
                            <input type="text" id="tambah_nama" name="nama" class="form-control"
                                placeholder="Nama Bagian" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <select class="form-control" name="company" id="company_select">
                                <option value="" disabled selected>Pilih Perusahaan</option>
                                @foreach ($data_company as $c)
                                    <option value="{{ $c->company_id }}">{{ $c->company_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kode Bagian</label>
                            <input type="text" id="tambah_kode" name="kode" class="form-control"
                                placeholder="Kode Bagian" autocomplete="off" required>
                        </div>
                        <div class="form-group dropdown">
                            <label>Nama Kepala Bagian</label>
                            <input type="text" id="tambah_kabag" name="kepala_bagian" class="form-control"
                                placeholder="Kepala Bagian" autocomplete="off">
                        </div>
                        <div class="form-group dropdown">
                            <label>Jabatan</label>
                            <input type="text" id="tambah_jabatan" name="jabatan" class="form-control"
                                placeholder="Jabatan" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
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
                <form action="{{ url('') }}/bagian/update" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" id="ubah_id" name="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Ubah Data Bagian</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Bagian</label>
                            <input type="text" id="ubah_nama" name="nama" class="form-control"
                                placeholder="Nama Bagian" autocomplete="off"
                                {{ $hakakses == 45 ? 'readonly' : 'required' }}>
                        </div>
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <select class="form-control" id="ubah_company" name="company"
                                data-readonly="{{ $hakakses == 45 ? 'true' : 'false' }}">
                                <option value="" disabled selected>Pilih Perusahaan</option>
                                @foreach ($data_company as $c)
                                    <option value="{{ $c->company_id }}">{{ $c->company_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kode Bagian</label>
                            <input type="text" id="ubah_kode" name="kode" class="form-control"
                                placeholder="Kode Bagian" autocomplete="off"
                                {{ $hakakses == 45 ? 'readonly' : 'required' }}>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Nama Kepala Bagian</label>
                            <input type="text" id="ubah_kabag" name="kepala_bagian" class="form-control"
                                placeholder="Kepala Bagian" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Jabatan</label>
                            <input type="text" id="ubah_jabatan" name="jabatan" class="form-control"
                                placeholder="Jabatan" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" id="ubah_keterangan" name="keterangan" class="form-control"
                                placeholder="Keterangan" autocomplete="off"
                                {{ $hakakses == 45 ? 'readonly' : 'required' }}>
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

        function kabagselect(kabag) {
            $('#myInput').val(kabag);
            document.getElementById("myDropdown").classList.toggle("show", false);
        }

        function ubahkabagselect(kabag) {
            $('#ubah_kabag').val(kabag);
            document.getElementById("myDropdownubah").classList.toggle("show", false);
        }

        function tambah() {
            $("#modal_tambah").modal('show');
        }

        function ubah(data) {
            $("#ubah_id").val(data.master_bagian_id);
            $("#ubah_kode").val(data.master_bagian_kode);
            $("#ubah_nama").val(data.master_bagian_nama);
            $("#ubah_company").val(data.company_id);
            $("#ubah_keterangan").val(data.master_bagian_keterangan);
            $("#ubah_kabag").val(data.master_bagian_kepala_bagian);
            $("#ubah_jabatan").val(data.master_bagian_jabatan);
            $("#modal_ubah").modal('show');
        }

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
                text: "Menghapus Data Bagian!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus Data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `{{ url('') }}/bagian/destroy/${id}/${status}`;
                }
            })
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });
    </script>

@endsection
