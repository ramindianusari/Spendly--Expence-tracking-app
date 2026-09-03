<?php
/**
 * home.php — Dashboard / Home Page
 * Expense Tracker App
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

requireLogin(); // Redirect to login if not authenticated

$user = currentUser();
$pdo  = getDBConnection();

/* ── Totals for the current month ── */
$monthStart = date('Y-m-01');
$monthEnd   = date('Y-m-t');

$totalsStmt = $pdo->prepare(
    "SELECT
        COALESCE(SUM(CASE WHEN type = 'income'  THEN amount ELSE 0 END), 0) AS total_income,
        COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
     FROM transactions
     WHERE user_id = :uid
       AND txn_date BETWEEN :start AND :end"
);
$totalsStmt->execute([':uid' => $user['id'], ':start' => $monthStart, ':end' => $monthEnd]);
$totals = $totalsStmt->fetch();

$totalIncome  = (float)$totals['total_income'];
$totalExpense = (float)$totals['total_expense'];
$balance      = $totalIncome - $totalExpense;

/* ── Recent 10 transactions (all time, newest first) ── */
$txnStmt = $pdo->prepare(
    "SELECT id, type, category, amount, note, txn_date
     FROM transactions
     WHERE user_id = :uid
     ORDER BY txn_date DESC, created_at DESC
     LIMIT 10"
);
$txnStmt->execute([':uid' => $user['id']]);
$transactions = $txnStmt->fetchAll();

/* ── Category → emoji mapping (server-side mirror of JS map) ── */
$categoryIcons = [
    'salary'     => '💰',
    'freelance'  => '💰',
    'food'       => '🍔',
    'transport'  => '🚌',
    'shopping'   => '🛍️',
    'health'     => '💊',
    'bills'      => '📄',
    'education'  => '📚',
    'travel'     => '✈️',
    'investment' => '📈',
    'other'      => '📦',
];

function getCategoryIcon(string $category, array $map): string {
    return $map[strtolower($category)] ?? '💳';
}

/* ── Greeting by time of day ── */
$hour     = (int)date('H');
$greeting = match(true) {
    $hour < 12 => 'Good morning,',
    $hour < 17 => 'Good afternoon,',
    $hour < 21 => 'Good evening,',
    default    => 'Good night,',
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home — Expense Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-shell">

    <!-- ══ TOP HEADER ══ -->
    <header class="home-header">
        <div>
            <p class="home-greeting-small"><?= e($greeting) ?></p>
            <h1 class="home-greeting-name"><?= e($user['name']) ?> 👋</h1>
        </div>
        <button class="btn-logout" id="logoutBtn" type="button" aria-label="Log out">
            Logout
        </button>
    </header>

    <!-- ══ BALANCE CARD ══ -->
    <section class="balance-card" aria-label="Financial summary">
        <p class="balance-label">Total Balance</p>
        <p
            class="balance-amount"
            id="totalBalance"
            data-value="<?= $balance ?>"
            aria-live="polite"
        >
            Rs. <?= number_format($balance, 0, '.', ',') ?>
        </p>

        <div class="balance-split">
            <div class="split-item">
                <p class="split-label">Income</p>
                <p class="split-value income">
                    <span class="split-arrow">↑</span>
                    Rs. <?= number_format($totalIncome, 0, '.', ',') ?>
                </p>
            </div>
            <div class="split-item">
                <p class="split-label">Expenses</p>
                <p class="split-value expense">
                    <span class="split-arrow">↓</span>
                    Rs. <?= number_format($totalExpense, 0, '.', ',') ?>
                </p>
            </div>
        </div>
    </section>

    <!-- ══ RECENT TRANSACTIONS ══ -->
    <section aria-label="Recent transactions">
        <div class="section-header">
            <h2 class="section-title">Recent Transactions</h2>
            <a href="transactions.php" class="section-link">See all</a>
        </div>

        <ul class="txn-list" role="list">
            <?php if (empty($transactions)): ?>
                <li class="empty-state">
                    <div class="empty-state-icon">💸</div>
                    <p class="empty-state-text">No transactions yet. Add your first one!</p>
                </li>
            <?php else: ?>
                <?php foreach ($transactions as $txn):
                    $isIncome = $txn['type'] === 'income';
                    $sign     = $isIncome ? '+' : '−';
                    $cls      = $isIncome ? 'income' : 'expense';
                    $icon     = getCategoryIcon($txn['category'], $categoryIcons);
                    $date     = date('Y-m-d', strtotime($txn['txn_date']));
                ?>
                <li class="txn-item" role="listitem">
                    <div class="txn-icon" aria-hidden="true"><?= $icon ?></div>
                    <div class="txn-info">
                        <p class="txn-category"><?= e(ucfirst($txn['category'])) ?></p>
                        <p class="txn-date"><?= e($date) ?></p>
                    </div>
                    <span class="txn-amount <?= $cls ?>" aria-label="<?= $sign ?>Rs. <?= number_format($txn['amount'], 0, '.', ',') ?>">
                        <?= $sign ?>Rs. <?= number_format($txn['amount'], 0, '.', ',') ?>
                    </span>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>

    <!-- ══ BOTTOM NAVIGATION ══ -->
    <nav class="bottom-nav" aria-label="Main navigation">
        <a href="home.php"         class="nav-item active" aria-current="page">
            <span class="nav-icon" aria-hidden="true">🏠</span>
            Home
        </a>
        <a href="add-expense.php"  class="nav-item">
            <span class="nav-icon" aria-hidden="true">➕</span>
            Expense
        </a>
        <a href="add-income.php"   class="nav-item">
            <span class="nav-icon" aria-hidden="true">💰</span>
            Income
        </a>
        <a href="reports.php"      class="nav-item">
            <span class="nav-icon" aria-hidden="true">📊</span>
            Reports
        </a>
    </nav>

</div><!-- /.app-shell -->

<script src="js/main.js"></script>
</body>
</html>
