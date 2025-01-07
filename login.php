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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MBP_DrugRx</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <?php renderNavbar(); ?>

    <!-- Login Form Section -->
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">Welcome Back</h1>
            
            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <p class="text-center text-sm text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="login_process.php" class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required
                           class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required
                           class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>

                <!-- Remember Me -->
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="rounded">
                    <span class="text-sm text-gray-600">Remember Me</span>
                </label>

                <!-- Login Button -->
                <button type="submit"
                        class="w-full bg-pink-500 text-white py-2 rounded-md hover:bg-pink-600 focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition">
                    Login
                </button>
            </form>

            <!-- Links -->
            <p class="text-center text-sm text-gray-600 mt-4">
                Forgot your password? <a href="reset_password.php" class="text-pink-500 hover:underline">Reset it here</a>
            </p>
            <p class="text-center text-sm text-gray-600 mt-2">
                Don't have an account? <a href="register.php" class="text-pink-500 hover:underline">Sign up</a>
            </p>
        </div>
    </div>


    <!-- Footer -->
    <?php renderFooter(); ?>
</body>
</html>
