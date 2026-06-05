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
use Ease\Html\H4Tag;
use Ease\Html\PTag;
use Ease\Html\SmallTag;
use Ease\TWB5\Badge;
use Ease\TWB5\Row;

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

// Collect all unique tags
$allTags = [];

foreach ($allCreds as $credData) {
    if (!empty($credData['tags'])) {
        foreach (explode(',', $credData['tags']) as $tag) {
            $tag = trim($tag);

            if (!empty($tag) && !isset($allTags[$tag])) {
                $allTags[$tag] = ['id' => $tag, 'name' => $tag];
            }
        }
    }
}

ksort($allTags);
$allTags = array_values($allTags);

$searchBox = new \Ease\Html\InputSearchTag('cred_search', '', [
    'placeholder' => _('Search credential types...'),
    'class' => 'form-control form-control-lg mb-3',
    'id' => 'cred_search',
]);
$cardView->addItem($searchBox);

// Tag filter
if (!empty($allTags)) {
    $oPage->includeJavaScript('js/selectize.min.js');
    $oPage->includeCss('css/selectize.bootstrap5.css');

    $cardView->addItem(new H4Tag(_('Filter by Topics')));
    $cardView->addItem(new PTag(_('Select topics to filter credential types. All are shown when no topics are selected.')));

    $filterRow = new Row();
    $tagFilter = new PillBox('cred_tag_filter', $allTags, [], [
        'class' => 'form-control mb-2',
        'placeholder' => _('Select topics to filter...'),
    ]);
    $filterRow->addColumn(10, $tagFilter);

    $resetButton = new \Ease\Html\ButtonTag(_('Reset Filter'), [
        'class' => 'btn btn-outline-secondary mb-2',
        'type' => 'button',
        'id' => 'reset-cred-tag-filter',
        'title' => _('Clear topic filter to show all credential types'),
    ]);
    $filterRow->addColumn(2, $resetButton);
    $cardView->addItem($filterRow);
    $cardView->addItem(new DivTag('', ['class' => 'mb-4']));
}

$countDiv = new DivTag(
    new SmallTag(
        ['<strong id="cred-visible-count">'.\count($allCreds).'</strong> ', _('credential types')],
        ['class' => 'text-muted'],
    ),
    ['class' => 'mb-3'],
);
$cardView->addItem($countDiv);

$cardsRow = new \Ease\TWB5\Row();

foreach ($allCreds as $credData) {
    $topicsList = !empty($credData['tags']) ? array_map('trim', explode(',', $credData['tags'])) : [];
    $topicsDataAttr = implode(',', $topicsList);

    $cardWrapper = new DivTag(null, [
        'class' => 'col-md-4 col-lg-3 mb-3 cred-card-wrapper',
        'data-cred-name' => mb_strtolower((string) ($credData['name'] ?? '')),
        'data-cred-code' => mb_strtolower((string) ($credData['code'] ?? '')),
        'data-cred-desc' => mb_strtolower((string) ($credData['description'] ?? '')),
        'data-tags' => $topicsDataAttr,
    ]);

    $card = new DivTag(null, ['class' => 'card h-100 cred-card']);
    $cardBody = new DivTag(null, ['class' => 'card-body text-center']);

    // Logo
    $logoDiv = new DivTag(null, ['class' => 'my-3']);
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
    $cardBody->addItem(new PTag(
            new \Ease\Html\PairTag('code', [], (string) ($credData['code'] ?? '')),
        ['class' => 'card-text'],
    ));

    // Description
    if (!empty($credData['description'])) {
        $desc = mb_strimwidth((string) $credData['description'], 0, 100, '...');
        $cardBody->addItem(new PTag(
            new SmallTag($desc, ['class' => 'text-muted']),
            ['class' => 'card-text'],
        ));
    }

    // Homepage link
    if (!empty($credData['homepage'])) {
        $cardBody->addItem(new PTag(
            new \Ease\Html\ATag($credData['homepage'], _('Homepage'), ['target' => '_blank', 'class' => 'text-decoration-none']),
            ['class' => 'card-text mb-1'],
        ));
    }

    // Topic badges
    if (!empty($topicsList)) {
        $topicBadges = new DivTag(null, ['class' => 'mt-2 tag-badges']);

        foreach ($topicsList as $topic) {
            if (!empty($topic)) {
                $topicBadges->addItem(new Badge($topic, 'secondary', ['class' => 'me-1 mb-1 tag-badge']));
            }
        }

        $cardBody->addItem($topicBadges);
    }

    // Version badge
    if (!empty($credData['version'])) {
        $cardBody->addItem(new \Ease\Html\SpanTag('v'.$credData['version'], ['class' => 'badge bg-info mt-2']));
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
.tag-badge { transition: all 0.3s ease-in-out; cursor: pointer; position: relative; }
.tag-badge.bg-primary { font-weight: bold !important; box-shadow: 0 2px 6px rgba(0,123,255,0.4) !important; transform: scale(1.1) !important; z-index: 2 !important; }
.tag-badge:hover { transform: scale(1.05); box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
.tag-badges { display: flex; flex-wrap: wrap; gap: 4px; justify-content: center; }
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

    // Tag filtering
    var CRED_TAG_KEY = 'multiflexi_eu_cred_tag_filter';
    var DEFAULT_ALL = 'all_tags_selected';
    var credTagSelectize = null;
    var allCredTags = [];
    var selectedCredTags = [];

    function saveCredTagSelection(tags) {
        try {
            if (tags.length === allCredTags.length) {
                localStorage.setItem(CRED_TAG_KEY, DEFAULT_ALL);
            } else {
                localStorage.setItem(CRED_TAG_KEY, JSON.stringify(tags));
            }
        } catch (e) {}
    }

    function loadCredTagSelection() {
        try {
            var saved = localStorage.getItem(CRED_TAG_KEY);
            if (!saved || saved === DEFAULT_ALL) return [];
            var parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) {
                return parsed.filter(function(t) { return allCredTags.includes(t); });
            }
            return [];
        } catch (e) { return []; }
    }

    function applyCredFilters() {
        var q = $('#cred_search').val().toLowerCase();
        var count = 0;
        $('.cred-card-wrapper').each(function() {
            var n = $(this).data('cred-name') || '';
            var c = $(this).data('cred-code') || '';
            var d = $(this).data('cred-desc') || '';
            var tags = $(this).attr('data-tags') || '';

            var matchesSearch = !q || n.includes(q) || c.includes(q) || d.toString().toLowerCase().includes(q) || tags.toLowerCase().includes(q);

            var matchesTags = true;
            if (selectedCredTags.length > 0) {
                var cardTags = tags.split(',').map(function(t) { return t.trim(); }).filter(function(t) { return t.length > 0; });
                if (cardTags.length === 0) {
                    matchesTags = false;
                } else {
                    matchesTags = false;
                    for (var i = 0; i < selectedCredTags.length; i++) {
                        if (cardTags.indexOf(selectedCredTags[i]) !== -1) { matchesTags = true; break; }
                    }
                }
            }

            if (matchesSearch && matchesTags) {
                $(this).attr('data-hidden','false').show(); count++;
            } else {
                $(this).attr('data-hidden','true').hide();
            }
        });
        $('#cred-visible-count').text(count);
        highlightCredTags(selectedCredTags);
    }

    function highlightCredTags(selected) {
        $('.tag-badge').each(function() {
            var tagText = $(this).text().trim();
            $(this).removeClass('bg-primary bg-secondary');
            $(this).addClass(selected.includes(tagText) ? 'bg-primary' : 'bg-secondary');
        });
    }

    $('#cred_search').on('keyup', function() { applyCredFilters(); });

    setTimeout(function initCredSelectize() {
        var el = $('#cred_tag_filterpillBox');
        if (el.length > 0 && el[0].selectize) {
            credTagSelectize = el[0].selectize;
            allCredTags = Object.keys(credTagSelectize.options);
            var saved = loadCredTagSelection();
            if (saved.length > 0) {
                credTagSelectize.setValue(saved, true);
                selectedCredTags = saved;
            }
            applyCredFilters();
            credTagSelectize.on('change', function(value) {
                selectedCredTags = Array.isArray(value) ? value : (value ? value.split(',') : []);
                saveCredTagSelection(selectedCredTags);
                applyCredFilters();
            });
            credTagSelectize.on('item_remove', function() {
                setTimeout(function() {
                    var cur = credTagSelectize.getValue();
                    selectedCredTags = Array.isArray(cur) ? cur : (cur ? cur.split(',') : []);
                    saveCredTagSelection(selectedCredTags);
                    applyCredFilters();
                }, 10);
            });
            $('#reset-cred-tag-filter').on('click', function() {
                credTagSelectize.clear(true);
                selectedCredTags = [];
                saveCredTagSelection(selectedCredTags);
                applyCredFilters();
            });
        } else if (el.length > 0) {
            setTimeout(initCredSelectize, 500);
        }
    }, 300);
});
JS);

$oPage->addItem(new PageBottom());

$oPage->draw();
