<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $siswas = Siswa::all();

        $stats = [
            'total' => $siswas->count(),
            'lulus' => $siswas->filter(fn($s) => trim(strtolower($s->status)) == 'lulus')->count(),
            'tidak_lulus' => $siswas->filter(fn($s) => trim(strtolower($s->status)) == 'tidak lulus')->count(),
        ];

        return view('admin.dashboard', compact('siswas', 'stats'));
    }

    public function importSiswaCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|max:2048'
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();

        $content = file_get_contents($path);
        $separator = str_contains($content, ';') ? ';' : ',';

        $handle = fopen($path, "r");
        fgetcsv($handle, 1000, $separator);

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                if (count($data) >= 5) {
                    Siswa::updateOrCreate(
                        [
                            'nis'  => $data[0],
                            'nisn' => $data[1],
                        ],
                        [
                            'nama'   => $data[2],
                            'kelas'  => $data[3],
                            'status' => trim(strtolower($data[4])),
                        ]
                    );
                }
            }
            DB::commit();
            fclose($handle);
            return back()->with('success', 'Data berhasil diimpor!');
        } catch (\Exception $e) {
            DB::rollback();
            fclose($handle);
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function uploadFotoKenangan(Request $request)
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

    private function handleZipUpload($file, $kelas)
    {
        if (!class_exists('ZipArchive')) {
            return back()->with('error', 'ZipArchive tidak tersedia di server.');
        }

        $zip = new \ZipArchive();
        $tmpPath = $file->getRealPath();

        if ($zip->open($tmpPath) !== true) {
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
            $zipEntry = $zip->statIndex($i);
            $entryName = $zipEntry['name'];
            if (
                str_ends_with($entryName, '/') ||
                str_contains($entryName, '__MACOSX') ||
                str_starts_with(basename($entryName), '.')
            ) {
                continue;
            }

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
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newFilename = 'kenangan_' . $siswa->nis . '_' . time() . '.' . $ext;
            $fileContent = $zip->getFromIndex($i);
            file_put_contents($destinationPath . '/' . $newFilename, $fileContent);
            $siswa->update(['foto_kenangan' => $storageFolder . '/' . $newFilename]);
            $uploadedCount++;
        }

        $zip->close();
        $message = "Berhasil upload $uploadedCount foto.";
        if ($skippedCount > 0) {
            $message .= " $skippedCount file dilewati.";
        }

        $sessionData = ['success' => $message];
        if (!empty($errors)) {
            $sessionData['zip_errors'] = $errors;
        }

        return back()->with($sessionData);
    }

    public function updateSiswa(Request $request, $id)
    {
        $request->validate([
            'nis'    => 'required|unique:siswas,nis,' . $id,
            'nisn'   => 'required|unique:siswas,nisn,' . $id,
            'nama'   => 'required',
            'kelas'  => 'required',
            'status' => 'required'
        ]);

        $siswa = Siswa::findOrFail($id);

        $siswa->update([
            'nis'    => $request->nis,
            'nisn'   => $request->nisn,
            'nama'   => $request->nama,
            'kelas'  => $request->kelas,
            'status' => trim(strtolower($request->status)),
        ]);

        return back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroySiswa($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return back()->with('success', 'Siswa berhasil dihapus!');
    }

    public function destroyAll()
    {
        DB::table('siswas')->truncate();
        return back()->with('success', 'Semua data berhasil dikosongkan.');
    }

    public function getSiswaApi()
    {
        $siswa = \App\Models\Siswa::all();
        return response()->json($siswa);
    }
}
