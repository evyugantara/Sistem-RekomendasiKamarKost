<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilPengelola;
use App\Models\Kampus;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\Kost;
use App\Models\FotoKost;
use App\Models\AtributKost;
use App\Models\Kamar;
use App\Models\AtributKamar;
use App\Models\KamarFavorit;
use App\Models\LogKontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class PengelolaController extends Controller
{
    /**
     * Dashboard Pengelola Kost
     */
    public function dashboard()
    {
        $user = Auth::user();
        $kosts = Kost::where('user_id', $user->id)->get();
        $kostIds = $kosts->pluck('id')->toArray();
        
        $totalKosts = $kosts->count();
        
        // Hitung metrik unit kamar
        $totalKamars = Kamar::whereIn('kost_id', $kostIds)->count();
        $kamarTersedia = Kamar::whereIn('kost_id', $kostIds)->where('status', 'tersedia')->count();
        $kamarTerisi = Kamar::whereIn('kost_id', $kostIds)->where('status', 'terisi')->count();

        // Riwayat kontak terakhir (untuk tabel riwayat bawah)
        $kamarIds = Kamar::whereIn('kost_id', $kostIds)->pluck('id')->toArray();
        $recentContacts = LogKontak::with(['user.profilMahasiswa', 'kamar.kost'])
            ->whereIn('kamar_id', $kamarIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pengelola.dashboard', compact(
            'user', 
            'totalKosts', 
            'totalKamars', 
            'kamarTersedia', 
            'kamarTerisi', 
            'recentContacts'
        ));
    }

    /**
     * Tampilkan Form Profil Pengelola
     */
    public function profil()
    {
        $user = Auth::user();
        $profil = $user->profilPengelola ?? new ProfilPengelola();
        return view('pengelola.profil', compact('user', 'profil'));
    }

    /**
     * Update Profil Pengelola
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $profil = $user->profilPengelola;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'ktp_number' => 'required|string|min:16',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        // Update core User
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Update Profil Pengelola
        if (!$profil) {
            $profil = new ProfilPengelola();
            $profil->user_id = $user->id;
        }
        $profil->ktp_number = $request->ktp_number;
        $profil->phone = $request->phone;
        $profil->address = $request->address;
        $profil->save();

        return redirect()->route('pengelola.profil')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Daftar Kost Milik Pengelola
     */
    public function kost()
    {
        $user = Auth::id();
        $kosts = Kost::with(['fotos', 'kampus'])->where('user_id', $user)->get();
        return view('pengelola.kost.index', compact('kosts'));
    }

    /**
     * Form Tambah Kost Baru
     */
    public function tambahKost()
    {
        $campuses = Kampus::all();
        if ($campuses->isEmpty()) {
            return redirect()->route('pengelola.kost')->with('error', 'Sistem belum memiliki data kampus. Silakan hubungi Admin.');
        }
        return view('pengelola.kost.tambah', compact('campuses'));
    }

    /**
     * Simpan Kost Baru ke Database
     */
    public function simpanKost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'kampus_id' => 'required|exists:kampus,id',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        Kost::create([
            'user_id' => Auth::id(),
            'kampus_id' => $request->kampus_id,
            'name' => $request->name,
            'price' => $request->price,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
        ]);

        return redirect()->route('pengelola.kost')->with('success', 'Kost baru berhasil ditambahkan! Silakan lengkapi fasilitas dan foto kost.');
    }

    /**
     * Form Edit Data Kost
     */
    public function editKost($id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        $campuses = Kampus::all();
        return view('pengelola.kost.edit', compact('kost', 'campuses'));
    }

    /**
     * Simpan Pembaruan Data Kost
     */
    public function updateKost(Request $request, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'kampus_id' => 'required|exists:kampus,id',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $kost->update([
            'name' => $request->name,
            'price' => $request->price,
            'kampus_id' => $request->kampus_id,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
        ]);

        return redirect()->route('pengelola.kost')->with('success', 'Data kost berhasil diperbarui!');
    }

    /**
     * Hapus Kost beserta Relasinya
     */
    public function hapusKost($id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        // Hapus file foto dari storage lokal sebelum menghapus record database
        foreach ($kost->fotos as $foto) {
            $filePath = public_path($foto->image_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $kost->delete();

        return redirect()->route('pengelola.kost')->with('success', 'Kost berhasil dihapus dari sistem.');
    }

    /**
     * Form Kelola Fasilitas / Atribut Kost
     */
    public function fasilitas($id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        // Ambil opsi kriteria yang saat ini aktif pada kost ini
        $currentAttrs = AtributKost::where('kost_id', $kost->id)
            ->pluck('opsi_kriteria_id')
            ->toArray();

        // Ambil semua kriteria yang dikelompokkan berdasarkan kategori
        $kriterias = Kriteria::with('opsiKriteria')->get();
        
        $kriteriaUmum = $kriterias->where('category', 'umum');
        $kriteriaPribadi = $kriterias->where('category', 'pribadi');
        $kriteriaBersama = $kriterias->where('category', 'bersama');

        return view('pengelola.kost.fasilitas', compact('kost', 'kriteriaUmum', 'kriteriaPribadi', 'kriteriaBersama', 'currentAttrs'));
    }

    /**
     * Simpan Fasilitas / Atribut Kost
     */
    public function simpanFasilitas(Request $request, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        // Opsi dikirim dalam format array input: attrs[kriteria_id] = [opsi_kriteria_id1, opsi_kriteria_id2, ...]
        $attrs = $request->input('attrs', []);

        // Hapus atribut lama
        AtributKost::where('kost_id', $kost->id)->delete();

        $savedCount = 0;
        foreach ($attrs as $kriteriaId => $opsiIds) {
            if (is_array($opsiIds)) {
                foreach ($opsiIds as $opsiId) {
                    if ($opsiId) {
                        AtributKost::create([
                            'kost_id' => $kost->id,
                            'kriteria_id' => $kriteriaId,
                            'opsi_kriteria_id' => $opsiId
                        ]);
                        $savedCount++;
                    }
                }
            } else {
                if ($opsiIds) {
                    AtributKost::create([
                        'kost_id' => $kost->id,
                        'kriteria_id' => $kriteriaId,
                        'opsi_kriteria_id' => $opsiIds
                    ]);
                    $savedCount++;
                }
            }
        }

        return redirect()->route('pengelola.kost')->with('success', 'Fasilitas & atribut kost berhasil diperbarui! (' . $savedCount . ' kriteria diterapkan)');
    }

    /**
     * Halaman Kelola Galeri Foto Kost
     */
    public function fotos($id)
    {
        $kost = Kost::with('fotos')->where('user_id', Auth::id())->findOrFail($id);
        return view('pengelola.kost.foto', compact('kost'));
    }

    /**
     * Simpan Foto Baru
     */
    public function simpanFoto(Request $request, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Buat folder uploads jika belum ada
            $destPath = public_path('uploads/kosts');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }

            $image->move($destPath, $fileName);
            $relativePath = 'uploads/kosts/' . $fileName;

            // Jika ini foto pertama, set sebagai foto utama (is_primary = true)
            $isPrimary = FotoKost::where('kost_id', $kost->id)->count() === 0;

            FotoKost::create([
                'kost_id' => $kost->id,
                'image_path' => $relativePath,
                'is_primary' => $isPrimary
            ]);

            return redirect()->route('pengelola.kost.fotos', $kost->id)->with('success', 'Foto baru berhasil diunggah!');
        }

        return redirect()->route('pengelola.kost.fotos', $kost->id)->with('error', 'Terjadi kesalahan saat mengunggah foto.');
    }

    /**
     * Hapus Foto dari Kost
     */
    public function hapusFoto($id, $fotoId)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        $foto = FotoKost::where('kost_id', $kost->id)->findOrFail($fotoId);

        // Hapus file fisik
        $filePath = public_path($foto->image_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $wasPrimary = $foto->is_primary;
        $foto->delete();

        // Jika foto yang dihapus adalah foto utama, set foto lainnya sebagai foto utama
        if ($wasPrimary) {
            $nextFoto = FotoKost::where('kost_id', $kost->id)->first();
            if ($nextFoto) {
                $nextFoto->update(['is_primary' => true]);
            }
        }

        return redirect()->route('pengelola.kost.fotos', $kost->id)->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Set Foto sebagai Foto Utama
     */
    public function setFotoUtama($id, $fotoId)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        // Set semua foto kost ini menjadi false
        FotoKost::where('kost_id', $kost->id)->update(['is_primary' => false]);

        // Set foto terpilih menjadi true
        $foto = FotoKost::where('kost_id', $kost->id)->findOrFail($fotoId);
        $foto->update(['is_primary' => true]);

        return redirect()->route('pengelola.kost.fotos', $kost->id)->with('success', 'Foto utama berhasil diubah.');
    }

    /**
     * Tampilkan Daftar Kamar Kost
     */
    public function kamar($kost_id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamars = Kamar::where('kost_id', $kost->id)->get();
        return view('pengelola.kost.kamar.index', compact('kost', 'kamars'));
    }

    /**
     * Form Tambah Kamar Baru
     */
    public function tambahKamar($kost_id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        
        // Ambil kriteria kategori pribadi untuk diisi di tingkat kamar
        $kriterias = Kriteria::with('opsiKriteria')->where('category', 'pribadi')->get();
        
        return view('pengelola.kost.kamar.tambah', compact('kost', 'kriterias'));
    }

    /**
     * Simpan Kamar Baru ke Database
     */
    public function simpanKamar(Request $request, $kost_id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,terisi',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kamars'), $filename);
            $imagePath = 'uploads/kamars/' . $filename;
        }

        $kamar = Kamar::create([
            'kost_id' => $kost->id,
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
            'image_path' => $imagePath,
        ]);

        // Simpan Fasilitas Pribadi (Atribut Kamar) - format array checkbox
        if ($request->filled('fasilitas')) {
            foreach ($request->fasilitas as $kriteriaId => $opsiIds) {
                if (is_array($opsiIds)) {
                    foreach ($opsiIds as $opsiId) {
                        if ($opsiId) {
                            AtributKamar::create([
                                'kamar_id' => $kamar->id,
                                'kriteria_id' => $kriteriaId,
                                'opsi_kriteria_id' => $opsiId
                            ]);
                        }
                    }
                } else {
                    if ($opsiIds) {
                        AtributKamar::create([
                            'kamar_id' => $kamar->id,
                            'kriteria_id' => $kriteriaId,
                            'opsi_kriteria_id' => $opsiIds
                        ]);
                    }
                }
            }
        }

        return redirect()->route('pengelola.kost.kamar', $kost->id)->with('success', 'Kamar baru berhasil ditambahkan!');
    }

    /**
     * Form Edit Data Kamar
     */
    public function editKamar($kost_id, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamar = Kamar::where('kost_id', $kost->id)->findOrFail($id);

        // Ambil kriteria kategori pribadi
        $kriterias = Kriteria::with('opsiKriteria')->where('category', 'pribadi')->get();

        // Ambil atribut kamar yang aktif
        $activeOpts = AtributKamar::where('kamar_id', $kamar->id)->pluck('opsi_kriteria_id')->toArray();

        return view('pengelola.kost.kamar.edit', compact('kost', 'kamar', 'kriterias', 'activeOpts'));
    }

    /**
     * Update Data Kamar
     */
    public function updateKamar(Request $request, $kost_id, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamar = Kamar::where('kost_id', $kost->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,terisi',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $kamar->image_path;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kamars'), $filename);
            $imagePath = 'uploads/kamars/' . $filename;
        }

        $kamar->update([
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
            'image_path' => $imagePath,
        ]);

        // Hapus atribut lama
        AtributKamar::where('kamar_id', $kamar->id)->delete();

        // Simpan atribut baru - format array checkbox
        if ($request->filled('fasilitas')) {
            foreach ($request->fasilitas as $kriteriaId => $opsiIds) {
                if (is_array($opsiIds)) {
                    foreach ($opsiIds as $opsiId) {
                        if ($opsiId) {
                            AtributKamar::create([
                                'kamar_id' => $kamar->id,
                                'kriteria_id' => $kriteriaId,
                                'opsi_kriteria_id' => $opsiId
                            ]);
                        }
                    }
                } else {
                    if ($opsiIds) {
                        AtributKamar::create([
                            'kamar_id' => $kamar->id,
                            'kriteria_id' => $kriteriaId,
                            'opsi_kriteria_id' => $opsiIds
                        ]);
                    }
                }
            }
        }

        return redirect()->route('pengelola.kost.kamar', $kost->id)->with('success', 'Data kamar berhasil diperbarui!');
    }

    /**
     * Hapus Kamar
     */
    public function hapusKamar($kost_id, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamar = Kamar::where('kost_id', $kost->id)->findOrFail($id);

        // Hapus gambar fisik jika ada
        if ($kamar->image_path && File::exists(public_path($kamar->image_path))) {
            File::delete(public_path($kamar->image_path));
        }

        $kamar->delete();

        return redirect()->route('pengelola.kost.kamar', $kost->id)->with('success', 'Kamar berhasil dihapus.');
    }
}
