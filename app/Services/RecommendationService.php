<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\PreferensiMahasiswa;

class RecommendationService
{
    /**
     * Hitung rekomendasi kamar berdasarkan preferensi mahasiswa menggunakan Cosine Similarity
     *
     * @param int $userId
     * @return array
     */
    public function getRecommendations(int $userId): array
    {
        // 1. Ambil semua opsi kriteria yang terdaftar di database untuk menentukan dimensi ruang vektor
        $allOptions = OpsiKriteria::orderBy('id')->get();
        $optionCount = $allOptions->count();

        if ($optionCount === 0) {
            return [];
        }

        // Buat mapping dari ID Opsi Kriteria ke index vektor (0 s.d N-1)
        $optionIndexMap = [];
        $optionNameMap = [];
        foreach ($allOptions as $index => $option) {
            $optionIndexMap[$option->id] = $index;
            $optionNameMap[$option->id] = $option->kriteria->name . ': ' . $option->value;
        }

        // Cari master opsi untuk kriteria "Harga Sewa" untuk pemetaan dinamis harga kamar
        $hargaSewaKriteria = Kriteria::where('name', 'Harga Sewa')->first();
        $hargaOptions = [];
        if ($hargaSewaKriteria) {
            $hargaOptions = OpsiKriteria::where('kriteria_id', $hargaSewaKriteria->id)->get();
        }

        // 2. Ambil preferensi mahasiswa
        $preferences = PreferensiMahasiswa::where('user_id', $userId)->get();
        if ($preferences->isEmpty()) {
            return [];
        }

        // Inisialisasi Vektor Preferensi Mahasiswa (Vector P)
        $vectorP = array_fill(0, $optionCount, 0);
        $prefDetails = [];
        foreach ($preferences as $pref) {
            if (isset($optionIndexMap[$pref->opsi_kriteria_id])) {
                $idx = $optionIndexMap[$pref->opsi_kriteria_id];
                $vectorP[$idx] = 1;
                $prefDetails[] = $optionNameMap[$pref->opsi_kriteria_id];
            }
        }

        // Hitung Magnitude Vektor P: ||P|| = sqrt(sum(P_i^2))
        $sumP2 = 0;
        foreach ($vectorP as $val) {
            $sumP2 += $val * $val;
        }
        $magnitudeP = sqrt($sumP2);

        if ($magnitudeP == 0) {
            return [];
        }

        // 3. Ambil semua kamar yang berstatus tersedia dan pemiliknya aktif
        $kamars = Kamar::with([
            'atributKamar.opsiKriteria.kriteria',
            'kost.atributKost.opsiKriteria.kriteria',
            'kost.fotos',
            'kost.kampus',
            'kost.user.profilPengelola'
        ])
        ->where('status', 'tersedia')
        ->whereHas('kost.user', function($q) {
            $q->where('status', 'active');
        })
        ->get();

        $results = [];

        foreach ($kamars as $kamar) {
            // Inisialisasi Vektor Atribut Kamar (Vector K)
            $vectorK = array_fill(0, $optionCount, 0);
            $kamarDetails = [];

            // a. Masukkan atribut spesifik kamar (Fasilitas Pribadi)
            foreach ($kamar->atributKamar as $attr) {
                if (isset($optionIndexMap[$attr->opsi_kriteria_id])) {
                    $idx = $optionIndexMap[$attr->opsi_kriteria_id];
                    $vectorK[$idx] = 1;
                    $kamarDetails[] = $optionNameMap[$attr->opsi_kriteria_id];
                }
            }

            // b. Masukkan atribut kost induknya (Fasilitas Bersama/Umum)
            if ($kamar->kost) {
                foreach ($kamar->kost->atributKost as $attr) {
                    if (isset($optionIndexMap[$attr->opsi_kriteria_id])) {
                        $idx = $optionIndexMap[$attr->opsi_kriteria_id];
                        $vectorK[$idx] = 1;
                        $kamarDetails[] = $optionNameMap[$attr->opsi_kriteria_id];
                    }
                }
            }

            // c. Petakan "Harga Sewa" kriteria secara dinamis berdasarkan harga kamar
            $hargaOpsiId = $this->getHargaSewaOpsiId($kamar->price, $hargaOptions);
            if ($hargaOpsiId && isset($optionIndexMap[$hargaOpsiId])) {
                $idx = $optionIndexMap[$hargaOpsiId];
                $vectorK[$idx] = 1;
                $kamarDetails[] = $optionNameMap[$hargaOpsiId];
            }

            // Hitung Magnitude Vektor K: ||K|| = sqrt(sum(K_i^2))
            $sumK2 = 0;
            foreach ($vectorK as $val) {
                $sumK2 += $val * $val;
            }
            $magnitudeK = sqrt($sumK2);

            // Hitung Perkalian Titik (Dot Product): P . K = sum(P_i * K_i)
            $dotProduct = 0;
            $matchedOptions = [];
            for ($i = 0; $i < $optionCount; $i++) {
                $prod = $vectorP[$i] * $vectorK[$i];
                $dotProduct += $prod;
                if ($prod > 0) {
                    $matchedOptions[] = $optionNameMap[$allOptions[$i]->id];
                }
            }

            // Hitung Cosine Similarity: Cos(theta) = (P . K) / (||P|| * ||K||)
            $similarity = 0;
            if ($magnitudeP > 0 && $magnitudeK > 0) {
                $similarity = $dotProduct / ($magnitudeP * $magnitudeK);
            }

            // Bulatkan skor kemiripan agar rapi
            $similarityRounded = round($similarity, 4);

            // Simpan detail perhitungan matematis untuk skripsi mahasiswa
            $calculationTrace = [
                'vector_p_raw' => $vectorP,
                'vector_k_raw' => $vectorK,
                'preferences_count' => $sumP2,
                'kamar_attributes_count' => $sumK2,
                'dot_product' => $dotProduct,
                'magnitude_p' => round($magnitudeP, 4),
                'magnitude_k' => round($magnitudeK, 4),
                'formula' => "Cosine Similarity = $dotProduct / (" . round($magnitudeP, 4) . " * " . round($magnitudeK, 4) . ")",
                'matched_options' => $matchedOptions,
                'unmatched_preferences' => array_diff($prefDetails, $matchedOptions),
            ];

            $results[] = [
                'kamar' => $kamar,
                'similarity' => $similarityRounded,
                'calculation' => $calculationTrace,
            ];
        }

        // 4. Urutkan hasil berdasarkan nilai similarity tertinggi ke terendah
        usort($results, function ($a, $b) {
            if ($b['similarity'] == $a['similarity']) {
                // Jika similarity sama, urutkan berdasarkan harga kamar termurah
                return $a['kamar']->price <=> $b['kamar']->price;
            }
            return $b['similarity'] <=> $a['similarity'];
        });

        return $results;
    }

    /**
     * Helper untuk menentukan opsi Harga Sewa berdasarkan nominal harga kamar
     */
    private function getHargaSewaOpsiId($price, $hargaOptions)
    {
        foreach ($hargaOptions as $option) {
            $val = trim($option->value);
            if ($val === '< 500 Ribu' && $price < 500000) {
                return $option->id;
            }
            if ($val === '500 Ribu - 1 Juta' && $price >= 500000 && $price <= 1000000) {
                return $option->id;
            }
            if ($val === '1 Juta - 1.5 Juta' && $price > 1000000 && $price <= 1500000) {
                return $option->id;
            }
            if ($val === '> 1.5 Juta' && $price > 1500000) {
                return $option->id;
            }
        }
        return null;
    }
}
