<?php
function renderHeroSection() {
    ?>
    <div class="overflow-x-hidden bg-gray-50">
        <section class="pt-16 bg-gray-50 sm:pt-20">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto text-center">
                    <h1 class="mt-8 px-6 text-lg text-gray-600 font-inter">Your Trusted Drug Interaction Checker</h1>
                    <p class="mt-5 text-4xl font-bold leading-tight text-gray-900 sm:leading-tight sm:text-5xl lg:text-6xl lg:leading-tight font-pj">
                        Safeguard your health with
                        <span class="relative inline-flex sm:inline">
                            <span class="bg-gradient-to-r from-pink-400 via-red-500 to-pink-600 blur-lg filter opacity-30 w-full h-full absolute inset-0"></span>
                            <span class="relative"> MBP_DrugRx </span>
                        </span>
                    </p>

                    <div class="px-8 sm:items-center sm:justify-center sm:px-0 sm:space-x-5 sm:flex mt-9">
                        <a
                            href="#features"
                            title="Explore Features"
                            class="inline-flex items-center justify-center w-full px-8 py-3 text-lg font-bold text-white transition-all duration-200 bg-pink-600 border-2 border-transparent sm:w-auto rounded-xl font-pj hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-600"
                            role="button"
                        >
                            Explore Features
                        </a>

                        <a
                            href="#how-it-works"
                            title="How It Works"
                            class="inline-flex items-center justify-center w-full px-6 py-3 mt-4 text-lg font-bold text-pink-600 transition-all duration-200 bg-transparent border-2 border-pink-600 sm:w-auto sm:mt-0 rounded-xl font-pj hover:bg-pink-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-600"
                            role="button"
                        >
                            <i class="fas fa-info-circle mr-2"></i>
                            How It Works
                        </a>
                    </div>

                    <p class="mt-8 text-base text-gray-500 font-inter">Quick, accurate, and reliable · Join the bus</p>
                </div>
            </div>

            <div class="pb-12 bg-white">
                <div class="relative">
                    <div class="absolute inset-0 h-2/3 bg-gray-50"></div>
                    <div class="relative mx-auto">
                        <div class="lg:max-w-6xl lg:mx-auto">
                            <img class="transform scale-110" src="https://cdn.rareblocks.xyz/collection/clarity/images/hero/2/illustration.png" alt="MBP_DrugRx Illustration" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
}
?>
