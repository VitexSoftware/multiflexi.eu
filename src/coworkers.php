<?php

declare(strict_types=1);

/**
 * This file is part of the MultiFlexi package
 *
 * https://multiflexi.eu/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MultiFlexi\Ui;

require_once __DIR__.'/init.php';

header('Content-Type: application/json');

$loggedUser = \Ease\Shared::user();

if (!$loggedUser->isLogged()) {
    echo json_encode(['success' => false, 'message' => _('Not authenticated')]);

    exit;
}

$action = \Ease\WebPage::getRequestValue('action', 'post');
$itemType = \Ease\WebPage::getRequestValue('item_type', 'post');
$itemId = (int) \Ease\WebPage::getRequestValue('item_id', 'post');
$userId = (int) \Ease\WebPage::getRequestValue('user_id', 'post');

if (!\in_array($itemType, ['app', 'credential_prototype'], true) || $itemId < 1 || $userId < 1) {
    echo json_encode(['success' => false, 'message' => _('Invalid parameters')]);

    exit;
}

// Verify the logged-in user is the owner of this item
$tableName = $itemType === 'app' ? 'apps' : 'credential_prototype';
$engine = new \Ease\SQL\Engine();
$pdo = $engine->getPdo();

$stmt = $pdo->prepare("SELECT user FROM {$tableName} WHERE id = ?");
$stmt->execute([$itemId]);
$row = $stmt->fetch(\PDO::FETCH_ASSOC);

if (!$row || (int) $row['user'] !== (int) $loggedUser->getMyKey()) {
    echo json_encode(['success' => false, 'message' => _('Only the owner can manage coworkers')]);

    exit;
}

// Prevent adding self as coworker
if ($userId === (int) $loggedUser->getMyKey()) {
    echo json_encode(['success' => false, 'message' => _('You are already the owner')]);

    exit;
}

switch ($action) {
    case 'add':
        $stmt = $pdo->prepare('INSERT IGNORE INTO item_coworker (item_type, item_id, user_id, granted_by, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$itemType, $itemId, $userId, $loggedUser->getMyKey()]);
        echo json_encode(['success' => true, 'message' => _('Coworker added')]);

        break;
    case 'remove':
        $stmt = $pdo->prepare('DELETE FROM item_coworker WHERE item_type = ? AND item_id = ? AND user_id = ?');
        $stmt->execute([$itemType, $itemId, $userId]);
        echo json_encode(['success' => true, 'message' => _('Coworker removed')]);

        break;

    default:
        echo json_encode(['success' => false, 'message' => _('Unknown action')]);
}
