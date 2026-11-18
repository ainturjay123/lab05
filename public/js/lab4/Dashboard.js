/**
 * Lab 4 dashboard bootstrap — adapted for Laravel.
 *
 * Removed: localStorage login check and JavaScript logout handler.
 * Laravel sessions, middleware and the CSRF-protected logout form now
 * handle authentication.
 */
document.addEventListener('DOMContentLoaded', async function () {
    const username = (window.inventoryConfig && window.inventoryConfig.username) || 'User';

    updateGreeting(username);

    const userNameSpan = document.getElementById('userName') || document.getElementById('userWelcome');
    if (userNameSpan) {
        userNameSpan.textContent = `Welcome, ${username}`;
    }

    // Initialize the Lab 4 inventory table, charts and filters.
    if (window.inventoryUI && typeof window.inventoryUI.init === 'function') {
        await window.inventoryUI.init();
        console.log('Laravel inventory dashboard initialized with database data.');
    }
});

function updateGreeting(username) {
    const greetingElement = document.getElementById('greeting') || document.getElementById('greetingTitle');
    if (!greetingElement) {
        return;
    }

    const hour = new Date().getHours();
    let timeOfDay = '';

    if (hour >= 5 && hour < 12) {
        timeOfDay = 'Good Morning';
    } else if (hour >= 12 && hour < 17) {
        timeOfDay = 'Good Afternoon';
    } else if (hour >= 17 && hour < 21) {
        timeOfDay = 'Good Evening';
    } else {
        timeOfDay = 'Good Night';
    }

    greetingElement.textContent = `${timeOfDay}, ${username}!`;
}
