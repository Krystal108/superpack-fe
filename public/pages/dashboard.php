<?php 
session_start();

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

    // Redirect to prevent form resubmission
    header('Location: /?page=dashboard');
    exit;
}

// if user accessed the dashboard without logging in, redirect them to the welcome page
if (!isset($_SESSION['loggedin'])) {
    header('Location: /?page=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="dashboardnew.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    
    <div class="container-sidebar">
        <?php include 'sidebar_small.php'?>
    </div>
    <div class="container-everything">
        <div class="container-all">
            <div class="container-top">
                
                <?php include 'header_2.php';?>
                <div class="top-widgets-container">
                    <div class="options">
                        <div class="option">
                            <div class="overview-card blue">
                                <div class="count-employee-total"></div>
                                <div class="info">Total Employees</div>
                            </div>
                        </div>
                    </div>
                    <div class="option">
                        <div class="overview-card orange">
                            <div class="count-ontime-total"></div>
                            <div class="info">On Time Today</div>
                        </div>
                    </div>
                    <div class="option" onclick="window.location.href='task_management.php?department=<?php echo $department;?>'" style="cursor: pointer;">
                        <div class="overview-card green">
                            <div class="button-widget-text">Click Here</div>
                            <div class="info">Check Task</div>
                        </div>
                    </div>
                    <div class="option">
                        <div class="overview-card red">
                            <div class="current-time-widget">&#8203;</div>
                            <div class="info">Current Time</div>
                        </div>
                    </div>
                    <div class="option" onclick="window.location.href='attendance_check.php?username=<?php echo $username?>'" style="cursor: pointer;">
                        <div class="overview-card green">
                            <div class="button-widget-text">Click Here</div>
                            <div class="info">Check Attendance</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-bottom">
                <div class="container-left">
                    <div class="graph-container">
                        <?php include 'bar_chart.php'; ?>
                    </div>
                    <div class="graph-container">
                        <?php include 'leave_table_widget.php'; ?>
                    </div>
                    <div class="graph-container">
                        <?php include 'employee_table_widget.php'; ?>
                    </div>
                    <div class="graph-container">
                        <?php include 'calendar.php'; ?>
                    </div>
                </div>
                <div class="container-right">
                    <div class="graph-container">
                        <?php include 'pie_chart.php'; ?>
                    </div>
                </div>
            </div>  
        </div>
    </div>

    <script>
        // Call endpoint /widgets/count-total-employees
        async function fetchTotalEmployees() {
            try {
            const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/widgets/count-total-employees');
            const data = await response.json();
            // log it to console
            console.log(data);
            // Update the total employees count
            document.querySelector('.count-employee-total').textContent = data.total_employees;
            } catch (error) {
            console.error('Error fetching total employees:', error);
            }
        }

        fetchTotalEmployees();

        // Call endpoint /widgets/on-time-check
        async function fetchOnTimeStatus() {
            try {
            const response = await fetch('https://6dvfd2bd-5000.asse.devtunnels.ms/widgets/on-time-check', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name: '<?php echo $_SESSION['username']; ?>' })
            });
            const data = await response.json();
            // log it to console
            console.log(data);
            // Update the on-time status
            document.querySelector('.count-ontime-total').textContent = data.status;
            } catch (error) {
            console.error('Error fetching on-time status:', error);
            }
        }

        // Call the function to fetch on-time status
        fetchOnTimeStatus();
        
        document.addEventListener('DOMContentLoaded', () => {
            const clock_widget = document.querySelector('.current-time-widget');
            const options = { hour: '2-digit', minute: '2-digit' };
            const locale = 'en-PH';

            if (clock_widget) {
                setInterval(() => {
                    const now = new Date();
                    clock_widget.textContent = now.toLocaleTimeString(locale, options);
                }, 1000);
            } else {
                console.error("Element with class 'current-time-widget' not found.");
            }
        });

        // Role-based widgets
        document.addEventListener('DOMContentLoaded', function() {
            // If role is Admin, hide other widgets

            // get the role of the user in the session
            const role = "<?php echo $_SESSION['role']; ?>";

            // Select count-employee-total and its parent
            var countEmployeeTotal = document.querySelector('.count-employee-total');
            var countEmployeeTotalParent = countEmployeeTotal.parentElement;


            // Select fourth graph-container
            var pieContainer = document.querySelectorAll('.graph-container')[4];
            
            // Select fifth graph-container
            var treemapContainer = document.querySelectorAll('.graph-container')[5];

            var employeeContainer = document.querySelectorAll('.graph-container')[2];

            var employeepieContainer = document.querySelectorAll('.graph-container')[6];

            if (role !== 'Admin') {
                countEmployeeTotalParent.style.display = 'none';
                pieContainer.style.display = 'none';
                treemapContainer.style.display = 'none';
                employeeContainer.style.display = 'none';
                
            } else {
                countEmployeeTotalParent.style.display = 'block';
                pieContainer.style.display = 'block';
                treemapContainer.style.display = 'block';
                employeepieContainer.style.display = 'none';
            }

        });
    </script>
</body>
</html>