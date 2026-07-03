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
                                @if (in_array($hakAkses, [1, 44]))
                                    <button type="button" class="btn btn-primary" onclick="tambah()"
                                        style="margin-bottom: 15px"><i class="fa fa-plus-circle"></i> Tambah Konfigurasi</button>
                                @endif
                                <table class="table table-bordered table-striped nowrap" style="width: 100%"
                                    id="cetakBuktiKasTable">
                                    <thead>
                                        <tr class="bg-primary text-white">
                                            <th style="width: 5%; text-align: center;">No.</th>
                                            <th style="width: 45%;">Perusahaan</th>
                                            <th style="width: 25%; text-align: center;">Status Konfigurasi</th>
                                            <th style="width: 25%; text-align: center;">Action</th>
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
        <div class="modal-dialog modal-lg" style="width: 95%;">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="form-tambah">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tambah Konfigurasi Cetak Bukti Kas</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company">Perusahaan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="company" id="company" required>
                                        <option value="" disabled selected>Pilih Perusahaan</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->company_id }}">{{ $company->company_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-bottom: 10px; padding: 10px;">
                            <i class="fa fa-info-circle"></i> <b>Tips:</b> Isi baris pertama, lalu gunakan tombol salin (<i class="fa fa-copy"></i>) untuk mengisi semua skenario sekaligus.
                        </div>

                        <div class="table-responsive" id="tambah-table-container">
                            <!-- Table injected via JS to prevent auto-datatable init -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan Data</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Modal Tambah Data --}}

    {{-- Modal Ubah Data --}}
    <div id="modal_ubah" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width: 95%;">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="form-ubah">
                    @csrf
                    <input type="hidden" name="company_id" id="ubah_company_id_val">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Bulk Update Master Cetak Bukti Kas: <span id="ubah_company_name" class="text-primary"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" style="margin-bottom: 10px; padding: 10px;">
                            <i class="fa fa-info-circle"></i> <b>Tips:</b> Isi baris pertama, lalu klik tombol <i class="fa fa-copy"></i> untuk menyalin data ke semua skenario di bawahnya.
                        </div>
                        <div class="table-responsive" id="edit-table-container">
                            <!-- Table injected via JS to prevent auto-datatable init -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan Semua Perubahan</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Modal Ubah Data --}}

    {{-- Modal Detail Data --}}
    <div id="modal_detail" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detail Konfigurasi: <span id="detail_company_name" class="text-primary"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" id="detail-table-container">
                        <!-- Table injected via JS to prevent auto-datatable init -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End Modal Detail Data --}}

    <script>
        function tambah() {
            const types = [{
                    b: 0,
                    m5: 0,
                    j25: 0,
                    label: 'Kas (<= 25jt)'
                },
                {
                    b: 0,
                    m5: 0,
                    j25: 1,
                    label: 'Kas (> 25jt)'
                },
                {
                    b: 1,
                    m5: 0,
                    j25: 0,
                    label: 'Bank (<= 5M)'
                },
                {
                    b: 1,
                    m5: 1,
                    j25: 0,
                    label: 'Bank (> 5M)'
                }
            ];

            var html = `
                <table class="table table-bordered table-condensed" style="font-size: 11px;">
                    <thead>
                        <tr class="bg-primary">
                            <th style="width: 140px; vertical-align: middle; text-align: center;">SKENARIO</th>
                            <th style="vertical-align: middle; text-align: center;">PEMBUAT (Sub Bagian)</th>
                            <th style="vertical-align: middle; text-align: center;">PEMERIKSA (Sub Bagian)</th>
                            <th style="vertical-align: middle; text-align: center;">PEMERIKSA (Bagian)</th>
                            <th style="vertical-align: middle; text-align: center;">PENYETUJU</th>
                            <th style="width: 40px; vertical-align: middle; text-align: center;">COPY</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            types.forEach((t, i) => {
                html += `
                <tr class="tambah-scenario-row">
                    <td style="vertical-align: middle;">
                        <b>${t.label}</b>
                        <input type="hidden" name="scenarios[${i}][is_bank]" value="${t.b}">
                        <input type="hidden" name="scenarios[${i}][lebih_dari_5_m]" value="${t.m5}">
                        <input type="hidden" name="scenarios[${i}][lebih_dari_25_jt]" value="${t.j25}">
                    </td>
                    <td>
                        <input type="text" name="scenarios[${i}][dibuat_sub_bagian]" class="form-control input-sm" placeholder="Jabatan" style="margin-bottom: 5px;">
                        <input type="text" name="scenarios[${i}][dibuat_sub_bagian_nama]" class="form-control input-sm" placeholder="Nama">
                    </td>
                    <td>
                        <input type="text" name="scenarios[${i}][diperiksa_oleh_sub_bagian]" class="form-control input-sm" placeholder="Jabatan" style="margin-bottom: 5px;" required>
                        <input type="text" name="scenarios[${i}][diperiksa_oleh_sub_bagian_nama]" class="form-control input-sm" placeholder="Nama">
                    </td>
                    <td>
                        <input type="text" name="scenarios[${i}][diperiksa_oleh_bagian]" class="form-control input-sm" placeholder="Jabatan" style="margin-bottom: 5px;" required>
                        <input type="text" name="scenarios[${i}][diperiksa_oleh_bagian_nama]" class="form-control input-sm" placeholder="Nama">
                    </td>
                    <td>
                        <input type="text" name="scenarios[${i}][disetujui_oleh]" class="form-control input-sm" placeholder="Jabatan" style="margin-bottom: 5px;" required>
                        <input type="text" name="scenarios[${i}][disetujui_oleh_nama]" class="form-control input-sm" placeholder="Nama">
                    </td>
                    <td style="vertical-align: middle; text-align: center;">
                        ${i == 0 ? '<button type="button" class="btn btn-info btn-xs" onclick="copyToAllTambah()" title="Salin ke semua skenario"><i class="fa fa-copy"></i></button>' : ''}
                    </td>
                </tr>
                `;
            });
            html += `
                    </tbody>
                </table>
            `;
            $('#tambah-table-container').html(html);
            $("#modal_tambah").modal('show');
        }

        function copyToAllTambah() {
            var firstRow = $('.tambah-scenario-row').first();
            var inputs = firstRow.find('input[type="text"]');

            $('.tambah-scenario-row').each(function(i) {
                if (i > 0) {
                    var targetInputs = $(this).find('input[type="text"]');
                    inputs.each(function(j) {
                        $(targetInputs[j]).val($(this).val());
                    });
                }
            });
        }

        function edit(company_id) {
            $.ajax({
                url: "{{ url('cetak-bukti-kas/data-by-company') }}/" + company_id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var scenarios = response.data;
                        $('#ubah_company_id_val').val(response.company.company_id);
                        $('#ubah_company_name').text(response.company.company_nama);

                        // Define scenario types
                        const types = [{
                                b: 0,
                                m5: 0,
                                j25: 0,
                                label: 'Kas (<= 25jt)'
                            },
                            {
                                b: 0,
                                m5: 0,
                                j25: 1,
                                label: 'Kas (> 25jt)'
                            },
                            {
                                b: 1,
                                m5: 0,
                                j25: 0,
                                label: 'Bank (<= 5M)'
                            },
                            {
                                b: 1,
                                m5: 1,
                                j25: 0,
                                label: 'Bank (> 5M)'
                            }
                        ];

                        var html = `
                            <table class="table table-bordered table-condensed" style="font-size: 11px;">
                                <thead>
                                    <tr class="bg-primary">
                                        <th style="width: 140px; vertical-align: middle; text-align: center;">SKENARIO</th>
                                        <th style="vertical-align: middle; text-align: center;">PEMBUAT (Sub Bagian)</th>
                                        <th style="vertical-align: middle; text-align: center;">PEMERIKSA (Sub Bagian)</th>
                                        <th style="vertical-align: middle; text-align: center;">PEMERIKSA (Bagian)</th>
                                        <th style="vertical-align: middle; text-align: center;">PENYETUJU</th>
                                        <th style="width: 40px; vertical-align: middle; text-align: center;">COPY</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        types.forEach((t, i) => {
                            // Find existing data for this scenario
                            var s = scenarios.find(x => x.is_bank == t.b && x.lebih_dari_5_m == t
                                .m5 && x.lebih_dari_25_jt == t.j25);
                            if (!s) s = {}; // empty if not found

                            html += `
                            <tr class="scenario-row" data-index="${i}">
                                <td style="vertical-align: middle;">
                                    <b>${t.label}</b>
                                    <input type="hidden" name="scenarios[${i}][is_bank]" value="${t.b}">
                                    <input type="hidden" name="scenarios[${i}][lebih_dari_5_m]" value="${t.m5}">
                                    <input type="hidden" name="scenarios[${i}][lebih_dari_25_jt]" value="${t.j25}">
                                </td>
                                <td>
                                    <input type="text" name="scenarios[${i}][dibuat_sub_bagian]" class="form-control input-sm" value="${s.dibuat_sub_bagian || ''}" placeholder="Jabatan" style="margin-bottom: 5px;">
                                    <input type="text" name="scenarios[${i}][dibuat_sub_bagian_nama]" class="form-control input-sm" value="${s.dibuat_sub_bagian_nama || ''}" placeholder="Nama">
                                </td>
                                <td>
                                    <input type="text" name="scenarios[${i}][diperiksa_oleh_sub_bagian]" class="form-control input-sm" value="${s.diperiksa_oleh_sub_bagian || ''}" placeholder="Jabatan" style="margin-bottom: 5px;">
                                    <input type="text" name="scenarios[${i}][diperiksa_oleh_sub_bagian_nama]" class="form-control input-sm" value="${s.diperiksa_oleh_sub_bagian_nama || ''}" placeholder="Nama">
                                </td>
                                <td>
                                    <input type="text" name="scenarios[${i}][diperiksa_oleh_bagian]" class="form-control input-sm" value="${s.diperiksa_oleh_bagian || ''}" placeholder="Jabatan" style="margin-bottom: 5px;">
                                    <input type="text" name="scenarios[${i}][diperiksa_oleh_bagian_nama]" class="form-control input-sm" value="${s.diperiksa_oleh_bagian_nama || ''}" placeholder="Nama">
                                </td>
                                <td>
                                    <input type="text" name="scenarios[${i}][disetujui_oleh]" class="form-control input-sm" value="${s.disetujui_oleh || ''}" placeholder="Jabatan" style="margin-bottom: 5px;">
                                    <input type="text" name="scenarios[${i}][disetujui_oleh_nama]" class="form-control input-sm" value="${s.disetujui_oleh_nama || ''}" placeholder="Nama">
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    ${i == 0 ? '<button type="button" class="btn btn-info btn-xs" onclick="copyToAll()" title="Salin ke semua skenario"><i class="fa fa-copy"></i></button>' : ''}
                                </td>
                            </tr>
                            `;
                        });

                        html += `
                                </tbody>
                            </table>
                        `;

                        $('#edit-table-container').html(html);
                        $('#modal_ubah').modal('show');
                    }
                }
            });
        }

        function copyToAll() {
            var firstRow = $('.scenario-row').first();
            var inputs = firstRow.find('input[type="text"]');

            $('.scenario-row').each(function(i) {
                if (i > 0) {
                    var targetInputs = $(this).find('input[type="text"]');
                    inputs.each(function(j) {
                        $(targetInputs[j]).val($(this).val());
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
                        var statusHtml = '';
                        if(item.total_records > 5) {
                            statusHtml = '<span class="label label-danger cursor-pointer" onclick="viewDetail(' + item.company_id + ')" style="font-size: 11px; padding: 5px 10px; cursor: pointer;" title="Klik untuk lihat detail"><i class="fa fa-exclamation-circle"></i> Error (>5 Skenario/Data Sampah)</span>';
                        } else if(item.valid_scenarios == 4 || item.total_records == 5) {
                            statusHtml = '<span class="label label-success cursor-pointer" onclick="viewDetail(' + item.company_id + ')" style="font-size: 11px; padding: 5px 10px; cursor: pointer;" title="Klik untuk lihat detail"><i class="fa fa-check-circle"></i> Lengkap (4/4 Skenario)</span>';
                        } else if(item.valid_scenarios > 0) {
                            statusHtml = '<span class="label label-warning cursor-pointer" onclick="viewDetail(' + item.company_id + ')" style="font-size: 11px; padding: 5px 10px; cursor: pointer;" title="Klik untuk lihat detail"><i class="fa fa-exclamation-triangle"></i> Sebagian (' + item.valid_scenarios + '/4 Skenario)</span>';
                        } else {
                            statusHtml = '<span class="label label-danger" style="font-size: 11px; padding: 5px 10px;"><i class="fa fa-times-circle"></i> Belum Dikonfigurasi (0/4)</span>';
                        }

                        table.row.add([
                            '<div style="text-align: center;">' + (index + 1) + '</div>',
                            '<b style="font-size: 13px;"><i class="fa fa-building-o text-muted" style="margin-right: 8px;"></i>' + item.company_nama + '</b>',
                            '<div style="text-align: center;">' + statusHtml + '</div>',
                            '<div style="text-align: center;">' +
                            '<button type="button" class="btn btn-warning btn-sm" style="margin-right: 5px;" onclick="edit(' +
                            item.company_id + ')" title="Edit Data Semua Skenario">' +
                            '<i class="fa fa-pencil" aria-hidden="true"></i> Edit Bulk' +
                            '</button>' +
                            '<button type="button" class="btn btn-danger btn-sm" onclick="destroy(' +
                            item.company_id + ')" title="Hapus Semua Skenario">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i> Hapus' +
                            '</button>' +
                            '</div>'
                        ]).draw();
                    });
                }
            });
        }
        function viewDetail(company_id) {
            $.ajax({
                url: "{{ url('cetak-bukti-kas/data-by-company') }}/" + company_id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var scenarios = response.data;
                        $('#detail_company_name').text(response.company.company_nama);

                        const types = [{
                                b: 0,
                                m5: 0,
                                j25: 0,
                                label: 'Kas (<= 25jt)'
                            },
                            {
                                b: 0,
                                m5: 0,
                                j25: 1,
                                label: 'Kas (> 25jt)'
                            },
                            {
                                b: 1,
                                m5: 0,
                                j25: 0,
                                label: 'Bank (<= 5M)'
                            },
                            {
                                b: 1,
                                m5: 1,
                                j25: 0,
                                label: 'Bank (> 5M)'
                            }
                        ];

                        var html = `
                            <table class="table table-bordered table-condensed table-striped" style="font-size: 11px;">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th style="width: 140px; vertical-align: middle; text-align: center;">SKENARIO</th>
                                        <th style="vertical-align: middle; text-align: center;">PEMBUAT (Sub Bagian)</th>
                                        <th style="vertical-align: middle; text-align: center;">PEMERIKSA (Sub Bagian)</th>
                                        <th style="vertical-align: middle; text-align: center;">PEMERIKSA (Bagian)</th>
                                        <th style="vertical-align: middle; text-align: center;">PENYETUJU</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        types.forEach((t, i) => {
                            var s = scenarios.find(x => x.is_bank == t.b && x.lebih_dari_5_m == t.m5 && x.lebih_dari_25_jt == t.j25);
                            
                            var getVal = function(val) {
                                return val ? '<b>' + val + '</b>' : '<i class="text-muted">-Kosong-</i>';
                            };

                            if (!s) {
                                html += `
                                <tr>
                                    <td style="vertical-align: middle;"><b>${t.label}</b></td>
                                    <td colspan="4" class="text-center text-danger" style="padding: 15px;"><i>Belum Dikonfigurasi</i></td>
                                </tr>
                                `;
                            } else {
                                html += `
                                <tr>
                                    <td style="vertical-align: middle;"><b>${t.label}</b></td>
                                    <td>
                                        <div style="margin-bottom: 5px;">Jabatan: ${getVal(s.dibuat_sub_bagian)}</div>
                                        <div>Nama: ${getVal(s.dibuat_sub_bagian_nama)}</div>
                                    </td>
                                    <td>
                                        <div style="margin-bottom: 5px;">Jabatan: ${getVal(s.diperiksa_oleh_sub_bagian)}</div>
                                        <div>Nama: ${getVal(s.diperiksa_oleh_sub_bagian_nama)}</div>
                                    </td>
                                    <td>
                                        <div style="margin-bottom: 5px;">Jabatan: ${getVal(s.diperiksa_oleh_bagian)}</div>
                                        <div>Nama: ${getVal(s.diperiksa_oleh_bagian_nama)}</div>
                                    </td>
                                    <td>
                                        <div style="margin-bottom: 5px;">Jabatan: ${getVal(s.disetujui_oleh)}</div>
                                        <div>Nama: ${getVal(s.disetujui_oleh_nama)}</div>
                                    </td>
                                </tr>
                                `;
                            }
                        });

                        html += `
                                </tbody>
                            </table>
                        `;

                        $('#detail-table-container').html(html);
                        $('#modal_detail').modal('show');
                    }
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
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan data, silakan cek konsol.'
                        });
                    }
                })
            })

            $('#form-ubah').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var company_id = $('#ubah_company_id_val').val();
                $.ajax({
                    url: "{{ url('cetak-bukti-kas/update') }}/" + company_id,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#modal_ubah').modal('hide');
                            fetchData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan data, silakan cek konsol.'
                        });
                    }
                })
            })
        })
    </script>
@endsection
