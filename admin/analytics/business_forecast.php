<?php
    include "../../db_connect.php";

    if (!isset($conn) || !$conn instanceof mysqli) {
        die(
            "Database connection failed. Check '../db_connect.php'. (Expecting a MySQLi connection)"
        );
    }

    function format_rm($value)
    {
        return "RM " . number_format($value, 2);
    }


    $dateFrom = $_GET["from"] ?? "2025-10-09";
    $dateTo = $_GET["to"] ?? "2025-11-08";

    $datetime1 = new DateTime($dateFrom);
    $datetime2 = new DateTime($dateTo);
    $interval = $datetime1->diff($datetime2);
    $historicalDays = max(1, $interval->days + 1);

    $stmt = $conn->prepare("
        SELECT
            DATE_FORMAT(order_date, '%b %d') AS date_label,
            DATE(order_date) AS full_date,
            SUM(total_price) AS daily_revenue
        FROM orders
        WHERE DATE(order_date) BETWEEN ? AND ?
        GROUP BY full_date
        ORDER BY full_date ASC
    ");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    $revenue_rows = $result->fetch_all(MYSQLI_ASSOC);
    $totalRevenue_Historical = array_sum(
        array_column($revenue_rows, "daily_revenue"),
    );
    $totalSales_Historical = 0;

    $stmt = $conn->prepare("
        SELECT COUNT(order_id) AS totalSales
        FROM orders
        WHERE DATE(order_date) BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSales_Historical = $result->fetch_assoc()["totalSales"] ?? 0;

    $avgDailyRevenue = $totalRevenue_Historical / $historicalDays;
    $avgDailySales = $totalSales_Historical / $historicalDays;

    $projected30DayRevenue = $avgDailyRevenue * 30;
    $projected30DaySales = $avgDailySales * 30;

    $revenueChartData = [
        "labels" => array_column($revenue_rows, "date_label"),
        "data" => array_column($revenue_rows, "daily_revenue"),
    ];

    $forecastChartData = [
        "labels" => [
            "Historical Revenue ({$historicalDays} days)",
            "Projected Revenue (Next 30 Days)",
        ],
        "data" => [$totalRevenue_Historical, $projected30DayRevenue],
    ];


    $total_records = count($revenue_rows);
    $paginationInfo =
        "Showing " .
        count($revenue_rows) .
        " of {$total_records} results (Daily Breakdown)";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Forecast - Impian Optometrist</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="report.css"> </head>
<body>

    <div class="report-container">
        <div class="header-bar">
            <h1 class="page-title">Business Forecast</h1>
            <a href="analytics.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <form action="business_forecast.php" method="GET" class="card filter-bar">
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
                <tbody>
                    <?php if (empty($revenue_rows)): ?>
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 20px;">No historical data found for this period.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_reverse($revenue_rows) as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(
                                    $row["date_label"],
                                ); ?></td>
                                <td><?php echo htmlspecialchars(
                                    number_format($row["daily_revenue"], 2),
                                ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="pagination">
                <span class="pagination-info"><?php echo htmlspecialchars(
                    $paginationInfo,
                ); ?></span>
                 <div class="pagination-controls">
                    <button id="prev-page" disabled>&laquo; Previous</button>
                    <button id="next-page" disabled>Next &raquo;</button>
                 </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const chartConfig = {
                font: { family: "'Inter', sans-serif" },
                plugins: {
                    legend: {
                        labels: {
                            font: { size: 13 },
                            boxWidth: 15
                        }
                    }
                },
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
                            backgroundColor: [
                                'rgba(0, 94, 162, 0.5)',
                                'rgba(0, 94, 162, 0.8)'
                            ],
                            borderColor: 'rgba(0, 94, 162, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'RM ' + value;
                                    }
                                }
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
            const tableData = <?php echo json_encode($revenue_rows); ?>;
            const dateFrom = "<?php echo htmlspecialchars($dateFrom); ?>";
            const dateTo = "<?php echo htmlspecialchars($dateTo); ?>";


            createRevenueChart(initialRevenueData.labels, initialRevenueData.data);
            createForecastChart(initialForecastData.labels, initialForecastData.data);

            
            function exportToCSV(data, filename) {
                const headers = ["Date", "Daily Revenue (RM)"];
                
                let csvContent = headers.join(",") + "\n";

                const reversedData = [...data].reverse(); 

                reversedData.forEach(row => {
                    const date = `"${row.date_label.replace(/"/g, '""')}"`; 
                    const revenue = row.daily_revenue;
                    csvContent += [date, revenue].join(",") + "\n";
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                const link = document.createElement("a");
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }


            document.getElementById('export-btn').addEventListener('click', () => {
                const filename = `forecast_data_${dateFrom}_to_${dateTo}.csv`;
                exportToCSV(tableData, filename);
            });

        });
    </sCRIPT>

</body>
</html>