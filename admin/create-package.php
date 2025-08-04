<?php
session_start();
require '../Backend/Database/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $package_name = $_POST['package_name'];
    $no_of_days = $_POST['no_of_days'];
    $location = $_POST['location'];
    $destination_covered = $_POST['destination_covered'];
    $accommodation_included = isset($_POST['accommodation_included']) ? 1 : 0;
    $hotel_type = $_POST['hotel_type'];
    $location_commute_included = isset($_POST['location_commute_included']) ? 1 : 0;
    $flights_included = isset($_POST['flights_included']) ? 1 : 0;
    $commute_airport_included = isset($_POST['commute_airport_included']) ? 1 : 0;
    $meals_included = isset($_POST['meals_included']) ? 1 : 0;
    $no_of_persons = $_POST['no_of_persons'];
    $price = $_POST['price'];
    $package_images = $_POST['package_images'];
    $additional_info = $_POST['additional_info'];
    $tags = $_POST['tags'];
    $contact_number = $_POST['contact_number'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $cancellation_policy = $_POST['cancellation_policy'];
    $refund_applicable = isset($_POST['refund_applicable']) ? 1 : 0;
    $category = $_POST['category'];

    $stmt = $conn->prepare("
        INSERT INTO custom_packages (
            package_name, no_of_days, location, destination_covered, accommodation_included,
            hotel_type, location_commute_included, flights_included, commute_airport_included,
            meals_included, no_of_persons, price, package_images, additional_info, tags,
            contact_number, is_active, cancellation_policy, refund_applicable, category
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssiiiiidsssssisss",
        $package_name, $no_of_days, $location, $destination_covered, $accommodation_included,
        $hotel_type, $location_commute_included, $flights_included, $commute_airport_included,
        $meals_included, $no_of_persons, $price, $package_images, $additional_info, $tags,
        $contact_number, $is_active, $cancellation_policy, $refund_applicable, $category
    );

    if ($stmt->execute()) {
        header("Location: dashboard.php?page=deals&msg=created");
        exit();
    } else {
        echo "Error inserting package: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Package</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Create New Package</h2>
    <form method="post">
        <div class="mb-3"><label class="form-label">Package Name</label><input type="text" name="package_name" class="form-control" required></div>
        <div class="mb-3"><label>No. of Days</label><input type="number" name="no_of_days" class="form-control" required></div>
        <div class="mb-3"><label>Location</label><input type="text" name="location" class="form-control" required></div>
        <div class="mb-3"><label>Destinations Covered</label><textarea name="destination_covered" class="form-control"></textarea></div>
        <div class="form-check mb-3"><input type="checkbox" name="accommodation_included" class="form-check-input"><label class="form-check-label">Accommodation Included</label></div>
        <div class="mb-3">
            <label>Hotel Type</label>
            <select name="hotel_type" class="form-control" required>
                <option value="eco">Eco</option>
                <option value="lux">Lux</option>
                <option value="deluxe">Deluxe</option>
            </select>
        </div>

        <?php
        $booleans = [
            'location_commute_included' => 'Location Commute Included',
            'flights_included' => 'Flights Included',
            'commute_airport_included' => 'Commute Airport Included',
            'meals_included' => 'Meals Included',
            'is_active' => 'Is Active',
            'refund_applicable' => 'Refund Applicable'
        ];
        foreach ($booleans as $field => $label): ?>
            <div class="form-check mb-2">
                <input type="checkbox" name="<?= $field ?>" class="form-check-input">
                <label class="form-check-label"><?= $label ?></label>
            </div>
        <?php endforeach; ?>

        <div class="mb-3"><label>No. of Persons</label><input type="number" name="no_of_persons" class="form-control" value="1"></div>
        <div class="mb-3"><label>Price</label><input type="number" name="price" step="0.01" class="form-control" required></div>
        <div class="mb-3"><label>Package Images (URLs)</label><textarea name="package_images" class="form-control"></textarea></div>
        <div class="mb-3"><label>Additional Info</label><textarea name="additional_info" class="form-control"></textarea></div>
        <div class="mb-3"><label>Tags (comma-separated)</label><input type="text" name="tags" class="form-control"></div>
        <div class="mb-3"><label>Contact Number</label><input type="text" name="contact_number" class="form-control"></div>
        <div class="mb-3"><label>Cancellation Policy</label><textarea name="cancellation_policy" class="form-control"></textarea></div>

        <div class="mb-3">
            <label>Package Category</label>
            <select name="category" class="form-control" required>
                <option value="honeymoon">Honeymoon</option>
                <option value="friends">Friends Trip</option>
                <option value="family">Family Trip</option>
                
                <option value="adventure">Adventure</option>
                <option value="spiritual">Spiritual</option>
                <option value="luxary">Luxary</option>
                <option value="budget">Budget</option>
                <option value="solo">Solo</option>
                <option value="cultural">Cultural</option>
                <option value="wildlife">Wildlife</option>
                <option value="beach">Beach</option>
                <option value="hill_station">Hill Station</option>
                <option value="corporate">Corporate</option>
                <option value="wellness">Wellness</option>
                <option value="weekend">Weekend</option>
                <option value="festive">Festive</option>
                <option value="custom">Custom</option>
                <option value="others">Others</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Create Package</button>
        <a href="dashboard.php?page=deals" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
