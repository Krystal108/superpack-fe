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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Employee Evaluations</title>
        <link rel="stylesheet" href="style_index.css">
        <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="dashboardnew.css">
        <style>

        .carousel-item {
            height: auto;
            padding: 20px;
        }
        .carousel-item img {
            object-fit: cover;
            width: 100%;
        }
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            font-size: 24px;
            margin: 10px 0;
        }
        .rating input {
            display: none;
        }
        .rating label {
            color: #ddd;
            cursor: pointer;
        }
        .rating input:checked ~ label {
            color: gold;
        }
        .form-content {
            max-height: 500px;
            overflow-y: auto;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
        }
        .form-group textarea {
            height: 150px;
        }
        .form-group input, .form-group select, .form-group textarea {
            font-size: 16px;
            padding: 10px;
        }
        .carousel-control-prev-icon, .carousel-control-next-icon {
            background-color: rgba(0,0,0,0.5);
            border-radius: 50%;
        }
        .print-btn {
            margin-bottom: 20px;
        }
        </style>
    </head>

    <body>
        <?php include 'sidebar_small.php'; ?>
        <?php include 'eval_sidebar.php'; ?>
        <div class="container-everything" style="height:100%;">
            <div class="container-all">
                <div class="container-top">
                    <?php include 'header_2.php'; ?>
                </div>
                <div class="container-search">
                    <div class="search-bar">
                        <form method="GET" action="" class="form-inline">
                            <div class="input-group mb-3 flex-grow-1">
                                <!-- Search input and button -->
                                <input type="hidden" name="department" value="">
                                <input type="text" class="form-control" name="search_id" placeholder="Search by ID" value=""style="border-radius: 10px 0 0 10px; border: 3px solid #131313; height:42px;">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" style="border-radius: 0; border: 3px solid #131313;" onclick="searchEmployeeEvaluation()">Search</button>
                                </div>
                            </div>
                            <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addEvaluationModal" style="border-radius: 0 10px 10px 0; border: 3px solid #131313;">Add Evaluation</button>
                        </form>
                    </div>
                    
                    </div>
                    <div class="container-bottom">
                        <div class="container-table">
                                <div class="tool-bar">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div style="color: #FFFAFA;">
                                        <span id="selected-count">0</span> items selected
                                    </div>
                                    
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        
                                        <!-- Start the form for deletion -->
                                        <form method="POST" id="deleteForm" style="display:inline;">
                                            <button type="submit" name="deleteTask" class="btn btn-danger" disabled>Del</button>
                                        </form>
                                        <button class="btn btn-primary" name="editTaskMod" data-toggle="modal" data-target="#editEvaluationModal" disabled data-id="<?php echo $task['id']; ?>">Edit</button>
                                        <!-- <button class="btn btn-secondary" onclick="window.print()">Print</button> -->
                                        
                                        <div>
                                            <form method="get" action="task_management.php">
                                                <input type="hidden" name="department" value="">
                                                <input type="hidden" name="export" value="excel">
                                                <button type="submit" class="btn btn-success">Export to Excel</button>
                                            </form>
                                        </div>
                                        <button class="btn btn-info" onclick="window.location.href='worker_eval.php'">Reset</button>
                                        <button class="btn btn-warning" type="button" onclick="toggle_filter()">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-container">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr> <!-- style="display: block; width: 100%; height: 100%; text-decoration: none; color: inherit;" -->
                                            <th class="checkbox-col"></th> <!-- Empty column for the checkbox -->
                                            
                                            <!-- Sorting by ID -->
                                            <th>
                                                <a class="sort-link" href="?sort=id&dir=<?php echo ($sort === 'id' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">
                                                    ID
                                                </a>
                                            </th>
                                            
                                            <!-- Sorting by Employee ID -->
                                            <th>
                                                <a class="sort-link" href="?sort=employee_id&dir=<?php echo ($sort === 'employee_id' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">
                                                    Employee ID
                                                </a>
                                            </th>
                                            
                                            <!-- Sorting by Name -->
                                            <th>
                                                <a class="sort-link" href="?sort=name&dir=<?php echo ($sort === 'name' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">
                                                    Name
                                                </a>
                                            </th>
                                            
                                            <!-- Sorting by Position -->
                                            <th>
                                                <a class="sort-link" href="?sort=position&dir=<?php echo ($sort === 'position' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">
                                                    Position
                                                </a>
                                            </th>
                                            
                                            <!-- Sorting by Department -->
                                            <th>
                                                <a class="sort-link" href="?sort=department&dir=<?php echo ($sort === 'department' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">
                                                    Department
                                                </a>
                                            </th>
                                            
                                            <!-- Sorting by Start Date -->
                                            <th>
                                                <a class="sort-link" href="?sort=start_date&dir=<?php echo ($sort === 'start_date' && $direction === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration:none;">
                                                    Start Date
                                                </a>
                                            </th>
                                            
                                            <!-- Comments (No sorting) -->
                                            <th>Comments</th>
                                            
                                            <!-- Performance (No sorting) -->
                                            <th>Performance</th> 
                                        </tr>

                                    </thead>

                                    <tbody class="evaluation-table">
                                        
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Evaluation Modal -->
        <div class="modal fade" id="addEvaluationModal" tabindex="-1" role="dialog" aria-labelledby="addEvaluationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEvaluationModalLabel">Add Evaluation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body form-content">
                            <div id="evaluationCarousel" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <!-- Slide 1: Worker Information -->
                                    <div class="carousel-item active">
                                        <div class="form-group">
                                            <label for="employee_id">Employee ID</label>
                                            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="position">Position</label>
                                            <input type="text" class="form-control" id="position" name="position" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="department">Department</label>
                                            <select class="form-control" id="department" name="department" required>
                                                <option value="">Select Department</option>
                                                <option value="Sales">Sales</option>
                                                <option value="Purchasing">Purchasing</option>
                                                <option value="Purchase Development">Purchase Development</option>
                                                <option value="Warehouse">Warehouse</option>
                                                <option value="Logistics">Logistics</option>
                                                <option value="Accounting">Accounting</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                    </div>

                                    <!-- Slide 2: Performance Evaluation -->
                                    <div class="carousel-item">
                                        <?php
                                        $questions = [
                                            "Quality of Work",
                                            "Punctuality",
                                            "Team Collaboration",
                                            "Problem Solving",
                                            "Communication Skills",
                                            "Leadership Skills",
                                            "Technical Skills",
                                            "Adaptability",
                                            "Creativity",
                                            "Overall Performance"
                                        ];
                                        shuffle($questions);
                                        foreach ($questions as $index => $question): ?>
                                        <div class="form-group">
                                            <label for="criteria_<?php echo $index + 1; ?>"><?php echo $question; ?></label>
                                            <div class="rating">
                                                <?php for ($j = 5; $j >= 1; $j--): ?>
                                                <input type="radio" id="criteria_<?php echo $index + 1; ?>_<?php echo $j; ?>" name="criteria_<?php echo $index + 1; ?>" value="<?php echo $j; ?>" required>
                                                <label for="criteria_<?php echo $index + 1; ?>_<?php echo $j; ?>">&#9733;</label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="form-group">
                                            <label for="comments">Additional Comments</label>
                                            <textarea class="form-control" id="comments" name="comments"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#evaluationCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#evaluationCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="addEvaluation" class="btn btn-primary">Save Evaluation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
         <!-- Edit Evaluation Modal -->
        <div class="modal fade" id="editEvaluationModal" tabindex="-1" role="dialog" aria-labelledby="editEvaluationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEvaluationModalLabel">Edit Evaluation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body form-content">
                            <div id="editEvaluationCarousel" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <!-- Slide 1: Worker Information -->
                                    <div class="carousel-item active">
                                        <input type="hidden" id="edit_id" name="id">
                                        <div class="form-group">
                                            <label for="edit_employee_id">Employee ID</label>
                                            <input type="text" class="form-control" id="edit_employee_id" name="employee_id" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_name">Name</label>
                                            <input type="text" class="form-control" id="edit_name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_position">Position</label>
                                            <input type="text" class="form-control" id="edit_position" name="position" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_department">Department</label>
                                            <select class="form-control" id="edit_department" name="department" required>
                                                <option value="">Select Department</option>
                                                <option value="Sales">Sales</option>
                                                <option value="Purchasing">Purchasing</option>
                                                <option value="Purchase Development">Purchase Development</option>
                                                <option value="Warehouse">Warehouse</option>
                                                <option value="Logistics">Logistics</option>
                                                <option value="Accounting">Accounting</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_start_date">Start Date</label>
                                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                                        </div>
                                    </div>

                                    <!-- Slide 2: Performance Evaluation -->
                                    <div class="carousel-item">
                                        <?php
                                        $questions = [
                                            "Quality of Work",
                                            "Punctuality",
                                            "Team Collaboration",
                                            "Problem Solving",
                                            "Communication Skills",
                                            "Leadership Skills",
                                            "Technical Skills",
                                            "Adaptability",
                                            "Creativity",
                                            "Overall Performance"
                                        ];
                                        shuffle($questions);
                                        foreach ($questions as $index => $question): ?>
                                        <div class="form-group">
                                            <label for="edit_criteria_<?php echo $index + 1; ?>"><?php echo $question; ?></label>
                                            <div class="rating">
                                                <?php for ($j = 5; $j >= 1; $j--): ?>
                                                <input type="radio" id="edit_criteria_<?php echo $index + 1; ?>_<?php echo $j; ?>" name="criteria_<?php echo $index + 1; ?>" value="<?php echo $j; ?>" required>
                                                <label for="edit_criteria_<?php echo $index + 1; ?>_<?php echo $j; ?>">&#9733;</label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="form-group">
                                            <label for="edit_comments">Additional Comments</label>
                                            <textarea class="form-control" id="edit_comments" name="comments"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#editEvaluationCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#editEvaluationCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="editEvaluation" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>

            // Change logo name 
            const logoName = document.querySelector('.logo_name');
            logoName.textContent = 'Employee Evaluation';

            function printTable() {
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print Evaluation</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body >');
            printWindow.document.write(document.querySelector('table').outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            }

           
            // Get the checkbox and delete button
            var checkbox = document.querySelector('input[name="task_checkbox[]"]');
            var deleteButton = document.querySelector('button[name="deleteTask"]');

            function updateSelectedCount(checkbox) {
                var selectedCount = $('input[name="task_checkbox[]"]:checked').length;
                $('#selected-count').text(selectedCount);

                // Enable or disable the delete button
                if (selectedCount > 0) {
                    $('button[name="deleteTask"]').prop('disabled', false);
                } else {
                    $('button[name="deleteTask"]').prop('disabled', true);
                }

                // Enable or disable the edit button
                if (selectedCount === 1) {
                    $('button[name="editTaskMod"]').prop('disabled', false);
                } else {
                    $('button[name="editTaskMod"]').prop('disabled', true);
                }

                // if there are more than one checkbox checked, disable the edit button
                if (selectedCount > 1) {
                    $('button[name="editTaskMod"]').prop('disabled', true);
                }
            }


            function toggle_filter() {
                var sidebar = document.querySelector('.filter-sidebar');
                if (sidebar.style.right === '-300px') {
                    sidebar.style.right = '0';
                } else {
                    sidebar.style.right = '-300px';
                }
            }

            // search emplyee evaluation
            async function searchEmployeeEvaluation() {
                const searchId = document.querySelector('input[name="search_id"]').value;

                const response = await fetch(`https://6dvfd2bd-5000.asse.devtunnels.ms/search-evaluation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        search_id: searchId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Display the data in the table
                    const tableBody = document.querySelector('table tbody');
                    tableBody.innerHTML = '';

                    const evalCount = data.id.length;

                    if (evalCount > 0) {
                        for( let i = 0; i < evalCount; i++) {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><input type="checkbox" name="task_checkbox[]" value="${data.id[i]}" onclick="updateSelectedCount(this)"></td>
                                <td>${data.id[i]}</td>
                                <td>${data.employee_id[i]}</td>
                                <td>${data.name[i]}</td>
                                <td>${data.position[i]}</td>
                                <td>${data.department[i]}</td>
                                <td>${data.start_date[i]}</td>
                                <td>${data.comments[i]}</td>
                                <td>${data.performance[i]}</td>
                            `;

                            tableBody.appendChild(row);
                        }


                    }
                }
                
            }

            // load evaluation data
            async function loadEvaluationData() {
                const response = await fetch(`https://6dvfd2bd-5000.asse.devtunnels.ms/load-evaluation`,
                {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {

                    // get the evaluation table
                    const tableBody = document.querySelector('.evaluation-table');
                    tableBody.innerHTML = '';

                    const evalCount = data.id.length;

                    if (evalCount > 0) {
                        for (let i = 0; i < evalCount; i++) {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><input type="checkbox" name="task_checkbox[]" value="${data.id[i]}" onclick="updateSelectedCount(this)"></td>
                                <td>${data.id[i]}</td>
                                <td>${data.employee_id[i]}</td>
                                <td>${data.name[i]}</td>
                                <td>${data.position[i]}</td>
                                <td>${data.department[i]}</td>
                                <td>${data.start_date[i]}</td>
                                <td>${data.comments[i]}</td>
                                <td>${data.performance[i]}</td>
                            `;

                            tableBody.appendChild(row);
                        }
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td colspan="9" style="text-align: center;">No evaluations found</td>
                        `;

                        tableBody.appendChild(row);
                    }
                
                }
            }

            loadEvaluationData();

            
            // Call toggleDeleteButton when checkbox state changes
            checkbox.addEventListener('change', toggleDeleteButton);
        

        </script>
    </body>
</html>