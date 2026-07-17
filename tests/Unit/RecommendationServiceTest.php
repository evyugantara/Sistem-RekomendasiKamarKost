<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\RecommendationService;
use App\Models\User;
use App\Models\ProfilPengelola;
use App\Models\Kampus;
use App\Models\Kost;
use App\Models\Kamar;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\AtributKamar;
use App\Models\PreferensiMahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    private $service;
    private $parseRangeMethod;
    private $getOpsiIdsMethod;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new RecommendationService();

     
        $this->parseRangeMethod = new ReflectionMethod(RecommendationService::class, 'parseRangeValue');
        $this->parseRangeMethod->setAccessible(true);

        $this->getOpsiIdsMethod = new ReflectionMethod(RecommendationService::class, 'getHargaSewaOpsiIds');
        $this->getOpsiIdsMethod->setAccessible(true);
    }

    public function test_it_can_parse_less_than_range_strings()
    {
        $parsed = $this->parseRangeMethod->invoke($this->service, '< 500 Ribu');
        
        $this->assertNotNull($parsed);
        $this->assertEquals('lt', $parsed['type']);
        $this->assertEquals(500000, $parsed['val']);
    }

    public function test_it_can_parse_dash_range_strings_with_ribu_and_juta()
    {
       
        $parsed1 = $this->parseRangeMethod->invoke($this->service, '400 Ribu - 600 Ribu');
        $this->assertNotNull($parsed1);
        $this->assertEquals('range', $parsed1['type']);
        $this->assertEquals(400000, $parsed1['min']);
        $this->assertEquals(600000, $parsed1['max']);

    
        $parsed2 = $this->parseRangeMethod->invoke($this->service, '500 Ribu - 1 Juta');
        $this->assertNotNull($parsed2);
        $this->assertEquals('range', $parsed2['type']);
        $this->assertEquals(500000, $parsed2['min']);
        $this->assertEquals(1000000, $parsed2['max']);

       
        $parsed3 = $this->parseRangeMethod->invoke($this->service, '1 Juta - 1,5 Juta');
        $this->assertNotNull($parsed3);
        $this->assertEquals('range', $parsed3['type']);
        $this->assertEquals(1000000, $parsed3['min']);
        $this->assertEquals(1500000, $parsed3['max']);
    }

    public function test_it_can_match_price_correctly_to_multiple_overlapping_ranges()
    {
     
        $optLessThan500 = new OpsiKriteria();
        $optLessThan500->id = 101;
        $optLessThan500->value = '< 500 Ribu';

        $optRange400to600 = new OpsiKriteria();
        $optRange400to600->id = 102;
        $optRange400to600->value = '400 Ribu - 600 Ribu';

        $optRange500to1000 = new OpsiKriteria();
        $optRange500to1000->id = 103;
        $optRange500to1000->value = '500 Ribu - 1 Juta';

        $optRange500to800 = new OpsiKriteria();
        $optRange500to800->id = 104;
        $optRange500to800->value = '500 Ribu - 800 Ribu';
        
        $hargaOptions = collect([
            $optLessThan500,
            $optRange400to600,
            $optRange500to1000,
            $optRange500to800
        ]);

       
        $matchedIds = $this->getOpsiIdsMethod->invoke($this->service, 500000, $hargaOptions);
        
        $this->assertContains(102, $matchedIds);
        $this->assertContains(103, $matchedIds);
        $this->assertContains(104, $matchedIds);
        $this->assertNotContains(101, $matchedIds);

       
        $matchedIds2 = $this->getOpsiIdsMethod->invoke($this->service, 300000, $hargaOptions);
        
        $this->assertContains(101, $matchedIds2);
        $this->assertNotContains(102, $matchedIds2);
        $this->assertNotContains(103, $matchedIds2);
    }

    public function test_it_calculates_cosine_similarity_correctly()
    {
        // Vektor P: [1, 1, 1, 0, 0] (panjang = √3)
        // Vektor K: [1, 0, 1, 1, 0] (panjang = √3)
        // Dot Product: (1*1) + (1*0) + (1*1) + (0*1) + (0*0) = 2
        // Similarity: 2 / (√3 * √3) = 2 / 3 = 0.6667
        $similarity = $this->service->cosineSimilarity([1, 1, 1, 0, 0], [1, 0, 1, 1, 0]);
        $this->assertEquals(0.6667, $similarity);
    }

    public function test_it_can_generate_recommendations_ordered_by_similarity_score()
    {
      
        $student = User::create([
            'name' => 'Test Student',
            'username' => 'teststudent',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'status' => 'active'
        ]);

       
        $owner = User::create([
            'name' => 'Test Owner',
            'username' => 'testowner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'pengelola',
            'status' => 'active'
        ]);
        ProfilPengelola::create([
            'user_id' => $owner->id,
            'ktp_number' => '1234567890123456',
            'phone' => '081234567890',
            'address' => 'Owner Address'
        ]);

       
        $campus = Kampus::create([
            'name' => 'Test Campus',
            'address' => 'Campus Address',
            'latitude' => -6.81245,
            'longitude' => 107.14090
        ]);

        
        $kost = Kost::create([
            'user_id' => $owner->id,
            'kampus_id' => $campus->id,
            'name' => 'Test Kost',
            'price' => 500000,
            'address' => 'Kost Address',
            'latitude' => -6.81245,
            'longitude' => 107.14090
        ]);

        
        $kriteriaHarga = Kriteria::create(['name' => 'Harga Sewa', 'type' => 'select', 'category' => 'umum']);
        $opsiHarga = OpsiKriteria::create(['kriteria_id' => $kriteriaHarga->id, 'value' => '500 Ribu - 1 Juta']);

        $kriteriaListrik = Kriteria::create(['name' => 'Sistem Listrik', 'type' => 'select', 'category' => 'pribadi']);
        $opsiToken = OpsiKriteria::create(['kriteria_id' => $kriteriaListrik->id, 'value' => 'Listrik Token (Prepaid)']);
        $opsiBulanan = OpsiKriteria::create(['kriteria_id' => $kriteriaListrik->id, 'value' => 'Listrik Bulanan (Include)']);

        $kriteriaWifi = Kriteria::create(['name' => 'Fasilitas Wi-Fi', 'type' => 'checkbox', 'category' => 'bersama']);
        $opsiWifi = OpsiKriteria::create(['kriteria_id' => $kriteriaWifi->id, 'value' => 'Ada Wi-Fi']);

        
        PreferensiMahasiswa::create([
            'user_id' => $student->id,
            'kriteria_id' => $kriteriaListrik->id,
            'opsi_kriteria_id' => $opsiToken->id
        ]);
        PreferensiMahasiswa::create([
            'user_id' => $student->id,
            'kriteria_id' => $kriteriaWifi->id,
            'opsi_kriteria_id' => $opsiWifi->id
        ]);

        
        $kamarA = Kamar::create([
            'kost_id' => $kost->id,
            'name' => 'Kamar A',
            'price' => 500000,
            'status' => 'tersedia'
        ]);
        AtributKamar::create([
            'kamar_id' => $kamarA->id,
            'kriteria_id' => $kriteriaListrik->id,
            'opsi_kriteria_id' => $opsiToken->id
        ]);
        AtributKamar::create([
            'kamar_id' => $kamarA->id,
            'kriteria_id' => $kriteriaWifi->id,
            'opsi_kriteria_id' => $opsiWifi->id
        ]);

      
        $kamarB = Kamar::create([
            'kost_id' => $kost->id,
            'name' => 'Kamar B',
            'price' => 550000,
            'status' => 'tersedia'
        ]);
        AtributKamar::create([
            'kamar_id' => $kamarB->id,
            'kriteria_id' => $kriteriaListrik->id,
            'opsi_kriteria_id' => $opsiBulanan->id
        ]);

     
        $results = $this->service->getRecommendations($student->id);

        $this->assertNotEmpty($results);
        $this->assertCount(2, $results);

        
        $this->assertEquals($kamarA->id, $results[0]['kamar']->id);
        $this->assertGreaterThan($results[1]['similarity'], $results[0]['similarity']);
        $this->assertEquals($kamarB->id, $results[1]['kamar']->id);
    }
}
