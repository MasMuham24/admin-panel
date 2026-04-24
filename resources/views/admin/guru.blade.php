@extends('layouts.app')

@section('title')
    <title>Kelola Akun Guru</title>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Kelola Akun Guru
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-1"></i> Tambah Guru
        </button>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.guru.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if (request('search'))
                    <div class="col-auto">
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr class="small text-uppercase">
                        <th class="ps-4" width="50">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Dibuat</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $guru)
                        <tr>
                            <td class="ps-4 text-muted">{{ $gurus->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                        style="width:36px; height:36px; font-size:14px; flex-shrink:0;">
                                        {{ strtoupper(substr($guru->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $guru->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $guru->email }}</td>
                            <td class="text-muted small">{{ $guru->created_at->format('d M Y') }}</td>
                            <td class="text-center pe-4">
                                <div class="btn-group gap-1">
                                    <button class="btn btn-sm btn-light border shadow-sm"
                                        onclick="openEditModal({{ $guru->id }}, '{{ $guru->name }}', '{{ $guru->email }}')">
                                        <i class="fas fa-edit text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border shadow-sm"
                                        onclick="confirmDelete({{ $guru->id }}, '{{ $guru->name }}')">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                    <form id="delete-form-{{ $guru->id }}"
                                        action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST"
                                        class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-user-slash fa-2x text-muted mb-2 d-block"></i>
                                <p class="text-muted fw-bold mb-0">Belum ada akun guru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">{{ $gurus->links() }}</div>

    {{-- ===== MODAL TAMBAH ===== --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.guru.store') }}" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="fw-bold"><i class="fas fa-plus me-2 text-primary"></i>Tambah Akun Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="guru@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                            required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Ulangi password" required>
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
                    <h5 class="fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Edit Akun Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">
                            Password Baru
                            <span class="text-muted fw-normal">(kosongkan jika tidak diubah)</span>
                        </label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Ulangi password baru">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openEditModal(id, name, email) {
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('formEdit').action = `/admin/guru/${id}`;
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }

        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus akun ini?',
                text: `Akun guru ${nama} akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                iconColor: '#28a745'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Akun Gagal Dibuat',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
