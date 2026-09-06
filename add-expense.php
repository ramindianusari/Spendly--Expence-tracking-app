<?php
/**
 * add-expense.php — Add Expense Page
 * Expense Tracker — Spendly
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$user    = currentUser();
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount   = trim($_POST['amount']   ?? '');
    $date     = trim($_POST['date']     ?? '');
    $category = trim($_POST['category'] ?? '');
    $note     = trim($_POST['note']     ?? '');

    // Validation
    if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0) {
        $error = 'Please enter a valid amount greater than 0.';
    } elseif ($date === '' || !strtotime($date)) {
        $error = 'Please select a valid date.';
    } elseif ($category === '') {
        $error = 'Please select a category.';
    } else {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO transactions (user_id, type, category, amount, note, txn_date)
             VALUES (:uid, :type, :cat, :amt, :note, :date)'
        );
        $stmt->execute([
            ':uid'  => $user['id'],
            ':type' => 'expense',
            ':cat'  => $category,
            ':amt'  => (float)$amount,
            ':note' => $note,
            ':date' => $date,
        ]);

        $success = 'Expense added successfully!';
    }
}

$today = date('Y-m-d');

$expenseCategories = [
    ['slug' => 'food',          'label' => 'Food',          'icon' => '🍔'],
    ['slug' => 'transport',     'label' => 'Transport',     'icon' => '🚗'],
    ['slug' => 'shopping',      'label' => 'Shopping',      'icon' => '🛍️'],
    ['slug' => 'bills',         'label' => 'Bills',         'icon' => '🏠'],
    ['slug' => 'education',     'label' => 'Education',     'icon' => '📚'],
    ['slug' => 'entertainment', 'label' => 'Entertainment', 'icon' => '🎬'],
    ['slug' => 'health',        'label' => 'Health',        'icon' => '💊'],
    ['slug' => 'other',         'label' => 'Other',         'icon' => '📦'],
];

$selectedCategory = $_POST['category'] ?? '';
$postedAmount     = $_POST['amount']   ?? '';
$postedDate       = $_POST['date']     ?? $today;
$postedNote       = $_POST['note']     ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Expense — Spendly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-shell">

    <!-- ── PAGE HEADER ── -->
    <header class="record-header">
        <span class="record-tag record-tag--expense">RECORD</span>
        <h1 class="record-title">Add Expense</h1>
    </header>

    <!-- ── FORM ── -->
    <main class="record-form-wrap">

        <?php if ($error !== ''): ?>
            <div class="form-alert form-alert--error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="form-alert form-alert--success" role="status">
                <?= e($success) ?> <a href="home.php">← Back to home</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="add-expense.php" id="expenseForm" novalidate>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label" for="amount">AMOUNT (Rs.)</label>
                <input
                    class="form-input amount-input"
                    type="number"
                    id="amount"
                    name="amount"
                    placeholder="0.00"
                    step="0.01"
                    min="0.01"
                    value="<?= e($postedAmount) ?>"
                    required
                />
            </div>

            <!-- Date -->
            <div class="form-group">
                <label class="form-label" for="date">DATE</label>
                <input
                    class="form-input"
                    type="date"
                    id="date"
                    name="date"
                    value="<?= e($postedDate) ?>"
                    required
                />
            </div>

            <!-- Category grid -->
            <div class="form-group">
                <label class="form-label">CATEGORY</label>
                <div class="category-grid">
                    <?php foreach ($expenseCategories as $cat): ?>
                        <button
                            type="button"
                            class="cat-btn <?= $selectedCategory === $cat['slug'] ? 'cat-btn--active-expense' : '' ?>"
                            data-value="<?= e($cat['slug']) ?>"
                            aria-pressed="<?= $selectedCategory === $cat['slug'] ? 'true' : 'false' ?>"
                        >
                            <span class="cat-icon"><?= $cat['icon'] ?></span>
                            <span class="cat-label"><?= e($cat['label']) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
                <!-- Hidden input carries the selected value -->
                <input type="hidden" name="category" id="categoryInput" value="<?= e($selectedCategory) ?>" />
            </div>

            <!-- Note -->
            <div class="form-group">
                <label class="form-label" for="note">NOTE (OPTIONAL)</label>
                <input
                    class="form-input"
                    type="text"
                    id="note"
                    name="note"
                    placeholder="What was this for?"
                    value="<?= e($postedNote) ?>"
                />
            </div>

            <button class="btn-record btn-record--expense" type="submit">Add Expense</button>
        </form>
    </main>

    <!-- ── BOTTOM NAV ── -->
    <nav class="bottom-nav" aria-label="Main navigation">
        <a href="home.php"        class="nav-item">
            <span class="nav-icon">🏠</span>Home
        </a>
        <a href="add-expense.php" class="nav-item active">
            <span class="nav-icon">➕</span>Expense
        </a>
        <a href="add-income.php"  class="nav-item">
            <span class="nav-icon">💰</span>Income
        </a>
        <a href="reports.php"     class="nav-item">
            <span class="nav-icon">📊</span>Reports
        </a>
    </nav>

</div>

<script src="js/main.js"></script>
<script>
// Category selector — expense page
(function () {
    const catBtns     = document.querySelectorAll('.cat-btn');
    const hiddenInput = document.getElementById('categoryInput');

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catBtns.forEach(b => {
                b.classList.remove('cat-btn--active-expense', 'cat-btn--active-income');
                b.setAttribute('aria-pressed', 'false');
            });
            btn.classList.add('cat-btn--active-expense');
            btn.setAttribute('aria-pressed', 'true');
            hiddenInput.value = btn.dataset.value;
        });
    });

    // Client-side validation: require category
    document.getElementById('expenseForm').addEventListener('submit', function (e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            alert('Please select a category.');
        }
        const amt = parseFloat(document.getElementById('amount').value);
        if (!amt || amt <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount.');
        }
    });
})();
</script>
</body>
</html>
