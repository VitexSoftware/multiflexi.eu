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

use Ease\Html\DivTag;
use Ease\Html\H5Tag;
use Ease\Html\PTag;

class CoworkersManager extends DivTag
{
    public function __construct(string $itemType, int $itemId)
    {
        parent::__construct(null, ['class' => 'coworkers-manager mt-3']);

        $oPage = WebPage::singleton();
        $oPage->includeJavaScript('js/selectize.min.js');
        $oPage->includeCss('css/selectize.bootstrap5.css');

        $this->addItem(new H5Tag(_('Coworkers')));
        $this->addItem(new PTag(_('Add users who can edit this item.'), ['class' => 'text-muted small']));

        // Fetch current coworkers
        $engine = new \MultiFlexi\Application();
        $pdo = $engine->getPdo();

        $stmt = $pdo->prepare('SELECT ic.user_id, u.login, u.firstname, u.lastname FROM item_coworker ic JOIN user u ON u.id = ic.user_id WHERE ic.item_type = ? AND ic.item_id = ?');
        $stmt->execute([$itemType, $itemId]);
        $currentCoworkers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currentIds = array_column($currentCoworkers, 'user_id');
        $currentIdsJson = json_encode(array_map('strval', $currentIds));

        // Fetch all users for the dropdown
        $allUsers = $pdo->query('SELECT id, login, firstname, lastname FROM user ORDER BY login')->fetchAll(\PDO::FETCH_ASSOC);

        $options = [];

        foreach ($allUsers as $user) {
            $label = $user['login'];

            if (!empty($user['firstname']) || !empty($user['lastname'])) {
                $label .= ' ('.trim($user['firstname'].' '.$user['lastname']).')';
            }

            $options[] = ['id' => (string) $user['id'], 'name' => $label];
        }

        $optionsJson = json_encode($options);

        $inputId = 'coworkers_'.$itemType.'_'.$itemId;

        $this->addItem(new \Ease\Html\InputTextTag($inputId, implode(',', $currentIds), [
            'id' => $inputId,
            'class' => 'form-control',
        ]));

        $this->addItem(new DivTag(null, ['id' => $inputId.'_status', 'class' => 'small mt-1']));

        $oPage->addJavaScript(<<<JS
$(document).ready(function() {
    setTimeout(function() {
        if (typeof $.fn.selectize === 'undefined') return;
        $('#{$inputId}').selectize({
            plugins: ['remove_button'],
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            persist: true,
            create: false,
            delimiter: ',',
            maxOptions: 10000,
            options: {$optionsJson},
            items: {$currentIdsJson},
            onItemAdd: function(value) {
                $.post('coworkers.php', {
                    action: 'add',
                    item_type: '{$itemType}',
                    item_id: {$itemId},
                    user_id: value
                }, function(resp) {
                    if (resp.success) {
                        $('#{$inputId}_status').html('<span class="text-success">' + resp.message + '</span>').fadeIn().delay(2000).fadeOut();
                    } else {
                        $('#{$inputId}_status').html('<span class="text-danger">' + resp.message + '</span>').fadeIn().delay(3000).fadeOut();
                    }
                }, 'json');
            },
            onItemRemove: function(value) {
                $.post('coworkers.php', {
                    action: 'remove',
                    item_type: '{$itemType}',
                    item_id: {$itemId},
                    user_id: value
                }, function(resp) {
                    if (resp.success) {
                        $('#{$inputId}_status').html('<span class="text-success">' + resp.message + '</span>').fadeIn().delay(2000).fadeOut();
                    } else {
                        $('#{$inputId}_status').html('<span class="text-danger">' + resp.message + '</span>').fadeIn().delay(3000).fadeOut();
                    }
                }, 'json');
            }
        });
    }, 200);
});
JS);
    }
}
