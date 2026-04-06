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
    ->where('apps.enabled', 1)
    ->orderBy('COALESCE(app_translations.name, apps.name)')
    ->fetchAll();

// Search box
$contentContainer = new DivTag();
$contentContainer->addItem(new H2Tag(_('Applications')));

$searchBox = new InputSearchTag('app_search', '', [
    'placeholder' => _('Search applications...'),
    'class' => 'form-control form-control-lg mb-3',
    'id' => 'app_search',
]);
$contentContainer->addItem($searchBox);

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
        'data-tags' => mb_strtolower($topicsDataAttr),
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
        $topicBadges = new DivTag(null, ['class' => 'mt-2']);

        foreach ($topicsList as $topic) {
            if (!empty($topic)) {
                $topicBadges->addItem(new Badge($topic, 'secondary', ['class' => 'me-1 mb-1']));
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
CSS);

// JavaScript - live search
$oPage->addJavaScript(<<<'JS'
$(document).ready(function() {
    // Click card to navigate to detail
    $('.app-card').click(function() {
        var link = $(this).find('a').attr('href');
        if (link) window.location.href = link;
    });

    // Live search by name, description, and tags
    $('#app_search').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        var visibleCount = 0;
        $('.app-card-wrapper').each(function() {
            var appName = $(this).data('app-name') || '';
            var appDesc = $(this).data('app-desc') || '';
            var appTags = $(this).data('tags') || '';
            if (appName.includes(searchText) || appDesc.includes(searchText) || appTags.includes(searchText)) {
                $(this).attr('data-hidden', 'false').show();
                visibleCount++;
            } else {
                $(this).attr('data-hidden', 'true').hide();
            }
        });
        $('#visible-count').text(visibleCount);
    });
});
JS);

$oPage->addItem(new PageBottom());
$oPage->draw();
