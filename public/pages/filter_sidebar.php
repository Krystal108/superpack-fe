<?php
// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data from the POST request
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : '';
    $user_department = isset($_POST['user_department']) ? htmlspecialchars($_POST['user_department']) : '';

    // Store data in session if required for later use
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['user_department'] = $user_department;

    $_SESSION['loggedin'] = true;  // Set logged in state to true
}

// Check if the 'department' key exists in the URL query parameters
if (isset($_GET['user_department'])) {
    $department_name = $_GET['user_department'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    
</style>
<body>
    <!-- Sidebar -->
    <div class="filter-sidebar">
        <!-- Textbox for Assigned -->
        <div>
        <label for="filter-assigned">Assigned:</label>
        <input type="text" id="filter-assigned" placeholder="Search by Name">
        </div>

        <!-- Dropdown for Status -->
        <div>
        <label for="filter-status">Status:</label>
        <select id="filter-status">
            <option value="">Select Status</option>
            <option value="Not Started">Not Started</option>
            <option value="In Progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
        </div>

        <!-- Calendar Range for Due Date -->
        <div>
        <label for="filter-due-date">Duration:</label>
        <input type="date" id="filter-due-date-start" placeholder="Start Date">
        <input type="date" id="filter-start-date-end" placeholder="End Date">

        </div>
        <!-- Dropdown for Priority -->
        <div>
        <label for="filter-priority">Priority:</label>
        <select id="filter-priority">
            <option value="">Select Priority</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>
        </div>


        <div class="filter-sidebar-buttons">
        <button id="filter-apply-button" onclick="apply_filter()">Apply</button>
        <button id="filter-back-button" onclick="toggle_filter()">Back</button>
        </div>
    </div>
    <script>
        // Function to toggle the filter sidebar
        function toggle_filter() {
            const filterSidebar = document.querySelector('.filter-sidebar');
            filterSidebar.style.right = filterSidebar.style.right === '0px' ? '-300px' : '0px';
        }

        function apply_filter() {
            const filterAssigned = document.getElementById('filter-assigned').value;
            const filterStatus = document.getElementById('filter-status').value;
            const filterDueDateStart = document.getElementById('filter-due-date-start').value;
            const filterStartDateEnd = document.getElementById('filter-start-date-end').value;
            const filterPriority = document.getElementById('filter-priority').value;
            const department = '<?php echo $_SESSION['user_department']; ?>';
            
            console.log(department);

            // Send the filter to the endpoint
            fetch("https://6dvfd2bd-5000.asse.devtunnels.ms/task-filter", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    assigned: filterAssigned,
                    status: filterStatus,
                    due_date_start: filterDueDateStart,
                    due_date_end: filterStartDateEnd,
                    priority: filterPriority,
                    department: department,
                }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);

                // Update the table dynamically
                const tableBody = document.querySelector('.tasks-table');
                tableBody.innerHTML = ""; // Clear existing rows

                const taskCount = data.task_ids.length;

                // Iterate through the tasks and add new rows

                for (let i = 0; i < taskCount; i++) {
                    // Determine the completion class
                    let completionClass = "completion-bar low";
                    if (data.task_completions[i] > 50) completionClass = "completion-bar medium";
                    if (data.task_completions[i] > 80) completionClass = "completion-bar high";
                    // Determine the status class
                    let statusClass = "status-not-started";
                    if (data.task_status[i] === "In Progress") statusClass = "status-in-progress";
                    if (data.task_status[i] === "Completed") statusClass = "status-completed";
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td><input type="checkbox" name="task_checkbox[]" value="${data.task_ids[i]}"></td>
                        <td>${data.task_ids[i]}</td>
                        <td>${data.task_names[i]}</td>
                        <td>${data.task_owners[i]}</td>
                        <td><span class="status ${statusClass}">${data.task_status[i]}</span></td>
                        <td>${data.task_start_dates[i]}</td>
                        <td>${data.task_due_dates[i]}</td>
                        <td><div class="${completionClass}" style="width: ${data.task_completions[i]}%"></div> ${data.task_completions[i]}%</td>
                        <td>${data.task_priorities[i]}</td>
                        <td>${data.task_durations[i]}</td>
                    `;
                    tableBody.appendChild(row);
                }

            })
            .catch((error) => {
                console.error('Error:', error);
            });

            // Close the filter sidebar
            toggle_filter();
        }

    </script>
</body>