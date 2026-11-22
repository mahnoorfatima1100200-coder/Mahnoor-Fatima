<?php
/**
 * Skinora - Admin Customer Messages
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Customer Inquiries & Messages';
$db = getDBConnection();

// Mark as read
if (isset($_GET['mark_read'])) {
    validateCsrfOrDie();
    $id = (int)$_GET['mark_read'];
    $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    redirect('admin/messages.php');
}

// Delete message
if (isset($_GET['delete'])) {
    validateCsrfOrDie();
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    setFlash('info', 'Message deleted.');
    redirect('admin/messages.php');
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Customer Contact Inquiries (<?= count($messages) ?>)</h3>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #6c757d;">No customer messages yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <tr style="background: <?= $m['is_read'] ? '#fff' : '#fdf7f5' ?>;">
                        <td>
                            <strong><?= e($m['name']) ?></strong><br>
                            <span style="font-size: 0.78rem; color: #6c757d;"><?= e($m['email']) ?> &bull; <?= e($m['phone']) ?></span>
                        </td>
                        <td><strong><?= e($m['subject']) ?></strong></td>
                        <td style="max-width: 320px; font-size: 0.86rem;"><?= e($m['message']) ?></td>
                        <td style="font-size: 0.78rem;"><?= date('M d, Y h:i A', strtotime($m['created_at'])) ?></td>
                        <td>
                            <span class="status-pill status-<?= $m['is_read'] ? 'active' : 'pending' ?>">
                                <?= $m['is_read'] ? 'Read' : 'New' ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <?php if (!$m['is_read']): ?>
                                    <a href="<?= BASE_URL ?>admin/messages.php?mark_read=<?= $m['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" class="btn-admin btn-admin-outline" style="padding: 2px 6px; font-size: 0.72rem;">Mark Read</a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>admin/messages.php?delete=<?= $m['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" class="btn-admin btn-admin-danger" style="padding: 2px 6px; font-size: 0.72rem;" onclick="return confirm('Delete message?');">&times;</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
