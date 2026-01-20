<section>

    {{-- HEADER --}}
    <div class="mb-3">
        <h5 class="font-weight-bold text-danger mb-1">
            Hapus Akun
        </h5>
        <p class="text-muted mb-0">
            Tindakan ini akan <b>menghapus akun secara permanen</b> beserta seluruh data yang terkait.
            Proses ini <b>tidak dapat dibatalkan</b>.
        </p>
    </div>

    {{-- WARNING BOX --}}
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Pastikan Anda sudah mencadangkan data penting sebelum melanjutkan.
    </div>

    {{-- BUTTON --}}
    <button class="btn btn-danger"
            data-toggle="modal"
            data-target="#deleteAccountModal">
        <i class="fas fa-trash mr-1"></i>
        Hapus Akun
    </button>

    {{-- MODAL CONFIRM --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Konfirmasi Hapus Akun
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                {{-- FORM --}}
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">

                        <p class="mb-2">
                            Apakah Anda yakin ingin <b>menghapus akun ini secara permanen</b>?
                        </p>

                        <p class="text-muted small">
                            Semua data, riwayat, dan akses akan hilang selamanya.
                        </p>

                        {{-- PASSWORD --}}
                        <div class="form-group mt-3">
                            <label for="delete_password">
                                Masukkan Password untuk Konfirmasi
                            </label>
                            <input type="password"
                                   id="delete_password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Password Anda"
                                   required>

                            @if ($errors->userDeletion->has('password'))
                                <small class="text-danger">
                                    {{ $errors->userDeletion->first('password') }}
                                </small>
                            @endif
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i>
                            Ya, Hapus Akun
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</section>
