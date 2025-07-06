<?php
    session_start();
    include '../backend/Database/db.php';
    if(!isset($_SESSION['user_id'])){
        echo "<script>window.location.href = '../';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];

// Prepare and execute query to get role
    $stmt = $pdo->prepare("SELECT ROLE FROM customers WHERE CUSTOMER_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        // User not found in DB, logout for safety
        session_destroy();
        echo "<script>alert('Access Denied.'); window.location.href = '../';</script>";
        exit;
    }

    if ($user['ROLE'] !== 'ADMIN') {
        // If not admin, deny access
        echo "<script>alert('Access Denied.'); window.location.href = '../';</script>";
        exit;
    }
    echo "Yup, you are admin now"


?>