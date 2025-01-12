<?php
// dashboard.php
// Main entry point for the authenticated dashboard area

require_once 'config.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    // Handle "Remember Me" functionality
    if (isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];
        $stmt = $db->prepare("
            SELECT user_id 
            FROM remember_me 
            WHERE token_hash = :token_hash 
              AND expires_at > NOW()
        ");
        $stmt->execute(['token_hash' => hash('sha256', $token)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Log the user in
            $_SESSION['user_id'] = $user['user_id'];
        } else {
            // Invalid or expired token
            header('Location: login.php');
            exit();
        }
    } else {
        // Redirect to login page if no session or valid cookie
        header('Location: login.php');
        exit();
    }
}

// Include the top navbar + sidebar
include 'components/dashboard_navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - mbp_drugrx</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white min-h-screen">

  <!-- Main content area -->
  <main class="pt-20 sm:ml-64 p-4 transition-all">
    <!-- The container for partials loaded via dashboard.js -->
    <div 
      id="content-area"
      class="mx-auto max-w-4xl bg-white rounded shadow p-6 min-h-[calc(100vh-5rem)]
             opacity-100 scale-100 transition"
    >
      <!-- Dashboard.js will load the partial content (index.php / profile.php / etc.) here -->
      <p class="text-center text-gray-500">Loading...</p>
    </div>
  </main>

  <!-- Scripts in correct order (no "type=module") -->
  <!-- If you use bubbleInfo.js for drug info popups, keep it here -->
  <script src="js/bubbleInfo.js"></script>
  
  <script src="js/blog.js"></script>

  <!-- If you have a "dashboardNav.js" for toggling sidebar or other nav behaviors -->
  <script src="js/dashboardNav.js"></script>

  <!-- 1) Global state + restore functions -->
  <script src="js/script.js"></script>
  <!-- 2) Interactions + Substitutes code, referencing window.appState -->
  <script src="js/interaction.js"></script>
  <script src="js/substitute.js"></script>
  <!-- 3) Dashboard logic that loads partials -->
  <script src="js/dashboard.js"></script>

</body>
</html>
