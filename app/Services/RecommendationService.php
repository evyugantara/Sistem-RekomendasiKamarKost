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

            
            $hargaOpsiIds = $this->getHargaSewaOpsiIds($kamar->price, $hargaOptions);
            foreach ($hargaOpsiIds as $opsiId) {
                if (isset($optionIndexMap[$opsiId])) {
                    $idx = $optionIndexMap[$opsiId];
                    $vectorK[$idx] = 1;
                    $kamarDetails[] = $optionNameMap[$opsiId];
                }
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


        
        public function cosineSimilarity(array $vectorP, array $vectorK): float
        {
            $dotProduct = 0;
            $sumP = 0;
            $sumK = 0;

            foreach ($vectorP as $i => $value) {
                $dotProduct += $value * $vectorK[$i];
                $sumP += $value * $value;
                $sumK += $vectorK[$i] * $vectorK[$i];
            }

            if ($sumP == 0 || $sumK == 0) {
                return 0;
            }

            return round(
                $dotProduct / (sqrt($sumP) * sqrt($sumK)),
                4
            );
        }

    
    private function getHargaSewaOpsiIds($price, $hargaOptions)
    {
        $matchedIds = [];
        foreach ($hargaOptions as $option) {
            $parsed = $this->parseRangeValue($option->value);
            if (!$parsed) {
                continue;
            }

            if ($parsed['type'] === 'lt' && $price < $parsed['val']) {
                $matchedIds[] = $option->id;
            } elseif ($parsed['type'] === 'gt' && $price > $parsed['val']) {
                $matchedIds[] = $option->id;
            } elseif ($parsed['type'] === 'range' && $price >= $parsed['min'] && $price <= $parsed['max']) {
                $matchedIds[] = $option->id;
            }
        }
        return $matchedIds;
    }

    private function parseRangeValue($val)
    {
        $val = strtolower(trim($val));
        $val = str_replace(',', '.', $val);

        $convertToNum = function($str) {
            $str = trim($str);
            $multiplier = 1;
            if (strpos($str, 'ribu') !== false) {
                $multiplier = 1000;
                $str = str_replace('ribu', '', $str);
            } elseif (strpos($str, 'juta') !== false) {
                $multiplier = 1000000;
                $str = str_replace('juta', '', $str);
            }
            return (float)$str * $multiplier;
        };

        if (strpos($val, '<') === 0) {
            $num = $convertToNum(substr($val, 1));
            return ['type' => 'lt', 'val' => $num];
        }

        if (strpos($val, '>') === 0) {
            $num = $convertToNum(substr($val, 1));
            return ['type' => 'gt', 'val' => $num];
        }

        if (strpos($val, '-') !== false) {
            $parts = explode('-', $val);
            if (count($parts) === 2) {
                $min = $convertToNum($parts[0]);
                $max = $convertToNum($parts[1]);
                return ['type' => 'range', 'min' => $min, 'max' => $max];
            }
        }

        return null;
    }
}
