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

/**
 * Homepage sections: MultiFlexi at the centre of an orbiting system of banks, AbraFlexi,
 * Pohoda and e-mail, two ways to use it (turnkey / self-hosted), audiences, how it works,
 * the app & credential catalogue from the database, interfaces, pricing, people and CTA.
 *
 * Texts reuse the msgids of whatis.php where possible, so their translations apply.
 */
class Landing
{
    private const ORBIT_INNER = 'M1382 362 A280 250 -14 1 1 838 498 A280 250 -14 1 1 1382 362';
    private const ORBIT_OUTER = 'M1459 343 A360 300 -14 1 1 761 517 A360 300 -14 1 1 1459 343';
    private const ICONS = [
        'bank' => 'M-7 -1.5h14M-5 -1.5v6m3.3-6v6m3.4-6v6m3.3-6v6M-7 5.5h14M0 -7.5l7.5 4.5h-15z',
        'doc' => 'M-6 -7.5h12v15h-12zM-3 -3h6M-3 0.5h6M-3 4h4',
        'pohoda' => 'M6.5 0a6.5 6.5 0 1 1-6.5-6.5M6.5-6.5L0 0',
        'mail' => 'M-7.5 -5h15v10h-15zM-7.5 -4.5l7.5 5.5 7.5-5.5',
    ];

    public static function hero(): string
    {
        $t = static fn (string $text): string => _($text);
        $orbiting = self::satellite($t('Bank'), 'bank', '--p2', self::ORBIT_INNER, 30, 0)
            .self::satellite('AbraFlexi', 'doc', '--p1', self::ORBIT_INNER, 30, -15)
            .self::satellite('Pohoda', 'pohoda', '--p4', self::ORBIT_OUTER, 44, -10)
            .self::satellite($t('E-mail'), 'mail', '--p3', self::ORBIT_OUTER, 44, -30)
            .self::pulse(self::ORBIT_INNER, 7, 0, '--p2')
            .self::pulse(self::ORBIT_INNER, 7, -3.5, '--p4')
            .self::pulse(self::ORBIT_OUTER, 9, -2, '--p3');

        $feed = '';

        foreach ([
            ['--p2', 'bank', $t('Fio bank statement imported'), $t('Pohoda · 23 transactions'), '06:00'],
            ['--p1', 'link', $t('Payments matched to invoices'), $t('AbraFlexi · by variable symbol'), '06:02'],
            ['--p3', 'mail', $t('Payment reminders sent'), $t('only to overdue customers'), '07:30'],
            ['--p5', 'down', $t('Received invoices from e-mail'), $t('loaded into AbraFlexi'), '08:00'],
            ['--p4', 'chart', $t('Weekly digest for the management'), $t('receivables and cash flow'), '08:15'],
            ['--p2', 'check', $t('Health check passed'), $t('Zabbix · all companies'), '09:00'],
        ] as [$color, $icon, $title, $detail, $time]) {
            $feed .= '<div class="cycle-item" style="--c:var('.$color.')"><div class="ic">'.self::uiIcon($icon).'</div><div><b>'.$title.'</b><span>'.$detail.'</span></div><time>'.$time.'</time></div>';
        }

        $inner = self::ORBIT_INNER;
        $outer = self::ORBIT_OUTER;

        return <<<HTML
<section class="hero">
  <div class="landscape" aria-hidden="true">
    <svg viewBox="0 0 1440 900" preserveAspectRatio="xMidYMax slice">
      <defs>
        <linearGradient id="bgG" x1="0" y1="0" x2="0" y2="1"><stop offset="0" style="stop-color:var(--art-bg1)"/><stop offset="1" style="stop-color:var(--art-bg2)"/></linearGradient>
        <filter id="blurG" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="70"/></filter>
        <filter id="glowG" x="-200%" y="-200%" width="500%" height="500%"><feGaussianBlur stdDeviation="3" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
        <linearGradient id="rimG" x1="0" y1="0" x2="1" y2="0"><stop offset="0" style="stop-color:var(--art-rim1)" stop-opacity="0"/><stop offset=".35" style="stop-color:var(--art-rim1)"/><stop offset=".7" style="stop-color:var(--art-rim2)"/><stop offset="1" style="stop-color:var(--art-rim2)" stop-opacity="0"/></linearGradient>
        <linearGradient id="shootG" x1="0" y1="0" x2="1" y2="0"><stop offset="0" style="stop-color:var(--text)" stop-opacity="0"/><stop offset="1" style="stop-color:var(--text)" stop-opacity=".9"/></linearGradient>
        <radialGradient id="ringPlanet" cx=".35" cy=".35" r=".8"><stop offset="0" style="stop-color:var(--p4)"/><stop offset="1" style="stop-color:var(--p2)"/></radialGradient>
      </defs>
      <rect width="1440" height="900" fill="url(#bgG)"/>
      <g filter="url(#blurG)" opacity=".7" class="nebula">
        <circle cx="1040" cy="330" r="210" style="fill:var(--art-n1)"/>
        <circle cx="1250" cy="470" r="170" style="fill:var(--art-n2)"/>
        <circle cx="880" cy="520" r="150" style="fill:var(--art-n3)"/>
        <circle cx="1320" cy="220" r="120" style="fill:var(--art-n4)"/>
      </g>
      <g class="stars" style="fill:var(--text)">
        <circle cx="120" cy="80" r="1.2" opacity=".7"/><circle cx="260" cy="210" r=".9" opacity=".5"/><circle cx="410" cy="120" r="1.4" opacity=".8"/><circle cx="560" cy="260" r=".8" opacity=".4"/>
        <circle cx="760" cy="60" r="1.1" opacity=".6"/><circle cx="880" cy="170" r="1.3" opacity=".7"/><circle cx="980" cy="90" r=".9" opacity=".5"/><circle cx="1120" cy="150" r="1.5" opacity=".9"/>
        <circle cx="1240" cy="70" r="1" opacity=".6"/><circle cx="1390" cy="330" r="1.2" opacity=".7"/><circle cx="660" cy="420" r=".8" opacity=".4"/><circle cx="330" cy="380" r="1" opacity=".5"/>
        <circle cx="40" cy="300" r="1" opacity=".5"/><circle cx="200" cy="640" r=".9" opacity=".4"/><circle cx="1420" cy="600" r="1.1" opacity=".6"/><circle cx="1000" cy="720" r=".8" opacity=".4"/>
      </g>
      <g class="peace-const" style="stroke:var(--text)" fill="none" stroke-width="1" stroke-linecap="round">
        <circle cx="640" cy="150" r="52" pathLength="100" class="draw"/>
        <path d="M640 98V202" pathLength="100" class="draw d2"/>
        <path d="M640 150L603 187M640 150L677 187" pathLength="100" class="draw d3"/>
        <g class="const-stars" style="fill:var(--text)" stroke="none">
          <circle cx="640" cy="98" r="2.2"/><circle cx="640" cy="202" r="2.2"/><circle cx="640" cy="150" r="2.6"/><circle cx="603" cy="187" r="1.8"/><circle cx="677" cy="187" r="1.8"/>
          <circle cx="588" cy="150" r="1.6"/><circle cx="692" cy="150" r="1.6"/><circle cx="603" cy="113" r="1.4"/><circle cx="677" cy="113" r="1.4"/>
        </g>
      </g>
      <line class="shoot s1" x1="0" y1="0" x2="120" y2="0" stroke="url(#shootG)" stroke-width="1.6" stroke-linecap="round"/>
      <line class="shoot s2" x1="0" y1="0" x2="90" y2="0" stroke="url(#shootG)" stroke-width="1.3" stroke-linecap="round"/>
      <g class="ringed">
        <ellipse cx="1375" cy="120" rx="38" ry="9" fill="none" style="stroke:var(--p3)" stroke-opacity=".7" stroke-width="2"/>
        <circle cx="1375" cy="120" r="17" fill="url(#ringPlanet)"/>
        <path d="M1337 120a38 9 0 0 0 76 0" fill="none" style="stroke:var(--p3)" stroke-width="2"/>
      </g>
      <g fill="none" style="stroke:var(--text)" stroke-opacity=".14" stroke-width="1">
        <path d="{$inner}"/>
        <path d="{$outer}" stroke-dasharray="3 7"/>
      </g>
      {$orbiting}
      <g class="ridge" data-depth="16">
        <circle cx="720" cy="2080" r="1300" style="fill:var(--art-planet)"/>
        <path d="M-580 2080 A1300 1300 0 0 1 2020 2080" fill="none" stroke="url(#rimG)" stroke-width="3"/>
      </g>
    </svg>
  </div>

  <div class="wrap hero-grid">
    <div class="hero-copy">
      <div class="eyebrow">Open source<i>/</i>{$t('Automation')}<i>/</i>{$t('Freedom')}</div>
      <h1>{$t('Automation that')} <span class="grad">{$t('runs by itself.')}</span></h1>
      <p class="lead">{$t('MultiFlexi runs, schedules and watches your integrations on top of AbraFlexi and Pohoda – for dozens of companies, with isolated credentials and a full history.')} <b>{$t('Cron on steroids for business automation.')}</b></p>
      <div class="hero-ctas">
        <a class="btn btn-glow" href="#cenik">{$t('Turnkey automation')} <span class="arrow">→</span></a>
        <a class="btn btn-line" href="install.php">{$t('Install for free')}</a>
      </div>
    </div>
    <div class="live" aria-label="{$t('Example of a MultiFlexi morning')}">
      <div class="live-head"><span class="dot"></span><b>MultiFlexi</b> {$t('this morning · example')}</div>
      <div class="cycle" data-cycle="4">{$feed}</div>
    </div>
  </div>
  <div class="hero-note hand">{$t('A better world starts with good software')} ☮</div>
</section>
HTML;
    }

    /**
     * Two ways to get MultiFlexi: set up and hosted by Vitex Software, or installed by yourself.
     */
    public static function paths(): string
    {
        $t = static fn (string $text): string => _($text);

        return <<<HTML
<section id="jak-zacit">
  <div class="wrap">
    <div class="head-row">
      <div class="reveal"><div class="eyebrow">{$t('Two ways to start')}</div><h2>{$t('We run it for you, or you run it yourself')}</h2></div>
      <div class="aside reveal" style="--d:1">{$t('Less manual work.')}<br>{$t('More time for what matters.')}</div>
    </div>
    <div class="duo">
      <div class="glass big-card reveal" style="--c:var(--p1)">
        <div class="badge-ic"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3l8 4v5c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V7z"/><path d="M8.5 12l2.5 2.5 4.5-5"/></svg></div>
        <h3>{$t('Turnkey from Vitex Software')}</h3>
        <div class="sub">{$t('We set it up, host it and watch over it')}</div>
        <p>{$t('78 ready-made automations for AbraFlexi and Pohoda: bank statements, payment matching, reminders, received invoices and digests. You only look at the results.')}</p>
        <div class="foot">
          <div class="hero-ctas">
            <a class="btn btn-glow btn-sm" href="#cenik">{$t('Pricing')} <span class="arrow">→</span></a>
            <a class="btn btn-line btn-sm" href="https://vitexsoftware.cz/kontakt.php">{$t('Get a free quote')}</a>
          </div>
        </div>
      </div>
      <div class="glass big-card reveal" style="--c:var(--p3);--d:1">
        <div class="badge-ic"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 17l6-6-6-6M12 19h8"/></svg></div>
        <h3>{$t('Open source, on your server')}</h3>
        <div class="sub">{$t('Free, MIT license')}</div>
        <p>{$t('Packages for Debian and Ubuntu with MySQL, PostgreSQL or SQLite. Add the repository and install:')}</p>
        <pre class="install-cmd"><code>sudo apt install multiflexi-mysql</code></pre>
        <div class="foot">
          <div class="hero-ctas">
            <a class="btn btn-line btn-sm" href="install.php">{$t('Installation guide')} <span class="arrow">→</span></a>
            <a class="btn btn-line btn-sm" href="https://github.com/VitexSoftware/MultiFlexi"><i class="fa-brands fa-github"></i> GitHub</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
HTML;
    }

    /**
     * Who is it for + how it works (texts of whatis.php).
     */
    public static function audience(): string
    {
        $t = static fn (string $text): string => _($text);
        $cards = '';

        foreach ([
            ['--p1', $t('Accounting Firms'), $t('Automating daily tasks for dozens of clients — bank imports, invoice generation, report exports — each running with that client\'s own credentials.')],
            ['--p2', $t('Managed Service Providers'), $t('Running system health checks, backup verifications, and compliance reports across customer environments on schedule.')],
            ['--p4', $t('Internal IT Teams'), $t('Synchronizing data between systems, dispatching notifications, and maintaining databases without hand-written automation glue.')],
        ] as $i => [$color, $title, $text]) {
            $cards .= '<div class="glass step reveal" style="--c:var('.$color.');--d:'.$i.'"><span class="n">0'.($i + 1).'</span><h3>'.$title.'</h3><p>'.$text.'</p></div>';
        }

        $steps = '';

        foreach ([
            $t('Register an application — import its JSON definition or create it in the web UI.'),
            $t('Add a company — create a tenant representing the organization whose tasks you\'re automating.'),
            $t('Assign credentials — store the company\'s API keys, database passwords, or SMTP accounts. MultiFlexi encrypts them and injects them as environment variables at runtime.'),
            $t('Create a RunTemplate — pick the application, the company, the schedule, and the credentials.'),
            $t('Jobs run automatically — the scheduler daemon creates job records when the schedule is due, and the executor daemon launches the application and captures all output.'),
        ] as $i => $text) {
            [$head, $rest] = array_map('trim', explode('—', $text, 2)) + [1 => ''];
            $steps .= '<li class="reveal" style="--d:'.$i.'"><span class="num">'.($i + 1).'</span><div><b>'.$head.'</b><p>'.$rest.'</p></div></li>';
        }

        return <<<HTML
<section id="pro-koho" style="padding-top:20px">
  <div class="wrap">
    <div class="head-row"><div class="reveal"><div class="eyebrow">{$t('Who is it for?')}</div><h2>{$t('Cron on steroids for business automation.')}</h2></div></div>
    <div class="steps">{$cards}</div>
    <div class="how glass reveal">
      <div><div class="eyebrow">{$t('How It Works')}</div><p class="dim">{$t('Think of it as cron on steroids for business automation: instead of managing scattered shell scripts and cron entries, you register applications once, assign them to companies with the right credentials, and let MultiFlexi handle scheduling, execution, output capture, and monitoring.')}</p><a class="link-more" href="whatis.php">{$t('What is MultiFlexi?')} <span class="arrow">→</span></a></div>
      <ol class="how-steps">{$steps}</ol>
    </div>
  </div>
</section>
HTML;
    }

    /**
     * Applications and credential types from the hub database.
     */
    public static function catalog(): string
    {
        $t = static fn (string $text): string => _($text);
        $currentLang = substr(\Ease\Locale::$localeUsed ?? 'en_US', 0, 2);

        try {
            $apper = new \MultiFlexi\Hub\Application();
            $appCount = (int) $apper->getFluentPDO()->from('apps')->count();
            $apps = $apper->getFluentPDO()
                ->from('apps')
                ->select('apps.id, apps.name, apps.description, apps.uuid, apps.version')
                ->select('COALESCE(app_translations.name, apps.name) AS localized_name')
                ->select('COALESCE(app_translations.description, apps.description) AS localized_description')
                ->leftJoin('app_translations ON app_translations.app_id = apps.id AND app_translations.lang = ?', $currentLang)
                ->orderBy('apps.DatUpdate DESC')
                ->limit(8)
                ->fetchAll();
            $credentialTypes = (new \MultiFlexi\Hub\CredentialProtoType())->listingQuery()->orderBy('created_at DESC')->limit(12)->fetchAll();
        } catch (\Throwable $exception) {
            return '';
        }

        $colors = ['--p1', '--p2', '--p3', '--p4', '--p5'];
        $cards = '';

        foreach ($apps as $i => $app) {
            $name = htmlspecialchars((string) ($app['localized_name'] ?? $app['name']));
            $description = htmlspecialchars(mb_strimwidth((string) ($app['localized_description'] ?? $app['description'] ?? ''), 0, 90, '…'));
            $image = !empty($app['uuid']) ? 'appimage.php?uuid='.urlencode((string) $app['uuid']) : 'images/apps.svg';
            $version = empty($app['version']) ? '' : '<span class="ver">v'.htmlspecialchars((string) $app['version']).'</span>';
            $cards .= '<a class="glass proj reveal" style="--c:var('.$colors[$i % 5].');--d:'.($i % 4).'" href="app.php?id='.(int) $app['id'].'">'
                .'<div class="top"><img src="'.$image.'" alt="" loading="lazy"><b>'.$name.'</b></div><p>'.$description.'</p>'.$version.'<span class="go" aria-hidden="true">→</span></a>';
        }

        $chips = '';

        foreach ($credentialTypes as $type) {
            $chips .= '<a class="chip-link" href="credentialtype.php?id='.(int) $type['id'].'"><img src="'.htmlspecialchars(\MultiFlexi\Hub\CredentialProtoType::logoUrl($type)).'" alt="" loading="lazy">'.htmlspecialchars((string) $type['name']).'</a>';
        }

        $appsTitle = sprintf(_('%d applications ready to run'), $appCount);

        return <<<HTML
<section id="aplikace">
  <div class="wrap">
    <div class="head-row">
      <div class="reveal"><div class="eyebrow">{$t('Apps')}</div><h2>{$appsTitle}</h2></div>
      <a class="link-more reveal" href="apps.php">{$t('View all applications')} <span class="arrow">→</span></a>
    </div>
    <div class="projects">{$cards}</div>
    <div class="cred-row reveal">
      <div><div class="eyebrow">{$t('Credential Types')}</div><p class="dim">{$t('Securely stored authentication data (API keys, database passwords, SMTP accounts). Credentials are scoped to a company and encrypted at rest with AES-256.')}</p></div>
      <div class="chips">{$chips}<a class="chip-link more" href="credentialtypes.php">{$t('View all credential types')} →</a></div>
    </div>
  </div>
</section>
HTML;
    }

    /**
     * Web UI, CLI, TUI, REST API, MCP (texts of whatis.php).
     */
    public static function interfaces(): string
    {
        $t = static fn (string $text): string => _($text);
        $items = '';

        foreach ([
            ['--p1', 'fa-solid fa-gauge-high', $t('Web UI'), $t('Bootstrap 5 dashboard with real-time metrics, company/application/credential management, job history, and live output streaming via WebSocket.')],
            ['--p2', 'fa-solid fa-terminal', $t('CLI'), $t('Full-featured command-line tool for scripting, CI/CD pipelines, and headless administration.')],
            ['--p4', 'fa-solid fa-keyboard', $t('TUI'), $t('Interactive terminal interface built with Bubbletea (Go) for keyboard-driven management.')],
            ['--p3', 'fa-solid fa-plug', $t('REST API'), $t('JSON/XML/YAML endpoints with HTTP Basic and token authentication for programmatic integration.')],
            ['--p5', 'fa-solid fa-robot', $t('MCP Server'), $t('Model Context Protocol server for AI agent integration.')],
        ] as $i => [$color, $icon, $title, $text]) {
            $items .= '<div class="glass iface reveal" style="--c:var('.$color.');--d:'.$i.'"><i class="'.$icon.'"></i><b>'.$title.'</b><p>'.$text.'</p></div>';
        }

        return <<<HTML
<section id="rozhrani" style="padding-top:0">
  <div class="wrap">
    <div class="head-row"><div class="reveal"><div class="eyebrow">{$t('Multiple Interfaces')}</div><h2>{$t('You interact with MultiFlexi through whichever interface fits your workflow:')}</h2></div></div>
    <div class="ifaces">{$items}</div>
  </div>
</section>
HTML;
    }

    public static function pricing(): string
    {
        $t = static fn (string $text): string => _($text);
        $plans = '';

        foreach ([
            ['', $t('Start'), $t('try one automation'), '290 '.$t('CZK'), $t('setup 2 900 CZK one-off'), [$t('1 automation of your choice'), $t('hosting included'), $t('tool updates'), $t('e-mail support')]],
            ['hot', $t('Operation'), $t('a typical company on AbraFlexi / Pohoda'), '690 '.$t('CZK'), $t('setup 4 900 CZK one-off'), [$t('2–3 automations of your choice'), $t('hosting + monitoring'), $t('automatic updates'), $t('priority support')]],
            ['', $t('Turnkey'), $t('complex operation and customisation'), $t('from').' 1 490 '.$t('CZK'), $t('setup from 9 900 CZK one-off'), [$t('unlimited automations'), $t('custom changes'), $t('priority SLA + phone'), $t('monthly report')]],
        ] as $i => [$class, $name, $for, $amount, $setup, $features]) {
            $tag = $class === 'hot' ? '<span class="tag">'.$t('most popular').'</span>' : '';
            $button = $class === 'hot' ? 'btn-glow' : 'btn-line';
            $plans .= '<div class="glass price '.$class.' reveal" style="--d:'.$i.'">'.$tag
                .'<h3>'.$name.'</h3><span class="dim">'.$for.'</span>'
                .'<div class="amount">'.$amount.' <small>/ '.$t('month').'</small></div>'
                .'<div class="setup">'.$setup.'</div>'
                .'<ul><li>'.implode('</li><li>', $features).'</li></ul>'
                .'<a class="btn '.$button.'" href="https://vitexsoftware.cz/kontakt.php">'.$t('I am interested').'</a></div>';
        }

        return <<<HTML
<section id="cenik">
  <div class="wrap">
    <div class="center-head reveal">
      <div class="eyebrow">{$t('Turnkey automation')}</div>
      <h2>{$t('One automation saves more than it costs')}</h2>
      <p class="dim" style="margin:12px 0 0">{$t('Set up, hosted and monitored by Vitex Software. Paying a year in advance = 2 months of operation free.')}</p>
    </div>
    <div class="pricing">{$plans}</div>
    <p class="fine">{$t('Prices are final, we are not VAT payers.')}</p>
  </div>
</section>
HTML;
    }

    public static function about(): string
    {
        $t = static fn (string $text): string => _($text);

        return <<<HTML
<section id="kdo" style="padding-top:30px">
  <div class="wrap">
    <div class="portrait-card reveal">
      <div class="portrait-media">
        <img src="images/vitex-portrait-1536.webp" srcset="images/vitex-portrait-900.webp 900w, images/vitex-portrait-1536.webp 1536w" sizes="(max-width: 900px) 100vw, 760px" alt="Vítězslav Dvořák" width="1536" height="1024" loading="lazy">
        <div class="portrait-tint"></div>
      </div>
      <div class="portrait-copy">
        <div class="eyebrow reveal" style="--d:1">{$t('Who is behind MultiFlexi')}</div>
        <blockquote class="reveal" style="--d:2">{$t('MultiFlexi is written, packaged and run by Vítězslav Dvořák from Vitex Software – everything as open source.')}</blockquote>
        <p class="reveal" style="--d:3">{$t('The same platform runs the automations of Vitex Software customers every day.')}</p>
        <div class="portrait-foot reveal" style="--d:4">
          <a class="btn btn-glow" href="https://vitexsoftware.cz/">vitexsoftware.cz <span class="arrow">→</span></a>
          <span class="hand signature">Vítězslav Dvořák ☮</span>
        </div>
      </div>
    </div>
  </div>
</section>
HTML;
    }

    public static function cta(): string
    {
        $t = static fn (string $text): string => _($text);
        $demo = MainMenu::DEMO_URL;

        return <<<HTML
<section id="kontakt" style="padding-top:30px">
  <div class="wrap">
    <div class="glass cta reveal">
      <img class="cta-avatar" src="images/multiflexi-logo.svg" alt="" width="96" height="96" style="padding:14px;object-fit:contain">
      <div class="hand" style="font-size:1.8rem;color:var(--p4);margin-bottom:8px">{$t('Ready to try it?')}</div>
      <h2>{$t('Click through the demo')} <span class="grad">{$t('or let us show you on your data.')}</span></h2>
      <p>{$t('Demo Instance — try MultiFlexi without installing')}</p>
      <div class="hero-ctas">
        <a class="btn btn-glow" href="{$demo}" target="_blank" rel="noopener">{$t('Try Demo')} <span class="arrow">→</span></a>
        <a class="btn btn-line" href="mailto:info@vitexsoftware.cz">info@vitexsoftware.cz</a>
      </div>
    </div>
  </div>
</section>
HTML;
    }

    private static function satellite(string $label, string $icon, string $color, string $path, int $duration, float $begin): string
    {
        return '<g class="sat" style="--sc:var('.$color.')"><circle r="17" class="sat-body"/><path d="'.self::ICONS[$icon].'" class="sat-ic"/>'
            .'<text y="33" text-anchor="middle" class="sat-label">'.htmlspecialchars($label).'</text>'
            .'<animateMotion dur="'.$duration.'s" begin="'.$begin.'s" repeatCount="indefinite" path="'.$path.'"/></g>';
    }

    private static function pulse(string $path, int $duration, float $begin, string $color): string
    {
        return '<circle r="2.6" class="pulse" style="fill:var('.$color.')"><animateMotion dur="'.$duration.'s" begin="'.$begin.'s" repeatCount="indefinite" path="'.$path.'"/></circle>';
    }

    private static function uiIcon(string $name): string
    {
        $paths = [
            'bank' => 'M3 10h18M5 10v8m4-8v8m6-8v8m4-8v8M3 21h18M12 3l9 5H3z',
            'link' => 'M9 15l6-6M10 6l1-1a4 4 0 0 1 6 6l-1 1M14 18l-1 1a4 4 0 0 1-6-6l1-1',
            'mail' => 'M4 6h16v12H4zM4 7l8 6 8-6',
            'down' => 'M12 3v12m-5-5l5 5 5-5M4 19h16',
            'chart' => 'M4 19V9m6 10V5m6 14v-7m4 7H2',
            'check' => 'M20 6L9 17l-5-5',
        ];

        return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="'.$paths[$name].'"/></svg>';
    }
}
