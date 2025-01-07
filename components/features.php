<?php
function renderFeaturesSection() {
    ?>
    <section class="py-12 bg-white sm:py-16 lg:py-20">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold leading-tight text-gray-900 sm:text-4xl xl:text-5xl">Explore Key Features</h2>
                <p class="mt-4 text-base leading-7 text-gray-600 sm:mt-8">Discover how MBP_DrugRx can simplify medication safety and interaction checks.</p>
            </div>

            <div class="grid grid-cols-1 mt-10 text-center sm:mt-16 sm:grid-cols-2 sm:gap-x-12 gap-y-12 md:grid-cols-3 xl:mt-24">
                <!-- Feature 1 -->
                <div class="feature-item md:p-8 lg:p-14" >
                    <i class="fas fa-pills fa-3x text-green-500"></i>
                    <h3 class="mt-12 text-xl font-bold text-gray-900">Comprehensive Interaction Check</h3>
                    <p class="mt-5 text-base text-gray-600">Scan for potential drug interactions to ensure safety with every prescription.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-item md:p-8 lg:p-14 md:border-l md:border-gray-200" >
                    <i class="fas fa-sync-alt fa-3x text-blue-500"></i>
                    <h3 class="mt-12 text-xl font-bold text-gray-900">Drug Substitution Recommendations</h3>
                    <p class="mt-5 text-base text-gray-600">Find alternative medications for unavailable drugs or cost-effective options.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-item md:p-8 lg:p-14 md:border-l md:border-gray-200" >
                    <i class="fas fa-shield-alt fa-3x text-purple-500"></i>
                    <h3 class="mt-12 text-xl font-bold text-gray-900">Safe and Secure Data</h3>
                    <p class="mt-5 text-base text-gray-600">Your data is encrypted and handled with the highest security standards.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-item md:p-8 lg:p-14 md:border-t md:border-gray-200" >
                    <i class="fas fa-mobile-alt fa-3x text-orange-500"></i>
                    <h3 class="mt-12 text-xl font-bold text-gray-900">Mobile-Friendly Design</h3>
                    <p class="mt-5 text-base text-gray-600">Access the platform seamlessly on mobile, tablet, or desktop devices.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-item md:p-8 lg:p-14 md:border-l md:border-gray-200 md:border-t" >
                    <i class="fas fa-search fa-3x text-red-500"></i>
                    <h3 class="mt-12 text-xl font-bold text-gray-900">Advanced Search Filters</h3>
                    <p class="mt-5 text-base text-gray-600">Easily find drugs and interactions with intelligent search and filters.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-item md:p-8 lg:p-14 md:border-l md:border-gray-200 md:border-t" >
                    <i class="fas fa-bell fa-3x text-yellow-500"></i>
                    <h3 class="mt-12 text-xl font-bold text-gray-900">Real-Time Notifications</h3>
                    <p class="mt-5 text-base text-gray-600">Receive updates and alerts about new interactions or drug changes.</p>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>
