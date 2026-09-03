<?php
/**
 * register.php — New user registration
 * Expense Tracker App
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (isLoggedIn()) {
    redirect('home.php');
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password =       $_POST['password'] ?? '';
    $confirm  =       $_POST['confirm']  ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $pdo  = getDBConnection();
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'An account with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $ins  = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $ins->execute([$name, $email, $hash]);

            $success = 'Account created! You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register — Expense Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-shell">
    <main class="login-page">

        <div class="login-logo" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 5h10v2H7V5zm0 3h5a3 3 0 0 1 0 6H9.41l4.3 4.29-1.42 1.42L7 14.41V12h5a1 1 0 0 0 0-2H7V8z"/>
            </svg>
        </div>

        <h1 class="login-heading">Create account</h1>
        <p class="login-subheading">Start tracking your finances today.</p>

        <?php if ($error !== ''): ?>
            <div class="error-msg" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="error-msg" role="status" style="background:rgba(74,222,128,.12);border-color:rgba(74,222,128,.3);color:#4ade80;">
                <?= e($success) ?> <a href="index.php" style="color:#4ade80;font-weight:700;">Log in →</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input class="form-input" type="text" id="name" name="name"
                    placeholder="Rahul" value="<?= e($_POST['name'] ?? '') ?>" required />
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input class="form-input" type="email" id="email" name="email"
                    placeholder="rahul@example.com" value="<?= e($_POST['email'] ?? '') ?>" required />
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password"
                    placeholder="Min. 8 characters" required />
            </div>
            <div class="form-group">
                <label class="form-label" for="confirm">Confirm Password</label>
                <input class="form-input" type="password" id="confirm" name="confirm"
                    placeholder="Re-enter your password" required />
            </div>
            <button class="btn-primary" type="submit">Create Account</button>
        </form>

        <p class="register-link">
            Already have an account? <a href="index.php">Log in</a>
        </p>
    </main>
</div>
</body>
</html>
