<?php
function renderDemoRequestSection() {
    ?>
    <section class="py-10 bg-gray-50 sm:py-16 lg:py-24">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
            <div class="text-center">
                <h2 class="text-3xl font-bold leading-tight text-gray-800 sm:text-4xl lg:text-5xl">Start Checking Drug Interactions Today</h2>
                <p class="mt-4 text-xl text-gray-700">Access a comprehensive tool to ensure safe and effective medication use.</p>

                <div class="flex flex-col items-center justify-center px-16 mt-8 space-y-4 sm:space-y-0 sm:space-x-4 sm:flex-row lg:mt-12 sm:px-0">
                    <a href="/signup.php" title="Sign Up" class="inline-flex items-center justify-center w-full px-8 py-4 text-base font-semibold text-white transition-all duration-200 bg-pink-600 border border-transparent rounded-md sm:w-auto hover:bg-pink-700 focus:ring-2 focus:ring-pink-500 focus:ring-offset-2" role="button">
                        Get Started for Free
                    </a>

                    <a href="/contact.php" title="Contact Support" class="inline-flex items-center justify-center w-full px-8 py-4 text-base font-semibold text-pink-600 transition-all duration-200 bg-transparent border border-pink-600 rounded-md sm:w-auto hover:bg-pink-600 hover:text-white focus:ring-2 focus:ring-pink-500 focus:ring-offset-2" role="button">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact Support
                    </a>
                </div>

                <p class="mt-6 text-base text-gray-800">Already have an account? <a href="/login.php" title="Log in" class="text-pink-600 transition-all duration-200 hover:text-pink-700 focus:text-pink-700 hover:underline">Log in</a></p>
            </div>
        </div>
    </section>
    <?php
}
?>
