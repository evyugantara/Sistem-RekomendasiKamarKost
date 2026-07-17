<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Kamar;
use App\Models\OpsiKriteria;
use App\Models\Kriteria;
use App\Models\PreferensiMahasiswa;
use App\Services\RecommendationService;
use ReflectionMethod;

class TestRecommendation extends Command
{
    protected $signature = 'test:recommendation {userId? : ID Mahasiswa/User}';
    protected $description = 'Kalkulasi detail langkah-per-langkah Cosine Similarity tingkat kriteria (16 dimensi) sesuai Bab III Skripsi';

    public function handle()
    {
      

        // 1. Ambil seluruh kriteria asli dari database (total kriteria, misal 16 kriteria)
        $kriterias = Kriteria::orderBy('id')->get();
        $kriteriaCount = $kriterias->count();

        if ($kriteriaCount === 0) {
            $this->error("Database kriteria kosong! Silakan jalankan seeder terlebih dahulu.");
            return 1;
        }

        // 2. Tentukan User/Mahasiswa yang akan diuji
        $userId = $this->argument('userId');
        $user = null;

        if ($userId) {
            $user = User::where('role', 'mahasiswa')->find($userId);
            if (!$user) {
                $this->error("User Mahasiswa dengan ID {$userId} tidak ditemukan!");
                return 1;
            }
        } else {
            $firstPref = PreferensiMahasiswa::first();
            if ($firstPref) {
                $user = User::find($firstPref->user_id);
            }
            if (!$user) {
                $user = User::where('role', 'mahasiswa')->first();
            }
        }

        if (!$user) {
            $this->error("Tidak ada user Mahasiswa/Penghuni di database untuk diuji!");
            return 1;
        }

        // 3. Bangun data preferensi terpilih per Kriteria (hanya 1 opsi terpilih per kriteria)
        $prefOptions = [];
        $isSimulated = false;

        foreach ($kriterias as $kriteria) {
            // Cari apakah user memiliki preferensi untuk kriteria ini
            $pref = PreferensiMahasiswa::where('user_id', $user->id)
                ->whereHas('opsiKriteria', function($q) use ($kriteria) {
                    $q->where('kriteria_id', $kriteria->id);
                })
                ->first();

            if ($pref) {
                $prefOptions[$kriteria->id] = $pref->opsiKriteria;
            } else {
                // Gunakan opsi tiruan/simulasi jika user belum mengisi
                $defaultOpsi = OpsiKriteria::where('kriteria_id', $kriteria->id)
                    ->where(function($q) {
                        $q->where('value', 'like', '%Campur%')
                          ->orWhere('value', 'like', '%400 Ribu - 600 Ribu%')
                          ->orWhere('value', 'like', '%400%')
                          ->orWhere('value', 'like', '%Token%')
                          ->orWhere('value', 'like', '%PDAM%')
                          ->orWhere('value', 'like', '%Mobil%')
                          ->orWhere('value', 'like', '%Kamar Mandi Dalam%')
                          ->orWhere('value', 'like', '%Duduk%')
                          ->orWhere('value', 'like', '%Kasur%')
                          ->orWhere('value', 'like', '%Lemari%')
                          ->orWhere('value', 'like', '%Meja%')
                          ->orWhere('value', 'like', '%AC%')
                          ->orWhere('value', 'like', '%Wi-Fi%')
                          ->orWhere('value', 'like', '%Jemuran%')
                          ->orWhere('value', 'like', '%Dapur%')
                          ->orWhere('value', 'like', '%Kulkas%');
                    })
                    ->first();

                if (!$defaultOpsi) {
                    $defaultOpsi = OpsiKriteria::where('kriteria_id', $kriteria->id)->first();
                }

                if ($defaultOpsi) {
                    $prefOptions[$kriteria->id] = $defaultOpsi;
                    $isSimulated = true;
                }
            }
        }

        // TAHAP 1: PEMBENTUKAN VEKTOR PREFERENSI PENGHUNI
        $this->warn("\n1. Implementasi Pembentukan Vektor Preferensi Penghuni");
        $this->line(" ➜ Nama Penghuni : " . $user->name . " (ID: " . $user->id . ")");
        if ($isSimulated) {
            $this->comment(" ⚠ Catatan: Penghuni belum mengisi preferensi di web, memuat preferensi simulasi.");
        } else {
            $this->line(" ➜ Preferensi dibentuk dari kriteria yang dipilih penghuni.");
        }

        // Vektor Preferensi P (selalu bernilai 1 karena ini adalah target yang diinginkan user)
        $vectorP = array_fill(0, $kriteriaCount, 1);

        // Tampilkan Tabel Preferensi Vektor A (Penghuni)
        $prefHeaders = ['No', 'Kriteria', 'Preferensi Terpilih (Atribut)', 'Nilai Biner (P)'];
        $prefRows = [];
        $idx = 0;
        foreach ($kriterias as $kriteria) {
            $opt = $prefOptions[$kriteria->id] ?? null;
            $prefRows[] = [
                $idx + 1,
                $kriteria->name,
                $opt ? $opt->value : '-',
                1
            ];
            $idx++;
        }
        $this->table($prefHeaders, $prefRows);

        // TAHAP 2: PEMBENTUKAN VEKTOR ATRIBUT KAMAR KOST
        $this->warn("\n2. Implementasi Pembentukan Vektor Atribut Kamar Kost");

        // Ambil SELURUH Kamar Tersedia
        $kamars = Kamar::with([
            'atributKamar',
            'kost.atributKost'
        ])
        ->where('status', 'tersedia')
        ->whereHas('kost.user', function($q) {
            $q->where('status', 'active');
        })
        ->get();

        if ($kamars->isEmpty()) {
            $this->error("Tidak ada Kamar tersedia di database untuk direkomendasikan.");
            return 1;
        }

        $this->line(" ➜ Jumlah Kamar Aktif yang Dievaluasi: " . $kamars->count() . " Kamar");

        // Kriteria harga
        $kriteriaHarga = Kriteria::where('name', 'Harga Sewa')->first();
        $hargaOptions = [];
        if ($kriteriaHarga) {
            $hargaOptions = OpsiKriteria::where('kriteria_id', $kriteriaHarga->id)->get();
        }

        $service = new RecommendationService();
        $getOpsiIdsMethod = new ReflectionMethod(RecommendationService::class, 'getHargaSewaOpsiIds');
        $getOpsiIdsMethod->setAccessible(true);

        // TAHAP 3: PERHITUNGAN COSINE SIMILARITY LANGKAH-PER-LANGKAH (16 DIMENSI)
        $this->warn("\n3. Perhitungan Tingkat Kemiripan (Cosine Similarity) Setiap Kamar");
        
        $this->line("");
        $this->info("                          P . K");
        $this->info("   Similarity(P, K) = ------------");
        $this->info("                       ||P|| ||K||");
        $this->line("");
        $this->line("Keterangan:");
        $this->line(" P   = Vektor preferensi penghuni.");
        $this->line(" K   = Vektor atribut kamar kost.");
        $this->line(" P·K = Nilai dot product (perkalian titik) antara kedua vektor.");
        $this->line(" ‖P‖ = Panjang vektor (magnitude) preferensi penghuni.");
        $this->line(" ‖K‖ = Panjang vektor (magnitude) atribut kamar kost.");
        $this->line("\n--------------------------------------------------------------------------------------------------");
        $this->line("                   MULAI PERHITUNGAN MANUAL (WHITE BOX TRACING - 16 DIMENSI)                       ");
        $this->line("--------------------------------------------------------------------------------------------------");

        // Hitung Magnitudo P
        $sumP2 = 0;
        $magPTerms = [];
        foreach ($vectorP as $val) {
            $sumP2 += $val * $val;
            $magPTerms[] = "{$val}²";
        }
        $magnitudeP = sqrt($sumP2);

        $rankingData = [];

        foreach ($kamars as $kIdx => $kamar) {
            $vectorK = array_fill(0, $kriteriaCount, 0);

            // Dapatkan ID atribut kamar & kost
            $kamarAttrIds = $kamar->atributKamar->pluck('opsi_kriteria_id')->toArray();
            $kostAttrIds = $kamar->kost ? $kamar->kost->atributKost->pluck('opsi_kriteria_id')->toArray() : [];
            
            // Dapatkan ID atribut harga sewa yang cocok untuk kamar ini
            $matchedHargaOpsiIds = $getOpsiIdsMethod->invoke($service, $kamar->price, $hargaOptions);

            $cIdx = 0;
            foreach ($kriterias as $kriteria) {
                $targetOpt = $prefOptions[$kriteria->id] ?? null;
                if ($targetOpt) {
                    if ($kriteria->name === 'Harga Sewa') {
                        // Untuk harga sewa, cek apakah opsi harga yang cocok mengandung ID target preferensi
                        if (in_array($targetOpt->id, $matchedHargaOpsiIds)) {
                            $vectorK[$cIdx] = 1;
                        }
                    } else {
                        // Cek apakah kamar atau kost memiliki opsi kriteria ID target preferensi
                        if (in_array($targetOpt->id, $kamarAttrIds) || in_array($targetOpt->id, $kostAttrIds)) {
                            $vectorK[$cIdx] = 1;
                        }
                    }
                }
                $cIdx++;
            }

            // Perhitungan Matematika Detail
            $dotProduct = 0;
            $dotProductTerms = [];
            for ($i = 0; $i < $kriteriaCount; $i++) {
                $prod = $vectorP[$i] * $vectorK[$i];
                $dotProduct += $prod;
                $dotProductTerms[] = "({$vectorP[$i]}×{$vectorK[$i]})";
            }

            // Hitung Magnitudo K
            $sumK2 = 0;
            $magKTerms = [];
            foreach ($vectorK as $val) {
                $sumK2 += $val * $val;
                $magKTerms[] = "{$val}²";
            }
            $magnitudeK = sqrt($sumK2);

            // Hitung Similarity
            $similarity = ($magnitudeP * $magnitudeK > 0) ? ($dotProduct / ($magnitudeP * $magnitudeK)) : 0;

            // Cetak Detail Kalkulasi Kamar
            $noKamar = $kIdx + 1;
            $this->info("\n{$noKamar}. Perhitungan Cosine Similarity: {$kamar->name} ({$kamar->kost->name})");
            
            // a. Dot Product
            $this->line(" a. Langkah pertama adalah menghitung dot product antara vektor A (Preferensi) dan Vektor Kamar:");
            $this->comment("    " . implode(' + ', $dotProductTerms));
            $this->info("    Hasilnya: P · Vektor Kamar = " . $dotProduct);
            
            // b. Panjang Vektor P
            $this->line(" b. Menghitung Panjang Vektor P (Preferensi Penghuni):");
            $this->line("    P = (" . implode(',', $vectorP) . ")");
            $this->comment("    Dikuadratkan menjadi: √(" . implode(' + ', $magPTerms) . ") = √{$sumP2} = " . round($magnitudeP, 4));
            
            // c. Panjang Vektor K
            $this->line(" c. Menghitung Panjang Vektor K (Atribut Kamar Kost):");
            $this->line("    K = (" . implode(',', $vectorK) . ")");
            $this->comment("    Dikuadratkan menjadi: √(" . implode(' + ', $magKTerms) . ") = √{$sumK2} = " . round($magnitudeK, 4));

            // Similarity
            $this->line(" d. Menghitung Similarity:");
            $this->info("    Similarity = {$dotProduct} / (" . round($magnitudeP, 4) . " × " . round($magnitudeK, 4) . ") = " . round($similarity, 4) . " (" . round($similarity * 100, 2) . "% Cocok)");

            $rankingData[] = [
                'kamar_id' => $kamar->id,
                'kamar_name' => $kamar->name,
                'kost_name' => $kamar->kost->name ?? 'Kost Tanpa Nama',
                'price' => $kamar->price,
                'dot_product' => $dotProduct,
                'magnitude_k' => round($magnitudeK, 4),
                'similarity' => $similarity
            ];
        }

        // Urutkan Peringkat
        usort($rankingData, function ($a, $b) {
            if ($b['similarity'] == $a['similarity']) {
                return $a['price'] <=> $b['price'];
            }
            return $b['similarity'] <=> $a['similarity'];
        });

        // TAHAP 4: IMPLEMENTASI HASIL PERHITUNGAN
        $this->warn("\n4. Implementasi Hasil Perhitungan");

        $this->info("\n--- TABEL HASIL PERHITUNGAN REKOMENDASI (TERURUT KEMIRIPAN TERTINGGI) ---");
        $rankHeaders = ['Rank', 'Nama Kost/Kamar', 'Nilai Similarity', 'Persentase Cocok', 'Rekomendasi Terpilih'];
        $rankRows = [];

        foreach ($rankingData as $index => $data) {
            $rankRows[] = [
                $index + 1,
                $data['kamar_name'] . ' (' . $data['kost_name'] . ')',
                round($data['similarity'], 4),
                round($data['similarity'] * 100, 2) . '%',
                $index + 1 // Urutan Peringkat
            ];
        }
        $this->table($rankHeaders, $rankRows);
        $this->info("==================================================================================================");

        return 0;
    }
}
