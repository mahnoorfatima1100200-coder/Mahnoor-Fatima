<?php
/**
 * Skinora - Admin Reviews Moderation
 */
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Customer Reviews Moderation';
$db = getDBConnection();

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    validateCsrfOrDie();
    $revId = (int)$_GET['id'];
    $act = $_GET['action'];

    if ($act === 'approve') {
        $db->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?")->execute([$revId]);
        setFlash('success', 'Review approved and published.');
    } elseif ($act === 'reject') {
        $db->prepare("UPDATE reviews SET status = 'rejected' WHERE id = ?")->execute([$revId]);
        setFlash('info', 'Review marked as rejected.');
    } elseif ($act === 'delete') {
        $db->prepare("DELETE FROM reviews WHERE id = ?")->execute([$revId]);
        setFlash('info', 'Review deleted.');
    }
    redirect('admin/reviews.php');
}

$stmt = $db->query("
    SELECT r.*, p.name AS product_name, p.slug AS product_slug 
    FROM reviews r 
    JOIN products p ON r.product_id = p.id 
    ORDER BY r.id DESC
");
$reviews = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Product Reviews & Ratings (<?= count($reviews) ?>)</h3>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Reviewer</th>
                <th>Rating</th>
                <th>Feedback</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #6c757d;">No reviews submitted yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                    <tr>
                        <td>
                            <strong><?= e($r['product_name']) ?></strong>
                        </td>
                        <td><?= e($r['reviewer_name']) ?></td>
                        <td><?= renderRatingStars($r['rating']) ?></td>
                        <td style="max-width: 300px; font-size: 0.84rem;"><?= e($r['review_text']) ?></td>
                        <td style="font-size: 0.78rem;"><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                        <td>
                            <span class="status-pill status-<?= $r['status'] === 'approved' ? 'delivered' : ($r['status'] === 'rejected' ? 'cancelled' : 'pending') ?>">
                                <?= ucfirst(e($r['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <?php if ($r['status'] !== 'approved'): ?>
                                    <a href="<?= BASE_URL ?>admin/reviews.php?action=approve&id=<?= $r['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" class="btn-admin btn-admin-success" style="padding: 2px 6px; font-size: 0.72rem;">Approve</a>
                                <?php endif; ?>
                                <?php if ($r['status'] !== 'rejected'): ?>
                                    <a href="<?= BASE_URL ?>admin/reviews.php?action=reject&id=<?= $r['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" class="btn-admin btn-admin-outline" style="padding: 2px 6px; font-size: 0.72rem;">Reject</a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>admin/reviews.php?action=delete&id=<?= $r['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" class="btn-admin btn-admin-danger" style="padding: 2px 6px; font-size: 0.72rem;" onclick="return confirm('Delete review?');">&times;</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
