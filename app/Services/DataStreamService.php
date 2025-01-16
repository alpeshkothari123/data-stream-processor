<?php

namespace App\Services;
use Illuminate\Support\Facades\Validator;

class DataStreamService
{
    
    public function analyzeStream(string $stream, int $k, int $top, array $exclude): array
    {
        $length = strlen($stream);
        $subsequenceCounts = [];

        for ($i = 0; $i <= $length - $k; $i++) {
            $subsequence = substr($stream, $i, $k);

            if (in_array($subsequence, $exclude)) {
                continue;
            }

            if (!isset($subsequenceCounts[$subsequence])) {
                $subsequenceCounts[$subsequence] = 0;
            }
            $subsequenceCounts[$subsequence]++;
        }

        arsort($subsequenceCounts);

        $result = [];
        $count = 0;

        foreach ($subsequenceCounts as $subsequence => $frequency) {
            if ($count >= $top) {
                break;
            }
            $result[] = [
                'subsequence' => $subsequence,
                'count' => $frequency,
            ];
            $count++;
        }

        return $result;
    }
}
