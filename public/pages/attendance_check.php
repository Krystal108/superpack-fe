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
                <div class="tool-bar">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addLeaveModal">Create Leave Request</button>
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="leave-table">
                            
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="container-bottom">
                <div class="container-table">
                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody class="attendance-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveModalLabel">Create Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="leaveType">Leave Type</label>
                            <select class="form-control" id="leaveType" name="leaveType">
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                                <option value="Maternity Leave">Maternity Leave</option>
                                <option value="Paternity Leave">Paternity Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label for="startDate">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="startDate">
                            </div>
                            <div class="col">
                                <label for="endDate">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="endDate">
                            </div>
                        </div>
                        <button type="submit" name="addLeave" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</body>

<script>
        console.log('<?php echo $role; ?>');
        console.log('attendance check');

        const clock = document.querySelector('.current-time');
        const options = {hour: '2-digit', minute: '2-digit'};
        const locale = 'en-PH';
        setInterval(() => {
            const now = new Date();
            clock.textContent = now.toLocaleTimeString(locale, options);
        }, 1000);

        // Change logo name 
        const logoName = document.querySelector('.logo_name');
        logoName.textContent = 'Attendance Check';

        // load attendance data
        async function loadAttendance() {
            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/load-attendance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: '<?php echo $_SESSION['username'] ?>'
                    })
                });

                const data = await response.json();
                console.log(data);

                if(data.success) {
                    const tbody = document.querySelector('.attendance-table');
                    tbody.innerHTML = '';

                    // Get the date from Time In
                    const date = new Date(data.attendance[0].start_date);
                    const options = {year: 'numeric', month: 'long', day: 'numeric'};
                    const formattedDate = date.toLocaleDateString('en-PH', options);

                    const attendanceCount = data.attendance.length;

                    if(attendanceCount > 0) {
                        for(let i = 0; i < attendanceCount; i++) {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `<td>${data.attendance[i].leave_type}</td><td>${data.attendance[i].start_date}</td><td>${data.attendance[i].end_date}</td><td>${data.attendance[i].status}</td>`;
                            tbody.appendChild(tr);
                        }
                    } else {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="4">No data found</td>';
                        tbody.appendChild(tr);
                    }
                }
            } catch (error) {
                    console.error(error);
            }
        }
        

        // load attendance admin data
        async function loadAttendanceAdmin() {
            try {
                const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/load-attendance-admin', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                console.log(data);

                if (data.success) {
                    const tbody = document.querySelector('.attendance-table');
                    if (!tbody) {
                        console.error('Table body with class "attendance-table" not found');
                        return;
                    }

                    tbody.innerHTML = ''; // Clear existing rows

                    const attendance = data.attendance; // Array of tuples
                    const attendanceCount = attendance.length;

                    if (attendanceCount > 0) {
                        for (let i = 0; i < attendanceCount; i++) {
                            const record = attendance[i]; // Access tuple

                            const id = record[0];
                            const name = record[1];
                            const position = record[2]; // This will be mapped to the "Position" header
                            const timeIn = record[3]; // datetime value
                            const timeOut = record[4]; // Can be null

                            // Format timeIn and handle null timeOut
                            const formattedTimeIn = new Date(timeIn).toLocaleString('en-PH', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric', 
                                hour: '2-digit', 
                                minute: '2-digit' 
                            });

                            const formattedTimeOut = timeOut
                                ? new Date(timeOut).toLocaleString('en-PH', { 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric', 
                                    hour: '2-digit', 
                                    minute: '2-digit' 
                                })
                                : 'N/A';

                            // Create table row
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${name}</td>
                                <td>${position}</td>
                                <td>${formattedTimeIn}</td>
                                <td>${formattedTimeOut}</td>
                                <td>${formattedTimeIn.split(',')[0]}</td> <!-- Extract just the date -->
                            `;
                            tbody.appendChild(tr);
                        }
                    } else {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="5">No data found</td>'; // Adjust colspan for 5 headers
                        tbody.appendChild(tr);
                    }
                }
            } catch (error) {
                console.error('Error loading attendance data:', error);
            }
        }


        // Call loadAttendance function if user role is not admin
        if('<?php echo $_SESSION['role'] ?>' !== 'Admin') {
            loadAttendance();
        } else {
            loadAttendanceAdmin();
        }

    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</html>
