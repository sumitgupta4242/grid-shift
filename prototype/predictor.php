<?php
// Generate start date for the next 30 days
$today = new DateTime('today');
$startDate = $today->modify('+1 day');

// Create an array for the static bar chart dates
$staticDates = [];
for ($i = 0; $i < 30; $i++) {
    $date = clone $startDate;
    $date->modify('+' . $i . ' days');
    $staticDates[] = $date->format('Y-m-d');
}

// Generate random bar heights for static chart
$staticBarHeights = [];
for ($i = 0; $i < 30; $i++) {
    $staticBarHeights[] = rand(5, 30); // Random values between 5 and 30
}

// Generate data for the dynamic chart
function generateEnergyData($daysInMonth, $currentDay)
{
    $data = [];
    for ($i = 1; $i <= $daysInMonth; $i++) {
        $data[] = ($i <= $currentDay) ? rand(50, 150) : null;
    }
    return $data;
}

$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$currentTimestamp = strtotime(date('Y-m'));
$selectedTimestamp = strtotime($currentMonth . "-01");
$isFutureMonth = $selectedTimestamp > $currentTimestamp;

if ($isFutureMonth) {
    $currentMonth = date('Y-m');
}

$timestamp = strtotime($currentMonth . "-01");
$daysInMonth = date('t', $timestamp);
$monthName = date('F Y', $timestamp);
$currentDay = (date('Y-m') === $currentMonth) ? date('j') : $daysInMonth;

$dynamicData = generateEnergyData($daysInMonth, $currentDay);

echo "<script>
    const dynamicData = " . json_encode($dynamicData) . ";
    const dynamicMonthName = '" . $monthName . "';
    const dynamicCurrentMonth = '" . $currentMonth . "';
    const daysInDynamicMonth = " . $daysInMonth . ";
    const dynamicCurrentDay = " . $currentDay . ";
    const isDynamicCurrentMonth = (dynamicCurrentMonth === '" . date('Y-m') . "');
</script>";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Common styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        #app {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        header, footer {
            background-color: #0056b3;
            color: white;
            padding: 10px;
            text-align: center;
            width: 100%;
        }
        footer {
            font-size: 0.9rem;
        }
        .chart-section {
            margin: 20px 0;
            width: 90%;
            max-width: 800px;
        }
        .static-chart .bar-wrapper {
            display: flex;
            justify-content: space-between;
            overflow-x: auto;
        }
        .bar {
            width: 20px;
            background-color:rgb(221, 20, 30);
            margin: 0 5px;
            border-radius: 5px 5px 0 0;
            transition: transform 0.3s, background-color 0.3s;
        }
        .bar:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }
        .bar span {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            color: #333;
            font-size: 12px;
        }
        .bar-label {
            text-align: center;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div id="app">
        <header>
            <h1>Enegy generations</h1>
        </header>

        <!-- Static Chart Section -->
        <div class="chart-section static-chart">
            <h2>predict Energy</h2>
            <div class="bar-wrapper">
                <?php for ($i = 0; $i < 30; $i++) { ?>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div class="bar" style="height: <?php echo $staticBarHeights[$i] * 10; ?>px;">
                            <span><?php echo $staticBarHeights[$i]; ?>kWh</span>
                        </div>
                        <div class="bar-label"><?php echo date('d M', strtotime($staticDates[$i])); ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Dynamic Chart Section -->
        <div class="chart-section">
            <h2>Energy Generate</h2>
            <div id="controls">
                <button id="prevBtn">Previous Month</button>
                <span id="dynamicMonth"></span>
                <button id="nextBtn">Next Month</button>
            </div>
            <canvas id="dynamicChart"></canvas>
        </div>

        <footer>
            © 2025 Energy Management System. All Rights Reserved.
        </footer>
    </div>

    <script>
        // Dynamic chart setup
        document.getElementById('dynamicMonth').textContent = dynamicMonthName;

        const labels = Array.from({ length: daysInDynamicMonth }, (_, i) => `${i + 1}`);
        const ctx = document.getElementById('dynamicChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Energy (kWh)',
                    data: dynamicData,
                    backgroundColor: dynamicData.map((_, i) => i < dynamicCurrentDay ? 'rgba(0, 123, 255, 0.7)' : 'rgba(255, 0, 0, 0.5)'),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = context.raw;
                                return value === null ? `No data` : `Energy: ${value} kWh`;
                            }
                        }
                    }
                }
            }
        });

        // Month navigation
        document.getElementById('prevBtn').addEventListener('click', () => navigateMonth(-1));
        document.getElementById('nextBtn').addEventListener('click', () => navigateMonth(1));
        document.getElementById('nextBtn').disabled = isDynamicCurrentMonth;

        function navigateMonth(offset) {
            const [year, month] = dynamicCurrentMonth.split('-').map(Number);
            const newDate = new Date(year, month - 1 + offset, 1);
            const newMonth = `${newDate.getFullYear()}-${String(newDate.getMonth() + 1).padStart(2, '0')}`;
            window.location.href = `?month=${newMonth}`;
        }
    </script>
</body>

</html>
