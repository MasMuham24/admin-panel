<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function dashboard()
    {
        $siswas = Siswa::latest()->paginate(10); 

        $stats = [
            'total'       => Siswa::count(),
            'lulus'       => Siswa::whereRaw('LOWER(status) = ?', ['lulus'])->count(),
            'tidak_lulus' => Siswa::whereRaw('LOWER(status) = ?', ['tidak lulus'])->count(),
        ];

        return view('guru.dashboard', compact('siswas', 'stats'));
    }

    public function siswaIndex(Request $request)
    {
        $query = Siswa::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('nis', 'like', '%' . $request->search . '%')
                ->orWhere('nisn', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $siswas = $query->paginate(15)->withQueryString();

        return view('guru.siswa.index', compact('siswas'));
    }

    public function siswaCreate()
    {
        return view('guru.siswa.create');
    }

    public function siswaStore(Request $request)
    {
        $request->validate([
            'nis'    => 'nullable|unique:siswas,nis',
            'nisn'   => 'nullable|unique:siswas,nisn',
            'nama'   => 'nullable',
            'kelas'  => 'nullable',
            'status' => 'nullable',
            'file_csv' => 'nullable|file|mimes:csv,txt|max:2048',
        ]);

        if ($request->hasFile('file_csv')) {
            return $this->importCsv($request->file('file_csv'));
        }

        Siswa::create([
            'nis'    => $request->nis,
            'nisn'   => $request->nisn,
            'nama'   => $request->nama,
            'kelas'  => $request->kelas,
            'status' => trim(strtolower($request->status)),
        ]);

        return redirect()->route('guru.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan!');
    }

    private function importCsv($file)
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->with('error', 'Gagal membaca file CSV.');
        }

        $header = fgetcsv($handle); // ambil header

        $requiredColumns = ['nis', 'nisn', 'nama', 'kelas', 'status'];

        // validasi header
        foreach ($requiredColumns as $col) {
            if (!in_array($col, $header)) {
                return back()->with('error', "Kolom '$col' tidak ditemukan di CSV.");
            }
        }

        $data = [];
        $success = 0;
        $failed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowData = array_combine($header, $row);

            try {
                Siswa::create([
                    'nis'    => $rowData['nis'],
                    'nisn'   => $rowData['nisn'],
                    'nama'   => $rowData['nama'],
                    'kelas'  => $rowData['kelas'],
                    'status' => trim(strtolower($rowData['status'])),
                ]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        fclose($handle);

        return redirect()->route('guru.siswa.index')
            ->with('success', "Import selesai: $success berhasil, $failed gagal.");
    }

    public function siswaEdit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('guru.siswa.edit', compact('siswa'));
    }

    public function siswaUpdate(Request $request, $id)
    {
        $request->validate([
            'nis'    => 'required|unique:siswas,nis,' . $id,
            'nisn'   => 'required|unique:siswas,nisn,' . $id,
            'nama'   => 'required',
            'kelas'  => 'required',
            'status' => 'required',
        ]);

        $siswa = Siswa::findOrFail($id);

        $siswa->update([
            'nis'    => $request->nis,
            'nisn'   => $request->nisn,
            'nama'   => $request->nama,
            'kelas'  => $request->kelas,
            'status' => trim(strtolower($request->status)),
        ]);

        return redirect()->route('guru.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function siswaDestroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return back()->with('success', 'Siswa berhasil dihapus!');
    }

    public function fotoIndex(Request $request)
    {
        $query = Siswa::whereNotNull('foto_kenangan');

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $siswas    = $query->paginate(20)->withQueryString();
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('guru.foto.index', compact('siswas', 'kelasList'));
    }

    public function fotoCreate()
    {
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        return view('guru.foto.create', compact('kelasList'));
    }

    public function fotoStore(Request $request)
    {
        $request->validate([
            'kelas'      => 'required',
            'foto_kelas' => 'required|file|mimes:jpeg,png,jpg,zip|max:51200',
        ]);

        if (!$request->hasFile('foto_kelas')) {
            return back()->with('error', 'Gagal memproses upload gambar.');
        }

        $file  = $request->file('foto_kelas');
        $kelas = $request->kelas;

        if (strtolower($file->getClientOriginalExtension()) === 'zip') {
            return $this->handleZipUpload($file, $kelas);
        }

        $filename = 'kenangan_' . str_replace(' ', '_', $kelas) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('foto_kenangan', $filename, 'public');

        if ($path) {
            Siswa::where('kelas', $kelas)->update(['foto_kenangan' => $path]);
            return redirect()->route('guru.foto.index')
                ->with('success', 'Foto kelas berhasil diupload!');
        }

        return back()->with('error', 'Gagal memproses upload gambar.');
    }

    public function fotoEdit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('guru.foto.edit', compact('siswa'));
    }

    public function fotoUpdate(Request $request, $id)
    {
        $request->validate([
            'foto_kelas' => 'required|file|mimes:jpeg,png,jpg|max:10240',
        ]);

        $siswa = Siswa::findOrFail($id);
        if ($siswa->foto_kenangan) {
            $oldPath = storage_path('app/public/' . $siswa->foto_kenangan);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file     = $request->file('foto_kelas');
        $filename = 'kenangan_' . $siswa->nis . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('foto_kenangan', $filename, 'public');

        $siswa->update(['foto_kenangan' => $path]);

        return redirect()->route('guru.foto.index')
            ->with('success', 'Foto kenangan berhasil diperbarui!');
    }

    public function fotoDestroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto_kenangan) {
            $filePath = storage_path('app/public/' . $siswa->foto_kenangan);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $siswa->update(['foto_kenangan' => null]);
        }

        return back()->with('success', 'Foto kenangan berhasil dihapus!');
    }

    private function handleZipUpload($file, $kelas)
    {
        if (!class_exists('ZipArchive')) {
            return back()->with('error', 'ZipArchive tidak tersedia di server.');
        }

        $zip     = new \ZipArchive();
        $tmpPath = $file->getRealPath();

        if ($zip->open($tmpPath) !== true) {
            return back()->with('error', 'Gagal membuka file ZIP.');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $uploadedCount     = 0;
        $skippedCount      = 0;
        $errors            = [];

        $storageFolder   = 'foto_kenangan';
        $destinationPath = storage_path('app/public/' . $storageFolder);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $zipEntry  = $zip->statIndex($i);
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

            $baseName     = pathinfo(basename($entryName), PATHINFO_FILENAME);
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
                $errors[]     = "Nama '$namaFromFile' tidak ditemukan di kelas $kelas (file: " . basename($entryName) . ")";
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
}
