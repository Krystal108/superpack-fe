<?php


$page = $_GET['page'] ?? null;

switch ($page) {
    case 'welcome':
        require_once __DIR__ . "/pages/welcome.php";
        break;
    case 'login':
        require_once __DIR__ . "/pages/login.php";
        break;
    case 'register':
        require_once __DIR__ . "/pages/register.php";
        break;
    case 'logout':
        require_once __DIR__ . "/pages/logout.php";
        break;
    case 'dashboard':
        require_once __DIR__ . "/pages/dashboard.php";
        break;
    case 'task-management':
        require_once __DIR__ . "/pages/task_management.php";
        break;
    case 'payroll':
        require_once __DIR__ . "/pages/payroll.php";
        break;
    case 'employee-list':
        require_once __DIR__ . "/pages/employee_list.php";
        break;
    case 'worker-eval':
        require_once __DIR__ . "/pages/worker_eval.php";
        break;
    case 'attendance-check':
        require_once __DIR__ . "/pages/attendance_check.php";
        break;
    case null:
        require_once __DIR__ . "/pages/welcome.php";
        break;
    default:
        require_once __DIR__ . "/pages/error.php";
        break;
}