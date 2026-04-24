@extends('layouts.guru')

@section('title', 'Foto Kenangan')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Foto Kenangan</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUpload">
            <i class="fas fa-upload me-1"></i> Upload Foto
        </button>
    </div>

    {{-- ── FILTER KELAS ── --}}
    <div class="table-container mb-4">
        <form method="GET" action="{{ route('guru.foto.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Filter Kelas</label>
                <select name="kelas" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('guru.foto.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>Foto</th>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $siswa)
                    <tr>
                        <td>{{ $siswas->firstItem() + $loop->index }}</td>
                        <td>
                            <img src="{{ asset('storage/' . $siswa->foto_kenangan) }}"
                                 class="photo-preview" alt="foto"
                                 style="cursor:pointer;"
                                 onclick="previewFoto('{{ asset('storage/' . $siswa->foto_kenangan) }}', '{{ addslashes($siswa->nama) }}')">
                        </td>
                        <td>{{ $siswa->nama }}</td>
                        <td>{{ $siswa->nis }}</td>
                        <td>{{ $siswa->kelas }}</td>
                        <td>
                            @php
                                $badge = match(trim(strtolower($siswa->status))) {
                                    'lulus'       => 'success',
                                    'tidak lulus' => 'danger',
                                    default       => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucwords($siswa->status) }}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm"
                                onclick="openEditFoto({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm"
                                onclick="openDeleteFoto({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada foto kenangan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $siswas->links() }}</div>
    </div>
</div>

{{-- ════════════════════════════════ MODAL UPLOAD ════════════════════════════════ --}}
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('guru.foto.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Foto Kenangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas" class="form-select @error('kelas') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k }}" {{ old('kelas') == $k ? 'selected' : '' }}>
                                    {{ $k }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            File Foto / ZIP <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="foto_kelas"
                               class="form-control @error('foto_kelas') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.zip" required>
                        <div class="form-text">
                            Upload <strong>1 gambar</strong> untuk semua siswa sekelas, atau
                            <strong>ZIP</strong> berisi foto dengan nama file = nama siswa
                            (contoh: <em>Ahmad Fauzi.jpg</em>).
                        </div>
                        @error('foto_kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tampilkan error dari ZIP jika ada --}}
                    @if(session('zip_errors'))
                        <div class="alert alert-warning py-2 small">
                            <strong>File dilewati:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach(session('zip_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════ MODAL EDIT FOTO ════════════════════════════════ --}}
<div class="modal fade" id="modalEditFoto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditFoto" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Ganti Foto Kenangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Siswa: <strong id="edit_foto_nama"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Foto Baru <span class="text-danger">*</span></label>
                        <input type="file" name="foto_kelas" class="form-control"
                               accept=".jpg,.jpeg,.png" required>
                        <div class="form-text">Maks. 10MB. Format: JPG, JPEG, PNG.</div>
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

{{-- ════════════════════════════════ MODAL HAPUS FOTO ════════════════════════════════ --}}
<div class="modal fade" id="modalHapusFoto" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formHapusFoto" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-trash me-2"></i>Hapus Foto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-0">
                    <p class="mb-1">Hapus foto milik <strong id="hapus_foto_nama"></strong>?</p>
                    <small class="text-muted">Data siswa tidak akan ikut terhapus.</small>
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

{{-- ════════════════════════════════ MODAL PREVIEW FOTO ════════════════════════════════ --}}
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white" id="preview_nama"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="preview_img" src="" alt="preview"
                     class="img-fluid rounded" style="max-height:70vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditFoto(id, nama) {
        document.getElementById('formEditFoto').action  = `/guru/foto/${id}`;
        document.getElementById('edit_foto_nama').textContent = nama;
        new bootstrap.Modal(document.getElementById('modalEditFoto')).show();
    }

    function openDeleteFoto(id, nama) {
        document.getElementById('formHapusFoto').action      = `/guru/foto/${id}`;
        document.getElementById('hapus_foto_nama').textContent = nama;
        new bootstrap.Modal(document.getElementById('modalHapusFoto')).show();
    }

    function previewFoto(url, nama) {
        document.getElementById('preview_img').src           = url;
        document.getElementById('preview_nama').textContent  = nama;
        new bootstrap.Modal(document.getElementById('modalPreview')).show();
    }

    // Buka kembali modal upload jika ada error validasi
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('modalUpload')).show();
    @endif
</script>
@endpush
