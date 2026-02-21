<?php
/**
 * includes/audit.php
 * Writes an entry to the audit_log table whenever a product is
 * created, updated, or deleted.
 *
 * Usage:
 *   logAction('CREATE', 'product', $productId, ['name' => 'Widget']);
 */

if (!defined('APP_RUNNING')) exit;

/**
 * @param string  $action    CREATE | UPDATE | DELETE
 * @param string  $entity    e.g. 'product'
 * @param int     $entityId  the PK of the affected row
 * @param array   $details   optional snapshot (will be JSON-encoded)
 */
function logAction(string $action, string $entity, int $entityId, array $details = []): void
{
    if (!isLoggedIn()) return;   // safety guard

    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO audit_log (user_id, action, entity, entity_id, details)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $_SESSION['user_id'],
        strtoupper($action),
        $entity,
        $entityId,
        json_encode($details, JSON_PRETTY_PRINT)
    ]);
}

/**
 * Retrieve the most recent audit entries (for the dashboard).
 *
 * @param int $limit  how many rows to return
 * @return array
 */
function getRecentAudit(int $limit = 10): array
{
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'SELECT a.*, u.name AS user_name
         FROM   audit_log a
         JOIN   users     u ON u.id = a.user_id
         ORDER  BY a.created_at DESC
         LIMIT  ?'
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}
