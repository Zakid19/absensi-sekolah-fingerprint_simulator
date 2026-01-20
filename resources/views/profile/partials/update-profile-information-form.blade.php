<section>

    {{-- HEADER --}}
    <div class="mb-3">
        <h5 class="font-weight-bold mb-1">
            Informasi Profil
        </h5>
        <p class="text-muted mb-0">
            Perbarui nama dan alamat email yang digunakan untuk login.
        </p>
    </div>

    {{-- FORM VERIFIKASI EMAIL --}}
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- FORM UPDATE PROFILE --}}
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- NAMA --}}
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $user->name) }}"
                   required
                   autofocus>

            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- EMAIL --}}
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email"
                   id="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $user->email) }}"
                   required>

            <small class="text-muted">
                Email digunakan sebagai akun login.
            </small>

            @error('email')
                <small class="text-danger d-block">{{ $message }}</small>
            @enderror

            {{-- STATUS VERIFIKASI --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2 p-2">
                    <small>
                        Email belum diverifikasi.
                        <button type="submit"
                                form="send-verification"
                                class="btn btn-link btn-sm p-0 align-baseline">
                            Kirim ulang email verifikasi
                        </button>
                    </small>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <small class="text-success">
                        Link verifikasi baru telah dikirim ke email Anda.
                    </small>
                @endif
            @endif
        </div>

        {{-- ACTION --}}
        <div class="mt-3 d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success ml-3">
                    Data berhasil disimpan.
                </span>
            @endif
        </div>

    </form>

</section>
