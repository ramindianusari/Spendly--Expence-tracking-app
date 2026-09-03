<?php
/**
 * index.php — Login Page
 * Expense Tracker App
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

// Already logged in → go to dashboard
if (isLoggedIn()) {
    redirect('home.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =       $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login successful — populate session
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            redirect('home.php');
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — Expense Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-shell">
    <main class="login-page">

        <!-- Logo / App icon -->
        <div class="login-logo" aria-hidden="true">
            <!-- Rupee / tracker icon -->
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M7 5h10v2H7V5zm0 3h5a3 3 0 0 1 0 6H9.41l4.3 4.29-1.42 1.42L7 14.41V12h5a1 1 0 0 0 0-2H7V8z"/>
            </svg>
        </div>

        <h1 class="login-heading">Welcome back</h1>
        <p class="login-subheading">Track every rupee, effortlessly.</p>

        <!-- Error message -->
        <?php if ($error !== ''): ?>
            <div class="error-msg" role="alert" id="loginError">
                <?= e($error) ?>
            </div>
        <?php else: ?>
            <div class="error-msg" id="loginError" style="display:none;"></div>
        <?php endif; ?>

        <!-- Login form -->
        <form id="loginForm" method="POST" action="index.php" novalidate>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input
                    class="form-input"
                    type="email"
                    id="email"
                    name="email"
                    placeholder="rahul@example.com"
                    value="<?= e($_POST['email'] ?? '') ?>"
                    autocomplete="email"
                    required
                />
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    class="form-input"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                />
            </div>

            <button class="btn-primary" type="submit">Log In</button>
        </form>

        <p class="register-link">
            No account? <a href="register.php">Create one</a>
        </p>

    </main>
</div>

<script src="js/main.js"></script>
</body>
</html>
