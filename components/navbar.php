<?php
function renderNavbar() {
    ?>
    <nav id="navbar" class="bg-gray-900 fixed w-full top-0 z-50 transition-transform transform translate-y-0">
        <div class="container mx-auto px-4 py-2 flex justify-between items-center">
            <!-- Logo -->
            <a href="landing.php" class="flex items-center space-x-2">
                <i class="fas fa-capsules text-pink-500 text-3xl"></i>
                <span class="text-xl font-bold text-white">MBP_DrugRx</span>
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-6">
                
                <a href="landing.php" class="text-gray-300 hover:text-pink-500 transition">Home</a>
                <a href="about_us.php" class="text-gray-300 hover:text-pink-500 transition">About</a>
                <a href="landing.php" class="text-gray-300 hover:text-pink-500 transition">Features</a>
                <a href="landing.php" class="text-gray-300 hover:text-pink-500 transition">How It Works</a>
                <a href="landing.php" class="text-gray-300 hover:text-pink-500 transition">Contact</a>
                <a href="login.php" class="text-gray-300 hover:text-pink-500 transition">Login</a>
                <a href="register.php" class="bg-pink-500 text-white px-4 py-2 rounded-md hover:bg-pink-600 transition">
                    Register
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="block md:hidden text-gray-300">
                <span class="material-icons">menu</span>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-800 text-gray-300">
            <a href="landing.php" class="block px-4 py-2 hover:bg-gray-700 hover:text-pink-500">Home</a>
            <a href="about_us.php" class="block px-4 py-2 hover:bg-gray-700 hover:text-pink-500">About</a>
            <a href="landing.php" class="block px-4 py-2 hover:bg-gray-700 hover:text-pink-500">Features</a>
            <a href="landing.php" class="block px-4 py-2 hover:bg-gray-700 hover:text-pink-500">How It Works</a>
            <a href="landing.php" class="block px-4 py-2 hover:bg-gray-700 hover:text-pink-500">Contact</a>
            <a href="login.php" class="block px-4 py-2 hover:bg-gray-700 hover:text-pink-500">Login</a>
            <a href="register.php" class="block px-4 py-2 bg-pink-500 text-white text-center rounded-md hover:bg-pink-600">
                Register
            </a>
        </div>
    </nav>

    <script>
        const navbar = document.getElementById('navbar');
        let lastScrollTop = 0;

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop) {
                // Scrolling down, hide navbar
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up, show navbar
                navbar.style.transform = 'translateY(0)';
            }
            lastScrollTop = scrollTop;
        });

        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
    <?php
}
?>
