<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\RecommendationService;

class CosineSimilarityTest extends TestCase
{
    public function test_cosine_similarity()
    {
        $service = new RecommendationService();

        $vectorP = [1,1,1,1,1];
        $vectorK = [1,1,1,1,0];

        $hasil = $service->cosineSimilarity($vectorP, $vectorK);

        $this->assertEquals(0.8944, $hasil);
    }
}