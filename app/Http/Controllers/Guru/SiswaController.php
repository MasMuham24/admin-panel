<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = siswa::latest()->get();
        return view('guru.siswa.index', compact('siswa'));
    }

    public function create()
    {
        return view('guru.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required',
            'kelas' => 'required',
            'status' => 'required',
        ]);

        siswa::create([
            'nama' => $request->nama,
            'nisn' => $request->nisn,
            'kelas' => $request->kelas,
            'status' => $request->status,
        ]);

        return redirect()->route('guru.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit(siswa $siswa)
    {
        return view('guru.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, siswa $siswa)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required',
            'kelas' => 'required',
            'status' => 'required',
        ]);

        $siswa->update([
            'nama' => $request->nama,
            'nisn' => $request->nisn,
            'kelas' => $request->kelas,
            'status' => $request->status,
        ]);

        return redirect()->route('guru.siswa.index')
            ->with('success', 'Siswa berhasil diupdate');
    }

    public function destroy(siswa $siswa)
    {
        $siswa->delete();

        return back()->with('success', 'Siswa berhasil dihapus');
    }
}
