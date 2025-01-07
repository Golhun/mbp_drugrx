<?php
// profile.php (PARTIAL)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'config.php';

// Fetch user details
try {
    $stmt = $db->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        header('Location: login.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header('Location: login.php?error=1');
    exit();
}
?>

<div class="max-w-4xl mx-auto p-4">
  <div class="flex items-center gap-6 mb-6">
    <div class="w-16 h-16 bg-pink-500 text-white flex items-center justify-center text-2xl font-bold rounded-full">
      <?php
        $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
        echo htmlspecialchars($initials);
      ?>
    </div>
    <div>
      <h1 class="text-2xl font-bold">
        <?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']); ?>
      </h1>
      <p class="text-gray-600">
        <?php echo htmlspecialchars($user['email']); ?>
      </p>
      <p class="text-sm text-gray-500">
        Joined: <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
      </p>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
    <h2 class="text-xl font-semibold mb-4">Profile Details</h2>
    <form action="update_profile.php" method="POST">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- First Name -->
        <div>
          <label for="first_name" class="block text-gray-700 font-medium">First Name</label>
          <input
            type="text"
            id="first_name"
            name="first_name"
            value="<?php echo htmlspecialchars($user['first_name']); ?>"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
            required
          />
        </div>
        <!-- Last Name -->
        <div>
          <label for="last_name" class="block text-gray-700 font-medium">Last Name</label>
          <input
            type="text"
            id="last_name"
            name="last_name"
            value="<?php echo htmlspecialchars($user['last_name']); ?>"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
            required
          />
        </div>
        <!-- Email -->
        <div class="col-span-2">
          <label for="email" class="block text-gray-700 font-medium">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($user['email']); ?>"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
            required
          />
        </div>
        <!-- Password -->
        <div>
          <label for="password" class="block text-gray-700 font-medium">New Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
          />
          <p class="text-sm text-gray-500 mt-1">Leave blank if you don't want to change.</p>
        </div>
        <!-- Confirm Password -->
        <div>
          <label for="confirm_password" class="block text-gray-700 font-medium">Confirm Password</label>
          <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
          />
        </div>
      </div>
      <!-- Submit -->
      <div class="mt-6 text-right">
        <button
          type="submit"
          class="px-6 py-2 bg-pink-500 text-white font-semibold rounded-md shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-400"
        >
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
