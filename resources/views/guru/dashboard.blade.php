@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="container-fluid">

        <h3 class="mb-4 fw-bold">Dashboard Guru</h3>

        {{-- 🔥 STAT --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-white shadow rounded-4" style="background:#0d6efd">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <i class="fas fa-users fa-2x"></i>
                        <div>
                            <small>Total Siswa</small>
                            <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white shadow rounded-4" style="background:#198754">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <i class="fas fa-user-check fa-2x"></i>
                        <div>
                            <small>Lulus</small>
                            <div class="fs-3 fw-bold">{{ $stats['lulus'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white shadow rounded-4" style="background:#dc3545">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <i class="fas fa-user-times fa-2x"></i>
                        <div>
                            <small>Tidak Lulus</small>
                            <div class="fs-3 fw-bold">{{ $stats['tidak_lulus'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔥 TABLE --}}
        <div class="card shadow rounded-4">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold">Daftar Siswa</h5>

                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                                    <td class="fw-bold">{{ $siswa->nama }}</td>
                                    <td>{{ $siswa->kelas }}</td>

                                    <td>
                                        @php
                                            $badge = strtolower($siswa->status) == 'lulus' ? 'success' : 'danger';
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ ucfirst($siswa->status) }}</span>
                                    </td>

                                    <td>
                                        @if ($siswa->foto_kenangan)
                                            <img src="{{ asset('storage/' . $siswa->foto_kenangan) }}" width="50"
                                                style="border-radius:8px;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm"
                                            onclick="openEditModal(
                                        {{ $siswa->id }},
                                        '{{ $siswa->nis }}',
                                        '{{ $siswa->nisn }}',
                                        '{{ $siswa->nama }}',
                                        '{{ $siswa->kelas }}',
                                        '{{ $siswa->status }}'
                                    )">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('guru.siswa.destroy', $siswa->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data siswa
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $siswas->links() }}
                </div>

            </div>
        </div>

    </div>

    {{-- 🔥 MODAL CREATE --}}
    <div class="modal fade" id="modalCreate">
        <div class="modal-dialog">
            <form action="{{ route('guru.siswa.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5>Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="nis" class="form-control mb-2" placeholder="NIS" required>
                    <input type="text" name="nisn" class="form-control mb-2" placeholder="NISN" required>
                    <input type="text" name="nama" class="form-control mb-2" placeholder="Nama" required>
                    <input type="text" name="kelas" class="form-control mb-2" placeholder="Kelas" required>

                    <select name="status" class="form-control">
                        <option value="lulus">Lulus</option>
                        <option value="tidak lulus">Tidak Lulus</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🔥 MODAL EDIT --}}
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <form id="formEdit" method="POST" class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5>Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="nis" id="editNis" class="form-control mb-2">
                    <input type="text" name="nisn" id="editNisn" class="form-control mb-2">
                    <input type="text" name="nama" id="editNama" class="form-control mb-2">
                    <input type="text" name="kelas" id="editKelas" class="form-control mb-2">

                    <select name="status" id="editStatus" class="form-control">
                        <option value="lulus">Lulus</option>
                        <option value="tidak lulus">Tidak Lulus</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning text-white">Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: 'Hapus siswa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya hapus'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
            return false;
        }

        function openEditModal(id, nis, nisn, nama, kelas, status) {
            document.getElementById('editNis').value = nis;
            document.getElementById('editNisn').value = nisn;
            document.getElementById('editNama').value = nama;
            document.getElementById('editKelas').value = kelas;
            document.getElementById('editStatus').value = status;

            document.getElementById('formEdit').action = `/guru/siswa/${id}`;

            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }
    </script>
@endpush
