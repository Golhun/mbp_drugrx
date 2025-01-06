document.addEventListener('DOMContentLoaded', () => {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach((item) => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');

        question.addEventListener('click', () => {
            const isOpen = answer.classList.contains('hidden');
            // Close all other answers
            faqItems.forEach((otherItem) => {
                otherItem.querySelector('.faq-answer').classList.add('hidden');
            });
            // Toggle the clicked answer
            if (isOpen) {
                answer.classList.remove('hidden');
            } else {
                answer.classList.add('hidden');
            }
        });
    });
});
