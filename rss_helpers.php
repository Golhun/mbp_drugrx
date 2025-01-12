<?php
// rss_helpers.php
// A unified library for fetching RSS feeds, caching in a flat JSON array, and merging multiple feeds.

/**
 * We define a single cache file (you can define different ones if you prefer).
 * Here, we store only a FLAT ARRAY (no 'timestamp' or nested keys).
 */
define('RSS_CACHE_FILE', __DIR__ . '/rss_cache.json');

/**
 * Attempt to fetch & parse a single feed from $url.
 * Return an array of items (title, description, link, pubDate) or empty array on failure.
 */
function fetchSingleFeed($url) {
    $items = [];
    try {
        $xmlString = @file_get_contents($url);
        if (!$xmlString) {
            return [];
        }
        $xml = @simplexml_load_string($xmlString);
        if (!$xml || !isset($xml->channel->item)) {
            return [];
        }

        foreach ($xml->channel->item as $entry) {
            $items[] = [
                'title'       => (string)($entry->title ?? ''),
                'description' => (string)($entry->description ?? ''),
                'link'        => (string)($entry->link ?? ''),
                'pubDate'     => (string)($entry->pubDate ?? ''),
            ];
        }
    } catch (\Exception $e) {
        // error_log('fetchSingleFeed error: '.$e->getMessage());
    }
    return $items;
}

/**
 * Merge multiple feed URLs into one array. Return empty if they all fail.
 */
function fetchMultipleFeeds(array $feedUrls) {
    $allItems = [];
    $anySuccess = false;

    foreach ($feedUrls as $url) {
        $items = fetchSingleFeed($url);
        if (!empty($items)) {
            $anySuccess = true;
        }
        $allItems = array_merge($allItems, $items);
    }
    // If never succeeded, return []
    if (!$anySuccess) {
        return [];
    }
    return $allItems;
}

/**
 * Load from the flat array cache file. Return an array or [] on error.
 */
function loadCache() {
    if (!file_exists(RSS_CACHE_FILE)) {
        return [];
    }
    try {
        $json = file_get_contents(RSS_CACHE_FILE);
        $data = json_decode($json, true);
        if (is_array($data)) {
            return $data;
        }
    } catch (\Exception $e) {
        // error_log('loadCache error: '.$e->getMessage());
    }
    return [];
}

/**
 * Save a flat array of items to the cache.
 */
function saveCache(array $items) {
    try {
        $json = json_encode($items, JSON_PRETTY_PRINT);
        file_put_contents(RSS_CACHE_FILE, $json);
    } catch (\Exception $e) {
        // error_log('saveCache error: '.$e->getMessage());
    }
}

/**
 * Attempt to fetch either a single or multiple feeds. If empty, fallback to cache.
 * If success, sort by pubDate desc and store in cache.
 *
 * $feedUrls can be a single string or an array of strings.
 */
function fetchRssWithFallback($feedUrls) {
    // Convert single string to array if needed
    if (is_string($feedUrls)) {
        $feedUrls = [$feedUrls];
    }

    // Attempt live fetch
    $items = [];
    if (count($feedUrls) === 1) {
        // Single feed
        $items = fetchSingleFeed($feedUrls[0]);
    } else {
        // Multiple feeds
        $items = fetchMultipleFeeds($feedUrls);
    }

    if (empty($items)) {
        // fallback to cache
        $items = loadCache();
    } else {
        // Sort by pubDate desc
        usort($items, function($a, $b) {
            return strtotime($b['pubDate'] ?? '0') - strtotime($a['pubDate'] ?? '0');
        });
        // Save
        saveCache($items);
    }

    return $items;
}

/**
 * Helper: snippet for description
 */
function snippet($html, $maxLen=120) {
    $text = strip_tags($html);
    if (strlen($text) > $maxLen) {
        $text = substr($text, 0, $maxLen) . '...';
    }
    return $text;
}
