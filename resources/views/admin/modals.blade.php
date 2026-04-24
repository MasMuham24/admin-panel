<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data"
            class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h5 class="fw-bold"><i class="fas fa-file-csv me-2 text-primary"></i>Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-bold">Pilih File CSV</label>
                <input type="file" name="file_csv" class="form-control mb-3" accept=".csv" required>

                <div class="alert alert-warning border-0 shadow-sm mb-0">
                    <div class="fw-bold small mb-1"><i class="fas fa-info-circle me-1"></i> Format Kolom CSV:</div>
                    <code class="small text-dark">nis, nisn, nama, kelas, status</code>
                    <hr class="my-2">
                    <ul class="mb-0 ps-3 small">
                        <li><strong>Status:</strong> lulus / tidak lulus</li>
                        <li>Pastikan tidak ada baris kosong</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Proses Import</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.upload_kenangan') }}" method="POST" enctype="multipart/form-data"
            class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h5 class="fw-bold"><i class="fas fa-images me-2 text-info"></i>Upload Foto Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Pilih Kelas</label>
                    <select name="kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @if (!empty($siswas))
                            @foreach ($siswas->pluck('kelas')->unique() as $kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">File Foto / ZIP</label>
                    <input type="file" name="foto_kelas" class="form-control" accept=".jpg,.jpeg,.png,.zip" required>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">
                    * Upload <strong>1 foto</strong> untuk semua siswa sekelas, atau upload <strong>ZIP</strong>
                    berisi banyak foto (nama file = NIS siswa, contoh: <code>12001.jpg</code>).
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info text-white px-4">Simpan Foto</button>
            </div>
        </form>
    </div>
</div>
