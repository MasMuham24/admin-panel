@extends('layouts.app')
@yield('title')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h4 class="fw-bold text-dark mb-0">Ringkasan Data Siswa</h4>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary flex-fill" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="fas fa-file-csv me-1"></i> <span class="d-none d-sm-inline">Import CSV</span>
            </button>
            <button class="btn btn-info text-white flex-fill" data-bs-toggle="modal" data-bs-target="#modalFoto">
                <i class="fas fa-camera me-1"></i> <span class="d-none d-sm-inline">Foto Kelas</span>
            </button>
            <button class="btn btn-outline-danger shadow-sm" onclick="confirmDestroyAll()">
                <i class="fas fa-trash"></i>
            </button>

            <form id="destroy-all-form" action="{{ route('admin.destroy_all') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm bg-primary text-white p-3 border-0 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75 small text-uppercase">Total Siswa</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['total'] }}</h2>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm bg-success text-white p-3 border-0 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75 small text-uppercase">Lulus</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['lulus'] }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm bg-danger text-white p-3 border-0 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75 small text-uppercase">Tidak Lulus</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['tidak_lulus'] }}</h2>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container shadow-sm border-0 bg-white rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr class="small text-uppercase">
                        <th class="ps-4">Foto</th>
                        <th>Identitas (NIS/NISN)</th>
                        <th>Nama Lengkap</th>
                        <th class="d-none d-sm-table-cell">Kelas</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $siswa)
                        <tr>
                            <td class="ps-4">
                                <img src="{{ $siswa->foto_kenangan ? Storage::url($siswa->foto_kenangan) : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama) }}"
                                    class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
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
                            <td class="text-center pe-4">
                                <div class="btn-group gap-1">
                                    <button class="btn btn-sm btn-light border shadow-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $siswa->id }}">
                                        <i class="fas fa-edit text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border shadow-sm"
                                        onclick="confirmDelete({{ $siswa->id }})">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                    <form id="delete-form-{{ $siswa->id }}"
                                        action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="{{ asset('img/folder.png') }}" style="width: 85px;"
                                    class="mb-3">
                                <p class="text-muted fw-bold">Belum ada data siswa ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($siswas as $siswa)
        <div class="modal fade" id="modalEdit{{ $siswa->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Edit Data Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">NIS</label>
                                    <input type="text" name="nis" class="form-control" value="{{ $siswa->nis }}"
                                        required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">NISN</label>
                                    <input type="text" name="nisn" class="form-control" value="{{ $siswa->nisn }}"
                                        required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ $siswa->nama }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Kelas</label>
                                <input type="text" name="kelas" class="form-control" value="{{ $siswa->kelas }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Status</label>
                                <select name="status" class="form-select">
                                    <option value="lulus"
                                        {{ trim(strtolower($siswa->status)) == 'lulus' ? 'selected' : '' }}>LULUS</option>
                                    <option value="tidak lulus"
                                        {{ trim(strtolower($siswa->status)) == 'tidak lulus' ? 'selected' : '' }}>TIDAK
                                        LULUS</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                background: '#fff',
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

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data siswa ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        function confirmDestroyAll() {
            Swal.fire({
                title: 'Kosongkan Seluruh Data?',
                text: "Tindakan ini akan menghapus SEMUA data siswa sekaligus!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kosongkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('destroy-all-form').submit();
                }
            })
        }
    </script>
@endsection
