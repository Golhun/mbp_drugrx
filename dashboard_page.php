<?php
// dashboard_page.php - Enhanced UI/UX with improved fonts, colors, and icons
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';       // for $db
require_once 'log_activity.php'; // if needed for additional calls

$user_id = $_SESSION['user_id'];

// 1) Last Login
$stmt = $db->prepare("SELECT last_login FROM users WHERE id = :uid");
$stmt->execute([':uid' => $user_id]);
$lastLogin = $stmt->fetchColumn();

// 1b) Recent login events
$stmt2 = $db->prepare("
  SELECT created_at 
  FROM activity_log
  WHERE user_id = :uid AND event_type='login'
  ORDER BY created_at DESC
  LIMIT 3
");
$stmt2->execute([':uid' => $user_id]);
$recentLogins = $stmt2->fetchAll(PDO::FETCH_COLUMN);

// 2) Recent Interactions
$stmt3 = $db->prepare("
  SELECT event_details, created_at
  FROM activity_log
  WHERE user_id = :uid AND event_type='check_interactions'
  ORDER BY created_at DESC
  LIMIT 5
");
$stmt3->execute([':uid' => $user_id]);
$recentInteractions = $stmt3->fetchAll(PDO::FETCH_ASSOC);

// 3) Recent Substitutes
$stmt4 = $db->prepare("
  SELECT event_details, created_at
  FROM activity_log
  WHERE user_id = :uid AND event_type='check_substitutes'
  ORDER BY created_at DESC
  LIMIT 5
");
$stmt4->execute([':uid' => $user_id]);
$recentSubstitutes = $stmt4->fetchAll(PDO::FETCH_ASSOC);

// 4) Totals
$stmt5 = $db->prepare("
  SELECT COUNT(*)
  FROM activity_log
  WHERE user_id = :uid AND event_type='check_interactions'
");
$stmt5->execute([':uid' => $user_id]);
$totalInteractions = $stmt5->fetchColumn();

$stmt6 = $db->prepare("
  SELECT COUNT(*)
  FROM activity_log
  WHERE user_id = :uid AND event_type='check_substitutes'
");
$stmt6->execute([':uid' => $user_id]);
$totalSubstitutes = $stmt6->fetchColumn();

// 5) Favorite / Frequent Drugs
$stmt7 = $db->prepare("
  SELECT drug_name, search_count
  FROM favorite_drugs
  WHERE user_id = :uid
  ORDER BY search_count DESC, last_searched DESC
  LIMIT 5
");
$stmt7->execute([':uid' => $user_id]);
$topDrugs = $stmt7->fetchAll(PDO::FETCH_ASSOC);

// Fetch user’s first name for greeting
$stmtUser = $db->prepare("SELECT first_name FROM users WHERE id = :uid");
$stmtUser->execute([':uid' => $user_id]);
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
$firstName = $userRow ? $userRow['first_name'] : 'User';

// We'll get today's date in a nice format
$today = date('d M, Y');
?>

<!-- Container referencing an improved style: bigger fonts, refined colors -->
<div class="space-y-6">

  <!-- Header + Greeting & Date -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
      <h1 class="text-4xl font-bold text-gray-800 flex items-center gap-2">
        Dashboard
      </h1>
      <p class="text-base text-gray-600 mt-2">
        Hello, <span class="font-semibold"><?php echo htmlspecialchars($firstName); ?></span>! 
        Here’s your latest activity and stats.
      </p>
    </div>
    <div class="mt-2 md:mt-0 text-gray-500 text-sm">
      <p><?php echo $today; ?></p>
    </div>
  </div>

  <!-- Stats Summary (3 boxes: Interactions, Substitutes, Last Login) -->
  <div class="grid grid-cols-3 gap-4">
    <!-- Interactions total -->
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg shadow p-5 text-white">
      <div class="flex items-center gap-3">
        <i class="fas fa-exchange-alt text-2xl"></i>
        <p class="text-lg font-semibold">Interactions</p>
      </div>
      <div class="mt-3 flex items-baseline gap-1">
        <span class="text-3xl font-bold"><?php echo $totalInteractions; ?></span>
        <span class="text-sm">checks</span>
      </div>
    </div>

    <!-- Substitutes total -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-5 text-white">
      <div class="flex items-center gap-3">
        <i class="fas fa-sync-alt text-2xl"></i>
        <p class="text-lg font-semibold">Substitutes</p>
      </div>
      <div class="mt-3 flex items-baseline gap-1">
        <span class="text-3xl font-bold"><?php echo $totalSubstitutes; ?></span>
        <span class="text-sm">searched</span>
      </div>
    </div>

    <!-- Last Login -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-5 text-white">
      <div class="flex items-center gap-3">
        <i class="fas fa-sign-in-alt text-2xl"></i>
        <p class="text-lg font-semibold">Last Login</p>
      </div>
      <div class="mt-3">
        <?php if ($lastLogin): ?>
          <span class="text-base">
            <?php echo date('d M, Y g:i a', strtotime($lastLogin)); ?>
          </span>
        <?php else: ?>
          <span class="text-sm">N/A</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Main Content Row: 2 columns (recent activity on left, side column on right) -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Left 2/3 column: recent interactions, recent substitutes -->
    <div class="md:col-span-2 space-y-4">

      <!-- Recent Activity -->
      <div class="bg-white rounded-lg shadow p-5">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-history text-pink-500"></i>
          Recent Activity
        </h2>

        <!-- Recent Interactions -->
        <div class="mb-6">
          <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-prescription-bottle-alt text-blue-500"></i>
            Recent Interactions
          </h3>
          <?php if ($recentInteractions): ?>
            <ul class="mt-2 space-y-1">
              <?php foreach ($recentInteractions as $interaction):
                $data = json_decode($interaction['event_details'], true);
                $drugs = $data['drugs'] ?? ['N/A'];
              ?>
              <li class="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                <span class="text-gray-700 font-medium">
                  <?php echo htmlspecialchars(implode(', ', $drugs)); ?>
                </span>
                <span class="text-xs text-gray-400">
                  <?php echo date('d M, g:i a', strtotime($interaction['created_at'])); ?>
                </span>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-sm text-gray-500 mt-2">No recent interactions found.</p>
          <?php endif; ?>
        </div>

        <!-- Recent Substitutes -->
        <div>
          <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-exchange-alt text-pink-500"></i>
            Recent Substitutes
          </h3>
          <?php if ($recentSubstitutes): ?>
            <ul class="mt-2 space-y-1">
              <?php foreach ($recentSubstitutes as $subs):
                $data = json_decode($subs['event_details'], true);
                $drugs = $data['selectedDrugs'] ?? ['N/A'];
              ?>
              <li class="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                <span class="text-gray-700 font-medium">
                  <?php echo htmlspecialchars(implode(', ', $drugs)); ?>
                </span>
                <span class="text-xs text-gray-400">
                  <?php echo date('d M, g:i a', strtotime($subs['created_at'])); ?>
                </span>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-sm text-gray-500 mt-2">No recent substitute searches found.</p>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- Right 1/3 column: mini-profile, favorite drugs, login history -->
    <div class="space-y-4">

      <!-- Mini user card (Profile) -->
      <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center gap-3">
          <div class="w-14 h-14 rounded-full bg-pink-500 flex items-center justify-center text-white text-2xl">
            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
          </div>
          <div>
            <p class="text-base font-bold text-gray-800">
              <?php echo htmlspecialchars($firstName); ?>
            </p>
            <p class="text-xs text-gray-500">@<?php echo strtolower($firstName); ?></p>
          </div>
        </div>
        <p class="mt-3 text-sm text-gray-600">
          (Optional user bio or more stats)
        </p>
      </div>

      <!-- Favorite / Frequent Drugs -->
      <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <i class="fas fa-star text-yellow-400"></i>
          Frequent Drugs
        </h3>
        <?php if ($topDrugs): ?>
          <ul class="space-y-2 text-sm">
            <?php foreach ($topDrugs as $fav): ?>
            <li class="flex justify-between items-center p-2 bg-gray-50 rounded">
              <span class="text-gray-700 font-medium">
                <?php echo htmlspecialchars($fav['drug_name']); ?>
              </span>
              <span class="text-xs text-gray-400">
                <?php echo (int)$fav['search_count']; ?> searches
              </span>
            </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-sm text-gray-500">No favorite drugs recorded yet.</p>
        <?php endif; ?>
      </div>

      <!-- Login History or other feed -->
      <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <i class="fas fa-user-clock text-green-500"></i>
          Login History
        </h3>
        <?php if ($recentLogins && count($recentLogins) > 0): ?>
          <ul class="space-y-2 text-sm">
            <?php foreach ($recentLogins as $logTime): ?>
            <li class="p-2 bg-gray-50 rounded flex justify-between items-center">
              <span class="text-gray-700 font-medium">Login</span>
              <span class="text-xs text-gray-400">
                <?php echo date('d M, g:i a', strtotime($logTime)); ?>
              </span>
            </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-sm text-gray-500">No recent login history found.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white rounded-lg shadow p-5 mt-4">
    <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
      <i class="fas fa-tools text-pink-500"></i>
      Quick Actions
    </h3>
    <div class="flex flex-wrap gap-4">
      <a href="index.php" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600 text-sm font-semibold">
        Check Interactions
      </a>
      <a href="substitutes.php" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600 text-sm font-semibold">
        Find Substitutes
      </a>
      <a href="profile.php" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600 text-sm font-semibold">
        View Profile
      </a>
    </div>
  </div>

  <!-- Help/Support -->
  <div class="bg-white rounded-lg shadow p-5 mt-4">
    <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
      <i class="fas fa-question-circle text-blue-500"></i>
      Help & Support
    </h3>
    <p class="text-sm">
      Need assistance? Check out our
      <a href="faq.php" class="text-blue-600 hover:underline">FAQ</a> or
      <a href="contact.php" class="text-blue-600 hover:underline">Contact Support</a>.
    </p>
  </div>
</div>
