<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $stmt = $conn->prepare("UPDATE contact_queries SET is_viewed = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    echo $stmt->execute() ? 'success' : 'fail';
    $stmt->close();
}
