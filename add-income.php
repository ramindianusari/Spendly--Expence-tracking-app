<?php
/**
 * add-income.php — Add Income Page
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
        $error = 'Please select an income source.';
    } else {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO transactions (user_id, type, category, amount, note, txn_date)
             VALUES (:uid, :type, :cat, :amt, :note, :date)'
        );
        $stmt->execute([
            ':uid'  => $user['id'],
            ':type' => 'income',
            ':cat'  => $category,
            ':amt'  => (float)$amount,
            ':note' => $note,
            ':date' => $date,
        ]);

        $success = 'Income added successfully!';
    }
}

$today = date('Y-m-d');

$incomeSources = [
    ['slug' => 'salary',     'label' => 'Salary'],
    ['slug' => 'freelance',  'label' => 'Freelance'],
    ['slug' => 'business',   'label' => 'Business'],
    ['slug' => 'investment', 'label' => 'Investment'],
    ['slug' => 'gift',       'label' => 'Gift'],
    ['slug' => 'other',      'label' => 'Other'],
];

$selectedSource = $_POST['category'] ?? '';
$postedAmount   = $_POST['amount']   ?? '';
$postedDate     = $_POST['date']     ?? $today;
$postedNote     = $_POST['note']     ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Income — Spendly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-shell">

    <!-- ── PAGE HEADER ── -->
    <header class="record-header">
        <span class="record-tag record-tag--income">RECORD</span>
        <h1 class="record-title">Add Income</h1>
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

        <form method="POST" action="add-income.php" id="incomeForm" novalidate>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label" for="amount">AMOUNT (₹)</label>
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

            <!-- Source pills -->
            <div class="form-group">
                <label class="form-label">SOURCE</label>
                <div class="source-grid">
                    <?php foreach ($incomeSources as $src): ?>
                        <button
                            type="button"
                            class="source-btn <?= $selectedSource === $src['slug'] ? 'source-btn--active' : '' ?>"
                            data-value="<?= e($src['slug']) ?>"
                            aria-pressed="<?= $selectedSource === $src['slug'] ? 'true' : 'false' ?>"
                        >
                            <?= e($src['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="category" id="sourceInput" value="<?= e($selectedSource) ?>" />
            </div>

            <!-- Note (optional, not shown in design but useful) -->
            <div class="form-group">
                <label class="form-label" for="note">NOTE (OPTIONAL)</label>
                <input
                    class="form-input"
                    type="text"
                    id="note"
                    name="note"
                    placeholder="e.g. September salary"
                    value="<?= e($postedNote) ?>"
                />
            </div>

            <button class="btn-record btn-record--income" type="submit">Add Income</button>
        </form>
    </main>

    <!-- ── BOTTOM NAV ── -->
    <nav class="bottom-nav" aria-label="Main navigation">
        <a href="home.php"        class="nav-item">
            <span class="nav-icon">🏠</span>Home
        </a>
        <a href="add-expense.php" class="nav-item">
            <span class="nav-icon">➕</span>Expense
        </a>
        <a href="add-income.php"  class="nav-item active">
            <span class="nav-icon">💰</span>Income
        </a>
        <a href="reports.php"     class="nav-item">
            <span class="nav-icon">📊</span>Reports
        </a>
    </nav>

</div>

<script src="js/main.js"></script>
<script>
// Source selector — income page
(function () {
    const srcBtns     = document.querySelectorAll('.source-btn');
    const hiddenInput = document.getElementById('sourceInput');

    srcBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            srcBtns.forEach(b => {
                b.classList.remove('source-btn--active');
                b.setAttribute('aria-pressed', 'false');
            });
            btn.classList.add('source-btn--active');
            btn.setAttribute('aria-pressed', 'true');
            hiddenInput.value = btn.dataset.value;
        });
    });

    // Client-side validation
    document.getElementById('incomeForm').addEventListener('submit', function (e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            alert('Please select an income source.');
            return;
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
