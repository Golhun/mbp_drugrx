<?php
// index.php (PARTIAL)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<div class="mb-6">
  <div class="flex border-b tabs">
    <button
      id="tab-interactions"
      class="px-4 py-2 text-gray-600 hover:text-blue-600 border-b-2 border-transparent transition active"
    >
      Drug Interactions
    </button>
    <button
      id="tab-substitutes"
      class="px-4 py-2 text-gray-600 hover:text-green-600 border-b-2 border-transparent transition"
    >
      Drug Substitutes
    </button>
  </div>
</div>

<div id="tab-content-container">
  <p class="text-gray-500">Loading sub-tab...</p>
</div>
