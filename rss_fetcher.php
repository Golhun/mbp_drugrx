<?php
// rss_fetcher.php
// Fetching partial items from a Drugs.com RSS feed for pagination.

function fetchRssBatch($rssUrl, $startIndex = 0, $count = 10) {
    $items = [];
    try {
        // Attempt to get the feed
        $xmlString = @file_get_contents($rssUrl);
        if (!$xmlString) {
            return $items;
        }

        $xml = @simplexml_load_string($xmlString);
        if (!$xml || !isset($xml->channel->item)) {
            return $items;
        }

        // Convert to array
        $allEntries = [];
        foreach ($xml->channel->item as $entry) {
            $allEntries[] = [
                'title'       => (string)($entry->title ?? ''),
                'description' => (string)($entry->description ?? ''),
                'link'        => (string)($entry->link ?? ''),
                'pubDate'     => (string)($entry->pubDate ?? ''),
            ];
        }

        // Slice the portion we want for pagination
        $batch = array_slice($allEntries, $startIndex, $count);
        $items = $batch;
    } catch (\Exception $e) {
        // Optionally log error
    }
    return $items;
}

function fetchRssTotalCount($rssUrl) {
    try {
        $xmlString = @file_get_contents($rssUrl);
        if (!$xmlString) return 0;
        $xml = @simplexml_load_string($xmlString);
        if (!$xml || !isset($xml->channel->item)) return 0;
        return count($xml->channel->item);
    } catch (\Exception $e) {
        return 0;
    }
}
