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

$oPage->addItem(new PageTop(_('Credential Types')));
$oPage->includeCSS('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css');

// View toggle buttons
$viewToggle = new \Ease\Html\DivTag(null, ['class' => 'd-flex justify-content-between align-items-center mb-3']);
$viewToggle->addItem(new \Ease\Html\H2Tag(_('Credential Types'), ['class' => 'mb-0']));
$toggleBtns = new \Ease\Html\DivTag(null, ['class' => 'btn-group', 'role' => 'group']);
$toggleBtns->addItem(new \Ease\Html\ButtonTag(
    '<i class="bi bi-grid-3x3-gap-fill"></i> '._('Cards'),
    ['class' => 'btn btn-outline-primary', 'id' => 'btn-cards-view', 'type' => 'button'],
));
$toggleBtns->addItem(new \Ease\Html\ButtonTag(
    '<i class="bi bi-table"></i> '._('Table'),
    ['class' => 'btn btn-primary', 'id' => 'btn-table-view', 'type' => 'button'],
));
$viewToggle->addItem($toggleBtns);
$oPage->container->addItem($viewToggle);

// ═══ CARD VIEW ═══
$cardView = new \Ease\Html\DivTag(null, ['id' => 'card-view', 'style' => 'display:none']);

$credProto = new \MultiFlexi\Hub\CredentialProtoType();
$allCreds = $credProto->listingQuery()->orderBy('name')->fetchAll();

$searchBox = new \Ease\Html\InputSearchTag('cred_search', '', [
    'placeholder' => _('Search credential types...'),
    'class' => 'form-control form-control-lg mb-3',
    'id' => 'cred_search',
]);
$cardView->addItem($searchBox);

$countDiv = new \Ease\Html\DivTag(
    new \Ease\Html\SmallTag(
        ['<strong id="cred-visible-count">'.\count($allCreds).'</strong> ', _('credential types')],
        ['class' => 'text-muted'],
    ),
    ['class' => 'mb-3'],
);
$cardView->addItem($countDiv);

$cardsRow = new \Ease\TWB5\Row();

foreach ($allCreds as $credData) {
    $cardWrapper = new \Ease\Html\DivTag(null, [
        'class' => 'col-md-4 col-lg-3 mb-3 cred-card-wrapper',
        'data-cred-name' => mb_strtolower((string) ($credData['name'] ?? '')),
        'data-cred-code' => mb_strtolower((string) ($credData['code'] ?? '')),
        'data-cred-desc' => mb_strtolower((string) ($credData['description'] ?? '')),
    ]);

    $card = new \Ease\Html\DivTag(null, ['class' => 'card h-100 cred-card']);
    $cardBody = new \Ease\Html\DivTag(null, ['class' => 'card-body text-center']);

    // Logo
    $logoDiv = new \Ease\Html\DivTag(null, ['class' => 'my-3']);
    $logoDiv->addItem(new \Ease\Html\ImgTag(
        \MultiFlexi\Hub\CredentialProtoType::logoUrl($credData),
        (string) ($credData['name'] ?? ''),
        ['style' => 'max-width: 64px; max-height: 64px;'],
    ));
    $cardBody->addItem($logoDiv);

    // Name with link
    $cardBody->addItem(new \Ease\Html\H5Tag(
        new \Ease\Html\ATag('credentialtype.php?id='.$credData['id'], (string) ($credData['name'] ?? ''), ['class' => 'text-decoration-none']),
        ['class' => 'card-title'],
    ));

    // Code badge
    $cardBody->addItem(new \Ease\Html\PTag(
            new \Ease\Html\PairTag('code', [], (string) ($credData['code'] ?? '')),
        ['class' => 'card-text'],
    ));

    // Description
    if (!empty($credData['description'])) {
        $desc = mb_strimwidth((string) $credData['description'], 0, 100, '...');
        $cardBody->addItem(new \Ease\Html\PTag(
            new \Ease\Html\SmallTag($desc, ['class' => 'text-muted']),
            ['class' => 'card-text'],
        ));
    }

    // Version badge
    if (!empty($credData['version'])) {
        $cardBody->addItem(new \Ease\Html\SpanTag('v'.$credData['version'], ['class' => 'badge bg-secondary']));
    }

    $card->addItem($cardBody);
    $cardWrapper->addItem($card);
    $cardsRow->addItem($cardWrapper);
}

$cardView->addItem($cardsRow);
$oPage->container->addItem($cardView);

// ═══ TABLE VIEW ═══
$tableView = new \Ease\Html\DivTag(null, ['id' => 'table-view']);
$tableView->addItem(new DBDataTable(new \MultiFlexi\Hub\CredentialProtoType()));
$oPage->container->addItem($tableView);

// CSS
$oPage->addCSS(<<<'CSS'
.cred-card { cursor: pointer; transition: all 0.2s; }
.cred-card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
.cred-card-wrapper[data-hidden="true"] { display: none; }
CSS);

// JS - view toggle and search
$oPage->addJavaScript(<<<'JS'
$(document).ready(function() {
    var VIEW_KEY = 'multiflexi_eu_creds_view';
    function setView(mode) {
        if (mode === 'cards') {
            $('#card-view').show(); $('#table-view').hide();
            $('#btn-cards-view').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn-table-view').removeClass('btn-primary').addClass('btn-outline-primary');
        } else {
            $('#card-view').hide(); $('#table-view').show();
            $('#btn-table-view').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn-cards-view').removeClass('btn-primary').addClass('btn-outline-primary');
        }
        try { localStorage.setItem(VIEW_KEY, mode); } catch(e) {}
    }
    $('#btn-cards-view').on('click', function() { setView('cards'); });
    $('#btn-table-view').on('click', function() { setView('table'); });
    var savedView = 'table';
    try { savedView = localStorage.getItem(VIEW_KEY) || 'table'; } catch(e) {}
    setView(savedView);

    // Card click navigation
    $('.cred-card').click(function() {
        var link = $(this).find('a').attr('href');
        if (link) window.location.href = link;
    });

    // Live search for card view
    $('#cred_search').on('keyup', function() {
        var q = $(this).val().toLowerCase();
        var count = 0;
        $('.cred-card-wrapper').each(function() {
            var n = $(this).data('cred-name') || '';
            var c = $(this).data('cred-code') || '';
            var d = $(this).data('cred-desc') || '';
            if (!q || n.includes(q) || c.includes(q) || d.toString().toLowerCase().includes(q)) {
                $(this).attr('data-hidden','false').show(); count++;
            } else {
                $(this).attr('data-hidden','true').hide();
            }
        });
        $('#cred-visible-count').text(count);
    });
});
JS);

$oPage->addItem(new PageBottom());

$oPage->draw();
