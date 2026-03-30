<?php

namespace app\services;

/**
 * ProductNormalizerService — normalizes product names for consistent matching.
 * 
 * Three-layer approach:
 * Layer 1: Alias exact lookup (handled in ProductCatalog::findByAlias)
 * Layer 2: Normalized string comparison (this class)
 * Layer 3: PHP similar_text fuzzy matching
 */
class ProductNormalizerService
{
    /** Common noise words to strip during normalization */
    private static array $stopwords = [
        'type',
        'merk',
        'ukuran',
        'buatan',
        'produk',
        'barang',
        'original',
        'import',
        'lokal',
        'baru',
        'standart',
        'standard',
        'no',
        'no.',
        'sni',
        'mutu',
        'grade',
    ];

    /**
     * Normalize a product name for consistent comparison.
     * - Lowercase
     * - Remove special chars
     * - Strip noise words
     * - Word-order independent (sorted)
     */
    public static function normalize(string $name): string
    {
        $name = mb_strtolower(trim($name));
        // Remove special characters, keep alphanumeric and spaces
        $name = preg_replace('/[^a-z0-9\s]/', ' ', $name);
        // Collapse multiple spaces
        $name = preg_replace('/\s+/', ' ', $name);
        // Split into words
        $words = explode(' ', trim($name));
        // Filter short and noise words
        $words = array_filter(
            $words,
            fn($w) =>
            strlen($w) > 1 && !in_array($w, self::$stopwords)
        );
        // Sort for word-order independence
        sort($words);
        return implode(' ', $words);
    }

    /**
     * Calculate similarity percentage between two product names (0-100).
     */
    public static function similarityScore(string $a, string $b): float
    {
        similar_text(self::normalize($a), self::normalize($b), $pct);
        return round($pct, 2);
    }

    /**
     * Find best catalog match from an array of candidates.
     * Each candidate must have 'id' and 'canonical_name'.
     * Returns null if no match above threshold.
     */
    public static function findBestMatch(string $rawName, array $catalog, float $threshold = 65.0): ?array
    {
        $best = null;
        $bestScore = 0;

        foreach ($catalog as $item) {
            $score = self::similarityScore($rawName, $item['canonical_name']);
            if ($score > $bestScore && $score >= $threshold) {
                $best = $item;
                $bestScore = $score;
            }
        }

        return $best ? array_merge($best, ['match_score' => $bestScore]) : null;
    }

    /**
     * Suggest a catalog match for a raw product name.
     * Returns ['catalog_id' => int|null, 'confidence' => float, 'method' => string]
     */
    public static function suggestCatalog(string $rawName, int $companyId = 1): array
    {
        // Layer 1: exact alias lookup
        $byAlias = \app\models\ProductCatalog::findByAlias($rawName, $companyId);
        if ($byAlias) {
            return [
                'catalog_id' => $byAlias->id,
                'canonical' => $byAlias->canonical_name,
                'confidence' => 100.0,
                'method' => 'alias_exact',
            ];
        }

        // Layer 2 + 3: fuzzy match
        $fuzzy = \app\models\ProductCatalog::fuzzyFind($rawName, $companyId);
        if ($fuzzy) {
            return [
                'catalog_id' => $fuzzy['id'],
                'canonical' => $fuzzy['canonical_name'],
                'confidence' => $fuzzy['score'],
                'method' => 'fuzzy',
            ];
        }

        return [
            'catalog_id' => null,
            'canonical' => null,
            'confidence' => 0.0,
            'method' => 'no_match',
        ];
    }
}
