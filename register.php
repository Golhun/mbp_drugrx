<?php
require_once './components/navbar.php';
require_once './components/footer.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - mbp_drugrx</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <?php renderNavbar(); ?>

    <!-- Register Form Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 sm:px-12">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-center text-gray-800">Create Your Account</h2>
                <p class="text-center text-gray-600 mt-2">Sign up to access mbp_drugrx</p>

                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6">
                        <ul>
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <form action="register_process.php" method="POST" class="mt-6">
                    <!-- First and Last Name -->
                    <div class="mb-4 flex gap-4">
                        <!-- First Name -->
                        <div class="w-1/2">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="block w-full p-3 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="First Name" required>
                        </div>

                        <!-- Last Name -->
                        <div class="w-1/2">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="block w-full p-3 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="Last Name" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" class="block w-full p-3 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="Enter your email" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" class="block w-full p-3 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="Enter your password" required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="block w-full p-3 mt-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="Confirm your password" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 text-white bg-pink-500 rounded-md hover:bg-pink-600 focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition">
                        Register
                    </button>
                </form>

                <p class="mt-6 text-center text-gray-600">
                    Already have an account? <a href="login.php" class="text-pink-500 hover:underline">Log in</a>
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php renderFooter(); ?>

</body>
</html>
