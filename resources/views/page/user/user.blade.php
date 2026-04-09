@extends('template.master')
@section('title', 'Main')
@section('konten')

    <!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">User</h3>
                <div class="row">
                    <div class="col-md-12">
                        <!-- TABLE -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tabel User</h3>
                            </div>
                            <div class="panel-body">
                                <button type="button" class="btn btn-primary" onclick="tambah()"
                                    style="margin-bottom: 15px">Tambah Data</button>
                                <table class="table table-bordered table-striped nowrap" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Username</th>
                                            <th>Company</th>
                                            <th>Bagian</th>
                                            <th>Hak Akses</th>
                                            <th>Email</th>
                                            <th>Nomor Handphone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->master_user_name }}</td>
                                                <td>{{ $value->company_nama }}</td>
                                                <td>{{ $value->master_bagian_nama }}</td>
                                                <td>{{ $value->master_hak_akses_nama }}</td>
                                                <td>{{ $value->user_emails }}</td>
                                                <td>{{ $value->nomor_handphone }}</td>
                                                <td>{{ $value->master_user_status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        onclick="ubah({{ json_encode($value) }}, '{{ $value->master_user_password_decrypt }}')"
                                                        title="Edit Data"><i class="fa fa-pencil" aria-hidden="true"></i>
                                                        Ubah Data</button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="hapus({{ $value->master_user_id }}, {{ $value->master_user_status }})"
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
                <form action="{{ url('') }}/user/store" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Tambah Data User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="text" id="tambah_username" name="username" class="form-control"
                                    placeholder="Username" autocomplete="off" required pattern="[A-Za-z0-9_]+">
                            </div>
                            <span id="alert_username">Anda dapat menggunakan huruf (a-z), angka (0-9), dan underscore (_),
                                Contoh : admin_12</span>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input type="password" id="tambah_password" name="password" class="form-control"
                                    placeholder="Password" minlength="4" autocomplete="new-password" required>
                            </div>
                        </div>
                        {{-- <div class="form-group">
            <label>Password Confirmation</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-key"></i></span>
              <input type="password" id="tambah_password_confirmation" class="form-control" placeholder="Password Confirmation" minlength="4" autocomplete="new-password" required>
            </div>
          </div> --}}
                        <div class="form-group">
                            <label>Company</label>
                            <select class="form-control" id="tambah_company" name="company" required>
                                <option value="" selected disabled>-- Pilih Company --</option>
                                @foreach ($company as $key => $value)
                                    <option value="{{ $value->company_id }}">{{ $value->company_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bagian</label>
                            <select class="form-control" id="tambah_bagian" name="bagian" required>
                                <option value="" selected disabled>-- Pilih Bagian --</option>
                                {{-- @foreach ($bagian as $key => $value)
                                    <option value="{{ $value->master_bagian_id }}">{{ $value->master_bagian_nama }}
                                    </option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hak Akses</label>
                            <select class="form-control" id="tambah_hak_akses" name="hak_akses" required>
                                <option value="" selected disabled>-- Pilih Hak Akses --</option>
                                @foreach ($hak_akses as $key => $value)
                                    <option value="{{ $value->master_hak_akses_id }}">{{ $value->master_hak_akses_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input type="text" id="email" name="email" class="form-control" placeholder="Email"
                                    minlength="4" autocomplete=off required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nomor Handphone</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control"
                                    placeholder="Nomor Handphone" minlength="4" autocomplete=off required>
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
                <form action="{{ url('') }}/user/update" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" id="ubah_id" name="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Ubah Data User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <div class="input-group">
                                <span class="input-group-addon" id="group_ubah_username"><i
                                        class="fa fa-user"></i></span>
                                <input type="text" id="ubah_username" name="username" class="form-control"
                                    placeholder="Username" value="Akuntansi" autocomplete="off" required
                                    pattern="[A-Za-z0-9_]+">
                            </div>
                            <span id="alert_username">Anda dapat menggunakan huruf (a-z), angka (0-9), dan underscore (_),
                                Contoh : admin_12</span>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input type="password" id="ubah_password" name="password" class="form-control"
                                    placeholder="Password" value="Akuntansi" minlength="4" autocomplete="new-password"
                                    required>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <label>Password Confirmation</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input type="password" id="ubah_password_confirmation" class="form-control" placeholder="Password Confirmation" value="Akuntansi" minlength="4" autocomplete="new-password" required>
                            </div>
                        </div> --}}
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
                            <label>Bagian</label>
                            <select class="form-control" id="ubah_bagian" name="bagian" required>
                                <option value="" selected disabled>-- Pilih Bagian --</option>
                                @foreach ($bagian as $key => $value)
                                    <option value="{{ $value->master_bagian_id }}">{{ $value->master_bagian_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hak Akses</label>
                            <select class="form-control" id="ubah_hak_akses" name="hak_akses" required>
                                <option value="" selected disabled>-- Pilih Hak Akses --</option>
                                @foreach ($hak_akses as $key => $value)
                                    <option value="{{ $value->master_hak_akses_id }}">{{ $value->master_hak_akses_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input type="text" id="ubah_email" name="email" class="form-control"
                                    placeholder="Email" minlength="4" autocomplete=off required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nomor Handphone</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                <input type="text" id="ubah_nomor_hp" name="nomor_hp" class="form-control"
                                    placeholder="Nomor Handphone" minlength="4" autocomplete=off required>
                            </div>
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
        function tambah() {
            $("#modal_tambah").modal('show');
        }

        function isiBagian(companyId, targetSelect, selectedValue = null) {
            targetSelect.empty();
            targetSelect.append('<option value="" selected disabled>-- Pilih Bagian --</option>');
            $.ajax({
                type: "GET",
                url: "{{ url('') }}/user/getBagianByCompany/" + companyId,
                success: function(data) {
                    $.each(data, function(key, value) {
                        let option = $('<option></option>')
                            .attr('value', value.master_bagian_id)
                            .text(value.master_bagian_nama);

                        if (value.master_bagian_id == selectedValue) {
                            option.attr('selected', 'selected');
                        }

                        targetSelect.append(option);
                    });
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }

        function ubah(data, password) {
            $("#ubah_id").val(data.master_user_id);
            $("#ubah_username").val(data.master_user_name);
            $("#ubah_password").val(password);
            $("#ubah_email").val(data.user_emails);
            $("#ubah_nomor_hp").val(data.nomor_handphone);
            $("#ubah_company").val(data.company_id);
            $("#ubah_hak_akses").val(data.master_hak_akses_id);
            isiBagian(data.company_id, $("#ubah_bagian"), data.master_bagian_id);

            $("#modal_ubah").modal('show');
        }

        function hapus(id, status) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Menghapus Data User!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus Data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `{{ url('') }}/user/destroy/${id}/${status}`;
                }
            })
        }

        function validate_username() {
            var username = $("#ubah_username").val();
            if (username.includes(" ")) {
                document.getElementById('alert_username').innerHTML =
                    'Maaf, hanya huruf (a-z), angka (0-9), dan underscore (_) yang diizinkan';
                document.getElementById('alert_username').style.cssText = "color:red";
                document.getElementById('group_ubah_username').style.cssText = "border-color:red";
                document.getElementById('ubah_username').style.cssText = "border-color:red";

            } else {
                document.getElementById('alert_username').innerHTML =
                    'Anda dapat menggunakan huruf (a-z), angka (0-9), dan underscore (_)';
                document.getElementById('alert_username').style.cssText = "color:light-grey";
                document.getElementById('group_ubah_username').style.cssText = "border-color:light-grey";
                document.getElementById('ubah_username').style.cssText = "border-color:light-grey";

            }
        }

        $('#ubah_company').on('change', function() {
            var companyId = $(this).val();
            var targetSelect = $('#ubah_bagian');
            isiBagian(companyId, targetSelect);
        });

        $('#tambah_company').on('change', function() {
            var companyId = $(this).val();
            var targetSelect = $('#tambah_bagian');
            isiBagian(companyId, targetSelect);
        });
    </script>

@endsection
