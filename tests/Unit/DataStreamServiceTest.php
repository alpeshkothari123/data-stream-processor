<?php

namespace Tests\Unit;

use App\Services\DataStreamService;
use Tests\TestCase;

class DataStreamServiceTest extends TestCase
{
    public function testAnalyzeStream()
    {
        $service = new DataStreamService();
        $result = $service->analyzeStream("AAABBBCCCAAABBBCCC", 3, 5, ['AAA']);

        $this->assertEquals([
            ['subsequence' => 'BBB', 'count' => 3],
            ['subsequence' => 'CCC', 'count' => 3],
        ], $result);
    }
}
