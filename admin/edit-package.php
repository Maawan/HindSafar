<?php
session_start();
require '../Backend/Database/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid package ID.");
}

$package_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM custom_packages WHERE package_id = ?");
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();
$package = $result->fetch_assoc();

if (!$package) {
    die("Package not found.");
}

// Handle form submission
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

    $updateStmt = $conn->prepare("
        UPDATE custom_packages SET
            package_name = ?, no_of_days = ?, location = ?, destination_covered = ?, accommodation_included = ?,
            hotel_type = ?, location_commute_included = ?, flights_included = ?, commute_airport_included = ?,
            meals_included = ?, no_of_persons = ?, price = ?, package_images = ?, additional_info = ?, tags = ?,
            contact_number = ?, is_active = ?, cancellation_policy = ?, refund_applicable = ?, category = ?
        WHERE package_id = ?
    ");

    $updateStmt->bind_param(
        "sississiiiidssssisssi",
        $package_name, $no_of_days, $location, $destination_covered, $accommodation_included,
        $hotel_type, $location_commute_included, $flights_included, $commute_airport_included,
        $meals_included, $no_of_persons, $price, $package_images, $additional_info, $tags,
        $contact_number, $is_active, $cancellation_policy, $refund_applicable, $category,
        $package_id
    );

    if ($updateStmt->execute()) {
        header("Location: dashboard.php?page=deals&msg=updated");
        exit();
    } else {
        echo "Update failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Package</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Edit Package: <?= htmlspecialchars($package['package_name']) ?></h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Package Name</label>
                <input type="text" name="package_name" class="form-control" value="<?= $package['package_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label>No. of Days</label>
                <input type="number" name="no_of_days" class="form-control" value="<?= $package['no_of_days'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="<?= $package['location'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Destinations Covered</label>
                <textarea name="destination_covered" class="form-control"><?= $package['destination_covered'] ?></textarea>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="accommodation_included" class="form-check-input" <?= $package['accommodation_included'] ? 'checked' : '' ?>>
                <label class="form-check-label">Accommodation Included</label>
            </div>
            <div class="mb-3">
                <label>Hotel Type</label>
                <select name="hotel_type" class="form-control">
                    <option <?= $package['hotel_type'] === 'eco' ? 'selected' : '' ?>>eco</option>
                    <option <?= $package['hotel_type'] === 'lux' ? 'selected' : '' ?>>lux</option>
                    <option <?= $package['hotel_type'] === 'deluxe' ? 'selected' : '' ?>>deluxe</option>
                </select>
            </div>

            <!-- Category Dropdown -->
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <?php
                    $categories = ['honeymoon', 'friends', 'family', 'adventure', 'spiritual', 'luxury', 'budget', 'solo', 'cultural', 'wildlife', 'beach', 'hill_station', 'corporate', 'wellness', 'weekend', 'festive', 'custom', 'others'];
                    foreach ($categories as $cat) {
                        $selected = ($package['category'] === $cat) ? 'selected' : '';
                        echo "<option value=\"$cat\" $selected>$cat</option>";
                    }
                    ?>
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
                    <input type="checkbox" name="<?= $field ?>" class="form-check-input" <?= $package[$field] ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= $label ?></label>
                </div>
            <?php endforeach; ?>

            <div class="mb-3">
                <label>No. of Persons</label>
                <input type="number" name="no_of_persons" class="form-control" value="<?= $package['no_of_persons'] ?>">
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="number" name="price" step="0.01" class="form-control" value="<?= $package['price'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Package Images (URLs)</label>
                <textarea name="package_images" class="form-control"><?= $package['package_images'] ?></textarea>
            </div>
            <div class="mb-3">
                <label>Additional Info</label>
                <textarea name="additional_info" class="form-control"><?= $package['additional_info'] ?></textarea>
            </div>
            <div class="mb-3">
                <label>Tags</label>
                <input type="text" name="tags" class="form-control" value="<?= $package['tags'] ?>">
            </div>
            <div class="mb-3">
                <label>Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="<?= $package['contact_number'] ?>">
            </div>
            <div class="mb-3">
                <label>Cancellation Policy</label>
                <textarea name="cancellation_policy" class="form-control"><?= $package['cancellation_policy'] ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Package</button>
            <a href="dashboard.php?page=deals" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
