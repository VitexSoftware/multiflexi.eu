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

$oPage->addItem(new PageTop(_('MultiFlexi Hub')));

// Hero section
$heroRow = new \Ease\TWB5\Row();
$heroRow->addTagClass('justify-content-md-center mb-4');
$heroRow->addColumn('2');
$heroRow->addColumn('8', new \Ease\Html\DivTag([
    new \Ease\Html\H2Tag(_('MultiFlexi Application & Credential Hub'), ['class' => 'text-center']),
    new \Ease\Html\PTag(
        _('Browse, share and discover applications and credential type definitions for MultiFlexi. Sign in to submit your own.'),
        ['class' => 'text-center text-muted'],
    ),
],));
$heroRow->addColumn('2');
$oPage->container->addItem($heroRow);

// Quick action buttons
$actionRow = new \Ease\TWB5\Row();
$actionRow->addTagClass('justify-content-center mb-4');
$actionRow->addColumn(3, new \Ease\TWB5\LinkButton('apps.php', '🧩 '._('Browse Applications'), 'primary btn-lg w-100'));
$actionRow->addColumn(3, new \Ease\TWB5\LinkButton('credentialtypes.php', '🔑 '._('Browse Credential Types'), 'primary btn-lg w-100'));
$actionRow->addColumn(3, new \Ease\TWB5\LinkButton('install.php', '📦 '._('Install MultiFlexi'), 'warning btn-lg w-100'));
$oPage->container->addItem($actionRow);

$appQuery = (new \MultiFlexi\Hub\Application())->listingQuery()->orderBy('DatCreate DESC');
$oPage->container->addItem(new \Ease\Html\H3Tag(_('Recent Applications'), ['class' => 'mt-4']));

$recentAppsData = $appQuery->limit(6)->orderBy('DatUpdate DESC')->fetchAll();

if (!empty($recentAppsData)) {
    $appsRow = new \Ease\TWB5\Row();

    foreach ($recentAppsData as $appData) {
        $card = new \Ease\Html\DivTag(null, ['class' => 'card h-100']);
        $cardBody = $card->addItem(new \Ease\Html\DivTag(null, ['class' => 'card-body']));

        $imgSrc = !empty($appData['uuid']) ? 'appimage.php?uuid='.$appData['uuid'] : 'images/apps.svg';
        $cardBody->addItem(new \Ease\Html\ImgTag($imgSrc, $appData['name'], ['height' => '40', 'class' => 'mb-2']));
        $cardBody->addItem(new \Ease\Html\H5Tag(
            new \Ease\Html\ATag('app.php?id='.$appData['id'], $appData['name']),
            ['class' => 'card-title'],
        ));
        $cardBody->addItem(new \Ease\Html\PTag(
            mb_strimwidth((string) $appData['description'], 0, 100, '...'),
            ['class' => 'card-text text-muted small'],
        ));

        if (!empty($appData['tags'])) {
            $tags = explode(',', (string) $appData['tags']);
            $tagsDiv = new \Ease\Html\DivTag(null, ['class' => 'mb-2 tags-container']);

            foreach ($tags as $tag) {
                $tagsDiv->addItem(new \Ease\Html\SpanTag(trim($tag), ['class' => 'badge bg-info me-1 tag-badge']));
            }

            $cardBody->addItem($tagsDiv);
        }

        if (!empty($appData['version'])) {
            $cardBody->addItem(new \Ease\Html\SpanTag('v'.$appData['version'], ['class' => 'badge bg-secondary']));
        }

        $appsRow->addColumn(2, new \Ease\Html\DivTag($card, ['class' => 'mb-3 app-card-wrapper']));
    }

    $oPage->container->addItem($appsRow);
} else {
    $oPage->container->addItem(new \Ease\Html\PTag(_('No applications submitted yet. Be the first!'), ['class' => 'text-muted']));
}

$oPage->container->addItem(new \Ease\TWB5\LinkButton('apps.php', _('View all applications').' →', 'outline-primary mb-4'));

// Recent Credential Types
$oPage->container->addItem(new \Ease\Html\H3Tag(_('Recent Credential Types'), ['class' => 'mt-4']));

$recentCreds = new \MultiFlexi\Hub\CredentialProtoType();
$recentCredsData = $recentCreds->listingQuery()->orderBy('created_at DESC')->limit(6)->fetchAll();

if (!empty($recentCredsData)) {
    $credsRow = new \Ease\TWB5\Row();

    foreach ($recentCredsData as $credData) {
        $card = new \Ease\Html\DivTag(null, ['class' => 'card h-100']);
        $cardBody = $card->addItem(new \Ease\Html\DivTag(null, ['class' => 'card-body text-center']));
        $cardBody->addItem(new \Ease\Html\ImgTag(
            \MultiFlexi\Hub\CredentialProtoType::logoUrl($credData),
            $credData['name'],
            ['style' => 'max-width: 64px; max-height: 64px;', 'class' => 'mb-2'],
        ));
        $cardBody->addItem(new \Ease\Html\H5Tag(
            new \Ease\Html\ATag('credentialtype.php?id='.$credData['id'], $credData['name']),
            ['class' => 'card-title'],
        ));
        $cardBody->addItem(new \Ease\Html\PTag(
            new \Ease\Html\CodeTag($credData['code']),
            ['class' => 'card-text'],
        ));

        if (!empty($credData['description'])) {
            $cardBody->addItem(new \Ease\Html\PTag(
                mb_strimwidth((string) $credData['description'], 0, 100, '...'),
                ['class' => 'card-text text-muted small'],
            ));
        }

        if (!empty($credData['version'])) {
            $cardBody->addItem(new \Ease\Html\SpanTag('v'.$credData['version'], ['class' => 'badge bg-secondary']));
        }

        $credsRow->addColumn(2, new \Ease\Html\DivTag($card, ['class' => 'mb-3']));
    }

    $oPage->container->addItem($credsRow);
} else {
    $oPage->container->addItem(new \Ease\Html\PTag(_('No credential types submitted yet. Be the first!'), ['class' => 'text-muted']));
}

$oPage->container->addItem(new \Ease\TWB5\LinkButton('credentialtypes.php', _('View all credential types').' →', 'outline-primary mb-4'));

$oPage->addItem(new PageBottom());

$oPage->draw();
