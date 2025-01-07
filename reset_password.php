<?php
require_once 'config.php';
require_once './components/navbar.php';
require_once './components/footer.php';
session_start();

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
  <title>Forgot your password? - MBP_DrugRx</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-900 text-white flex flex-col">

<?php renderNavbar(); ?>

<div class="flex flex-1 flex-col md:flex-row mt-10">
  <!-- LEFT (Form) -->
  <div class="md:w-1/2 flex flex-col justify-center px-8 py-12 bg-gray-900">
    <div class="max-w-md w-full mx-auto">
      <h2 class="text-2xl sm:text-3xl font-bold mb-2">Forgot your password?</h2>
      <p class="text-sm text-gray-300 mb-6">
        Enter your email address and we will send you a link to reset your password if the account exists.
      </p>

      <?php if ($message): ?>
        <p class="text-green-400 mb-4">
          <?php echo htmlspecialchars($message); ?>
        </p>
      <?php endif; ?>

      <form method="POST" action="reset_password.php" class="space-y-4">
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
        </div>

        <div class="flex justify-between items-center mt-4">
          <a href="login.php" class="text-sm text-gray-400 hover:text-pink-400">
            ← Back
          </a>
          <button
            type="submit"
            class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600
                   focus:ring-2 focus:ring-pink-400 transition"
          >
            Send Email
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- RIGHT (Image) -->
  <div class="hidden md:block md:w-1/2">
    <img
      src="https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?q=80&w=2140&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
      alt="Background books"
      class="w-full h-screen object-cover"
    >
  </div>
</div>
<?php renderFooter(); ?>
</body>
</html>
