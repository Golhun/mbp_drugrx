<?php
function renderDashboardNavbar() {
    // Protect the dashboard
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Fetch user data
    global $db;
    try {
        $stmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: login.php');
            exit();
        }

        $first_name = $user['first_name'];
        $last_name  = $user['last_name'];
        $email      = $user['email'];

        // For top-right avatar fallback
        $user_initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
    } catch (PDOException $e) {
        error_log("Error fetching user details: " . $e->getMessage());
        header('Location: login.php?error=1');
        exit();
    }
    ?>

<!-- TOP NAVBAR (Using the same color scheme from your landing page: bg-gray-900, pink-500 accents) -->
<nav class="fixed top-0 z-50 w-full bg-gray-900 border-b border-gray-700">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <!-- Left Section: Hamburger + Brand -->
      <div class="flex items-center justify-start rtl:justify-end">
        <!-- Mobile Sidebar Toggle Button -->
        <button 
          data-drawer-target="logo-sidebar" 
          data-drawer-toggle="logo-sidebar" 
          aria-controls="logo-sidebar"
          type="button" 
          class="inline-flex items-center p-2 text-sm text-gray-300 rounded-lg sm:hidden hover:bg-gray-800
                 focus:outline-none focus:ring-2 focus:ring-gray-600"
        >
          <span class="sr-only">Open sidebar</span>
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
             <path clip-rule="evenodd" fill-rule="evenodd"
               d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 
                  010 1.5H2.75A.75.75 0 012 4.75zm0 
                  10.5a.75.75 0 01.75-.75h7.5a.75.75 0
                  010 1.5h-7.5a.75.75 0 
                  01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0
                  010 1.5H2.75A.75.75 0 012 10z"
             ></path>
          </svg>
        </button>

        <!-- Brand / Logo -->
        <a href="#" class="flex ms-2 md:me-24 items-center gap-2">
          <!-- Use the same capsule + pink accent from the landing page -->
          <i class="fas fa-capsules text-pink-500 text-3xl"></i>
          <span class="self-center text-xl font-bold text-white">
            mbp_drugrx
          </span>
        </a>
      </div>

      <!-- Right Section: User Menu -->
      <div class="flex items-center">
        <div class="flex items-center ms-3 relative">
          <!-- Avatar / Top-right user menu button -->
          <button 
            type="button"
            class="user-menu-button flex text-sm bg-pink-500 hover:bg-pink-600 rounded-full focus:ring-2 focus:ring-pink-300
                   transition-colors"
            aria-expanded="false"
          >
            <span class="sr-only">Open user menu</span>
            <img 
              class="w-8 h-8 rounded-full object-cover"
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($first_name . ' ' . $last_name); ?>&background=FF0090&color=fff&rounded=true" 
              alt="User photo"
            >
          </button>

          <!-- The dropdown menu (hidden by default) -->
          <div 
            class="user-menu-dropdown z-50 hidden my-4 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded shadow 
                   absolute top-12 right-0 w-44"
          >
            <!-- User info block -->
            <div class="px-4 py-3">
              <p class="text-sm font-semibold text-white">
                <?php echo htmlspecialchars("$first_name $last_name"); ?>
              </p>
              <p class="text-sm font-medium text-pink-200 truncate">
                <?php echo htmlspecialchars($email); ?>
              </p>
            </div>
            <!-- Menu links -->
            <ul class="py-1">
              <li>
                <a href="#"
                   class="block px-4 py-2 text-sm text-gray-300 hover:bg-pink-500 hover:text-white transition"
                >
                  My Profile
                </a>
              </li>
              <li>
                <a href="logout.php"
                   class="block px-4 py-2 text-sm text-gray-300 hover:bg-pink-500 hover:text-white transition"
                >
                  Sign out
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div><!-- End Right Section -->
    </div>
  </div>
</nav>

<!-- SIDEBAR (Keep the same color scheme: dark background, pink highlights on hover) -->
<aside 
  id="logo-sidebar"
  class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full 
         bg-gray-900 border-r border-gray-700 sm:translate-x-0"
  aria-label="Sidebar"
>
  <div class="h-full px-3 pb-4 overflow-y-auto bg-gray-900">
    <ul class="space-y-2 font-medium">
      <!-- Drug Info Link -->
      <li>
        <a href="#"
           class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-pink-500 hover:text-white
                  transition group"
           id="nav-drug-info"
        >
          <i class="fas fa-prescription-bottle-alt text-gray-400 group-hover:text-white"></i>
          <span class="ms-3">Drug Info</span>
        </a>
      </li>

      <!-- Blog link -->
      <li>
        <a href="#"
           class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-pink-500 hover:text-white
                  transition group"
           id="nav-blog"
        >
          <i class="fas fa-blog text-gray-400 group-hover:text-white"></i>
          <span class="ms-3">Blog</span>
        </a>
      </li>

      <!-- Profile Link -->
      <li>
        <a href="#"
           class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-pink-500 hover:text-white
                  transition group"
           id="nav-profile"
        >
          <i class="fas fa-user text-gray-400 group-hover:text-white"></i>
          <span class="ms-3">Profile</span>
        </a>
      </li>

      <!-- Logout -->
      <li>
        <a href="logout.php"
           class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-pink-500 hover:text-white
                  transition group"
        >
          <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-white"></i>
          <span class="ms-3">Sign out</span>
        </a>
      </li>
    </ul>
  </div>
</aside>

<!-- 
  If you'd like to replicate the example fully, place your main content
  in <div class="p-4 sm:ml-64 mt-14"> ... </div>, or wherever your content goes.
-->
<?php
} // end function
renderDashboardNavbar();
