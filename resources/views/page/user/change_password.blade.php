@extends('template.master')
@section('title', 'SPP | Ubah Password')
@section('kabag', 'active')
@section('header')
@endsection
@section('konten')

    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <!-- FORM SPP -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title" style="text-align:center; font-weight:bold">Ubah Password</h3>
                            </div>
                            <div class="panel-body">
                                <div id="panel_change_password">
                                    <form action="{{ url('') }}/user/change_password" method="post"
                                        enctype="multipart\form-data">

                                        {{ csrf_field() }}
                                        @if (\Session::has('alert'))
                                            <div class="alert alert-danger">
                                                <div>{{ Session::get('alert') }}</div>
                                            </div>
                                        @endif
                                        @if (\Session::has('alert-success'))
                                            <div class="alert alert-success">
                                                <div>{{ Session::get('alert-success') }}</div>
                                            </div>
                                        @endif
                                        <div class="form-group row col-sm-12">
                                            <input type="hidden" id="ubah_id" name="id"
                                                value="{{ $user->master_user_id }}">
                                            <div class="col-sm-6">
                                                <label>Password saat ini</label>
                                            </div>
                                            <div class="col-sm-6" style="text-align:right">
                                                <input type="text" id="current_password" name="current_password"
                                                    placeholder="Password saat ini" class="form-control" required>
                                            </div>

                                        </div>
                                        <div class="form-group row col-sm-12">
                                            <div class="col-sm-6">
                                                <label>Password baru</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" id="new_password" name="new_password"
                                                    placeholder="Password baru" class="form-control" required>
                                                @error('new_password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row col-sm-12">
                                            <div class="col-sm-6">
                                                <label>Ketik ulang password baru</label>
                                            </div>
                                            <div class="col-sm-6" style="text-align:right">
                                                <input type="text" id="new_password_validate"
                                                    name="new_password_validate"
                                                    placeholder="Konfirmasi password baru"class="form-control" required>
                                                @error('new_password_validate')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row col-sm-12">
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-success">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#formChangePassword').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {},
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).next('.text-danger')
                                .remove();
                            $('#' + key).after('<span class="text-danger">' + value.join(', ') +
                                '</span>');
                        });
                    }
                }
            });
        });
    </script>
@endsection

@section('footer')
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js"></script>
    <script src="{{ asset('') }}assets/vendor/kartik-v/bootstrap-fileinput/js/locales/id.js"></script>
    <script src="{{ asset('') }}assets/vendor/ckeditor/ckeditor5-build-inline/build/ckeditor.js"></script>
@endsection
