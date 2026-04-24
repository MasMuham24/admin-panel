@extends('layouts.app')

@section('title')
    <title>Kelola Foto Kenangan</title>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-images me-2 text-info"></i>Kelola Foto Kenangan
        </h4>
        <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalUpload">
            <i class="fas fa-upload me-1"></i> Upload Foto
        </button>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert ZIP Errors --}}
    @if (session('zip_errors'))
        <div class="alert alert-warning alert-dismissible fade show rounded-3">
            <strong><i class="fas fa-exclamation-triangle me-1"></i>Beberapa file dilewati:</strong>
            <ul class="mb-0 mt-1">
                @foreach (session('zip_errors') as $err)
                    <li style="font-size:0.85rem;">{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter & Search --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.foto_kenangan.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                                {{ $kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if (request('kelas') || request('search'))
                    <div class="col-auto">
                        <a href="{{ route('admin.foto_kenangan.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <form id="formBulkDelete" action="{{ route('admin.foto_kenangan.bulk_delete') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Total: {{ $siswas->total() }} siswa dengan foto kenangan</small>
            <button type="button" class="btn btn-sm btn-danger d-none" id="btnBulkDelete" onclick="confirmBulkDelete()">
                <i class="fas fa-trash me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr class="small text-uppercase">
                            <th class="ps-3" width="40">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th>Foto</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th class="d-none d-sm-table-cell">Kelas</th>
                            <th>Status</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" class="form-check-input check-item" name="ids[]"
                                        value="{{ $siswa->id }}">
                                </td>
                                <td>
                                    <img src="{{ Storage::url($siswa->foto_kenangan) }}" alt="{{ $siswa->nama }}"
                                        class="rounded-circle shadow-sm"
                                        style="width:40px; height:40px; object-fit:cover; cursor:pointer;"
                                        onclick="previewFoto('{{ Storage::url($siswa->foto_kenangan) }}', '{{ $siswa->nama }}')">
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $siswa->nis }}</div>
                                    <div class="small text-muted">{{ $siswa->nisn }}</div>
                                </td>
                                <td class="fw-bold text-dark">{{ $siswa->nama }}</td>
                                <td class="d-none d-sm-table-cell text-muted">{{ $siswa->kelas }}</td>
                                <td>
                                    <span
                                        class="badge {{ trim(strtolower($siswa->status)) == 'lulus' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                                        {{ strtoupper($siswa->status) }}
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group gap-1">
                                        <button type="button" class="btn btn-sm btn-light border shadow-sm"
                                            onclick="openEditModal({{ $siswa->id }}, '{{ $siswa->nama }}')">
                                            <i class="fas fa-edit text-warning"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light border shadow-sm"
                                            onclick="confirmDelete({{ $siswa->id }}, '{{ $siswa->nama }}')">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="{{ asset('img/folder.png') }}" style="width:85px;" class="mb-3">
                                    <p class="text-muted fw-bold">Belum ada foto kenangan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $siswas->links() }}
    </div>

    {{-- ===================== MODAL UPLOAD ===================== --}}
    <div class="modal fade" id="modalUpload" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.foto_kenangan.upload') }}" method="POST" enctype="multipart/form-data"
                class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="fw-bold"><i class="fas fa-upload me-2 text-info"></i>Upload Foto Kenangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Kelas</label>
                        <select name="kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">File Foto / ZIP</label>
                        <input type="file" name="foto_kelas" class="form-control" accept=".jpg,.jpeg,.png,.zip"
                            required>
                    </div>
                    <p class="text-muted mb-0" style="font-size:0.75rem;">
                        * Upload <strong>1 foto</strong> untuk semua siswa sekelas, atau
                        <strong>ZIP</strong> dengan nama file = nama siswa
                        (contoh: <code>AHMAD-RIZKY.jpg</code>).
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MODAL EDIT ===================== --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEdit" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
                @csrf
                @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Ganti Foto Kenangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3 text-muted">Siswa: <strong id="editNamaSiswa" class="text-dark"></strong></p>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Foto Baru (JPG/PNG)</label>
                        <input type="file" name="foto_kelas" class="form-control" accept=".jpg,.jpeg,.png" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MODAL PREVIEW ===================== --}}
    <div class="modal fade" id="modalPreview" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h6 class="fw-bold mb-0" id="previewNama"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="previewImg" src="" alt="" class="img-fluid rounded-3">
                </div>
            </div>
        </div>
    </div>

    {{-- Form hapus single --}}
    <form id="formDelete" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check all
        document.getElementById('checkAll').addEventListener('change', function() {
            document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
            updateBulkBtn();
        });

        document.querySelectorAll('.check-item').forEach(cb => {
            cb.addEventListener('change', updateBulkBtn);
        });

        function updateBulkBtn() {
            const checked = document.querySelectorAll('.check-item:checked').length;
            const btn = document.getElementById('btnBulkDelete');
            document.getElementById('selectedCount').textContent = checked;
            checked > 0 ? btn.classList.remove('d-none') : btn.classList.add('d-none');
        }

        // Bulk delete
        function confirmBulkDelete() {
            const count = document.querySelectorAll('.check-item:checked').length;
            Swal.fire({
                title: 'Hapus foto terpilih?',
                text: `${count} foto kenangan akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('formBulkDelete').submit();
                }
            });
        }

        // Single delete
        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus foto ini?',
                text: `Foto kenangan milik ${nama} akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formDelete');
                    form.action = `/admin/foto-kenangan/${id}`;
                    form.submit();
                }
            });
        }

        // Edit modal
        function openEditModal(id, nama) {
            document.getElementById('editNamaSiswa').textContent = nama;
            document.getElementById('formEdit').action = `/admin/foto-kenangan/${id}`;
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }

        // Preview foto
        function previewFoto(url, nama) {
            document.getElementById('previewImg').src = url;
            document.getElementById('previewNama').textContent = nama;
            new bootstrap.Modal(document.getElementById('modalPreview')).show();
        }

        // SweetAlert session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                iconColor: '#28a745'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
