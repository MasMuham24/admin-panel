<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class FotoKenanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::whereNotNull('foto_kenangan');

        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $siswas = $query->orderBy('kelas')->orderBy('nama')->paginate(15)->withQueryString();
        $kelasList = Siswa::pluck('kelas')->unique()->sort()->values();

        return view('admin.foto_kenangan', compact('siswas', 'kelasList'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'kelas' => 'required',
            'foto_kelas' => 'required|file|mimes:jpeg,png,jpg,zip|max:51200'
        ]);

        if (!$request->hasFile('foto_kelas')) {
            return back()->with('error', 'Gagal memproses upload gambar.');
        }

        $file = $request->file('foto_kelas');
        $kelas = $request->kelas;

        if (strtolower($file->getClientOriginalExtension()) === 'zip') {
            return $this->handleZipUpload($file, $kelas);
        }

        $filename = 'kenangan_' . str_replace(' ', '_', $kelas) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('foto_kenangan', $filename, 'public');

        if ($path) {
            Siswa::where('kelas', $kelas)->update(['foto_kenangan' => $path]);
            return back()->with('success', 'Foto kelas berhasil diupload!');
        }

        return back()->with('error', 'Gagal memproses upload gambar.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'foto_kelas' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto_kenangan) {
            $oldPath = storage_path('app/public/' . $siswa->foto_kenangan);
            if (file_exists($oldPath)) unlink($oldPath);
        }

        $file = $request->file('foto_kelas');
        $filename = 'kenangan_' . $siswa->nis . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('foto_kenangan', $filename, 'public');

        $siswa->update(['foto_kenangan' => $path]);

        return back()->with('success', 'Foto kenangan ' . $siswa->nama . ' berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto_kenangan) {
            $oldPath = storage_path('app/public/' . $siswa->foto_kenangan);
            if (file_exists($oldPath)) unlink($oldPath);
            $siswa->update(['foto_kenangan' => null]);
        }

        return back()->with('success', 'Foto kenangan ' . $siswa->nama . ' berhasil dihapus!');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:siswas,id'
        ]);

        $siswas = Siswa::whereIn('id', $request->ids)->get();

        foreach ($siswas as $siswa) {
            if ($siswa->foto_kenangan) {
                $oldPath = storage_path('app/public/' . $siswa->foto_kenangan);
                if (file_exists($oldPath)) unlink($oldPath);
                $siswa->update(['foto_kenangan' => null]);
            }
        }

        return back()->with('success', count($siswas) . ' foto kenangan berhasil dihapus!');
    }

    private function handleZipUpload($file, $kelas)
    {
        if (!class_exists('ZipArchive')) {
            return back()->with('error', 'ZipArchive tidak tersedia di server.');
        }

        $zip = new \ZipArchive();

        if ($zip->open($file->getRealPath()) !== true) {
            return back()->with('error', 'Gagal membuka file ZIP.');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $uploadedCount = 0;
        $skippedCount = 0;
        $errors = [];

        $storageFolder = 'foto_kenangan';
        $destinationPath = storage_path('app/public/' . $storageFolder);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->statIndex($i)['name'];

            if (
                str_ends_with($entryName, '/') ||
                str_contains($entryName, '__MACOSX') ||
                str_starts_with(basename($entryName), '.')
            ) continue;

            $ext = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExtensions)) {
                $skippedCount++;
                continue;
            }

            $baseName = pathinfo(basename($entryName), PATHINFO_FILENAME);
            $namaFromFile = strtoupper(str_replace(['-', '_'], ' ', $baseName));

            $siswa = Siswa::where('kelas', $kelas)
                ->whereRaw('UPPER(nama) = ?', [$namaFromFile])
                ->first();

            if (!$siswa) {
                $siswa = Siswa::where('kelas', $kelas)
                    ->whereRaw('UPPER(nama) LIKE ?', ['%' . $namaFromFile . '%'])
                    ->first();
            }

            if (!$siswa) {
                $errors[] = "Nama '$namaFromFile' tidak ditemukan di kelas $kelas (file: " . basename($entryName) . ")";
                $skippedCount++;
                continue;
            }

            if ($siswa->foto_kenangan) {
                $oldPath = storage_path('app/public/' . $siswa->foto_kenangan);
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $newFilename = 'kenangan_' . $siswa->nis . '_' . time() . '.' . $ext;
            file_put_contents($destinationPath . '/' . $newFilename, $zip->getFromIndex($i));

            $siswa->update(['foto_kenangan' => $storageFolder . '/' . $newFilename]);
            $uploadedCount++;
        }

        $zip->close();

        $message = "Berhasil upload $uploadedCount foto.";
        if ($skippedCount > 0) $message .= " $skippedCount file dilewati.";

        $sessionData = ['success' => $message];
        if (!empty($errors)) $sessionData['zip_errors'] = $errors;

        return back()->with($sessionData);
    }
}
