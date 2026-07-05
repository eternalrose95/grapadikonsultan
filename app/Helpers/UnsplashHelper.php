<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnsplashHelper
{
    /**
     * Base URL for Unsplash API
     */
    private const API_URL = 'https://api.unsplash.com';

    /**
     * Cache duration in seconds (24 hours)
     */
    private const CACHE_DURATION = 86400;

    /**
     * Get Unsplash Access Key from config
     */
    private static function getAccessKey(): ?string
    {
        return config('services.unsplash.access_key');
    }

    /**
     * Search for photos and get a random one from results
     * 
     * @param string|null $query Search query
     * @param int $width Desired image width
     * @return string|null Image URL or null if not found
     */
    public static function searchPhoto(?string $query = null, int $width = 800): ?string
    {
        $accessKey = self::getAccessKey();
        
        if (empty($accessKey)) {
            Log::warning('Unsplash API key not configured');
            return self::getFallbackImage($query);
        }

        $query = self::cleanQuery($query);
        if (empty($query)) {
            $query = 'business technology';
        }

        // Cache key based on query
        $cacheKey = 'unsplash_' . md5($query);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($query, $width, $accessKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Client-ID ' . $accessKey,
                ])->get(self::API_URL . '/search/photos', [
                    'query' => $query,
                    'per_page' => 10,
                    'orientation' => 'landscape',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data['results'])) {
                        // Get a random photo from results
                        $photo = $data['results'][array_rand($data['results'])];
                        
                        // Return the URL with width parameter
                        return $photo['urls']['regular'] ?? $photo['urls']['small'];
                    }
                }

                Log::warning('Unsplash API returned no results for query: ' . $query);
            } catch (\Exception $e) {
                Log::error('Unsplash API error: ' . $e->getMessage());
            }

            return self::getFallbackImage($query);
        });
    }

    /**
     * Get a consistent photo for the same article (based on ID)
     * 
     * @param int|string $id Unique identifier
     * @param string|null $query Search query
     * @param int $width Desired image width
     * @return string Image URL
     */
    public static function getConsistentPhoto($id, ?string $query = null, int $width = 800): string
    {
        $accessKey = self::getAccessKey();
        
        if (empty($accessKey)) {
            return self::getFallbackImage($query);
        }

        $originalQuery = self::cleanQuery($query);
        if (empty($originalQuery)) {
            $originalQuery = 'business technology';
        }

        // Cache key based on id and query for consistency
        $cacheKey = 'unsplash_article_' . $id . '_' . md5($originalQuery);

        return Cache::remember($cacheKey, self::CACHE_DURATION * 7, function () use ($id, $originalQuery, $width, $accessKey) {
            // List of queries to try - original first, then fallback to generic queries
            $queriesToTry = [
                $originalQuery,
                'business',
                'technology',
                'office',
            ];

            foreach ($queriesToTry as $query) {
                try {
                    $response = Http::timeout(10)->withHeaders([
                        'Authorization' => 'Client-ID ' . $accessKey,
                    ])->get(self::API_URL . '/search/photos', [
                        'query' => $query,
                        'per_page' => 30,
                        'orientation' => 'landscape',
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        
                        if (!empty($data['results'])) {
                            // Use ID to deterministically select a photo (consistent result)
                            $index = abs(crc32((string)$id)) % count($data['results']);
                            $photo = $data['results'][$index];
                            
                            return $photo['urls']['regular'] ?? $photo['urls']['small'];
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Unsplash API error for query "' . $query . '": ' . $e->getMessage());
                }
            }

            // Only use fallback if all API attempts failed
            return self::getFallbackImage($originalQuery);
        });
    }

    /**
     * Get fallback image URL when API fails
     */
    private static function getFallbackImage(?string $query = null): string
    {
        // Use free source.unsplash.com as fallback
        $query = self::cleanQuery($query) ?: 'business,technology';
        return 'https://source.unsplash.com/800x600/?' . urlencode($query);
    }

    /**
     * Clean and format search query
     */
    private static function cleanQuery(?string $query): string
    {
        if (empty($query)) {
            return '';
        }
        
        // Remove special characters
        $query = preg_replace('/[^\w\s,]/u', '', $query);
        $query = trim(preg_replace('/\s+/', ' ', $query));
        
        return strtolower(substr($query, 0, 100));
    }

    /**
     * Get image for article based on title or category
     */
    public static function getArticleImage(
        ?string $title = null, 
        ?string $category = null, 
        ?int $articleId = null,
        int $width = 800,
        int $height = 600
    ): string {
        // Prioritize category for more relevant images
        $query = $category ?? self::extractKeywords($title);
        
        if ($articleId) {
            return self::getConsistentPhoto($articleId, $query, $width);
        }
        
        return self::searchPhoto($query, $width) ?? self::getFallbackImage($query);
    }

    /**
     * Extract keywords from title for search query
     */
    private static function extractKeywords(?string $title): string
    {
        if (empty($title)) {
            return 'business technology';
        }
        
        // Common stop words to remove
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 
                      'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'as',
                      'yang', 'dan', 'atau', 'di', 'ke', 'dari', 'untuk', 'dengan',
                      'ini', 'itu', 'adalah', 'akan', 'telah', 'sudah', 'bisa', 'dapat'];
        
        $words = preg_split('/\s+/', strtolower($title));
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        $keywords = array_slice(array_values($keywords), 0, 3);
        
        return !empty($keywords) ? implode(' ', $keywords) : 'business technology';
    }
}
