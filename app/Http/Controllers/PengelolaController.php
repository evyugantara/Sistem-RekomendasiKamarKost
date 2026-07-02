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
    
    public function dashboard()
    {
        $user = Auth::user();
        $kosts = Kost::where('user_id', $user->id)->get();
        $kostIds = $kosts->pluck('id')->toArray();
        
        $totalKosts = $kosts->count();
        
        
        $totalKamars = Kamar::whereIn('kost_id', $kostIds)->count();
        $kamarTersedia = Kamar::whereIn('kost_id', $kostIds)->where('status', 'tersedia')->count();
        $kamarTerisi = Kamar::whereIn('kost_id', $kostIds)->where('status', 'terisi')->count();

        
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

    
    public function profil()
    {
        $user = Auth::user();
        $profil = $user->profilPengelola ?? new ProfilPengelola();
        return view('pengelola.profil', compact('user', 'profil'));
    }

    
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

        
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        
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

    
    public function kost()
    {
        $user = Auth::id();
        $kosts = Kost::with(['fotos', 'kampus'])->where('user_id', $user)->get();
        return view('pengelola.kost.index', compact('kosts'));
    }

    
    public function tambahKost()
    {
        $campuses = Kampus::all();
        if ($campuses->isEmpty()) {
            return redirect()->route('pengelola.kost')->with('error', 'Sistem belum memiliki data kampus. Silakan hubungi Admin.');
        }
        return view('pengelola.kost.tambah', compact('campuses'));
    }

    
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

    
    public function editKost($id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        $campuses = Kampus::all();
        return view('pengelola.kost.edit', compact('kost', 'campuses'));
    }

    
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

    
    public function hapusKost($id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        
        foreach ($kost->fotos as $foto) {
            $filePath = public_path($foto->image_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $kost->delete();

        return redirect()->route('pengelola.kost')->with('success', 'Kost berhasil dihapus dari sistem.');
    }

    
    public function fasilitas($id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        
        $currentAttrs = AtributKost::where('kost_id', $kost->id)
            ->pluck('opsi_kriteria_id')
            ->toArray();

        
        $kriterias = Kriteria::with('opsiKriteria')->get();
        
        $kriteriaUmum = $kriterias->where('category', 'umum');
        $kriteriaPribadi = $kriterias->where('category', 'pribadi');
        $kriteriaBersama = $kriterias->where('category', 'bersama');

        return view('pengelola.kost.fasilitas', compact('kost', 'kriteriaUmum', 'kriteriaPribadi', 'kriteriaBersama', 'currentAttrs'));
    }

    
    public function simpanFasilitas(Request $request, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        
        $attrs = $request->input('attrs', []);

        
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

    
    public function fotos($id)
    {
        $kost = Kost::with('fotos')->where('user_id', Auth::id())->findOrFail($id);
        return view('pengelola.kost.foto', compact('kost'));
    }

    
    public function simpanFoto(Request $request, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            
            $destPath = public_path('uploads/kosts');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }

            $image->move($destPath, $fileName);
            $relativePath = 'uploads/kosts/' . $fileName;

            
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

    
    public function hapusFoto($id, $fotoId)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        $foto = FotoKost::where('kost_id', $kost->id)->findOrFail($fotoId);

        
        $filePath = public_path($foto->image_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $wasPrimary = $foto->is_primary;
        $foto->delete();

        
        if ($wasPrimary) {
            $nextFoto = FotoKost::where('kost_id', $kost->id)->first();
            if ($nextFoto) {
                $nextFoto->update(['is_primary' => true]);
            }
        }

        return redirect()->route('pengelola.kost.fotos', $kost->id)->with('success', 'Foto berhasil dihapus.');
    }

    
    public function setFotoUtama($id, $fotoId)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($id);
        
        
        FotoKost::where('kost_id', $kost->id)->update(['is_primary' => false]);

        
        $foto = FotoKost::where('kost_id', $kost->id)->findOrFail($fotoId);
        $foto->update(['is_primary' => true]);

        return redirect()->route('pengelola.kost.fotos', $kost->id)->with('success', 'Foto utama berhasil diubah.');
    }

    
    public function kamar($kost_id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamars = Kamar::where('kost_id', $kost->id)->get();
        return view('pengelola.kost.kamar.index', compact('kost', 'kamars'));
    }

    
    public function tambahKamar($kost_id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        
        
        $kriterias = Kriteria::with('opsiKriteria')->where('category', 'pribadi')->get();
        
        return view('pengelola.kost.kamar.tambah', compact('kost', 'kriterias'));
    }

    
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

    
    public function editKamar($kost_id, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamar = Kamar::where('kost_id', $kost->id)->findOrFail($id);

        
        $kriterias = Kriteria::with('opsiKriteria')->where('category', 'pribadi')->get();

        
        $activeOpts = AtributKamar::where('kamar_id', $kamar->id)->pluck('opsi_kriteria_id')->toArray();

        return view('pengelola.kost.kamar.edit', compact('kost', 'kamar', 'kriterias', 'activeOpts'));
    }

    
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

        
        AtributKamar::where('kamar_id', $kamar->id)->delete();

        
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

    
    public function hapusKamar($kost_id, $id)
    {
        $kost = Kost::where('user_id', Auth::id())->findOrFail($kost_id);
        $kamar = Kamar::where('kost_id', $kost->id)->findOrFail($id);

        
        if ($kamar->image_path && File::exists(public_path($kamar->image_path))) {
            File::delete(public_path($kamar->image_path));
        }

        $kamar->delete();

        return redirect()->route('pengelola.kost.kamar', $kost->id)->with('success', 'Kamar berhasil dihapus.');
    }
}
