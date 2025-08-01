<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - HindSafar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            height: 100%;
            overflow: hidden;
            font-family: sans-serif;
        }

        body {
            display: flex;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            height: 100vh;
            background-color: #343a40;
            padding-top: 40px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main {
            margin-left: 220px;
            padding: 30px;
            height: 100vh;
            overflow-y: auto;
            background-color: #f8f9fa;
            width: calc(100% - 220px);
        }

        h3 {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <p style="color:#fff; width:100%; text-align:center;">----- Admin Dash -----</p>
    <a href="dashboard.php?page=queries">📩 Queries</a>
    <a href="dashboard.php?page=deals">💼 Manage Deals</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="main">
    <?php
    $page = $_GET['page'] ?? 'queries';
    if ($page === 'queries') {
        include 'queries.php';
    } elseif ($page === 'deals') {
        include 'manage-deals.php';
    } else {
        echo "<h3>Page not found.</h3>";
    }
    ?>
</div>

</body>
</html>
