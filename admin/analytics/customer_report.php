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
            COUNT(DISTINCT user_id) AS totalCustomers,
            SUM(total_price) AS totalRevenue
        FROM orders
        WHERE DATE(order_date) BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    $kpi_data = $result->fetch_assoc();

    $totalCustomers = $kpi_data["totalCustomers"] ?? 0;
    $totalRevenue = $kpi_data["totalRevenue"] ?? 0;

    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT user_id) AS newCustomers
        FROM orders o1
        WHERE DATE(o1.order_date) BETWEEN ? AND ?
        AND NOT EXISTS (
            SELECT 1 FROM orders o2
            WHERE o2.user_id = o1.user_id AND DATE(o2.order_date) < ?
        )
    ");
    $stmt->bind_param("sss", $dateFrom, $dateTo, $dateFrom);
    $stmt->execute();
    $result = $stmt->get_result();
    $newCustomers = $result->fetch_assoc()["newCustomers"] ?? 0;

    $returningCustomers = $totalCustomers - $newCustomers;

    $avgRevenuePerCustomer =
        $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;

    $customerTypeChartData = [
        "labels" => ["New Customers", "Returning Customers"],
        "data" => [$newCustomers, $returningCustomers],
    ];

    $stmt = $conn->prepare("
        SELECT
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            SUM(o.total_price) AS total_spent
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE DATE(o.order_date) BETWEEN ? AND ?
        GROUP BY o.user_id
        ORDER BY total_spent DESC
        LIMIT 5
    ");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    $top_customer_rows = $result->fetch_all(MYSQLI_ASSOC);

    $topCustomerChartData = [
        "labels" => array_column($top_customer_rows, "customer_name"),
        "data" => array_column($top_customer_rows, "total_spent"),
    ];

    $stmt = $conn->prepare("
        SELECT
            u.id,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            u.email,
            MIN(o.order_date) AS first_order_date,
            MAX(o.order_date) AS last_order_date,
            COUNT(o.order_id) AS total_orders,
            COALESCE(SUM(o.total_price), 0) AS lifetime_spent
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        GROUP BY u.id
        ORDER BY lifetime_spent DESC
        LIMIT 10
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $tableRows = $result->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT COUNT(id) AS total FROM users");
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
    <title>Customer Report - Impian Optometrist</title> <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="report.css"> </head>
<body>

    <div class="report-container">
        <div class="header-bar">
            <h1 class="page-title">Customer Report</h1> <a href="analytics.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <form action="customer_report.php" method="GET" class="card filter-bar"> <div class="filter-group">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Export as CSV</span>
                </button>
            </div>
        </form>

        <div class="kpi-grid">
            <div class="card kpi-card">
                <h3 class="kpi-title">Total Customers</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    $totalCustomers,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">New Customers</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    $newCustomers,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Returning Customers</h3>
                <p class="kpi-value"><?php echo htmlspecialchars(
                    $returningCustomers,
                ); ?></p>
            </div>
            <div class="card kpi-card">
                <h3 class="kpi-title">Avg. Revenue / Customer</h3>
                <p class="kpi-value"><?php echo format_rm(
                    $avgRevenuePerCustomer,
                ); ?></p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="card chart-container">
                <h3 class="chart-title">New vs. Returning Customers</h3>
                <div class="chart-wrapper" style="max-height: 350px;">
                    <canvas id="customerTypeChart"></canvas>
                </div>
            </div>
            <div class="card chart-container">
                <h3 class="chart-title">Top Customers by Spend</h3>
                <div class="chart-wrapper">
                    <canvas id="topCustomerChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card table-container">
            <div class="table-header">
                <h3 class="table-title">Customer List (All-Time)</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Customer Since</th>
                        <th>Last Order</th>
                        <th>Total Orders</th>
                        <th>Lifetime Spent (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tableRows)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">No customers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tableRows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(
                                    $row["customer_name"],
                                ); ?></td>
                                <td><?php echo htmlspecialchars(
                                    $row["email"],
                                ); ?></td>
                                <td>
                                    <?php if ($row["first_order_date"]) {
                                        $date = new DateTime(
                                            $row["first_order_date"],
                                        );
                                        echo $date->format("d M Y");
                                    } else {
                                        echo "N/A";
                                    } ?>
                                </td>
                                <td>
                                    <?php if ($row["last_order_date"]) {
                                        $date = new DateTime(
                                            $row["last_order_date"],
                                        );
                                        echo $date->format("d M Y");
                                    } else {
                                        echo "N/A";
                                    } ?>
                                </td>
                                <td><?php echo htmlspecialchars(
                                    $row["total_orders"],
                                ); ?></td>
                                <td><?php echo htmlspecialchars(
                                    number_format($row["lifetime_spent"], 2),
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

            function createPieChart(labels, data) {
                const ctxPie = document.getElementById('customerTypeChart').getContext('2d');
                new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Customers',
                            data: data,
                            backgroundColor: [
                                'rgba(0, 94, 162, 0.8)',
                                'rgba(0, 94, 162, 0.4)'
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: chartConfig.plugins.legend.labels
                            }
                        }
                    }
                });
            }

            function createTopCustomerChart(labels, data) {
                const ctxBar = document.getElementById('topCustomerChart').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Spent (RM)',
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
                            x: {
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

            const initialPieData = <?php echo json_encode(
                $customerTypeChartData,
            ); ?>;
            const initialTopCustomerData = <?php echo json_encode(
                $topCustomerChartData,
            ); ?>;
            
            const tableData = <?php echo json_encode($tableRows); ?>;

            createPieChart(initialPieData.labels, initialPieData.data);
            createTopCustomerChart(initialTopCustomerData.labels, initialTopCustomerData.data);

            function formatDateForCSV(dateString) {
                if (!dateString) {
                    return "N/A";
                }
                const date = new Date(dateString);
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                const day = date.getDate().toString().padStart(2, '0');
                const month = months[date.getMonth()];
                const year = date.getFullYear();
                return `${day} ${month} ${year}`;
            }

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

            function exportToCSV(data, filename) {
                const headers = [
                    "Customer",
                    "Email",
                    "Customer Since",
                    "Last Order",
                    "Total Orders",
                    "Lifetime Spent (RM)"
                ];

                let csvContent = headers.join(",") + "\n";

                data.forEach(row => {
                    const rowData = [
                        escapeCSV(row.customer_name),
                        escapeCSV(row.email),
                        escapeCSV(formatDateForCSV(row.first_order_date)),
                        escapeCSV(formatDateForCSV(row.last_order_date)),
                        row.total_orders,
                        parseFloat(row.lifetime_spent).toFixed(2) 
                    ];
                    csvContent += rowData.join(",") + "\n";
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
                const filename = 'customer_report_all_time.csv';
                exportToCSV(tableData, filename);
            });

        });
    </sCRIPT>

</body>
</html>