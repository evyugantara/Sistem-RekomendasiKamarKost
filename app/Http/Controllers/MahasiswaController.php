<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilMahasiswa;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\PreferensiMahasiswa;
use App\Models\Kost;
use App\Models\Kamar;
use App\Models\KamarFavorit;
use App\Models\LogKontak;
use App\Models\LogRekomendasi;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    
    public function dashboard()
    {
        $user = Auth::user();
        $favCount = KamarFavorit::where('user_id', $user->id)->count();
        $prefCount = PreferensiMahasiswa::where('user_id', $user->id)->count();
        $contactCount = LogKontak::where('user_id', $user->id)->count();
        
        
        $recentLogs = LogRekomendasi::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();

        return view('mahasiswa.dashboard', compact('user', 'favCount', 'prefCount', 'contactCount', 'recentLogs'));
    }

    
    public function profil()
    {
        $user = Auth::user();
        $profil = $user->profilMahasiswa ?? new ProfilMahasiswa();
        return view('mahasiswa.profil', compact('user', 'profil'));
    }

    
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $profil = $user->profilMahasiswa;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'gender' => 'required|in:Laki-laki,Perempuan',
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
            $profil = new ProfilMahasiswa();
            $profil->user_id = $user->id;
        }
        $profil->gender = $request->gender;
        $profil->phone = $request->phone;
        $profil->address = $request->address;
        $profil->save();

        return redirect()->route('mahasiswa.profil')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    
    public function rekomendasi()
    {
        $user = Auth::id();
        
        
        $recommendations = $this->recommendationService->getRecommendations($user);

        
        $mahasiswaPrefs = PreferensiMahasiswa::with(['kriteria', 'opsiKriteria'])
            ->where('user_id', $user)
            ->get();

        
        $currentPrefs = $mahasiswaPrefs->pluck('opsi_kriteria_id')->toArray();

        
        $kriterias = Kriteria::with('opsiKriteria')->get();
        $kriteriaUmum = $kriterias->where('category', 'umum');
        $kriteriaPribadi = $kriterias->where('category', 'pribadi');
        $kriteriaBersama = $kriterias->where('category', 'bersama');

        return view('mahasiswa.rekomendasi', compact(
            'recommendations', 
            'mahasiswaPrefs', 
            'currentPrefs', 
            'kriteriaUmum', 
            'kriteriaPribadi', 
            'kriteriaBersama'
        ));
    }

    
    public function savePreferensi(Request $request)
    {
        $user = Auth::id();
        
        
        
        $prefs = $request->input('prefs', []);

        
        PreferensiMahasiswa::where('user_id', $user)->delete();

        $savedCount = 0;
        $prefSummaryArray = [];

        foreach ($prefs as $kriteriaId => $opsiIds) {
            if (is_array($opsiIds)) {
                foreach ($opsiIds as $opsiId) {
                    if ($opsiId) {
                        PreferensiMahasiswa::create([
                            'user_id' => $user,
                            'kriteria_id' => $kriteriaId,
                            'opsi_kriteria_id' => $opsiId
                        ]);

                        
                        $opsi = OpsiKriteria::with('kriteria')->find($opsiId);
                        if ($opsi) {
                            $prefSummaryArray[] = $opsi->kriteria->name . ': ' . $opsi->value;
                        }
                        $savedCount++;
                    }
                }
            } else {
                if ($opsiIds) {
                    PreferensiMahasiswa::create([
                        'user_id' => $user,
                        'kriteria_id' => $kriteriaId,
                        'opsi_kriteria_id' => $opsiIds
                    ]);

                    
                    $opsi = OpsiKriteria::with('kriteria')->find($opsiIds);
                    if ($opsi) {
                        $prefSummaryArray[] = $opsi->kriteria->name . ': ' . $opsi->value;
                    }
                    $savedCount++;
                }
            }
        }

        if ($savedCount > 0) {
            
            $recommendations = $this->recommendationService->getRecommendations($user);
            $resultsCount = count($recommendations);

            
            LogRekomendasi::create([
                'user_id' => $user,
                'preference_summary' => implode(', ', $prefSummaryArray),
                'results_count' => $resultsCount
            ]);

            return redirect()->route('mahasiswa.rekomendasi')->with('success', 'Preferensi berhasil disimpan dan rekomendasi diperbarui!');
        }

        return redirect()->route('mahasiswa.rekomendasi')->with('error', 'Silakan pilih minimal satu preferensi kriteria.');
    }

    
    public function favorit()
    {
        $user = Auth::id();
        $favorits = KamarFavorit::with(['kamar.kost.fotos', 'kamar.kost.kampus'])->where('user_id', $user)->get();

        return view('mahasiswa.favorit', compact('favorits'));
    }

    
    public function toggleFavorit($id)
    {
        $user = Auth::id();
        $fav = KamarFavorit::where('user_id', $user)->where('kamar_id', $id)->first();

        if ($fav) {
            $fav->delete();
            $status = 'removed';
            $msg = 'Kamar berhasil dihapus dari daftar favorit Anda.';
        } else {
            KamarFavorit::create([
                'user_id' => $user,
                'kamar_id' => $id
            ]);
            $status = 'added';
            $msg = 'Kamar berhasil disimpan ke daftar favorit Anda.';
        }

        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }

    
    public function contact($id, $type)
    {
        $user = Auth::id();
        $kamar = Kamar::with('kost.user.profilPengelola')->findOrFail($id);
        $kost = $kamar->kost;
        $pengelola = $kost->user;
        $profil = $pengelola->profilPengelola;

        if (!$profil || !$profil->phone) {
            return redirect()->back()->with('error', 'Kontak pengelola kost tidak tersedia.');
        }

        
        LogKontak::create([
            'user_id' => $user,
            'kamar_id' => $id,
            'contact_type' => $type
        ]);

        $phone = $profil->phone;

        if ($type === 'whatsapp') {
            
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (strpos($cleanPhone, '0') === 0) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            
            
            $message = urlencode("Halo Bpk/Ibu " . $pengelola->name . ", saya tertarik dengan \"" . $kamar->name . "\" di Kost \"" . $kost->name . "\" yang dipublikasikan di Web Rekomendasi Kost. Apakah masih ada kamar kosong?");
            
            $waUrl = "https://api.whatsapp.com/send?phone=" . $cleanPhone . "&text=" . $message;
            return redirect()->away($waUrl);
        } else {
            
            return redirect()->away('tel:' . $phone);
        }
    }

    
    public function clearPreferensi()
    {
        $user = Auth::id();
        PreferensiMahasiswa::where('user_id', $user)->delete();
        return redirect()->route('mahasiswa.rekomendasi')->with('success', 'Preferensi Anda berhasil direset/dihapus.');
    }
}
