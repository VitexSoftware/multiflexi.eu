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
use Ease\Html\H2Tag;
use Ease\Html\H4Tag;
use Ease\Html\InputSearchTag;
use Ease\Html\PTag;
use Ease\Html\SmallTag;
use Ease\TWB5\Badge;
use Ease\TWB5\Card;
use Ease\TWB5\Row;

require_once __DIR__.'/init.php';

$oPage->addItem(new PageTop(_('Applications')));

// Fetch all applications with localized names
$apper = new \MultiFlexi\Application();
$currentLang = substr(\Ease\Locale::$localeUsed ?? 'en_US', 0, 2);

$allApps = $apper->getFluentPDO()
    ->from('apps')
    ->select('apps.id, apps.name, apps.description, apps.uuid, apps.image, apps.tags, apps.homepage, apps.version')
    ->select('COALESCE(app_translations.name, apps.name) AS localized_name')
    ->select('COALESCE(app_translations.description, apps.description) AS localized_description')
    ->leftJoin('app_translations ON app_translations.app_id = apps.id AND app_translations.lang = ?', $currentLang)
    ->orderBy('COALESCE(app_translations.name, apps.name)')
    ->fetchAll();

// Collect all unique tags from applications
$allTags = [];

foreach ($allApps as $app) {
    if (!empty($app['tags'])) {
        foreach (explode(',', $app['tags']) as $tag) {
            $tag = trim($tag);

            if (!empty($tag) && !isset($allTags[$tag])) {
                $allTags[$tag] = ['id' => $tag, 'name' => $tag];
            }
        }
    }
}

ksort($allTags);
$allTags = array_values($allTags);

// Search box
$contentContainer = new DivTag();
$contentContainer->addItem(new H2Tag(_('Applications')));

$searchBox = new InputSearchTag('app_search', '', [
    'placeholder' => _('Search applications...'),
    'class' => 'form-control form-control-lg mb-3',
    'id' => 'app_search',
]);
$contentContainer->addItem($searchBox);

// Tag filter using PillBox
if (!empty($allTags)) {
    $oPage->includeJavaScript('js/selectize.min.js');
    $oPage->includeCss('css/selectize.bootstrap5.css');

    $contentContainer->addItem(new H4Tag(_('Filter by Tags')));
    $contentContainer->addItem(new PTag(_('Select tags to filter applications. All applications are shown when no tags are selected.')));

    $filterRow = new Row();
    $tagFilter = new PillBox('tag_filter', $allTags, [], [
        'class' => 'form-control mb-2',
        'placeholder' => _('Select tags to filter applications...'),
    ]);
    $filterRow->addColumn(10, $tagFilter);

    $resetButton = new \Ease\Html\ButtonTag(_('Reset Filter'), [
        'class' => 'btn btn-outline-secondary mb-2',
        'type' => 'button',
        'id' => 'reset-tag-filter',
        'title' => _('Select all tags to show all applications'),
    ]);
    $filterRow->addColumn(2, $resetButton);
    $contentContainer->addItem($filterRow);
    $contentContainer->addItem(new DivTag('', ['class' => 'mb-4']));
}

// Count display
$countDiv = new DivTag(
    new SmallTag(['<strong id="visible-count">'.\count($allApps).'</strong> ', _('applications')], ['class' => 'text-muted']),
    ['class' => 'mb-3'],
);
$contentContainer->addItem($countDiv);

// Build card grid
$cardsRow = new Row();

foreach ($allApps as $app) {
    $displayName = $app['localized_name'] ?? $app['name'];
    $displayDescription = $app['localized_description'] ?? $app['description'] ?? '';
    $topicsList = !empty($app['tags']) ? array_map('trim', explode(',', $app['tags'])) : [];
    $topicsDataAttr = implode(',', $topicsList);

    $cardWrapper = new DivTag(null, [
        'class' => 'col-md-4 col-lg-3 mb-3 app-card-wrapper',
        'data-app-name' => mb_strtolower($displayName),
        'data-app-desc' => mb_strtolower($displayDescription),
        'data-tags' => $topicsDataAttr,
    ]);

    $card = new Card(null, ['class' => 'h-100 app-card']);
    $cardBody = new DivTag(null, ['class' => 'card-body text-center']);

    // App logo
    $logoDiv = new DivTag(null, ['class' => 'my-3']);
    $appImage = empty($app['image']) ? 'appimage.php?uuid='.$app['uuid'] : $app['image'];
    $logoDiv->addItem(new \Ease\Html\ImgTag($appImage, $displayName, ['style' => 'max-width: 80px; max-height: 80px;']));
    $cardBody->addItem($logoDiv);

    // App name with link
    $nameTag = new \Ease\Html\H5Tag(null, ['class' => 'card-title']);
    $nameTag->addItem(new \Ease\Html\ATag('app.php?id='.$app['id'], $displayName, ['class' => 'text-decoration-none']));
    $cardBody->addItem($nameTag);

    // Description
    if (!empty($displayDescription)) {
        $desc = mb_strlen($displayDescription) > 100 ? mb_substr($displayDescription, 0, 97).'...' : $displayDescription;
        $cardBody->addItem(new PTag(new SmallTag($desc, ['class' => 'text-muted']), ['class' => 'card-text']));
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

    $card->addItem($cardBody);
    $cardWrapper->addItem($card);
    $cardsRow->addItem($cardWrapper);
}

$contentContainer->addItem($cardsRow);
$oPage->container->addItem($contentContainer);

// CSS
$oPage->addCSS(<<<'CSS'
.app-card {
    cursor: pointer;
    transition: all 0.2s;
}
.app-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
.app-card-wrapper[data-hidden="true"] {
    display: none;
}
.tag-badge {
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    position: relative;
}
.tag-badge.bg-primary {
    font-weight: bold !important;
    box-shadow: 0 2px 6px rgba(0,123,255,0.4) !important;
    transform: scale(1.1) !important;
    z-index: 2 !important;
}
.tag-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.tag-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    justify-content: center;
}
CSS);

// JavaScript - live search and tag filtering
$oPage->addJavaScript(<<<'JS'
$(document).ready(function() {
    // Click card to navigate to detail
    $('.app-card').click(function() {
        var link = $(this).find('a').attr('href');
        if (link) window.location.href = link;
    });

    // Tag filtering with localStorage support
    const STORAGE_KEY = 'multiflexi_eu_tag_filter';
    const DEFAULT_ALL_SELECTED = 'all_tags_selected';

    var tagFilterSelectize = null;
    var allAvailableTags = [];
    var selectedTags = [];

    function saveTagSelection(tags) {
        try {
            if (tags.length === allAvailableTags.length) {
                localStorage.setItem(STORAGE_KEY, DEFAULT_ALL_SELECTED);
            } else {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(tags));
            }
        } catch (e) {}
    }

    function loadTagSelection() {
        try {
            var saved = localStorage.getItem(STORAGE_KEY);
            if (!saved || saved === DEFAULT_ALL_SELECTED) {
                return [];
            }
            var parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) {
                return parsed.filter(function(t) { return allAvailableTags.includes(t); });
            }
            return [];
        } catch (e) {
            return [];
        }
    }

    function applyFilters() {
        var searchText = $('#app_search').val().toLowerCase();
        var visibleCount = 0;

        $('.app-card-wrapper').each(function() {
            var appName = $(this).data('app-name') || '';
            var appDesc = $(this).data('app-desc') || '';
            var appTags = $(this).attr('data-tags') || '';

            // Text search filter
            var matchesSearch = !searchText ||
                appName.includes(searchText) ||
                appDesc.toString().toLowerCase().includes(searchText) ||
                appTags.toLowerCase().includes(searchText);

            // Tag filter: show all when no tags selected, filter when tags chosen
            var matchesTags = true;
            if (selectedTags.length > 0) {
                var cardTags = appTags.split(',').map(function(t) { return t.trim(); }).filter(function(t) { return t.length > 0; });
                if (cardTags.length === 0) {
                    matchesTags = false;
                } else {
                    matchesTags = false;
                    for (var i = 0; i < selectedTags.length; i++) {
                        if (cardTags.indexOf(selectedTags[i]) !== -1) {
                            matchesTags = true;
                            break;
                        }
                    }
                }
            }

            if (matchesSearch && matchesTags) {
                $(this).attr('data-hidden', 'false').show();
                visibleCount++;
            } else {
                $(this).attr('data-hidden', 'true').hide();
            }
        });

        $('#visible-count').text(visibleCount);
        highlightSelectedTags(selectedTags);
    }

    function highlightSelectedTags(selected) {
        $('.tag-badge').each(function() {
            var tagText = $(this).text().trim();
            $(this).removeClass('bg-primary bg-secondary');
            if (selected.includes(tagText)) {
                $(this).addClass('bg-primary');
            } else {
                $(this).addClass('bg-secondary');
            }
        });
    }

    // Live search
    $('#app_search').on('keyup', function() {
        applyFilters();
    });

    // Tag filtering with Selectize
    setTimeout(function initSelectize() {
        var element = $('#tag_filterpillBox');
        if (element.length > 0 && element[0].selectize) {
            tagFilterSelectize = element[0].selectize;
            allAvailableTags = Object.keys(tagFilterSelectize.options);

            var savedSelection = loadTagSelection();
            if (savedSelection.length > 0) {
                tagFilterSelectize.setValue(savedSelection, true);
                selectedTags = savedSelection;
            }
            applyFilters();

            tagFilterSelectize.on('change', function(value) {
                selectedTags = Array.isArray(value) ? value : (value ? value.split(',') : []);
                saveTagSelection(selectedTags);
                applyFilters();
            });

            tagFilterSelectize.on('item_remove', function() {
                setTimeout(function() {
                    var current = tagFilterSelectize.getValue();
                    selectedTags = Array.isArray(current) ? current : (current ? current.split(',') : []);
                    saveTagSelection(selectedTags);
                    applyFilters();
                }, 10);
            });

            $('#reset-tag-filter').on('click', function() {
                tagFilterSelectize.clear(true);
                selectedTags = [];
                saveTagSelection(selectedTags);
                applyFilters();
            });
        } else if (element.length > 0) {
            setTimeout(initSelectize, 500);
        }
    }, 300);
});
JS);

$oPage->addItem(new PageBottom());
$oPage->draw();
