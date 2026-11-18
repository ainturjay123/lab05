document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    // Do not run login-page logic on dashboard.html.
    if (!loginForm) {
        return;
    }

    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const feedbackDiv = document.getElementById('loginFeedback');

    // If the user is already logged in, redirect only from the login page.
    if (localStorage.getItem('isLoggedIn') === 'true') {
        window.location.replace('dashboard.html');
        return;
    }

    loginForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const username = usernameInput ? usernameInput.value.trim() : '';
        const password = passwordInput ? passwordInput.value : '';

        if (feedbackDiv) feedbackDiv.innerHTML = '';

        if (!username || !password) {
            showFeedback('Please enter both username and password.', 'danger');
            return;
        }

        const validUsername = 'admin';
        const validPassword = 'password123';

        if (username === validUsername && password === validPassword) {
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('user', username);

            showFeedback('Login successful! Redirecting...', 'success');
            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.textContent = 'Logging in...';
            }

            setTimeout(function () {
                window.location.href = 'dashboard.html';
            }, 500);
        } else {
            showFeedback('Invalid username or password.', 'danger');
            if (passwordInput) {
                passwordInput.value = '';
                passwordInput.focus();
            }
        }
    });

    function showFeedback(message, type) {
        if (!feedbackDiv) return;
        feedbackDiv.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
    }
});
