<?php
/**
 * reports.php — Reports Page
 * Spendly Expense Tracker
 * Currency: Sri Lankan Rupees (Rs.)
 * Numbers: exact, no abbreviation
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$user = currentUser();
$pdo  = getDBConnection();

/* ── Month selector (default = current month) ── */
$selYear  = (int)($_GET['year']  ?? date('Y'));
$selMonth = (int)($_GET['month'] ?? date('n'));

// Clamp to valid range
$selMonth = max(1, min(12, $selMonth));
$selYear  = max(2020, min((int)date('Y') + 1, $selYear));

$monthStart = sprintf('%04d-%02d-01', $selYear, $selMonth);
$monthEnd   = date('Y-m-t', strtotime($monthStart));
$monthLabel = date('F Y', strtotime($monthStart)); // e.g. "September 2026"

/* ── Summary totals ── */
$summaryStmt = $pdo->prepare(
    "SELECT
        COALESCE(SUM(CASE WHEN type='income'  THEN amount ELSE 0 END),0) AS total_income,
        COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END),0) AS total_expense
     FROM transactions
     WHERE user_id = :uid AND txn_date BETWEEN :start AND :end"
);
$summaryStmt->execute([':uid' => $user['id'], ':start' => $monthStart, ':end' => $monthEnd]);
$summary = $summaryStmt->fetch();

$totalIncome  = (float)$summary['total_income'];
$totalExpense = (float)$summary['total_expense'];
$balance      = $totalIncome - $totalExpense;

/* ── Spending by Category (expenses only) ── */
$catStmt = $pdo->prepare(
    "SELECT category, SUM(amount) AS total
     FROM transactions
     WHERE user_id = :uid
       AND type = 'expense'
       AND txn_date BETWEEN :start AND :end
     GROUP BY category
     ORDER BY total DESC"
);
$catStmt->execute([':uid' => $user['id'], ':start' => $monthStart, ':end' => $monthEnd]);
$categoryData = $catStmt->fetchAll();

/* ── Prepare Chart.js data ── */
$chartLabels = [];
$chartValues = [];
$chartColors = [
    '#4ade80', '#f87171', '#60a5fa', '#fb923c',
    '#a78bfa', '#f472b6', '#facc15', '#34d399',
];

foreach ($categoryData as $i => $row) {
    $chartLabels[] = ucfirst($row['category']);
    $chartValues[] = (float)$row['total'];
}

$chartLabelsJson = json_encode($chartLabels);
$chartValuesJson = json_encode($chartValues);
$chartColorsJson = json_encode(array_slice($chartColors, 0, count($chartLabels)));

/* ── Rs. formatter helper (exact, no abbreviation) ── */
function fmtRs(float $amount): string {
    return 'Rs. ' . number_format($amount, 0, '.', ',');
}

/* ── Prev / Next month links ── */
$prevMonth = $selMonth - 1; $prevYear = $selYear;
if ($prevMonth < 1)  { $prevMonth = 12; $prevYear--; }
$nextMonth = $selMonth + 1; $nextYear = $selYear;
if ($nextMonth > 12) { $nextMonth = 1;  $nextYear++; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reports — Spendly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-shell">

    <!-- ── HEADER ── -->
    <header class="report-header">
        <div class="report-month-nav">
            <a href="reports.php?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="month-nav-btn" aria-label="Previous month">‹</a>
            <div>
                <p class="report-month-label"><?= strtoupper(e($monthLabel)) ?></p>
                <h1 class="report-title">Reports</h1>
            </div>
            <a href="reports.php?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="month-nav-btn" aria-label="Next month">›</a>
        </div>
    </header>

    <main class="report-body">

        <!-- ── SUMMARY CARDS ── -->
        <div class="report-cards">
            <div class="report-card report-card--income">
                <p class="rcard-label">INCOME</p>
                <p class="rcard-value"><?= fmtRs($totalIncome) ?></p>
            </div>
            <div class="report-card report-card--expense">
                <p class="rcard-label">EXPENSES</p>
                <p class="rcard-value"><?= fmtRs($totalExpense) ?></p>
            </div>
            <div class="report-card report-card--balance">
                <p class="rcard-label">BALANCE</p>
                <p class="rcard-value"><?= fmtRs($balance) ?></p>
            </div>
        </div>

        <!-- ── SPENDING BY CATEGORY ── -->
        <section class="report-section">
            <h2 class="report-section-title">Spending by Category</h2>

            <?php if (empty($categoryData)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <p class="empty-state-text">No expenses recorded for <?= e($monthLabel) ?>.</p>
                </div>
            <?php else: ?>
                <div class="chart-card">
                    <canvas id="barChart" height="220" aria-label="Spending by category bar chart"></canvas>
                </div>

                <!-- Category breakdown table -->
                <ul class="cat-breakdown-list">
                    <?php
                    $colors = ['#4ade80','#f87171','#60a5fa','#fb923c','#a78bfa','#f472b6','#facc15','#34d399'];
                    foreach ($categoryData as $i => $row):
                        $color = $colors[$i % count($colors)];
                    ?>
                    <li class="cat-breakdown-item">
                        <span class="cat-dot" style="background:<?= $color ?>"></span>
                        <span class="cat-name"><?= e(ucfirst($row['category'])) ?></span>
                        <span class="cat-amount"><?= fmtRs((float)$row['total']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

    </main>

    <!-- ── BOTTOM NAV ── -->
    <nav class="bottom-nav" aria-label="Main navigation">
        <a href="home.php"        class="nav-item"><span class="nav-icon">🏠</span>Home</a>
        <a href="add-expense.php" class="nav-item"><span class="nav-icon">➕</span>Expense</a>
        <a href="add-income.php"  class="nav-item"><span class="nav-icon">💰</span>Income</a>
        <a href="reports.php"     class="nav-item active"><span class="nav-icon">📊</span>Reports</a>
    </nav>

</div>

<script>
(function () {
    const labels = <?= $chartLabelsJson ?>;
    const values = <?= $chartValuesJson ?>;
    const colors = <?= $chartColorsJson ?>;

    if (!labels.length) return;

    const ctx = document.getElementById('barChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data:            values,
                backgroundColor: colors,
                borderRadius:    8,
                borderSkipped:   false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rs. ' + ctx.parsed.y.toLocaleString('en-LK')
                    }
                }
            },
            scales: {
                x: {
                    grid:  { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#8b93a7', font: { size: 12 } }
                },
                y: {
                    grid:  { color: 'rgba(255,255,255,0.05)' },
                    ticks: {
                        color: '#8b93a7',
                        font:  { size: 12 },
                        // Exact numbers — no abbreviation
                        callback: val => 'Rs. ' + val.toLocaleString('en-LK')
                    },
                    beginAtZero: true
                }
            }
        }
    });
})();
</script>
</body>
</html>
