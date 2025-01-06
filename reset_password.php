<?php
require_once 'config.php';
require_once './components/navbar.php'; // Include the navbar

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32));

    $stmt = $db->prepare("UPDATE users SET reset_token = :token WHERE email = :email");
    $stmt->execute(['token' => $token, 'email' => $email]);

    $resetLink = "http://localhost:3000/reset.php?token=$token";
    sendEmail($email, "Reset Your Password", "reset_email.php", ['reset_link' => $resetLink]);

    $message = 'Password reset link sent to your email.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body class="bg-gray-50">
    <?php renderNavbar(); ?> <!-- Render the navbar -->

    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-3xl font-bold mb-4 text-center">Reset Password</h1>
            <?php if ($message): ?>
                <p class="text-center text-sm text-green-600 mb-4"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form method="POST" action="reset_password.php" class="space-y-4">
                <input type="email" name="email" placeholder="Enter your email" required
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                    Send Reset Link
                </button>
            </form>
            <p class="text-center text-sm text-gray-600 mt-4">
                Remembered your password? <a href="login.php" class="text-blue-500 hover:underline">Go back to login</a>
            </p>
        </div>
    </div>
</body>
</html>
