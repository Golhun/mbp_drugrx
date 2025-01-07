<?php
function renderFooter() {
    ?>
    <footer class="bg-gray-900 text-gray-400 py-6">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
            <div class="grid grid-cols-2 md:col-span-3 lg:grid-cols-6 gap-y-16 gap-x-12">
                <!-- Logo and About -->
                <div class="col-span-2 md:col-span-3 lg:col-span-2 lg:pr-8">
                    <i class="fas fa-capsules fa-3x text-pink-500"></i>
                    <p class="text-base leading-relaxed text-gray-400 mt-7">
                        mbp_drugrx is your trusted solution for drug interaction and substitute checking. Safeguard your health and make informed decisions with ease.
                    </p>

                    <ul class="flex items-center space-x-3 mt-9">
                        <li>
                            <a href="#" class="text-gray-400 transition-all duration-200 hover:text-pink-500">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 transition-all duration-200 hover:text-pink-500">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 transition-all duration-200 hover:text-pink-500">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 transition-all duration-200 hover:text-pink-500">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Company Links -->
                <div>
                    <p class="text-sm font-semibold tracking-widest text-gray-500 uppercase">Company</p>
                    <ul class="mt-6 space-y-4">
                        <li>
                            <a href="#" class="flex text-base text-gray-400 transition-all duration-200 hover:text-pink-500">About</a>
                        </li>
                        <li>
                            <a href="#features" class="flex text-base text-gray-400 transition-all duration-200 hover:text-pink-500">Features</a>
                        </li>
                        <li>
                            <a href="#how-it-works" class="flex text-base text-gray-400 transition-all duration-200 hover:text-pink-500">How It Works</a>
                        </li>
                        <li>
                            <a href="#" class="flex text-base text-gray-400 transition-all duration-200 hover:text-pink-500">Contact Us</a>
                        </li>
                    </ul>
                </div>

                <!-- Help Links -->
                <div>
                    <p class="text-sm font-semibold tracking-widest text-gray-500 uppercase">Help</p>
                    <ul class="mt-6 space-y-4">
                        <li>
                            <a href="#customer-support" class="flex text-base text-gray-400 transition-all duration-200 hover:text-pink-500">Customer Support</a>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter Subscription -->
                <div class="col-span-2 md:col-span-1 lg:col-span-2 lg:pl-8">
                    <p class="text-sm font-semibold tracking-widest text-gray-500 uppercase">Subscribe to Updates</p>
                    <form action="#" method="POST" class="mt-6">
                        <div>
                            <label for="email" class="sr-only">Email</label>
                            <input type="email" name="email" id="news_letter_email" placeholder="Enter your email"
                                   class="block w-full p-4 text-gray-900 placeholder-gray-500 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500" />
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-4 mt-3 font-semibold text-white bg-pink-500 rounded-md hover:bg-pink-600 focus:bg-pink-600">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <hr class="mt-16 mb-10 border-gray-700" />

            <p class="text-sm text-center text-gray-500">© <?php echo date("Y"); ?> mbp_drugrx. All Rights Reserved.</p>
        </div>
    </footer>
    <?php
}
?>
