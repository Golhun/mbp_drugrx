
<?php
// Include the configuration and session
require_once 'config.php';
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    // Handle "Remember Me" functionality
    if (isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];
        $stmt = $db->prepare("SELECT user_id FROM remember_me WHERE token_hash = :token_hash AND expires_at > NOW()");
        $stmt->execute(['token_hash' => hash('sha256', $token)]);
        $user = $stmt->fetch();

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

// Include the new navbar with your top bar + sidebar
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

  <main class="pt-20 sm:ml-64 p-4 transition-all">
    <!-- Center the loaded partials in a container -->
    <div 
      id="content-area"
      class="mx-auto max-w-4xl bg-white rounded shadow p-6 min-h-[calc(100vh-5rem)] 
             opacity-100 scale-100 transition"
    >
      <!-- Dashboard.js will load the partial content (index.php / profile.php) here -->
      <p class="text-center text-gray-500">Loading...</p>
    </div>
  </main>

  <!-- Load your dashboard logic for partials/tab switching, etc. -->
    <!-- Load scripts in the correct order (NO "type=module") -->
    <script src="js/dashboardNav.js"></script>
  <!-- 1) Global state + restore functions -->
  <script src="js/script.js"></script>
  <!-- 2) Interactions + Substitutes code, which read from window.appState -->
  <script src="js/interaction.js"></script>
  <script src="js/substitute.js"></script>
  <!-- 3) Finally, the dashboard logic that does partial loading -->
  <script src="js/dashboard.js"></script>
</body>
</html>
