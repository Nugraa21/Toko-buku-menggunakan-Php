function confirmAction(message) {
    return confirm(message || "Are you sure you want to proceed?");
}

// Add simple fade-in for elements with .fade-in class if needed dynamically
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.book-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s forwards`;
    });
});
