<?php
include "../../db_connect.php";

if (!isset($conn) || !$conn instanceof mysqli) {
    die("Database connection failed.");
}

function format_rm($value)
{
    return "RM " . number_format($value, 2);
}

$dateFrom = $_GET["from"] ?? "2025-10-09";
$dateTo = $_GET["to"] ?? "2025-11-08";

$startDate = new DateTime($dateFrom);
$endDate = new DateTime($dateTo);
$endDate->modify("+1 day");

$period = new DatePeriod($startDate, new DateInterval("P1D"), $endDate);
$interval = $startDate->diff($endDate);
$historicalDays = max(1, $interval->days);

$stmt = $conn->prepare("
        SELECT DATE(order_date) as full_date, SUM(total_price) as daily_revenue
        FROM orders
        WHERE DATE(order_date) BETWEEN ? AND ?
        GROUP BY full_date
    ");
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();

$dbData = [];
while ($row = $result->fetch_assoc()) {
    $dbData[$row["full_date"]] = (float) $row["daily_revenue"];
}

$revenue_rows = [];
$x = [];
$y = [];
$n = 0;
$totalRevenue_Historical = 0;

foreach ($period as $key => $date) {
    $dateStr = $date->format("Y-m-d");
    $val = $dbData[$dateStr] ?? 0;

    $revenue_rows[] = [
        "date_label" => $date->format("M d"),
        "full_date" => $dateStr,
        "daily_revenue" => $val,
    ];

    $totalRevenue_Historical += $val;

    $x[] = $n;
    $y[] = $val;
    $n++;
}

$avgDailyRevenue = $n > 0 ? $totalRevenue_Historical / $n : 0;

$sumX = array_sum($x);
$sumY = array_sum($y);
$sumXX = 0;
$sumXY = 0;

for ($i = 0; $i < $n; $i++) {
    $sumXX += $x[$i] * $x[$i];
    $sumXY += $x[$i] * $y[$i];
}

$slope = 0;
$intercept = 0;

if ($n > 1 && $n * $sumXX - $sumX * $sumX != 0) {
    $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
    $intercept = ($sumY - $slope * $sumX) / $n;
} else {
    $intercept = $n > 0 ? $sumY / $n : 0;
}

$projected30DayRevenue = 0;
for ($i = 1; $i <= 30; $i++) {
    $futureX = $n + $i;
    $trendVal = $slope * $futureX + $intercept;
    $stabilizedVal = ($trendVal + $avgDailyRevenue) / 2;
    $finalVal = max(0, $stabilizedVal);
    $projected30DayRevenue += $finalVal;
}

$stmt = $conn->prepare(
    "SELECT COUNT(order_id) AS totalSales FROM orders WHERE DATE(order_date) BETWEEN ? AND ?",
);
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$resSales = $stmt->get_result()->fetch_assoc();
$totalSales_Historical = $resSales["totalSales"] ?? 0;

$avgDailySales = $n > 0 ? $totalSales_Historical / $n : 0;
$projected30DaySales = $avgDailySales * 30;

$revenueChartData = [
    "labels" => array_column($revenue_rows, "date_label"),
    "data" => array_column($revenue_rows, "daily_revenue"),
];

$forecastChartData = [
    "labels" => ["Historical Revenue", "Projected Revenue (Next 30 Days)"],
    "data" => [$totalRevenue_Historical, $projected30DayRevenue],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Forecast - Impian Optometrist</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="report.css">

    <style>
        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-top: 1px solid #eee;
            margin-top: 10px;
        }
        .btn-page {
            padding: 8px 16px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #555;
            transition: all 0.2s;
        }
        .btn-page:hover:not(:disabled) {
            background: #f9fafb;
            border-color: #ccc;
            color: #000;
        }
        .btn-page:disabled {
            background: #f3f4f6;
            color: #aaa;
            cursor: not-allowed;
        }
        .page-info {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header-bar">
            <h1 class="page-title">Business Forecast</h1>
            <a href="analytics.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <form action="" method="GET" class="card filter-bar">
            <div class="filter-group">
                <label for="date-from">Historical From</label>
                <input type="date" id="date-from" name="from" value="<?php echo htmlspecialchars(
                    $dateFrom,
                ); ?>">
            </div>
            <div class="filter-group">
                <label for="date-to">Historical To</label>
                <input type="date" id="date-to" name="to" value="<?php echo htmlspecialchars(
                    $dateTo,
                ); ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Run Forecast</span>
                </button>
                <button type="button" class="btn btn-primary" id="export-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Export as CSV</span>
                </button>
            </div>
        </form>

        <div class="kpi-grid">
            <div class="card kpi-card">
                <h3 class="kpi-title">Avg. Daily Revenue (Historical)</h3>
                <p class="kpi-value"><?php echo format_rm(
                    $avgDailyRevenue,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Avg. Daily Sales (Historical)</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    number_format($avgDailySales, 1),
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Projected 30-Day Revenue</h3>
                <p class="kpi-value"><?php echo format_rm(
                    $projected30DayRevenue,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Projected 30-Day Sales</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    round($projected30DaySales),
                ); ?></p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="card chart-container">
                <h3 class="chart-title">Historical Revenue Trend</h3>
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="card chart-container">
                <h3 class="chart-title">Revenue Forecast</h3>
                <div class="chart-wrapper">
                    <canvas id="forecastChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card table-container">
            <div class="table-header">
                <h3 class="table-title">Historical Daily Data (Basis for Forecast)</h3>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Daily Revenue (RM)</th>
                    </tr>
                </thead>
                <tbody id="data-table-body"></tbody>
            </table>

            <div class="pagination-controls">
                <button id="btn-prev" class="btn-page" disabled>Previous</button>
                <span id="page-info" class="page-info">Loading...</span>
                <button id="btn-next" class="btn-page">Next</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const chartConfig = {
                font: { family: "'Inter', sans-serif" },
                plugins: { legend: { labels: { font: { size: 13 }, boxWidth: 15 } } },
                interaction: { intersect: false, mode: 'index' }
            };

            function createRevenueChart(labels, data) {
                const ctxLine = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Revenue (RM)',
                            data: data,
                            borderColor: 'rgba(0, 94, 162, 1)',
                            backgroundColor: 'rgba(0, 94, 162, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(0, 94, 162, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } },
                        plugins: chartConfig.plugins,
                        interaction: chartConfig.interaction
                    }
                });
            }

            function createForecastChart(labels, data) {
                const ctxBar = document.getElementById('forecastChart').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Revenue (RM)',
                            data: data,
                            backgroundColor: ['rgba(0, 94, 162, 0.5)', 'rgba(0, 94, 162, 0.8)'],
                            borderColor: 'rgba(0, 94, 162, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function(value) { return 'RM ' + value; } }
                            }
                        }
                    }
                });
            }

            const initialRevenueData = <?php echo json_encode(
                $revenueChartData,
            ); ?>;
            const initialForecastData = <?php echo json_encode(
                $forecastChartData,
            ); ?>;
            const rawTableData = <?php echo json_encode($revenue_rows); ?>;

            const forecastSummary = [
                ["Report Type", "Business Forecast Report"],
                ["Historical Period", "<?php echo $dateFrom .
                    " to " .
                    $dateTo; ?>"],
                ["Generated On", "<?php echo date("Y-m-d H:i:s"); ?>"],
                [],
                ["FORECAST METRICS", ""],
                ["Historical Avg Daily Revenue", "RM <?php echo number_format(
                    $avgDailyRevenue,
                    2,
                ); ?>"],
                ["Historical Avg Daily Sales", "<?php echo number_format(
                    $avgDailySales,
                    1,
                ); ?> units"],
                ["Projected 30-Day Revenue", "RM <?php echo number_format(
                    $projected30DayRevenue,
                    2,
                ); ?>"],
                ["Projected 30-Day Sales", "<?php echo round(
                    $projected30DaySales,
                ); ?> units"],
                ["Trend Slope (Growth Factor)", "<?php echo number_format(
                    $slope,
                    4,
                ); ?>"],
                []
            ];

            createRevenueChart(initialRevenueData.labels, initialRevenueData.data);
            createForecastChart(initialForecastData.labels, initialForecastData.data);

            const itemsPerPage = 6;
            let currentPage = 1;
            const displayData = [...rawTableData].reverse();
            const totalItems = displayData.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            const tableBody = document.getElementById('data-table-body');
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');
            const pageInfo = document.getElementById('page-info');

            function renderTable(page) {
                tableBody.innerHTML = '';
                if (totalItems === 0) {
                    tableBody.innerHTML = '<tr><td colspan="2" style="text-align:center; padding:20px;">No historical data found.</td></tr>';
                    pageInfo.textContent = 'Showing 0 results';
                    btnPrev.disabled = true;
                    btnNext.disabled = true;
                    return;
                }

                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const paginatedItems = displayData.slice(start, end);

                paginatedItems.forEach(row => {
                    const tr = document.createElement('tr');
                    const revenueFormatted = parseFloat(row.daily_revenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    tr.innerHTML = `<td>${row.date_label}</td><td>${revenueFormatted}</td>`;
                    tableBody.appendChild(tr);
                });

                const showStart = start + 1;
                const showEnd = Math.min(end, totalItems);
                pageInfo.textContent = `Showing ${showStart}-${showEnd} of ${totalItems} results`;
                btnPrev.disabled = page === 1;
                btnNext.disabled = page === totalPages;
            }

            btnPrev.addEventListener('click', () => {
                if (currentPage > 1) { currentPage--; renderTable(currentPage); }
            });
            btnNext.addEventListener('click', () => {
                if (currentPage < totalPages) { currentPage++; renderTable(currentPage); }
            });
            renderTable(currentPage);

            function exportToCSV(tableData, summaryData, filename) {
                let csvContent = "";

                summaryData.forEach(row => {
                    const rowString = row.map(field => `"${String(field).replace(/"/g, '""')}"`).join(",");
                    csvContent += rowString + "\n";
                });

                csvContent += "DAILY DATA BREAKDOWN\n";
                const headers = ["Date", "Daily Revenue (RM)"];
                csvContent += headers.join(",") + "\n";

                tableData.forEach(row => {
                    const date = `"${row.date_label.replace(/"/g, '""')}"`;
                    const revenue = row.daily_revenue;
                    csvContent += [date, revenue].join(",") + "\n";
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                link.setAttribute("href", URL.createObjectURL(blob));
                link.setAttribute("download", filename);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            document.getElementById('export-btn').addEventListener('click', () => {
                const dateFrom = "<?php echo htmlspecialchars($dateFrom); ?>";
                const dateTo = "<?php echo htmlspecialchars($dateTo); ?>";
                const filename = `forecast_report_${dateFrom}_to_${dateTo}.csv`;

                exportToCSV(displayData, forecastSummary, filename);
            });
        });
    </script>
</body>
</html>
