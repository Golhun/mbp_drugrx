<?php
// blog.php (Public Full Page)
session_start();
require_once 'config.php';
require_once './components/navbar.php';
require_once './components/footer.php';

// You can store your feed in a variable or environment
$feedUrl = 'https://www.drugs.com/feeds/medical_news.xml';

// -------------- RSS Fetcher Logic --------------
function fetchAllRssItems($url) {
    $items = [];
    try {
        $xmlString = @file_get_contents($url);
        if (!$xmlString) return $items;

        $xml = @simplexml_load_string($xmlString);
        if (!$xml || !isset($xml->channel->item)) return $items;

        foreach ($xml->channel->item as $entry) {
            $items[] = [
                'title'       => (string)($entry->title ?? ''),
                'description' => (string)($entry->description ?? ''),
                'link'        => (string)($entry->link ?? ''),
                'pubDate'     => (string)($entry->pubDate ?? ''),
            ];
        }
    } catch (\Exception $e) {
        // log or ignore
    }
    return $items;
}

// -------------- Get & Paginate --------------
$items = fetchAllRssItems($feedUrl);
$totalItems = count($items);

// Sort by pubDate descending if you prefer
usort($items, function($a, $b) {
    return strtotime($b['pubDate']) - strtotime($a['pubDate']);
});

$perPage = 10; // items per page
$totalPages = max(1, ceil($totalItems / $perPage));
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
if ($page > $totalPages) $page = $totalPages;
$startIndex = ($page - 1) * $perPage;
$pageItems = array_slice($items, $startIndex, $perPage);

// -------------- Utility to strip HTML for snippet --------------
function snippet($html, $maxLen = 120) {
    // remove tags
    $text = strip_tags($html);
    if (strlen($text) > $maxLen) {
        $text = substr($text, 0, $maxLen) . '...';
    }
    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Drug News & Blog - Public</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 flex flex-col min-h-screen">

  <!-- Navbar (if you want your site’s top nav) -->
  <?php renderNavbar(); ?>

  <main class="container mx-auto p-4 flex-1">
    <h2 class="text-3xl font-bold mb-4 text-gray-700">Drug News & Blog</h2>
    <p class="text-sm text-gray-500 mb-4">
      Source: 
      <a href="https://www.drugs.com" target="_blank" rel="noopener" class="underline text-blue-600">Drugs.com</a>. 
      We do not guarantee accuracy. <strong class="text-red-500">No liability accepted.</strong>
    </p>

    <?php if ($totalItems === 0): ?>
      <p class="text-gray-500">No feed data available or feed is unreachable.</p>
    <?php else: ?>
      <!-- Grid of Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php foreach ($pageItems as $post): ?>
          <article class="bg-white rounded shadow p-4 flex flex-col">
            <h3 class="text-lg font-bold mb-2 line-clamp-2 hover:text-pink-600">
              <?php echo htmlspecialchars($post['title']); ?>
            </h3>
            <p class="text-xs text-gray-400 mb-2"><?php echo htmlspecialchars($post['pubDate']); ?></p>
            <p class="text-sm text-gray-700 flex-1"><?php echo snippet($post['description']); ?></p>
            <div class="mt-3 text-right">
              <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" rel="noopener"
                 class="text-blue-600 text-sm hover:underline"
              >
                Read More
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center space-x-2">
        <?php
        // We'll only show up to 10 page links
        $maxLinks = 12;
        $startPage = max(1, $page - 5);
        $endPage = min($totalPages, $startPage + $maxLinks - 1);

        // Prev link
        if ($page > 1) {
            $prev = $page - 1;
            echo "<a href='?page=$prev' class='px-3 py-1 border rounded hover:bg-gray-100'>Prev</a>";
        }

        for ($p = $startPage; $p <= $endPage; $p++) {
            $classes = ($p == $page) 
              ? "bg-pink-500 text-white px-3 py-1 rounded" 
              : "bg-white border px-3 py-1 rounded hover:bg-gray-100";
            echo "<a href='?page=$p' class='$classes'>$p</a>";
        }

        // Next link
        if ($page < $totalPages) {
            $next = $page + 1;
            echo "<a href='?page=$next' class='px-3 py-1 border rounded hover:bg-gray-100'>Next</a>";
        }
        ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <?php renderFooter(); ?>
</body>
</html>
