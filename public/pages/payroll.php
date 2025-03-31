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
    <title>Payroll</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboardnew.css">
    <style>
    </style>

    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
</head>
<body>
    <?php include 'sidebar_small.php'; ?>
    <div class="container-everything" style="height:100%;">
            <div class="container-all">
                <div class="container-top">
                    <?php include 'header_2.php'; ?>
                </div>
                <div class="container-search">
                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>OT Pay</th>
                                    <th>Late deduct</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="additional-pay-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div style="border-top:5px solid #131313; width:100%; height:1px;"></div>
                <div class="container-search"  style="height:100%;">
                    <div class="search-bar">
                        <form method="GET" action="" class="form-inline">
                            <div class="input-group mb-3 flex-grow-1">
                                <!-- Search input and button -->
                                <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">
                                <input type="text" class="form-control" name="search_id" placeholder="Search by ID" value=""style="border-radius: 10px 0 0 10px; border: 3px solid #131313; height:42px;">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" style="border-radius: 0; border: 3px solid #131313;" onclick="searchPayroll()">Search</button>
                                </div>
                            </div>
                            <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addPayrollModal" style="border-radius: 0 10px 10px 0; border: 3px solid #131313;">Add Record</button>
                        </form>
                    </div>
                    <div class="tool-bar">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div style="color: #FFFAFA;">
                                <span id="selected-count">0</span> items selected
                            </div>
                            
                            <div class="d-flex align-items-center" style="gap:10px;">
                                
                                <!-- Start the form for deletion -->
                                <form method="POST" id="deleteForm" style="display:inline;">
                                    <button type="button" name="deleteTask" onclick="deletePayroll()" class="btn btn-danger" disabled>Del</button>
                                </form>
                                <button class="btn btn-primary" name="editTaskMod" data-toggle="modal" data-target="#editTaskModal" disabled data-id="<?php echo $task['id']; ?>">Edit</button>
                                
                                <button class="btn btn-info" onclick="window.location.href='/?pages=payroll'">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="checkbox-col"></th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Salary</th>
                                    <th>Daily Rate</th>
                                    <th>Basic Pay</th>
                                    <th>Overtime Pay</th>
                                    <th>Late deduct</th>
                                    <th>Gross Pay</th>
                                    <th>SSS deduct</th>
                                    <th>Pag-IBIG deduct</th>
                                    <th>PhilHealth deduct</th>
                                    <th>Total deduct</th>
                                    <th>Net Salary</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>

                            <tbody class="payroll-table">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
    </div>
    <!-- Additional Pay Modal -->
    <div class="modal fade" id="addPayModal" tabindex="-1" role="dialog" aria-labelledby="addPayModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPayModalLabel">Set New Values</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ot_pay">OT Pay</label>
                            <input type="number" class="form-control" id="ot_pay" name="ot_pay" required>
                        </div>
                        <div class="form-group">
                            <label for="late_deduct">Late deduct</label>
                            <input type="number" class="form-control" id="late_deduct" name="late_deduct" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="addPay" onclick="newAdditionalPay()" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Pay Modal -->
    <div class="modal fade" id="editPayModal" tabindex="-1" role="dialog" aria-labelledby="editPayModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPayModalLabel">Edit Additional Pay Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ot_pay">OT Pay</label>
                            <input type="number" class="form-control" id="ot_pay_edit" name="ot_pay" required>
                        </div>
                        <div class="form-group ">
                            <label for="late_deduct">Late deduct</label>
                            <input type="number" class="form-control" id="late_deduct_edit" name="late_deduct" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="editPay" onclick="editAdditionalPay()" class="btn btn-primary">Edit Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Payroll Records Modal -->
    <div class="modal fade" id="addPayrollModal" tabindex="-1" role="dialog" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPayrollModalLabel">New Payroll Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body form-content">
                        <div id="payrollCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <!-- Slide 1: Basic Information -->
                                <div class="carousel-item active">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control" id="position" name="position" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="salary">Salary</label>
                                        <input type="number" class="form-control" id="salary" name="salary" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="daily_rate">Daily Rate</label>
                                        <input type="number" class="form-control" id="daily_rate" name="daily_rate" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="basic_pay">Basic Pay</label>
                                        <input type="number" class="form-control" id="basic_pay" name="basic_pay" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ot_pay">Overtime Pay</label>
                                        <input type="number" class="form-control" id="ot_pay" name="ot_pay" required>
                                    </div>
                                </div>

                                <!-- Slide 2: Payroll Details -->
                                <div class="carousel-item">
                                    
                                    <div class="form-group">
                                        <label for="late_deduct">Late deduct</label>
                                        <input type="number" class="form-control" id="late_deduct" name="late_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gross_pay">Gross Pay</label>
                                        <input type="number" class="form-control" id="gross_pay" name="gross_pay" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="sss_deduct">SSS deduct</label>
                                        <input type="number" class="form-control" id="sss_deduct" name="sss_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="pagibig_deduct">Pag-IBIG deduct</label>
                                        <input type="number" class="form-control" id="pagibig_deduct" name="pagibig_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="philhealth_deduct">PhilHealth deduct</label>
                                        <input type="number" class="form-control" id="philhealth_deduct" name="philhealth_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="net_salary">Net Salary</label>
                                        <input type="number" class="form-control" id="net_salary" name="net_salary" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Carousel Controls -->
                            <a class="carousel-control-prev" href="#payrollCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#payrollCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="addPayroll" class="btn btn-primary" onclick="newPayroll()">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Payroll Records Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Payroll Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body form-content">
                        <div id="editPayrollCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <!-- Slide 1: Basic Information -->
                                <div class="carousel-item active">
                                    <input type="hidden" id="edit_id" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <div class="form-group">
                                        <label for="edit_name">Name</label>
                                        <input type="text" class="form-control" id="edit_name" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_position">Position</label>
                                        <input type="text" class="form-control" id="edit_position" name="position" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_salary">Salary</label>
                                        <input type="number" class="form-control" id="edit_salary" name="salary" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_daily_rate">Daily Rate</label>
                                        <input type="number" class="form-control" id="edit_daily_rate" name="daily_rate" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_basic_pay">Basic Pay</label>
                                        <input type="number" class="form-control" id="edit_basic_pay" name="basic_pay" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_ot_pay">Overtime Pay</label>
                                        <input type="number" class="form-control" id="edit_ot_pay" name="ot_pay" required>
                                    </div>
                                </div>

                                <!-- Slide 2: Payroll Details -->
                                <div class="carousel-item">
                                    <div class="form-group">
                                        <label for="edit_late_deduct">Late deduct</label>
                                        <input type="number" class="form-control" id="edit_late_deduct" name="late_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_gross_pay">Gross Pay</label>
                                        <input type="number" class="form-control" id="edit_gross_pay" name="gross_pay" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_sss_deduct">SSS deduct</label>
                                        <input type="number" class="form-control" id="edit_sss_deduct" name="sss_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_pagibig_deduct">Pag-IBIG deduct</label>
                                        <input type="number" class="form-control" id="edit_pagibig_deduct" name="pagibig_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_philhealth_deduct">PhilHealth deduct</label>
                                        <input type="number" class="form-control" id="edit_philhealth_deduct" name="philhealth_deduct" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_net_salary">Net Salary</label>
                                        <input type="number" class="form-control" id="edit_net_salary" name="net_salary" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Carousel Controls -->
                            <a class="carousel-control-prev" href="#editPayrollCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#editPayrollCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="editPayroll" class="btn btn-primary" onclick="editPayrollRecords()">Edit Changes</button>
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
        logoName.textContent = 'Payroll';

        function updateSelectedCount() {
            var selectedCount = document.querySelectorAll('input[name="task_checkbox[]"]:checked').length;
            document.getElementById('selected-count').textContent = selectedCount;

            // Toggle buttons based on the number of selected checkboxes
            toggleButtons(selectedCount);
        }

        // Function to toggle the delete and edit buttons
        function toggleButtons(selectedCount) {
            // Get the delete and edit buttons
            var deleteButton = document.querySelector('button[name="deleteTask"]');
            var editButton = document.querySelector('button[name="editTaskMod"]');

            // Enable delete button if at least one checkbox is selected
            deleteButton.disabled = selectedCount === 0;

            // Enable edit button only if exactly one checkbox is selected
            editButton.disabled = selectedCount !== 1;
        }

        // Attach event listeners to all checkboxes
        document.querySelectorAll('input[name="task_checkbox[]"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
            });
        });

        // load additional pay records
        async function loadAdditionalPay() {
            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/load-additional-pay', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    const tbody = document.querySelector('.additional-pay-tbody');
                    tbody.innerHTML = '';

                    const additionalPayCount = data.additional_id.length;

                    if (additionalPayCount > 0) {
                        for (let i = 0; i < additionalPayCount; i++) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${data.ot_pay[i]}</td>
                                <td>${data.late_deduct[i]}</td>
                                <td>${data.date_created[i]}</td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#editPayModal" data-id="${data.additional_id[i]}">Edit</button>
                                </td>
                            `;

                            tbody.appendChild(tr);
                        }
                    } else {
                        // If there are no payroll records
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td colspan="4" class="text-center">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addPayModal">Set a Value</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    }
                } 

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Load payroll records
        loadAdditionalPay();

        // Add new additional pay record
        async function newAdditionalPay() {
            const ot_pay = document.getElementById('ot_pay').value;
            const late_deduct = document.getElementById('late_deduct').value;

            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/add-additional-pay', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ot_pay: ot_pay,
                        late_deduct: late_deduct
                    })
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    loadAdditionalPay();
                }

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Edit additional pay record
        async function editAdditionalPay(additionalId) {
            const ot_pay = document.getElementById('ot_pay_edit').value;
            const late_deduct = document.getElementById('late_deduct_edit').value;


            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/edit-additional-pay', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ot_pay: ot_pay,
                        late_deduct: late_deduct,
                    })
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    loadAdditionalPay();
                    loadPayroll();

                }

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Load payroll records
        async function loadPayroll() {
            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/load-payroll', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    const tbody = document.querySelector('.payroll-table');
                    tbody.innerHTML = '';

                    const payrollCount = data.payroll_id.length;

                    if (payrollCount > 0) {
                        for (let i = 0; i < payrollCount; i++) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="checkbox" name="task_checkbox[]" value="${data.payroll_id[i]}"></td>
                                <td>${data.name[i]}</td>
                                <td>${data.position[i]}</td>
                                <td>${data.salary[i]}</td>
                                <td>${data.daily_rate[i]}</td>
                                <td>${data.basic_pay[i]}</td>
                                <td>${data.ot_pay[i]}</td>
                                <td>${data.late_deduct[i]}</td>
                                <td>${data.gross_pay[i]}</td>
                                <td>${data.sss_deduct[i]}</td>
                                <td>${data.pagibig_deduct[i]}</td>
                                <td>${data.philhealth_deduct[i]}</td>
                                <td>${data.total_deduct[i]}</td>
                                <td>${data.net_salary[i]}</td>
                                <td>${data.date_created[i]}</td>
                            `;

                            tbody.appendChild(tr);
                        }
                    } else {
                        // If there are no payroll records
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td colspan="14" class="text-center">
                                No payroll records found
                            </td>
                        `;
                        tbody.appendChild(tr);
                    }

                    // Update the selected count
                    updateSelectedCount();

                    // Attach event listeners to all checkboxes
                    document.querySelectorAll('input[name="task_checkbox[]"]').forEach(function(checkbox) {
                        checkbox.addEventListener('change', function() {
                            updateSelectedCount();
                        });

                    });
                } 

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Load payroll records
        loadPayroll();

        // search payroll records
        async function searchPayroll() {
            const searchId = document.querySelector('input[name="search_id"]').value;

            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/search-payroll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        search_id: searchId
                    })
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    const tbody = document.querySelector('.payroll-table');
                    tbody.innerHTML = '';

                    const payrollCount = data.payroll_id.length;

                    if (payrollCount > 0) {
                        for (let i = 0; i < payrollCount; i++) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="checkbox" name="task_checkbox[]" value="${data.payroll_id[i]}"></td>
                                <td>${data.name[i]}</td>
                                <td>${data.position[i]}</td>
                                <td>${data.salary[i]}</td>
                                <td>${data.daily_rate[i]}</td>
                                <td>${data.basic_pay[i]}</td>
                                <td>${data.ot_pay[i]}</td>
                                <td>${data.late_deduct[i]}</td>
                                <td>${data.gross_pay[i]}</td>
                                <td>${data.sss_deduct[i]}</td>
                                <td>${data.pagibig_deduct[i]}</td>
                                <td>${data.philhealth_deduct[i]}</td>
                                <td>${data.total_deduct[i]}</td>
                                <td>${data.net_salary[i]}</td>
                                <td>${data.date_created[i]}</td>
                            `;

                            tbody.appendChild(tr);
                        }
                    } else {
                        // If there are no payroll records
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td colspan="14" class="text-center">
                                No payroll records found
                            </td>
                        `;
                        tbody.appendChild(tr);
                    }
                } 

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // delete payroll records
        async function deletePayroll() {
            const selectedIds = Array.from(document.querySelectorAll('input[name="task_checkbox[]"]:checked')).map(e => e.value);

            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/delete-payroll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        selected_ids: selectedIds
                    })
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    loadPayroll();
                }

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // add payroll records
        async function newPayroll() {
            const name = document.getElementById('name').value;
            const position = document.getElementById('position').value;
            const salary = document.getElementById('salary').value;
            const daily_rate = document.getElementById('daily_rate').value;
            const basic_pay = document.getElementById('basic_pay').value;
            const ot_pay = document.getElementById('ot_pay').value;
            const late_deduct = document.getElementById('late_deduct').value;
            const gross_pay = document.getElementById('gross_pay').value;
            const sss_deduct = document.getElementById('sss_deduct').value;
            const pagibig_deduct = document.getElementById('pagibig_deduct').value;
            const philhealth_deduct = document.getElementById('philhealth_deduct').value;
            const net_salary = document.getElementById('net_salary').value;

            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/add-payroll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        position: position,
                        salary: salary,
                        daily_rate: daily_rate,
                        basic_pay: basic_pay,
                        ot_pay: ot_pay,
                        late_deduct: late_deduct,
                        gross_pay: gross_pay,
                        sss_deduct: sss_deduct,
                        pagibig_deduct: pagibig_deduct,
                        philhealth_deduct: philhealth_deduct,
                        net_salary: net_salary
                    })
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    loadPayroll();
                }

            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Edit payroll records
        async function editPayrollRecords() {
            const id = document.querySelector('input[name="task_checkbox[]"]:checked').value;
            const name = document.getElementById('edit_name').value;
            const position = document.getElementById('edit_position').value;
            const salary = document.getElementById('edit_salary').value;
            const daily_rate = document.getElementById('edit_daily_rate').value;
            const basic_pay = document.getElementById('edit_basic_pay').value;
            const ot_pay = document.getElementById('edit_ot_pay').value;
            const late_deduct = document.getElementById('edit_late_deduct').value;
            const gross_pay = document.getElementById('edit_gross_pay').value;
            const sss_deduct = document.getElementById('edit_sss_deduct').value;
            const pagibig_deduct = document.getElementById('edit_pagibig_deduct').value;
            const philhealth_deduct = document.getElementById('edit_philhealth_deduct').value;
            const net_salary = document.getElementById('edit_net_salary').value;

            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/edit-payroll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        name: name,
                        position: position,
                        salary: salary,
                        daily_rate: daily_rate,
                        basic_pay: basic_pay,
                        ot_pay: ot_pay,
                        late_deduct: late_deduct,
                        gross_pay: gross_pay,
                        sss_deduct: sss_deduct,
                        pagibig_deduct: pagibig_deduct,
                        philhealth_deduct: philhealth_deduct,
                        net_salary: net_salary
                    })
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    loadPayroll();
                }

            } catch (error) {
                console.error('Error:', error);
            }
        }



    </script>
</body>
</html>