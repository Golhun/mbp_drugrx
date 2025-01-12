<?php
// blog_page.php (Dashboard partial) - multiple feeds no reload
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<div class="space-y-6">
  <h2 class="text-2xl font-bold mb-2">Blog Page</h2>
  <p class="text-xs text-gray-500">
    Source: <a href="https://www.drugs.com" target="_blank" rel="noopener" class="underline text-blue-600">Drugs.com</a>.
    We do not guarantee accuracy. <strong class="text-red-500">No liability accepted.</strong>
  </p>

  <!-- We'll have a container for the feed items -->
  <div id="rss-container" class="mb-8"></div>
  <!-- We'll have controls for pagination -->
  <div id="pagination-controls" class="flex space-x-2 justify-center"></div>
</div>

 
<!-- The same blog.js script but maybe you define feedUrl differently? -->
<script>
  // If you want a different feed or multiple feeds for the dashboard,
  // you'd adapt the feedUrl or pass some param to blog.js
</script>
