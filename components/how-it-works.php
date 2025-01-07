<?php
function renderHowItWorksSection() {
    ?>
    <section class="py-10 bg-white sm:py-16 lg:py-24">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl font-bold leading-tight text-black sm:text-4xl lg:text-5xl">How Does mbp_drugrx Work?</h2>
                <p class="max-w-lg mx-auto mt-4 text-base leading-relaxed text-gray-600">
                    Using mbp_drugrx is simple and effective. Follow these steps to ensure safe and informed medication decisions.
                </p>
            </div>

            <div class="relative mt-12 lg:mt-20">
                <div class="absolute inset-x-0 hidden xl:px-44 top-2 md:block md:px-20 lg:px-28">
                    <img class="w-full" src="https://cdn.rareblocks.xyz/collection/celebration/images/steps/2/curved-dotted-line.svg" alt="Step Connector" />
                </div>

                <div class="relative grid grid-cols-1 text-center gap-y-12 md:grid-cols-3 gap-x-12">
                    <!-- Step 1 -->
                    <div>
                        <div class="flex items-center justify-center w-16 h-16 mx-auto bg-white border-2 border-pink-600 rounded-full shadow">
                            <span class="text-xl font-semibold text-pink-600">1</span>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold leading-tight text-black md:mt-10">Enter Medications</h3>
                        <p class="mt-4 text-base text-gray-600">
                            Input the drugs you are taking into the mbp_drugrx system. Our platform handles the rest.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div>
                        <div class="flex items-center justify-center w-16 h-16 mx-auto bg-white border-2 border-pink-600 rounded-full shadow">
                            <span class="text-xl font-semibold text-pink-600">2</span>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold leading-tight text-black md:mt-10">Check for Interactions</h3>
                        <p class="mt-4 text-base text-gray-600">
                            Instantly see potential drug interactions, safety concerns, and recommended precautions.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div>
                        <div class="flex items-center justify-center w-16 h-16 mx-auto bg-white border-2 border-pink-600 rounded-full shadow">
                            <span class="text-xl font-semibold text-pink-600">3</span>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold leading-tight text-black md:mt-10">Find Substitutes</h3>
                        <p class="mt-4 text-base text-gray-600">
                            Discover safe and effective drug substitutes when a specific medication isn’t available.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>
