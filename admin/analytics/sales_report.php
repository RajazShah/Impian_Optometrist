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

$stmt = $conn->prepare("
    SELECT
        SUM(total_price) AS totalRevenue,
        COUNT(order_id) AS totalSales
    FROM orders
    WHERE DATE(order_date) BETWEEN ? AND ?
");
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();
$kpi_data = $result->fetch_assoc();

$totalRevenue = $kpi_data["totalRevenue"] ?? 0;
$totalSales = $kpi_data["totalSales"] ?? 0;
$avgOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

$stmt = $conn->prepare("
    SELECT SUM(oi.quantity) AS totalUnitsSold
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    WHERE DATE(o.order_date) BETWEEN ? AND ?
");
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();
$totalUnitsSold = $result->fetch_assoc()["totalUnitsSold"] ?? 0;

$stmt = $conn->prepare("
    SELECT
        DATE_FORMAT(order_date, '%b %d') AS date_label,
        SUM(total_price) AS daily_revenue
    FROM orders
    WHERE DATE(order_date) BETWEEN ? AND ?
    GROUP BY date_label
    ORDER BY DATE(order_date) ASC
");
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();
$revenue_rows = $result->fetch_all(MYSQLI_ASSOC);
$revenueChartData = [
    "labels" => array_column($revenue_rows, "date_label"),
    "data" => array_column($revenue_rows, "daily_revenue"),
];

$stmt = $conn->prepare("
    SELECT
        i.item_name,
        SUM(oi.quantity) AS units_sold
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN item i ON oi.product_id = i.ITEM_ID
    WHERE DATE(o.order_date) BETWEEN ? AND ?
    GROUP BY oi.product_id
    ORDER BY units_sold DESC
    LIMIT 5
");
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();
$product_rows = $result->fetch_all(MYSQLI_ASSOC);
$productsChartData = [
    "labels" => array_column($product_rows, "item_name"),
    "data" => array_column($product_rows, "units_sold"),
];

$stmt = $conn->prepare("
    SELECT
        o.order_id,
        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
        o.order_date,
        o.order_status,
        o.total_price
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE DATE(o.order_date) BETWEEN ? AND ?
    ORDER BY o.order_date DESC
    LIMIT 10
");
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();
$tableRows = $result->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare(
    "SELECT COUNT(order_id) AS total FROM orders WHERE DATE(order_date) BETWEEN ? AND ?",
);
$stmt->bind_param("ss", $dateFrom, $dateTo);
$stmt->execute();
$result = $stmt->get_result();
$total_records = $result->fetch_assoc()["total"] ?? 0;
$paginationInfo =
    "Showing " . count($tableRows) . " of {$total_records} results";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Impian Optometrist</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="report.css">
</head>
<body>

    <div class="report-container">
        <div class="header-bar">
            <h1 class="page-title">Sales Report</h1>
            <a href="analytics.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <form action="sales_report.php" method="GET" class="card filter-bar">
            <div class="filter-group">
                <label for="date-from">From</label>
                <input type="date" id="date-from" name="from" value="<?php echo htmlspecialchars(
                    $dateFrom,
                ); ?>">
            </div>
            <div class="filter-group">
                <label for="date-to">To</label>
                <input type="date" id="date-to" name="to" value="<?php echo htmlspecialchars(
                    $dateTo,
                ); ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                <button type="button" class="btn btn-primary" id="export-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Export as CSV</span>
                </button>
            </div>
        </form>

        <div class="kpi-grid">
            <div class="card kpi-card">
                <h3 class="kpi-title">Total Revenue</h3>
                <p class="kpi-value"><?php echo format_rm($totalRevenue); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Total Sales</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    $totalSales,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Avg. Order Value</h3>
                <p class="kpi-value"><?php echo format_rm(
                    $avgOrderValue,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Total Units Sold</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    $totalUnitsSold,
                ); ?></p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="card chart-container">
                <h3 class="chart-title">Revenue Over Time</h3>
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="card chart-container">
                <h3 class="chart-title">Top Selling Products</h3>
                <div class="chart-wrapper">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card table-container">
            <div class="table-header">
                <h3 class="table-title">Recent Orders</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tableRows)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No transactions found for this period.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tableRows as $row): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars(
                                    $row["order_id"],
                                ); ?></td>
                                <td><?php echo htmlspecialchars(
                                    $row["customer_name"],
                                ); ?></td>
                                <td>
                                    <?php
                                    $date = new DateTime($row["order_date"]);
                                    echo $date->format("d M Y");
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars(
                                    $row["order_status"],
                                ); ?></td>
                                <td><?php echo htmlspecialchars(
                                    number_format($row["total_price"], 2),
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
                    <button id="next-page">Next &raquo;</button>
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

            function createProductsChart(labels, data) {
                const ctxBar = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Units Sold',
                            data: data,
                            backgroundColor: [
                                'rgba(0, 94, 162, 0.8)',
                                'rgba(0, 94, 162, 0.7)',
                                'rgba(0, 94, 162, 0.6)',
                                'rgba(0, 94, 162, 0.5)',
                                'rgba(0, 94, 162, 0.4)'
                            ],
                            borderColor: 'rgba(0, 94, 162, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: { beginAtZero: true }
                        }
                    }
                });
            }

            const initialRevenueData = <?php echo json_encode(
                $revenueChartData,
            ); ?>;
            const initialProductsData = <?php echo json_encode(
                $productsChartData,
            ); ?>;

            const tableData = <?php echo json_encode($tableRows); ?>;
            const dateFrom = "<?php echo htmlspecialchars($dateFrom); ?>";
            const dateTo = "<?php echo htmlspecialchars($dateTo); ?>";

            createRevenueChart(initialRevenueData.labels, initialRevenueData.data);
            createProductsChart(initialProductsData.labels, initialProductsData.data);

            function escapeCSV(field) {
                if (field === null || field === undefined) {
                    return "";
                }
                let str = String(field);
                if (str.includes(',') || str.includes('"') || str.includes('\n')) {
                    return `"${str.replace(/"/g, '""')}"`;
                }
                return str;
            }

            function formatDateForCSV(dateString) {
                if (!dateString) {
                    return "N/A";
                }
                const date = new Date(dateString);
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                const day = date.getDate();
                const month = months[date.getMonth()];
                const year = date.getFullYear();
                return `${day} ${month} ${year}`;
            }

            function exportToCSV(data, filename) {
                const headers = ["Order ID", "Customer", "Date", "Status", "Amount (RM)"];
                let csvContent = headers.join(",") + "\n";

                data.forEach(row => {
                    const rowData = [
                        escapeCSV(`#${row.order_id}`),
                        escapeCSV(row.customer_name),
                        escapeCSV(formatDateForCSV(row.order_date)),
                        escapeCSV(row.order_status),
                        parseFloat(row.total_price).toFixed(2)
                    ];
                    csvContent += rowData.join(",") + "\n";
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf8;' });
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
                const filename = `sales_report_${dateFrom}_to_${dateTo}.csv`;
                exportToCSV(tableData, filename);
            });

        });
    </sCRIPT>

</body>
</html>
