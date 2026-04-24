@extends('layouts.guru')

@section('title', 'Data Siswa')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Data Siswa</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-1"></i> Tambah Siswa
            </button>
        </div>

        {{-- ── FILTER & SEARCH ── --}}
        <div class="table-container mb-4">
            <form method="GET" action="{{ route('guru.siswa.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">Cari Siswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Nama / NIS / NISN...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Kelas</label>
                    <input type="text" name="kelas" value="{{ request('kelas') }}" class="form-control"
                        placeholder="Contoh: XII TKJ 1">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak lulus" {{ request('status') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('guru.siswa.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- ── TABEL ── --}}
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                            <tr>
                                <td>{{ $siswas->firstItem() + $loop->index }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nisn }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>
                                    @php
                                        $badge = match (trim(strtolower($siswa->status))) {
                                            'lulus' => 'success',
                                            'tidak lulus' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucwords($siswa->status) }}</span>
                                </td>
                                <td>
                                    @if ($siswa->foto_kenangan)
                                        <img src="{{ asset('storage/' . $siswa->foto_kenangan) }}" class="photo-preview"
                                            alt="foto">
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm"
                                        onclick="openEditModal(
                                    {{ $siswa->id }},
                                    '{{ addslashes($siswa->nis) }}',
                                    '{{ addslashes($siswa->nisn) }}',
                                    '{{ addslashes($siswa->nama) }}',
                                    '{{ addslashes($siswa->kelas) }}',
                                    '{{ $siswa->status }}'
                                )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="openDeleteModal({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Tidak ada data siswa ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $siswas->links() }}</div>
        </div>
    </div>

    {{-- ════════════════════════════════ MODAL TAMBAH ════════════════════════════════ --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('guru.siswa.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis') }}" required>
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn') }}" required>
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="kelas"
                                class="form-control @error('kelas') is-invalid @enderror" value="{{ old('kelas') }}"
                                required>
                            @error('kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="lulus" {{ old('status') == 'lulus' ? 'selected' : '' }}>Lulus
                                </option>
                                <option value="tidak lulus" {{ old('status') == 'tidak lulus' ? 'selected' : '' }}>Tidak
                                    Lulus</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════ MODAL EDIT ════════════════════════════════ --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEdit" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" id="edit_nis" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" id="edit_nisn" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="kelas" id="edit_kelas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="lulus">Lulus</option>
                                <option value="tidak lulus">Tidak Lulus</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════ MODAL HAPUS ════════════════════════════════ --}}
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="formHapus" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fas fa-trash me-2"></i>Hapus Siswa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-0">
                        <p class="mb-1">Hapus data <strong id="hapus_nama"></strong>?</p>
                        <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openEditModal(id, nis, nisn, nama, kelas, status) {
            document.getElementById('formEdit').action = `/guru/siswa/${id}`;
            document.getElementById('edit_nis').value = nis;
            document.getElementById('edit_nisn').value = nisn;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_kelas').value = kelas;
            document.getElementById('edit_status').value = status;
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }

        function openDeleteModal(id, nama) {
            document.getElementById('formHapus').action = `/guru/siswa/${id}`;
            document.getElementById('hapus_nama').textContent = nama;
            new bootstrap.Modal(document.getElementById('modalHapus')).show();
        }

        // Buka kembali modal tambah jika ada error validasi
        @if ($errors->any() && !old('_method'))
            new bootstrap.Modal(document.getElementById('modalTambah')).show();
        @endif
    </script>
@endpush
