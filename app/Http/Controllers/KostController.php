<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Kamar;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\Kampus;
use App\Models\KamarFavorit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KostController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Kost::with(['fotos', 'kampus', 'atributKost.opsiKriteria', 'kamars'])
            ->whereHas('user', function($q) {
                $q->where('status', 'active');
            });

        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        
        if ($request->filled('jenis')) {
            $jenisVal = $request->jenis; 
            $query->whereHas('atributKost.opsiKriteria', function($q) use ($jenisVal) {
                $q->where('value', $jenisVal);
            });
        }

        
        if ($request->filled('harga')) {
            $hargaRange = $request->harga;
            if ($hargaRange === 'under_500') {
                $query->where('price', '<', 500000);
            } elseif ($hargaRange === '500_1m') {
                $query->whereBetween('price', [500000, 1000000]);
            } elseif ($hargaRange === '1m_15m') {
                $query->whereBetween('price', [1000000, 1500000]);
            } elseif ($hargaRange === 'above_15m') {
                $query->where('price', '>', 1500000);
            }
        }

        $kosts = $query->orderBy('created_at', 'desc')->get();

        return view('landing', compact('kosts'));
    }

    public function show(Request $request, $id)
    {
        $kost = Kost::with(['fotos', 'kampus', 'atributKost.opsiKriteria.kriteria', 'user.profilPengelola', 'kamars.atributKamar.opsiKriteria.kriteria'])
            ->whereHas('user', function($q) {
                $q->where('status', 'active');
            })
            ->findOrFail($id);
        
        
        $kampus = $kost->kampus;

        
        $kamars = $kost->kamars;

        
        $highlightedKamarId = $request->query('kamar');
        $highlightedKamar = null;
        if ($highlightedKamarId) {
            $highlightedKamar = $kamars->where('id', $highlightedKamarId)->first();
        }
        if (!$highlightedKamar) {
            $highlightedKamar = $kamars->first();
        }

        
        $atributUmum = [];
        $atributBersama = [];

        foreach ($kost->atributKost as $attr) {
            $kategori = $attr->kriteria->category;
            $namaKriteria = $attr->kriteria->name;
            $nilaiOpsi = $attr->opsiKriteria->value;

            if ($kategori === 'bersama') {
                $atributBersama[] = ['name' => $namaKriteria, 'value' => $nilaiOpsi];
            } else {
                $atributUmum[] = ['name' => $namaKriteria, 'value' => $nilaiOpsi];
            }
        }

        
        $atributPribadi = [];
        if ($highlightedKamar) {
            foreach ($highlightedKamar->atributKamar as $attr) {
                $namaKriteria = $attr->kriteria->name;
                $nilaiOpsi = $attr->opsiKriteria->value;
                $atributPribadi[] = ['name' => $namaKriteria, 'value' => $nilaiOpsi];
            }
        }

        
        $isFavorit = false;
        if (Auth::check() && Auth::user()->role === 'mahasiswa' && $highlightedKamar) {
            $isFavorit = KamarFavorit::where('user_id', Auth::id())->where('kamar_id', $highlightedKamar->id)->exists();
        }

        return view('detail-kost', compact(
            'kost', 
            'kampus', 
            'kamars',
            'highlightedKamar',
            'atributUmum', 
            'atributBersama', 
            'atributPribadi',
            'isFavorit'
        ));
    }
}
