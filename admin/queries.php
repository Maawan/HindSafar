<?php
require '../Backend/Database/db.php';

// Handle "Mark as Viewed" update (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    echo "Request Recieved";
    $stmt = $conn->prepare("UPDATE customer_queries SET is_viewed = 1 WHERE query_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false],404);
    }
    exit(); // Stop further output for AJAX response
}

// Otherwise, normal GET request to render the table
$result = $conn->query("SELECT * FROM customer_queries ORDER BY created_at DESC");
?>

<h3>Customer Queries</h3>
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            
            
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Viewed?</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            
            
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['subject']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td>
                <?= $row['is_viewed'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' ?>
            </td>
            <td>
                <?php if (!$row['is_viewed']): ?>
                    <button class="btn btn-sm btn-primary mark-viewed" data-id="<?= $row['query_id'] ?>">Mark as Viewed</button>
                <?php else: ?>
                    <button class="btn btn-sm btn-secondary" disabled>Viewed</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<script>
document.querySelectorAll(".mark-viewed").forEach(button => {
    button.addEventListener("click", () => {
        const id = button.dataset.id;
        try {
            fetch('', {  // send to same file
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.text())
            .then(data => {
               location.reload();
            });
        } catch (error) {
            
        }
        
    });
});
</script>
