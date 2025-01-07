<?php
function renderFAQSection() {
    ?>
    <section class="py-10 bg-gray-50 sm:py-16 lg:py-24">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl font-bold leading-tight text-gray-800 sm:text-4xl lg:text-5xl">Frequently Asked Questions</h2>
                <p class="max-w-xl mx-auto mt-4 text-base leading-relaxed text-gray-700">Answers to common questions about mbp_drugrx</p>
            </div>

            <div class="max-w-3xl mx-auto mt-8 space-y-4 md:mt-16">
                <!-- FAQ 1 -->
                <div class="faq-item transition-all duration-200 bg-white border border-gray-300 shadow-lg cursor-pointer hover:bg-gray-100">
                    <button type="button" class="faq-question flex items-center justify-between w-full px-4 py-5 sm:p-6">
                        <span class="flex text-lg font-semibold text-gray-800"> How does the drug interaction checker work? </span>
                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer hidden px-4 pb-5 sm:px-6 sm:pb-6">
                        <p>mbp_drugrx uses a comprehensive database to identify potential interactions between the medications you input. It provides clear recommendations to ensure safe usage.</p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item transition-all duration-200 bg-white border border-gray-300 shadow-lg cursor-pointer hover:bg-gray-100">
                    <button type="button" class="faq-question flex items-center justify-between w-full px-4 py-5 sm:p-6">
                        <span class="flex text-lg font-semibold text-gray-800"> Can I find substitutes for unavailable drugs? </span>
                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer hidden px-4 pb-5 sm:px-6 sm:pb-6">
                        <p>Yes, mbp_drugrx suggests alternative medications based on the same active ingredients, therapeutic uses, and safety profiles.</p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item transition-all duration-200 bg-white border border-gray-300 shadow-lg cursor-pointer hover:bg-gray-100">
                    <button type="button" class="faq-question flex items-center justify-between w-full px-4 py-5 sm:p-6">
                        <span class="flex text-lg font-semibold text-gray-800"> Is mbp_drugrx suitable for healthcare professionals? </span>
                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer hidden px-4 pb-5 sm:px-6 sm:pb-6">
                        <p>Yes, mbp_drugrx provides detailed insights and is trusted by healthcare providers to make informed decisions for patient care.</p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item transition-all duration-200 bg-white border border-gray-300 shadow-lg cursor-pointer hover:bg-gray-100">
                    <button type="button" class="faq-question flex items-center justify-between w-full px-4 py-5 sm:p-6">
                        <span class="flex text-lg font-semibold text-gray-800"> Is the database regularly updated? </span>
                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer hidden px-4 pb-5 sm:px-6 sm:pb-6">
                        <p>Yes, our database is frequently updated to ensure accuracy and reliability with the latest drug information.</p>
                    </div>
                </div>
            </div>

            <p class="text-center text-gray-700 mt-9">Didn’t find the answer you were looking for? <a href="/contact.php" title="Contact Support" class="font-medium text-pink-600 hover:text-pink-700 hover:underline">Contact our support team</a></p>
        </div>
    </section>
    <?php
}
?>
