<?php
session_start();

if (!empty($_SESSION['authenticated'])) {
    header('Location: index.php');
    exit;
}

$showLoginError = isset($_SESSION['login_error']);
$showLoggedOutMessage = isset($_SESSION['login_logged_out']);
unset($_SESSION['login_error']);
unset($_SESSION['login_logged_out']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="docs/login.css">
</head>

<body>
    <main class="login-page">
        <section class="login-card" aria-labelledby="login-title">
            <a class="brand" href="login.php" aria-label="Student Grade home">
                <span class="brand-mark" aria-hidden="true">SG</span>
                <span>Student<span class="brand-accent">Grade</span></span>
            </a>

            <div class="login-heading">
                <p class="eyebrow">Student portal</p>
                <h1 id="login-title">Welcome back</h1>
                <p class="login-subtitle">Sign in to manage student grades and results.</p>
            </div>

            <?php if ($showLoginError): ?>
                <p id="login-error" class="login-message login-message--error" role="alert">
                    The username or password is incorrect. Please try again.
                </p>
                <script>
                    window.setTimeout(() => {
                        const message = document.getElementById('login-error');
                        if (!message) return;

                        message.classList.add('login-message--fading');
                        window.setTimeout(() => message.remove(), 1000);
                    }, 5000);
                </script>
            <?php endif; ?>
            <?php if ($showLoggedOutMessage): ?>
                <p class="login-message login-message--success" role="status">You have been logged out.</p>
            <?php endif; ?>

            <form class="login-form" action="login_process.php" method="post">
                <div class="form-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username" required>
                </div>
                <div class="form-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                    <div class="password-input-wrap">
                        <button class="password-toggle" type="button" id="toggle-password"
                            aria-controls="password" aria-pressed="false">Show</button>
                    </div>
                </div>
                <button class="login-button" type="submit">Sign in <span aria-hidden="true">→</span></button>
            </form>

            <p class="login-footer">Secure access to your academic records</p>
        </section>
    </main>

    <script>
        const toggleButton = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');

        toggleButton.addEventListener('click', () => {
            const isPasswordVisible = passwordInput.type === 'text';
            passwordInput.type = isPasswordVisible ? 'password' : 'text';
            toggleButton.textContent = isPasswordVisible ? 'Show' : 'Hide';
            toggleButton.setAttribute('aria-pressed', String(!isPasswordVisible));
        });
    </script>
</body>

</html>