
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pie Chart Example</title>
    <style>
        #pie-chart {
            width: 100%;
            height: 100%; /* Ensures the chart fills the height of its container */
            background-color: #f0f0f0;
        }
        .pie-container {
            width: 100%;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="pie-container">
        <h2>Pie Chart</h2>
        <div id="pie-chart"></div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            // fetch the data for the pie chart from the backend
            async function fetchPieData() {
                try {
                    const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/charts/pie-chart');
                    const data = await response.json();
                    
                    // Log data to the console for debugging
                    console.log('Pie Chart Data:', data);

                    // Store the data in sessionStorage
                    sessionStorage.setItem('tables', JSON.stringify(data.table_names));
                    sessionStorage.setItem('counts', JSON.stringify(data.row_counts));

                    renderPieChart();

                } catch (error) {
                    console.error('Error fetching pie chart data:', error);
                }
            }

            function renderPieChart() {
                // Get data from sessionStorage
                const tables = JSON.parse(sessionStorage.getItem('tables'));
                const counts = JSON.parse(sessionStorage.getItem('counts'));

                // Pie Chart Options
                var pieOptions = {
                    series: counts,
                    chart: {
                        type: 'donut',
                        width: '100%',
                        height: 300,
                    },
                    labels: tables.map(table => table.replace('tasks', '').replace('_', ' ').toUpperCase()),
                    legend: {
                        position: 'right'
                    }
                };

                // Render Pie Chart
                var pieChart = new ApexCharts(document.querySelector("#pie-chart"), pieOptions);
                pieChart.render();

            }

            fetchPieData();
        });
    </script>
</body>
</html>