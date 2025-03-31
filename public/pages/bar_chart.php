<?php

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data from the POST request
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : '';
    $user_department = isset($_POST['user_department']) ? htmlspecialchars($_POST['user_department']) : '';
    $loggedin = isset($_POST['loggedin']) ? htmlspecialchars($_POST['loggedin']) : false;

    // Store data in session if required for later use
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['user_department'] = $user_department;
    $_SESSION['loggedin'] = $loggedin;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Chart</title>
    <style>
        #bar-chart {
            width: 100%;
            height: 100%; /* Ensures the chart fills the height of its container */
        }
        .bar-chart .bar {
            animation: grow 1s ease-out;
        }

        /* Custom animation for hover effect */
        .bar-chart .bar:hover {
            animation: none;
            background-color: #28a745;
        }
        .bar-container {
            margin: 0 auto;
            text-align: left;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="bar-container">
        <h2>My Attendance Record</h2>
        <div id="bar-chart"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            async function fetchBarData() {
                try {
                    const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/charts/bar-chart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name: '<?php echo $_SESSION['username'] ?>' })
                    });
                    const data = await response.json();

                    console.log('Bar Chart Data:', data);

                    sessionStorage.setItem('on_time_data', JSON.stringify(data.on_time_data));
                    sessionStorage.setItem('late_data', JSON.stringify(data.late_data));
                    sessionStorage.setItem('absent_data', JSON.stringify(data.absent_data));
                    sessionStorage.setItem('categories', JSON.stringify(data.categories));

                    renderBarChart();
                } catch (error) {
                    console.error('Error fetching bar chart data:', error);
                }
            }

            function renderBarChart() {
                const onTimeData = JSON.parse(sessionStorage.getItem('on_time_data'));
                const lateData = JSON.parse(sessionStorage.getItem('late_data'));
                const absentData = JSON.parse(sessionStorage.getItem('absent_data'));
                const categories = JSON.parse(sessionStorage.getItem('categories'));

                const parsedOnTimeData = onTimeData.map(Number);
                const parsedLateData = lateData.map(Number);
                const parsedAbsentData = absentData.map(Number);

                const barOptions = {
                    chart: {
                        type: 'bar',
                        height: 185,
                        stacked: true,
                        width: '100%', // Set the width to 100% for dynamic sizing
                        toolbar: {
                            show: false, // Hide the toolbar if not needed
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true
                        }
                    },
                    series: [
                        { name: 'On Time', data: parsedOnTimeData },
                        { name: 'Late', data: parsedLateData },
                        { name: 'Absent', data: parsedAbsentData }
                    ],
                    xaxis: {
                        categories: categories
                    },
                    tooltip: {
                        y: {
                            formatter: val => `${val} days`
                        }
                    },
                    responsive: [
                        {
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 300,
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false, // Switch to vertical on small screens
                                    },
                                },
                            },
                        },
                    ],
                };

                const barChart = new ApexCharts(document.querySelector('#bar-chart'), barOptions);
                barChart.render();

                // Resize the chart on window resize
                window.addEventListener('resize', () => barChart.updateOptions(barOptions));
            }

            fetchBarData();
        });

    </script>

</body>
</html>

