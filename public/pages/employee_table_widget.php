<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Widget</title>
    <style>
        .table-container {
            max-height: 280px; /* Adjust height as needed */
            overflow-y: auto;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        table.table-widget {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Ensures consistent column widths */
            
            background-color: #f9f9f9;
        }
        
        .table-widget th, .table-widget td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            word-wrap: break-word; /* Allows text to break in smaller cells */
        }
        
        .table-widget th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        
        .table-widget tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .table-widget tr:hover {
            background-color: #f1f1f1;
        }
        
        .table-container::-webkit-scrollbar {
            width: 10px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 5px;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    

    <div class="table-container">
    <h2>New Employees</h2>
        <table class="table-widget">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Position</th>
                    <th>Start Date</th>
                </tr>
            </thead>
            <tbody class="employee-table">
                <!-- Data will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

    <script>
        // Fetch the data for the table from the backend
        async function fetchEmployeeData() {
            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/tables/employee-table');
                const data = await response.json();
                
                // Log the data to the console for debugging
                console.log(data);

                // Store the data in sessionStorage (stringified to ensure correct format)
                sessionStorage.setItem('employeeData', JSON.stringify(data.employee_names));
                sessionStorage.setItem('employeePositions', JSON.stringify(data.employee_positions));
                sessionStorage.setItem('employeeStartDates', JSON.stringify(data.employee_start_dates));

                renderEmployeeTable();
            } catch (error) {
                console.error('Error fetching employee data:', error);
            }
        }

        // Render the table with the fetched data
        function renderEmployeeTable(){
            // Get the data from sessionStorage and parse it into arrays
            const employeeData = JSON.parse(sessionStorage.getItem('employeeData'));
            const employeePositions = JSON.parse(sessionStorage.getItem('employeePositions'));
            const employeeStartDates = JSON.parse(sessionStorage.getItem('employeeStartDates'));

            // Get the table body element
            const tableEmployeeBody = document.querySelector('.table-widget .employee-table');
            
            // Clear any existing rows
            tableEmployeeBody.innerHTML = '';

            // Loop through the data and create a row for each entry
            for (let i = 0; i < employeeData.length; i++) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${employeeData[i]}</td>
                    <td>${employeePositions[i]}</td>
                    <td>${employeeStartDates[i]}</td>
                `;
                tableEmployeeBody.appendChild(row);
            }
            
        }

        // Fetch and render the employee data when the page loads
        fetchEmployeeData();
    </script>
</body>
</html>
