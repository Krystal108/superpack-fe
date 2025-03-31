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
    <h2>My Leaves</h2>
        <table class="table-widget">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="leave-table">
                <!-- Data will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

    <script>
        // Fetch the data for the table from the backend
        async function fetchLeaveData() {
            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/tables/leave-table', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username: '<?php echo $_SESSION['username']?>' })
                
                })
                const data = await response.json();

                // Log the data to the console for debugging
                console.log(data);

                // Store the data in sessionStorage (stringified to ensure correct format)
                sessionStorage.setItem('leave_type', JSON.stringify(data.leave_types));
                sessionStorage.setItem('start_date', JSON.stringify(data.start_dates));
                sessionStorage.setItem('end_date', JSON.stringify(data.end_dates));
                sessionStorage.setItem('status', JSON.stringify(data.statuses));

                renderLeaveTable();

            } catch (error) {
                console.error('Error fetching employee data:', error);
            }
        }

        function renderLeaveTable() {
            // Get the data from sessionStorage and parse it into arrays
            const leaveType = JSON.parse(sessionStorage.getItem('leave_type'));
            const startDate = JSON.parse(sessionStorage.getItem('start_date'));
            const endDate = JSON.parse(sessionStorage.getItem('end_date'));
            const status = JSON.parse(sessionStorage.getItem('status'));

            // Get the table body element
            const tableLeaveBody = document.querySelector('.table-widget .leave-table');

            // Clear the table body before inserting new data
            tableLeaveBody.innerHTML = '';

            // Insert the data into the table
            for (let i = 0; i < leaveType.length; i++) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${leaveType[i]}</td>
                    <td>${startDate[i]}</td>
                    <td>${endDate[i]}</td>
                    <td>${status[i]}</td>
                `;
                tableLeaveBody.appendChild(row);
            }
        }

        // Call the function to fetch and display the data
        fetchLeaveData();
    </script>
</body>
</html>
