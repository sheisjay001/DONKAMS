<?php
require_once '../config/db.php';
include 'includes/header.php';

// Fetch Users
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="section-header">
    <h2>Users</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <span class="status-badge" style="background: <?php echo $row['role'] == 'admin' ? '#e74c3c' : '#3498db'; ?>; color: white;">
                            <?php echo ucfirst($row['role']); ?>
                        </span>
                    </td>
                    <td><?php echo isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A'; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
