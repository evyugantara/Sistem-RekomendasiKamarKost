<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\PreferensiMahasiswa;

class RecommendationService
{
    
    public function getRecommendations(int $userId): array
    {
        
        $allOptions = OpsiKriteria::orderBy('id')->get();
        $optionCount = $allOptions->count();

        if ($optionCount === 0) {
            return [];
        }

        
        $optionIndexMap = [];
        $optionNameMap = [];
        foreach ($allOptions as $index => $option) {
            $optionIndexMap[$option->id] = $index;
            $optionNameMap[$option->id] = $option->kriteria->name . ': ' . $option->value;
        }

        
        $hargaSewaKriteria = Kriteria::where('name', 'Harga Sewa')->first();
        $hargaOptions = [];
        if ($hargaSewaKriteria) {
            $hargaOptions = OpsiKriteria::where('kriteria_id', $hargaSewaKriteria->id)->get();
        }

        
        $preferences = PreferensiMahasiswa::where('user_id', $userId)->get();
        if ($preferences->isEmpty()) {
            return [];
        }

        
        $vectorP = array_fill(0, $optionCount, 0);
        $prefDetails = [];
        foreach ($preferences as $pref) {
            if (isset($optionIndexMap[$pref->opsi_kriteria_id])) {
                $idx = $optionIndexMap[$pref->opsi_kriteria_id];
                $vectorP[$idx] = 1;
                $prefDetails[] = $optionNameMap[$pref->opsi_kriteria_id];
            }
        }

        
        $sumP2 = 0;
        foreach ($vectorP as $val) {
            $sumP2 += $val * $val;
        }
        $magnitudeP = sqrt($sumP2);

        if ($magnitudeP == 0) {
            return [];
        }

        
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
            
            $vectorK = array_fill(0, $optionCount, 0);
            $kamarDetails = [];

            
            foreach ($kamar->atributKamar as $attr) {
                if (isset($optionIndexMap[$attr->opsi_kriteria_id])) {
                    $idx = $optionIndexMap[$attr->opsi_kriteria_id];
                    $vectorK[$idx] = 1;
                    $kamarDetails[] = $optionNameMap[$attr->opsi_kriteria_id];
                }
            }

            
            if ($kamar->kost) {
                foreach ($kamar->kost->atributKost as $attr) {
                    if (isset($optionIndexMap[$attr->opsi_kriteria_id])) {
                        $idx = $optionIndexMap[$attr->opsi_kriteria_id];
                        $vectorK[$idx] = 1;
                        $kamarDetails[] = $optionNameMap[$attr->opsi_kriteria_id];
                    }
                }
            }

            
            $hargaOpsiId = $this->getHargaSewaOpsiId($kamar->price, $hargaOptions);
            if ($hargaOpsiId && isset($optionIndexMap[$hargaOpsiId])) {
                $idx = $optionIndexMap[$hargaOpsiId];
                $vectorK[$idx] = 1;
                $kamarDetails[] = $optionNameMap[$hargaOpsiId];
            }

            
            $sumK2 = 0;
            foreach ($vectorK as $val) {
                $sumK2 += $val * $val;
            }
            $magnitudeK = sqrt($sumK2);

            
            $dotProduct = 0;
            $matchedOptions = [];
            for ($i = 0; $i < $optionCount; $i++) {
                $prod = $vectorP[$i] * $vectorK[$i];
                $dotProduct += $prod;
                if ($prod > 0) {
                    $matchedOptions[] = $optionNameMap[$allOptions[$i]->id];
                }
            }

            
            $similarity = 0;
            if ($magnitudeP > 0 && $magnitudeK > 0) {
                $similarity = $dotProduct / ($magnitudeP * $magnitudeK);
            }

            
            $similarityRounded = round($similarity, 4);

            
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

        
        usort($results, function ($a, $b) {
            if ($b['similarity'] == $a['similarity']) {
                
                return $a['kamar']->price <=> $b['kamar']->price;
            }
            return $b['similarity'] <=> $a['similarity'];
        });

        return $results;
    }

    
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
