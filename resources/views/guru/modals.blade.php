{{-- ===== MODAL TAMBAH ===== --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h5 class="fw-bold">
                    <i class="fas fa-plus me-2 text-primary"></i>Tambah Akun Guru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                {{-- 🔥 IMPORT CSV --}}
                <div class="mb-3">
                    <label class="form-label small fw-bold">Import CSV (Opsional)</label>
                    <input type="file" name="file_csv" class="form-control">
                    <small class="text-muted">Upload CSV untuk tambah banyak akun guru</small>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- ===== MODAL EDIT ===== --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEdit" method="POST" class="modal-content border-0 shadow">
            @csrf
            @method('PUT')

            <div class="modal-header border-0">
                <h5 class="fw-bold">
                    <i class="fas fa-edit me-2 text-warning"></i>Edit Akun Guru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" id="editName" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" id="editEmail" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">
                        Password Baru
                        <span class="text-muted">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning text-white rounded-pill px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>
