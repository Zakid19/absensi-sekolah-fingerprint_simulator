<section>

    {{-- HEADER --}}
    <div class="mb-3">
        <h5 class="font-weight-bold mb-1">
            Ubah Password
        </h5>
        <p class="text-muted mb-0">
            Gunakan password yang kuat dan tidak mudah ditebak untuk menjaga keamanan akun.
        </p>
    </div>

    {{-- FORM UPDATE PASSWORD --}}
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        {{-- PASSWORD SAAT INI --}}
        <div class="form-group">
            <label for="current_password">Password Saat Ini</label>
            <input type="password"
                   id="current_password"
                   name="current_password"
                   class="form-control"
                   autocomplete="current-password"
                   required>

            @if ($errors->updatePassword->has('current_password'))
                <small class="text-danger">
                    {{ $errors->updatePassword->first('current_password') }}
                </small>
            @endif
        </div>

        {{-- PASSWORD BARU --}}
        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control"
                   autocomplete="new-password"
                   required>

            <small class="text-muted">
                Minimal 8 karakter, disarankan kombinasi huruf dan angka.
            </small>

            @if ($errors->updatePassword->has('password'))
                <small class="text-danger d-block">
                    {{ $errors->updatePassword->first('password') }}
                </small>
            @endif
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   class="form-control"
                   autocomplete="new-password"
                   required>

            @if ($errors->updatePassword->has('password_confirmation'))
                <small class="text-danger">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </small>
            @endif
        </div>

        {{-- ACTION --}}
        <div class="mt-3 d-flex align-items-center">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-key mr-1"></i>
                Simpan Password
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success ml-3">
                    Password berhasil diperbarui.
                </span>
            @endif
        </div>

    </form>

</section>
