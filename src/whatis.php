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

use Ease\Html\ATag;
use Ease\Html\DivTag;
use Ease\Html\H2Tag;
use Ease\Html\H3Tag;
use Ease\Html\H4Tag;
use Ease\Html\ImgTag;
use Ease\Html\LiTag;
use Ease\Html\PTag;
use Ease\Html\UlTag;
use Ease\TWB5\Accordion;
use Ease\TWB5\LinkButton;
use Ease\TWB5\Row;

require_once __DIR__.'/init.php';

$oPage->addItem(new PageTop(_('What is MultiFlexi?')));

// --- Hero ---
$hero = new DivTag(null, ['class' => 'text-center mb-4']);
$hero->addItem(new H2Tag(_('What is MultiFlexi?'), ['class' => 'mb-3']));
$hero->addItem(new PTag(
    _('MultiFlexi is an open-source task scheduling and automation platform for business system integrations. It lets organizations run applications — importers, exporters, reports, health checks — on a schedule across multiple companies, with isolated credentials and full execution history.'),
    ['class' => 'lead text-muted'],
));
$oPage->container->addItem($hero);

$oPage->container->addItem(new DivTag(
    new ImgTag('images/overview/dashboard.png', _('MultiFlexi dashboard'), ['class' => 'img-fluid rounded shadow-sm']),
    ['class' => 'text-center mb-4'],
));

$oPage->container->addItem(new PTag(
    _('Think of it as cron on steroids for business automation: instead of managing scattered shell scripts and cron entries, you register applications once, assign them to companies with the right credentials, and let MultiFlexi handle scheduling, execution, output capture, and monitoring.'),
    ['class' => 'mb-4'],
));

// --- Who is it for? ---
$oPage->container->addItem(new H3Tag(_('Who is it for?'), ['class' => 'mt-5 mb-3']));

$audienceRow = new Row();
$audiences = [
    [_('Accounting Firms'), _('Automating daily tasks for dozens of clients — bank imports, invoice generation, report exports — each running with that client\'s own credentials.')],
    [_('Managed Service Providers'), _('Running system health checks, backup verifications, and compliance reports across customer environments on schedule.')],
    [_('Internal IT Teams'), _('Synchronizing data between systems, dispatching notifications, and maintaining databases without hand-written automation glue.')],
];

foreach ($audiences as $audience) {
    $col = new DivTag(null, ['class' => 'card h-100']);
    $body = new DivTag(null, ['class' => 'card-body']);
    $body->addItem(new H4Tag($audience[0], ['class' => 'card-title']));
    $body->addItem(new PTag($audience[1], ['class' => 'card-text text-muted']));
    $col->addItem($body);
    $audienceRow->addColumn(4, new DivTag($col, ['class' => 'mb-3']));
}

$oPage->container->addItem($audienceRow);

// --- Key Concepts ---
$oPage->container->addItem(new H3Tag(_('Key Concepts'), ['class' => 'mt-5 mb-3']));
$oPage->container->addItem(new PTag(
    _('MultiFlexi is built around five core entities that work together:'),
    ['class' => 'mb-3'],
));

// Diagram
$oPage->container->addItem(new DivTag(
    new ImgTag('images/overview/entity-diagram.svg', _('MultiFlexi entity relationships'), ['class' => 'img-fluid', 'style' => 'max-width: 700px;']),
    ['class' => 'text-center mb-4'],
));

// Concept cards
$concepts = [
    [_('Company'), _('A tenant or organization you automate tasks for. Each company has its own set of credentials and job history, ensuring full data isolation.'), null],
    [_('Application'), _('A tool that MultiFlexi can execute — a CLI binary, a PHP script, a Docker image. Applications are registered once and can be assigned to any company.'), 'images/overview/apps-listing.png'],
    [_('Credential'), _('Securely stored authentication data (API keys, database passwords, SMTP accounts). Credentials are scoped to a company and encrypted at rest with AES-256.'), 'images/overview/credential-types.png'],
    [_('RunTemplate'), _('The link between an application and a company: "run this app for this company on this schedule with these credentials."'), 'images/overview/runtemplates.png'],
    [_('Job'), _('A single execution of a RunTemplate. MultiFlexi records the start time, exit code, stdout, stderr, and any output files as searchable artifacts.'), null],
];

$conceptsAccordion = new Accordion('accordionConcepts', [], false, ['class' => 'mb-4']);

foreach ($concepts as $i => $concept) {
    $body = new DivTag(null);
    $body->addItem(new PTag($concept[1], ['class' => 'text-muted']));

    if (!empty($concept[2])) {
        $body->addItem(new DivTag(
            new ImgTag($concept[2], $concept[0], ['class' => 'img-fluid rounded shadow-sm']),
            ['class' => 'text-center my-3'],
        ));
    }

    $conceptsAccordion->addAccordionItem($concept[0], $body, $i === 0);
}

$oPage->container->addItem($conceptsAccordion);

// --- How It Works ---
$oPage->container->addItem(new H3Tag(_('How It Works'), ['class' => 'mt-5 mb-3']));

$steps = [
    _('Register an application — import its JSON definition or create it in the web UI.'),
    _('Add a company — create a tenant representing the organization whose tasks you\'re automating.'),
    _('Assign credentials — store the company\'s API keys, database passwords, or SMTP accounts. MultiFlexi encrypts them and injects them as environment variables at runtime.'),
    _('Create a RunTemplate — pick the application, the company, the schedule, and the credentials.'),
    _('Jobs run automatically — the scheduler daemon creates job records when the schedule is due, and the executor daemon launches the application and captures all output.'),
];

$stepsList = new \Ease\Html\OlTag(null, ['class' => 'list-group list-group-numbered mb-4']);

foreach ($steps as $step) {
    $stepsList->addItem(new LiTag($step, ['class' => 'list-group-item']));
}

$oPage->container->addItem($stepsList);

// --- Execution Environments ---
$oPage->container->addItem(new H3Tag(_('Execution Environments'), ['class' => 'mt-5 mb-3']));
$oPage->container->addItem(new PTag(
    _('MultiFlexi can launch applications in several ways:'),
    ['class' => 'mb-3'],
));

$envs = [
    [_('Native'), _('run the command directly on the server')],
    [_('Docker'), _('run inside a Docker container with the application\'s OCI image')],
    [_('Podman'), _('rootless container execution')],
    [_('Kubernetes'), _('launch as a K8s pod')],
    [_('Azure Container Instances'), _('run in the cloud')],
];

$envAccordion = new Accordion('accordionEnvs', [], true, ['class' => 'mb-4']);

foreach ($envs as $i => $env) {
    $envAccordion->addAccordionItem($env[0], new PTag($env[1], ['class' => 'text-muted mb-0']), $i === 0);
}

$oPage->container->addItem($envAccordion);

$oPage->container->addItem(new PTag(
    _('The executor is pluggable: each RunTemplate can use a different execution backend depending on the application\'s requirements.'),
    ['class' => 'text-muted mb-4'],
));

// --- Multiple Interfaces ---
$oPage->container->addItem(new H3Tag(_('Multiple Interfaces'), ['class' => 'mt-5 mb-3']));
$oPage->container->addItem(new PTag(
    _('You interact with MultiFlexi through whichever interface fits your workflow:'),
    ['class' => 'mb-3'],
));

$interfaces = [
    [_('Web UI'), _('Bootstrap 5 dashboard with real-time metrics, company/application/credential management, job history, and live output streaming via WebSocket.')],
    [_('CLI'), _('Full-featured command-line tool for scripting, CI/CD pipelines, and headless administration.').' (multiflexi-cli)'],
    [_('TUI'), _('Interactive terminal interface built with Bubbletea (Go) for keyboard-driven management.').' (multiflexi-tui)'],
    [_('REST API'), _('JSON/XML/YAML endpoints with HTTP Basic and token authentication for programmatic integration.')],
    [_('MCP Server'), _('Model Context Protocol server for AI agent integration.')],
];

$ifaceRow = new Row();

foreach ($interfaces as $iface) {
    $card = new DivTag(null, ['class' => 'card h-100']);
    $body = new DivTag(null, ['class' => 'card-body']);
    $body->addItem(new H4Tag($iface[0], ['class' => 'card-title']));
    $body->addItem(new PTag($iface[1], ['class' => 'card-text text-muted small']));
    $card->addItem($body);
    $ifaceRow->addColumn(4, new DivTag($card, ['class' => 'mb-3']));
}

$oPage->container->addItem($ifaceRow);

// --- Monitoring ---
$oPage->container->addItem(new H3Tag(_('Monitoring and Observability'), ['class' => 'mt-5 mb-3']));

$monList = new UlTag(null, ['class' => 'list-group mb-4']);
$monItems = [
    _('Zabbix integration with Low-Level Discovery — auto-discovers companies and applications, monitors job success rates and execution times'),
    _('OpenTelemetry support for distributed tracing'),
    _('Structured logging to syslog, database, and custom targets'),
    _('Universal artifact preservation — every job\'s stdout, stderr, and output files are stored and searchable'),
];

foreach ($monItems as $item) {
    $monList->addItem(new LiTag($item, ['class' => 'list-group-item']));
}

$oPage->container->addItem($monList);

// --- Getting Started ---
$oPage->container->addItem(new H3Tag(_('Getting Started'), ['class' => 'mt-5 mb-3']));
$oPage->container->addItem(new PTag(_('Ready to try it?'), ['class' => 'mb-3']));

$ctaRow = new Row();
$ctaRow->addColumn(3, new LinkButton('apps.php', '🧩 '._('Browse Apps'), 'primary btn-lg w-100 mb-2'));
$ctaRow->addColumn(3, new LinkButton('credentialtypes.php', '🔑 '._('Credential Types'), 'primary btn-lg w-100 mb-2'));
$ctaRow->addColumn(3, new LinkButton('install.php', '📦 '._('Install'), 'warning btn-lg w-100 mb-2'));
$ctaRow->addColumn(3, new LinkButton('https://demo.multiflexi.eu', '🚀 '._('Try Demo'), 'success btn-lg w-100 mb-2'));
$oPage->container->addItem($ctaRow);

$linksDiv = new DivTag(null, ['class' => 'mt-4 mb-4']);
$linksDiv->addItem(new UlTag([
    new LiTag(new ATag('https://github.com/VitexSoftware/MultiFlexi', _('MultiFlexi on GitHub'), ['target' => '_blank'])),
    new LiTag(new ATag('/doc/', _('Documentation'))),
    new LiTag(new ATag('https://demo.multiflexi.eu', _('Demo Instance — try MultiFlexi without installing'), ['target' => '_blank'])),
]));
$oPage->container->addItem($linksDiv);

$oPage->addItem(new PageBottom());
$oPage->draw();
