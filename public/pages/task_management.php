<?php
session_start();

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

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: /?page=login');
    exit();
}

// Include the database configuration file
if (isset($_SESSION['user_department'])) {
    $department = $_SESSION['user_department'];

    switch ($department) {
        case 'sales':
            // Use the sales_tasks table
            $tasksTable = 'sales_tasks';
            break;
        case 'purchasing':
            // Use the purchasing_tasks table
            $tasksTable = 'purchasing_tasks';
            break;
        case 'proddev':
            // Use the proddev_tasks table
            $tasksTable = 'proddev_tasks';
            break;
        case 'warehouse':
            // Use the warehouse_tasks table
            $tasksTable = 'warehouse_tasks';
            break;
        case 'logistics':
            // Use the logistics_tasks table
            $tasksTable = 'logistics_tasks';
            break;
        case 'accounting':
            // Use the accounting_tasks table
            $tasksTable = 'accounting_tasks';
            break;
        default:
            die("Invalid department specified.");
    }
} else {
    die("Department not specified.");
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboardnew.css">
    <style>
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'filter_sidebar.php'?>
    <?php include 'sidebar_small.php'?>
    <div class="container-everything" style="height:100%;">
        <div class="container-all">
            <div class="container-top">
                <?php include 'header_2.php';?>
            </div>
            <div class="container-search">
                <div class="search-bar">
                    <form method="GET" action="" class="form-inline">
                        <div class="input-group mb-3 flex-grow-1">
                            <!-- Search input and button -->
                            <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>"> 

                            <input type="text" class="form-control" name="search_id" placeholder="Search by ID" value="" style="border-radius: 10px 0 0 10px; border: 3px solid #131313; height:42px;">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="performSearch()" style="border-radius: 0; border: 3px solid #131313;">Search</button>
                            </div>
                        </div>
                        <!-- Add Task button aligned to the right -->
                        <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addTaskModal" style="border-radius: 0 10px 10px 0 ; border: 3px solid #131313;">Add Task</button>
                    </form>
                </div>
    
            </div>
            <div class="container-bottom">
                <div class="container-table">
                    <div class="tool-bar">
                        <div class="d-flex justify-content-between align-items-center mb-3" >
                            <div style="color: #FFFAFA;">
                                <span id="selected-count">0</span> items selected
                            </div>
                            
                            <div class="d-flex align-items-center" style="gap:10px;">
                                
                                <!-- Start the form for deletion -->
                                <form method="POST" id="deleteForm" style="display:inline;">
                                    <button type="button" name="deleteTask" class="btn btn-danger" disabled onclick="confirmDelete()">Del</button>
                                </form>
                                <button class="btn btn-primary" name="editTaskMod" data-toggle="modal" data-target="#editTaskModal" disabled data-id="<?php echo $task['employee_id']; ?>">Edit</button>
                                <!-- <button class="btn btn-secondary" onclick="window.print()">Print</button> -->
                                
                                <div>
                                    <form method="get" action="task_management.php">
                                        <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">
                                        <button type="submit" class="btn btn-success" onclick="exportToExcel()">Export to Excel</button>
                                    </form>
                                </div>
                                <button class="btn btn-info" onclick="window.location.href='/?page=task-management'">Reset</button>
                                <button class="btn btn-warning" onclick="toggle_filter()">Filter</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr> <!-- style="display: block; width: 100%; height: 100%; text-decoration: none; color: inherit;" -->
                                    <th class="checkbox-col"></th> <!-- Empty column for the checkbox -->
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=employee_id&dir=<?php echo ($sort === 'employee_id' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">ID</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=task&dir=<?php echo ($sort === 'task' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Task</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=owner&dir=<?php echo ($sort === 'owner' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Assigned</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=status&dir=<?php echo ($sort === 'status' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Status</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=start_date&dir=<?php echo ($sort === 'start_date' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Start Date</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=due_date&dir=<?php echo ($sort === 'due_date' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Due Date</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=completion&dir=<?php echo ($sort === 'completion' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Completion</a></th>
                                    <th><a class="sort-link" href="#" style="display: block; width: 100%; height: 100%; text-decoration: none; color: inherit;" style="text-decoration:none;">Priority</a></th>
                                    <th><a class="sort-link" href="?department=<?php echo urlencode($department); ?>&sort=duration&dir=<?php echo ($sort === 'duration' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">Duration</a></th>
                                </tr>
                            </thead>

                            <tbody class="tasks-table">
                                <!-- This will dynamically change -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTaskModalLabel">New Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="modal-all">
                                <div class="form-group">
                                    <label for="task">Task</label>
                                    <input type="text" class="form-control" id="task" name="task" required>
                                </div>
                                <div class="modal-group">
                                    <div class="modal-left">
                                        <div class="form-group">
                                            <label for="owner">Assigned To</label>
                                            <input type="text" class="form-control" id="owner" name="owner" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="start_date">Start</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="completion">Completion (%)</label>
                                            <input type="number" class="form-control" id="completion" name="completion" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="modal-right">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="Not Started">Not Started</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="due_date">End</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="priority">Priority</label>
                                            <select class="form-control" id="priority" name="priority" required>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                            </select>
                                        </div>
                                    </div> 
                                       
                                    <input type="hidden" class="form-control" id="duration" name="duration" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" name="addTask" class="btn btn-primary" onclick="newTask()">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="editTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" id="editTaskForm">
                        <div class="modal-body">
                            <div class="modal-all">
                                <div class="form-group">
                                    <label for="edit_task">Task</label>
                                    <input type="text" class="form-control" id="edit_task" name="task" required>
                                </div>
                                <div class="modal-group">
                                    <div class="modal-left">
                                        <div class="form-group">
                                            <label for="edit_owner">Assigned To</label>
                                            <input type="text" class="form-control" id="edit_owner" name="owner" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_start_date">Start</label>
                                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_completion">Completion (%)</label>
                                            <input type="number" class="form-control" id="edit_completion" name="completion" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="modal-right">
                                        <div class="form-group">
                                            <label for="edit_status">Status</label>
                                            <select class="form-control" id="edit_status" name="status" required>
                                                <option value="Not Started">Not Started</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_due_date">End</label>
                                            <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_priority">Priority</label>
                                            <select class="form-control" id="edit_priority" name="priority" required>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                            </select>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="task_id" name="task_id">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" name="editTask" class="btn btn-primary" onclick="saveTaskChanges()">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            async function loadTasks() {
                try {
                    const response = await fetch("https://6dvfd2bd-5000.asse.devtunnels.ms/load-tasks", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ department: '<?php echo $_SESSION['user_department']; ?>' })
                    });
                    const data = await response.json();
                    console.log(data);

                    // Store the data in sessionStorage
                    sessionStorage.setItem('task_ids', JSON.stringify(data.task_ids));
                    sessionStorage.setItem('task_names', JSON.stringify(data.task_names));
                    sessionStorage.setItem('task_owners', JSON.stringify(data.task_owners));
                    sessionStorage.setItem('task_status', JSON.stringify(data.task_status));
                    sessionStorage.setItem('task_start_dates', JSON.stringify(data.task_start_dates));
                    sessionStorage.setItem('task_due_dates', JSON.stringify(data.task_due_dates));
                    sessionStorage.setItem('task_completions', JSON.stringify(data.task_completions));
                    sessionStorage.setItem('task_priorities', JSON.stringify(data.task_priorities));
                    sessionStorage.setItem('task_durations', JSON.stringify(data.task_durations));

                    // Insert the data into the table
                    const tableBody = document.querySelector('.tasks-table');
                    const taskIds = JSON.parse(sessionStorage.getItem('task_ids'));
                    const taskNames = JSON.parse(sessionStorage.getItem('task_names'));
                    const taskOwners = JSON.parse(sessionStorage.getItem('task_owners'));
                    const taskStatuses = JSON.parse(sessionStorage.getItem('task_status'));
                    const taskStartDates = JSON.parse(sessionStorage.getItem('task_start_dates'));
                    const taskDueDates = JSON.parse(sessionStorage.getItem('task_due_dates'));
                    const taskCompletions = JSON.parse(sessionStorage.getItem('task_completions'));
                    const taskPriorities = JSON.parse(sessionStorage.getItem('task_priorities'));
                    const taskDurations = JSON.parse(sessionStorage.getItem('task_durations'));

                    // Clear the table body before inserting new data
                    tableBody.innerHTML = '';

                    // Loop through the data and create table rows
                    for (let i = 0; i < taskIds.length; i++) {
                        // Determine the completion class
                        let completionClass = "completion-bar low";
                        if (taskCompletions[i] > 50) completionClass = "completion-bar medium";
                        if (taskCompletions[i] > 80) completionClass = "completion-bar high";

                        // Determine the status class
                        let statusClass = "status-not-started";
                        if (taskStatuses[i] === "In Progress") statusClass = "status-in-progress";
                        if (taskStatuses[i] === "Completed") statusClass = "status-completed";

                        // Insert the data into the table
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><input type="checkbox" name="task_checkbox[]" value="${taskIds[i]}"></td>
                            <td>${taskIds[i]}</td>
                            <td>${taskNames[i]}</td>
                            <td>${taskOwners[i]}</td>
                            <td><span class="status ${statusClass}">${taskStatuses[i]}</span></td>
                            <td>${taskStartDates[i]}</td>
                            <td>${taskDueDates[i]}</td>
                            <td><div class="${completionClass}" style="width: ${taskCompletions[i]}%"></div> ${taskCompletions[i]}% </td>
                            <td>${taskPriorities[i]}</td>
                            <td>${taskDurations[i]}</td>
                        `;
                        tableBody.appendChild(row);
                    }

                    // Add event listeners to checkboxes and update button states
                    attachCheckboxEventListeners();
                    updateButtonsState();
                } catch (error) {
                    console.error("Error:", error);
                }
            }

            // Function to update delete and edit button states
            function updateButtonsState() {
                const selectedCheckboxes = document.querySelectorAll('input[name="task_checkbox[]"]:checked').length;
                const deleteButton = document.querySelector('button[name="deleteTask"]');
                const editButton = document.querySelector('button[name="editTaskMod"]');

                // Enable/disable buttons based on checkbox selection
                if (deleteButton) deleteButton.disabled = selectedCheckboxes === 0;
                if (editButton) editButton.disabled = selectedCheckboxes !== 1;

                // Update selected count (optional)
                document.getElementById('selected-count').textContent = selectedCheckboxes;
            }

            // Function to attach event listeners to checkboxes
            function attachCheckboxEventListeners() {
                const tableBody = document.querySelector('.tasks-table');
                tableBody.addEventListener('change', function (event) {
                    if (event.target && event.target.name === "task_checkbox[]") {
                        updateButtonsState();
                    }
                });
            }

            // Load tasks on page load
            loadTasks();

            // Function to confirm deletion
            async function confirmDelete() {
                const selectedCheckboxes = document.querySelectorAll('input[name="task_checkbox[]"]:checked');
                const taskIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
                const department = '<?php echo $_SESSION['user_department']; ?>';

                // Check if any tasks are selected
                if (taskIds.length === 0) {
                    alert("Please select at least one task to delete.");
                    return false; // Prevent further execution
                }

                // Confirm the delete action
                const confirmation = confirm("Are you sure you want to delete the selected tasks?");
                if (!confirmation) {
                    return false; // Prevent further execution if the user cancels
                }

                try {
                    // Make the delete request
                    const response = await fetch("https://6dvfd2bd-5000.asse.devtunnels.ms/delete-task", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ task_ids: taskIds, department: department })
                    });

                    const result = await response.json();

                    // Check the result of the delete operation
                    console.log(result);

                    // Check the result of the delete operation
                    if (result.success) {
                        alert("Tasks deleted successfully.");
                        // redirect to the same page to refresh the data
                        window.location.href="/?page=task-management";

                    } else {
                        alert("Failed to delete tasks. Please try again.");
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("An error occurred while deleting tasks. Please try again.");
                }

                return false; // Prevent form submission

            }

            // Function to add a new task
            function newTask() {
                const task = document.getElementById('task').value;
                const owner = document.getElementById('owner').value;
                const status = document.getElementById('status').value;
                const start_date = document.getElementById('start_date').value;
                const due_date = document.getElementById('due_date').value;
                const completion = document.getElementById('completion').value;
                const priority = document.getElementById('priority').value;
                const duration = document.getElementById('duration').value;
                const department = '<?php echo $_SESSION['user_department']; ?>';

                fetch("https://6dvfd2bd-5000.asse.devtunnels.ms/add-task", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ task: task, owner: owner, status: status, start_date: start_date, due_date: due_date, completion: completion, priority: priority, duration: duration, department: department })
                })
                .then(response => response.json())
                .then(result => {
                    console.log(result);
                    if (result.success) {
                        alert("Task added successfully.");

                        // redirect to the same page to refresh the data
                        window.location.href="/?page=task-management";

                    } else {
                        alert("Failed to add task. Please try again.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while adding the task. Please try again.");
                });
            }
            
            // Function to edit a task
            function saveTaskChanges() {
                // get the task id from checkbox value
                const task_id = document.querySelector('input[name="task_checkbox[]"]:checked').value;
                const task = document.getElementById('edit_task').value;
                const owner = document.getElementById('edit_owner').value;
                const status = document.getElementById('edit_status').value;
                const start_date = document.getElementById('edit_start_date').value;
                const due_date = document.getElementById('edit_due_date').value;
                const completion = document.getElementById('edit_completion').value;
                const priority = document.getElementById('edit_priority').value;
                const department = '<?php echo $_SESSION['user_department']; ?>';

                fetch("https://6dvfd2bd-5000.asse.devtunnels.ms/edit-task", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ task_id: task_id, task: task, owner: owner, status: status, start_date: start_date, due_date: due_date, completion: completion, priority: priority, department: department })
                })
                .then(response => response.json())
                .then(result => {
                    console.log(result);
                    if (result.success) {
                        alert("Task updated successfully.");

                        // redirect to the same page to refresh the data
                        window.location.href="/?page=task-management";

                    } else {
                        alert("Failed to update task. Please try again.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while updating the task. Please try again.");
                });
            }

            function performSearch() {
                const searchId = document.querySelector('input[name="search_id"]').value;
                const department = '<?php echo $_SESSION['user_department']; ?>';

                fetch("https://6dvfd2bd-5000.asse.devtunnels.ms/search-task", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ search_id: searchId, department: department })
                })
                .then(response => response.json())
                .then(result => {
                    console.log(result);

                    if (result.success) {
                        const taskCount = result.task_ids.length; // Count the number of tasks
                        alert(`Found ${taskCount} tasks successfully.`);
                        
                        // Update the table dynamically
                        const tableBody = document.querySelector('.tasks-table');
                        tableBody.innerHTML = ""; // Clear existing rows

                        // Iterate through the tasks and add new rows
                        for (let i = 0; i < taskCount; i++) {
                            // Determine the completion class
                            let completionClass = "completion-bar low";
                            if (result.task_completions[i] > 50) completionClass = "completion-bar medium";
                            if (result.task_completions[i] > 80) completionClass = "completion-bar high";

                            // Determine the status class
                            let statusClass = "status-not-started";
                            if (result.task_status[i] === "In Progress") statusClass = "status-in-progress";
                            if (result.task_status[i] === "Completed") statusClass = "status-completed";

                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td><input type="checkbox" name="task_checkbox[]" value="${result.task_ids[i]}"></td>
                                <td>${result.task_ids[i]}</td>
                                <td>${result.task_names[i]}</td>
                                <td>${result.task_owners[i]}</td>
                                <td><span class="status ${statusClass}">${result.task_status[i]}</span></td>
                                <td>${result.task_start_dates[i]}</td>
                                <td>${result.task_due_dates[i]}</td>
                                <td><div class="${completionClass}" style="width: ${result.task_completions[i]}%"></div> ${result.task_completions[i]}%</td>
                                <td>${result.task_priorities[i]}</td>
                                <td>${result.task_durations[i]}</td>
                            `;
                            tableBody.appendChild(row);
                        }
                    } else {
                        alert("No tasks found or invalid response. Please try again.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while finding the task. Please try again.");
                });
            }

            // Export to .xls file
            function exportToExcel() {
                // get the table rows
                const rows = document.querySelectorAll('.tasks-table tr');

                // initialize CSV content
                let csvContent = '';

                // 

                // add headers
                const headers = ['ID', 'Task', 'Assigned', 'Status', 'Start Date', 'Due Date', 'Completion', 'Priority', 'Duration'];
                csvContent += headers.join(',') + '\n';

                // iterate through each. row
                rows.forEach(row => {
                    const cols = row.querySelectorAll('td');
                    const rowData = Array.from(cols).map(col => col.innerText).join(',');
                    csvContent += rowData + '\n';
                });

                // create a download link
                const downloadLink = document.createElement("a");

                // set the download link attributes
                downloadLink.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);
                downloadLink.download = 'tasks.csv';

                // trigger the download
                downloadLink.click();
            }

        </script>

</body>
</html>
