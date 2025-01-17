<?php
// blog.php - Single feed (No full page reload approach)
require_once './components/navbar.php';
require_once './components/footer.php';

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
  <?php renderNavbar(); ?>

  <main class="container mx-auto p-4 flex-1">
    <h2 class="text-3xl font-bold mb-4 text-gray-700">Drug News &amp; Blog (AJAX Pagination)</h2>
    <p class="text-sm text-gray-500 mb-4">
      Source:
      <a href="https://www.drugs.com" target="_blank" rel="noopener" class="underline text-blue-600">
        Drugs.com
      </a>.
      We do not guarantee accuracy. <strong class="text-red-500">No liability accepted.</strong>
    </p>

    <!-- Our container for the feed items -->
    <div id="rss-container" class="mb-8"></div>
    <!-- Our container for pagination buttons -->
    <div id="pagination-controls" class="flex justify-center space-x-2"></div>
  </main>

  <?php renderFooter(); ?>

  <!-- The updated blog.js for no reload -->
  <script src="js/blog.js"></script>
</body>
</html>
