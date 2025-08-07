<h3>Manage Deals / Custom Packages</h3>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Existing Packages</h5>
        <a href="create-package.php" class="btn btn-primary">➕ Create New Package</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Package Name</th>
                <th>Location</th>
                <th>Days</th>
                <th>Price (₹)</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../Backend/Database/db.php';
            $result = mysqli_query($conn, "SELECT * FROM custom_packages ORDER BY created_at DESC");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['package_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . $row['no_of_days'] . "</td>";
                echo "<td>" . number_format($row['price'], 2) . "</td>";
                echo "<td>" . ($row['is_active'] ? '<span class=\"badge bg-success\">Yes</span>' : '<span class=\"badge bg-danger\">No</span>') . "</td>";
                echo "<td>
                        <a href='edit-package.php?id=" . $row['package_id'] . "' class='btn btn-sm btn-warning'>✏️ Edit</a>
                        <a href='#" . $row['package_id'] . "' class='btn btn-sm btn-secondary'>" . ($row['is_active'] ? '🔕 Deactivate' : '🔔 Activate') . "</a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>
