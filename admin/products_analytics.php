<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Analytics Dashboard</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Apply the Inter font family */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen w-full bg-gray-100 p-6 sm:p-10 text-gray-900"> <!-- Light theme body -->

    <div class="mx-auto max-w-7xl">
        
        <!-- Header -->
        <header class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Product Analytics Dashboard</h1>
                <p class="text-sm text-gray-600">Welcome back, here's your product performance summary.</p>
            </div>
            <!-- Exit Button -->
            <button class="rounded-lg border border-gray-300 bg-white p-2 shadow-sm transition-all hover:bg-gray-50 hover:shadow-md" aria-label="Exit Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-gray-700">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </header>

        <!-- KPI Cards Grid (Hardcoded) -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Card 1: Total Revenue -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-600">Total Revenue</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-green-500"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold text-gray-900">$405,231.89</p>
                    <p class="mt-1 text-xs text-green-600">+20.1% from last month</p>
                </div>
            </div>

            <!-- Card 2: Active Users -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-600">Active Users</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-blue-500"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold text-gray-900">+2,350</p>
                    <p class="mt-1 text-xs text-green-600">+18.3% from last month</p>
                </div>
            </div>

            <!-- Card 3: Total Sales -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-600">Total Sales</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-orange-500"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold text-gray-900">+12,234</p>
                    <p class="mt-1 text-xs text-green-600">+5.2% from last month</p>
                </div>
            </div>

            <!-- Card 4: Conversion Rate -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-600">Conversion Rate</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-red-500"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-bold text-gray-900">4.82%</p>
                    <p class="mt-1 text-xs text-red-600">-1.1% from last month</p>
                </div>
            </div>

        </div>

        <!-- Charts and Table Grid (Simplified Layout) -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
          
            <!-- Sales Overview Bar Chart Card -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Sales Overview (Monthly)</h3>
                <div class="h-72 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Active Users Line Chart Card -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Active Users (Weekly)</h3>
                <div class="h-72 w-full">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
          
            <!-- Top Products Table (spans full width on new row) -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-lg lg:col-span-2">
                <h3 class="p-6 text-lg font-semibold text-gray-900">Top Performing Products</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Product Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Product ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Units Sold
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Total Revenue
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Table rows are now hardcoded -->
                            <tr class="transition-colors duration-200 hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">Classic Leather Wallet</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">prod_001</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">1,200</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">$48,000</td>
                            </tr>
                            <tr class="transition-colors duration-200 hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">Wireless Bluetooth Earbuds</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">prod_002</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">850</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">$84,150</td>
                            </tr>
                            <tr class="transition-colors duration-200 hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">Smart Home Hub</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">prod_003</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">600</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">$59,400</td>
                            </tr>
                            <tr class="transition-colors duration-200 hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">Ergonomic Office Chair</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">prod_004</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">350</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">$104,650</td>
                            </tr>
                            <tr class="transition-colors duration-200 hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">Portable Power Bank</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">prod_005</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">1,500</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">$37,500</td>
                            </tr>
Etr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- end charts and table grid -->
        
    </div> <!-- end max-w-7xl -->


    <script>
        // Wait for the DOM to be fully loaded before running scripts
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- Mock Data (for charts only) ---

            const salesData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                data: [4000, 3000, 5000, 4500, 6000, 5500, 7000]
            };

            const userData = {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
                data: [1200, 2100, 1800, 2400, 2200, 3100]
            };

            /**
             * Renders the charts using Chart.js
             */
            function renderCharts() {
                const gridColor = '#E5E7EB'; // gray-200
                const labelColor = '#4B5563'; // gray-600

                // Chart.js default styles for light mode
                Chart.defaults.color = labelColor;
                Chart.defaults.borderColor = gridColor;

                // --- Sales Bar Chart ---
                const salesCtx = document.getElementById('salesChart').getContext('2d');
                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: salesData.labels,
                        datasets: [{
                            label: 'Sales',
                            data: salesData.data,
                            backgroundColor: '#3B82F6', // blue-500
                            borderRadius: 4,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: labelColor }
                            },
                            tooltip: {
                                backgroundColor: '#FFFFFF',
                                titleColor: '#1F2937',
                                bodyColor: '#1F2937',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                borderRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor },
                                ticks: { color: labelColor }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: labelColor }
                            }
                        }
                    }
                });

                // --- Active Users Line Chart ---
                const usersCtx = document.getElementById('usersChart').getContext('2d');
                new Chart(usersCtx, {
                    type: 'line',
                    data: {
                        labels: userData.labels,
                        datasets: [{
                            label: 'Users',
                            data: userData.data,
                            borderColor: '#10B981', // emerald-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#10B981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: labelColor }
                            },
                            tooltip: {
                                backgroundColor: '#FFFFFF',
                                titleColor: '#1F2937',
                                bodyColor: '#1F2937',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                borderRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                grid: { color: gridColor },
                                ticks: { color: labelColor }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: labelColor }
                            }
                        }
                    }
                });
            }

            // --- Initial Render Call ---
            renderCharts();
        });
    </script>

</body>
</html>

