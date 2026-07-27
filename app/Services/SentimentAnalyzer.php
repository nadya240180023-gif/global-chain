<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalyzer
{
    /**
     * Analyze the sentiment of a given text using lexicon-based matching.
     *
     * @param string $text
     * @return array
     */
    public function analyze(string $text): array
    {
        if (empty(trim($text))) {
            return [
                'sentiment' => 'Neutral',
                'score' => 0,
                'positive_matches' => [],
                'negative_matches' => [],
                'pos_count' => 0,
                'neg_count' => 0
            ];
        }

        // Clean and tokenize text
        $cleanText = strtolower(strip_tags($text));
        // Replace non-alphabetic chars with spaces
        $cleanText = preg_replace('/[^a-z\s]/', '', $cleanText);
        $words = preg_split('/\s+/', $cleanText, -1, PREG_SPLIT_NO_EMPTY);

        // Fetch words from DB
        $dbPos = PositiveWord::pluck('word')->map('strtolower')->toArray();
        $dbNeg = NegativeWord::pluck('word')->map('strtolower')->toArray();

        $defaultPos = [
            'pertumbuhan', 'stabil', 'pemulihan', 'keuntungan', 'solid', 'sukses', 'optimis', 'meningkat', 'efisiensi', 'kerjasama',
            'growth', 'stable', 'recovery', 'gain', 'profit', 'solid', 'success', 'optimistic', 'increase', 'efficiency', 'cooperation', 'positive', 'boost', 'improve', 'rise'
        ];
        $defaultNeg = [
            'krisis', 'memburuk', 'resesi', 'lemah', 'penurunan', 'kepadatan', 'penundaan', 'penumpukan', 'mogok', 'bencana', 'badai', 'peringatan', 'konflik', 'sanksi', 'risiko',
            'crisis', 'worsen', 'recession', 'weak', 'decline', 'congestion', 'delay', 'strike', 'disaster', 'storm', 'warning', 'conflict', 'sanction', 'risk', 'negative', 'drop', 'loss', 'fail', 'hit', 'worry'
        ];

        $positiveLexicon = array_unique(array_merge($dbPos, $defaultPos));
        $negativeLexicon = array_unique(array_merge($dbNeg, $defaultNeg));

        $posMatches = [];
        $negMatches = [];

        foreach ($words as $word) {
            if (in_array($word, $positiveLexicon)) {
                $posMatches[] = $word;
            }
            if (in_array($word, $negativeLexicon)) {
                $negMatches[] = $word;
            }
        }

        $posCount = count($posMatches);
        $negCount = count($negMatches);

        if ($posCount > $negCount) {
            $sentiment = 'Positive';
        } elseif ($negCount > $posCount) {
            $sentiment = 'Negative';
        } else {
            $sentiment = 'Neutral';
        }

        // Sentiment score between -100 and +100 based on word proportion, or simple difference
        $totalMatches = $posCount + $negCount;
        $score = 0;
        if ($totalMatches > 0) {
            $score = round((($posCount - $negCount) / $totalMatches) * 100);
        }

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'positive_matches' => array_unique($posMatches),
            'negative_matches' => array_unique($negMatches),
            'pos_count' => $posCount,
            'neg_count' => $negCount
        ];
    }
}
