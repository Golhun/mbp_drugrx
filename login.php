<?php
require_once 'config.php';
require_once './components/navbar.php'; 
require_once './components/footer.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign In - MBP_DrugRx</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind + Icons -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    rel="stylesheet"
  >

  <style>
    body {
      /* White background overall */
      background-color: #fff;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
  </style>
</head>
<body>
  <!-- Navbar at top -->
  <?php renderNavbar(); ?>

  <!-- Main Container (flex) -->
  <main class="flex flex-1 flex-col md:flex-row mt-10 ">
    <!-- LEFT Panel: Dark form area -->
    <div class="md:w-1/2 px-8 py-12 bg-gray-900 text-white flex flex-col justify-center">
      <div class="max-w-md w-full mx-auto">
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">Sign in to your account</h1>
        <p class="text-sm text-gray-300 mb-6">
          Don’t have an account?
          <a href="register.php" class="text-pink-500 hover:text-pink-400">Create one now →</a>
        </p>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
          <p class="text-red-400 mb-4">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
          </p>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login_process.php" class="space-y-4">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="john.doe@example.com"
              required
              class="w-full px-3 py-2 rounded bg-gray-800 focus:bg-gray-700
                     focus:outline-none focus:ring-2 focus:ring-pink-500 
                     text-gray-200 placeholder-gray-400"
            >
            <p class="text-xs text-gray-500 mt-1">
              Please enter your email address
            </p>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input
              type="password"
              name="password"
              id="password"
              placeholder="********"
              required
              class="w-full px-3 py-2 rounded bg-gray-800 focus:bg-gray-700
                     focus:outline-none focus:ring-2 focus:ring-pink-500 
                     text-gray-200 placeholder-gray-400"
            >
            <p class="text-xs text-gray-500 mt-1">
              Hold <code>Ctrl</code> to display your password temporarily.
            </p>
          </div>

          <!-- Remember Me + Forgot -->
          <div class="flex items-center justify-between text-sm mt-4">
            <label class="inline-flex items-center space-x-2">
              <input
                type="checkbox"
                name="remember"
                class="rounded bg-gray-700 focus:ring-pink-500"
              >
              <span>Remember Me</span>
            </label>

            <a href="reset_password.php" class="text-sm text-pink-400 hover:text-pink-300">
              Forgot Password?
            </a>
          </div>

          <!-- Sign In Button -->
          <button
            type="submit"
            class="w-full mt-6 py-2 bg-pink-500 rounded text-white font-semibold
                   hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-400 
                   transition"
          >
            Sign in
          </button>
        </form>
      </div>
    </div>

    <!-- RIGHT Panel: Image -->
    <div class="hidden md:block md:w-1/2">
      <img
        src="https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?q=80&w=2140&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
        alt="Background books"
        class="w-full h-full object-cover"
      >
    </div>
  </main>

  <!-- Footer (on white background) -->
  <?php renderFooter(); ?>
  <script src="js/showPasswordCtrl.js"></script>

</body>
</html>
