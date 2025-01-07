<?php
require_once 'config.php';
require_once './components/navbar.php'; // Include the navbar

$message = '';
$messageType = 'text-red-600'; // Default message style for errors

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $db->prepare("UPDATE users SET password = :password, reset_token = NULL WHERE reset_token = :token");
    $stmt->execute(['password' => $password, 'token' => $token]);

    if ($stmt->rowCount() > 0) {
        $message = 'Password successfully reset! You can now log in.';
        $messageType = 'text-green-600'; // Success message style
        header('refresh:3;url=login.php'); // Redirect after 3 seconds
    } else {
        $message = 'Invalid or expired token.';
    }
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
            <h1 class="text-3xl font-bold mb-4 text-center">Reset Your Password</h1>
            <?php if ($message): ?>
                <p class="text-center text-sm <?php echo $messageType; ?> mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <?php if (empty($message) || $messageType === 'text-red-600'): ?>
            <form method="POST" action="reset.php?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>" class="space-y-4">
                <input type="password" name="password" placeholder="New Password" required
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                    Reset Password
                </button>
            </form>
            <?php endif; ?>
            <p class="text-center text-sm text-gray-600 mt-4">
                Remembered your password? <a href="login.php" class="text-blue-500 hover:underline">Go to Login</a>
            </p>
        </div>
    </div>
</body>
</html>
