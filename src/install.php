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

$oPage->addItem(new PageTop(_('MultiFlexi Installation')));
$oPage->includeCSS('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css');

/* ── Inline CSS for install page (cyberpunk-inspired, matching repo pages) ── */
$oPage->addCss(<<<'CSS'
.mf-install-hero {
    background: linear-gradient(135deg, #1a1f36 0%, #1e2d4a 60%, #0f3460 100%);
    color: #c0d0e8;
    border-radius: 12px;
    padding: 2.5rem 2rem;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 4px 24px rgba(0,0,0,0.25);
}
.mf-install-hero h1 { color: #fff; font-size: 2rem; margin-bottom: 0.5rem; }
.mf-install-hero p  { color: rgba(255,255,255,0.7); font-size: 1rem; margin-bottom: 0; }

/* Setup card */
.mf-setup-card {
    border: 1px solid rgba(30,45,74,0.3);
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    margin-bottom: 2rem;
}
.mf-setup-header {
    background: linear-gradient(135deg, #1e2d4a 0%, #243a5e 100%);
    border-radius: 10px 10px 0 0;
    padding: 1.2rem 1.5rem;
    border-bottom: none;
}
.mf-setup-header h2 { color: #fff; font-size: 1.15rem; margin: 0; font-weight: 700; letter-spacing: 0.03em; }
.mf-setup-header p  { color: rgba(255,255,255,0.65); font-size: 0.82rem; margin: 0.3rem 0 0; }
.mf-setup-body { padding: 1.5rem; }

/* Teal-flavoured form controls */
.mf-label-teal { font-weight: 600; color: #1e2d4a; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.06em; }
.mf-select-teal { border: 1px solid #b0c4de; border-radius: 6px; font-size: 0.88rem; }
.mf-select-teal:focus { border-color: #1e6fbf; box-shadow: 0 0 0 0.2rem rgba(30,111,191,0.15); }

/* Component card */
.mf-comp-card { background: #f0f4fa; border: 1px solid #d0dbe8; border-radius: 8px; padding: 1rem; }
.mf-comp-card .form-check-label { font-size: 0.88rem; color: #344055; }

/* Step headings */
.mf-step-h3 { color: #1e2d4a; font-weight: 700; font-size: 0.95rem; margin-top: 1.8rem; border-left: 3px solid #1e6fbf; padding-left: 0.6rem; }
.mf-step-h3 i { margin-right: 0.3rem; color: #1e6fbf; }

/* Pre blocks */
.mf-pre {
    background: #1a1f36;
    color: #39ff14;
    padding: 0.9rem 3.5rem 0.9rem 1rem;
    border-radius: 6px;
    border-left: 3px solid #1e6fbf;
    font-family: 'Courier New', monospace;
    font-size: 0.82rem;
    position: relative;
    overflow-x: auto;
    margin-bottom: 1rem;
}
.mf-pre-config {
    background: #1a1f36;
    color: #00f5ff;
    padding: 0.9rem 3.5rem 0.9rem 1rem;
    border-radius: 6px;
    border-left: 3px solid #1a8754;
    font-family: 'Courier New', monospace;
    font-size: 0.82rem;
    position: relative;
    overflow-x: auto;
    margin-bottom: 1rem;
}

/* Copy button */
.mf-copy-btn {
    position: absolute;
    top: 6px; right: 6px;
    border-radius: 4px;
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    cursor: pointer;
    transition: all 0.15s;
}
.mf-copy-btn:hover { background: rgba(255,255,255,0.18); color: #fff; }

/* Quick install alert */
.mf-quick-alert {
    background: #eef5ff;
    border: 1px solid #b0c4de;
    border-left: 3px solid #1e6fbf;
    border-radius: 6px;
    padding: 1rem;
    font-size: 0.88rem;
}
.mf-quick-cmd {
    background: #1a1f36;
    color: #00f5ff;
    padding: 0.6rem 1rem;
    border-radius: 4px;
    margin-top: 0.5rem;
    margin-bottom: 0;
    cursor: pointer;
    font-family: 'Courier New', monospace;
    font-size: 0.82rem;
    border: 1px solid rgba(30,45,74,0.3);
}
.mf-quick-cmd:hover { border-color: #1e6fbf; }

/* No-distro placeholder */
.mf-no-distro { text-align: center; padding: 2.5rem; color: #8898aa; }
.mf-no-distro i { font-size: 2.5rem; opacity: 0.25; color: #1e6fbf; display: block; margin-bottom: 0.5rem; }

/* Repo badge */
.mf-repo-badge { display: inline-block; padding: 0.25rem 0.7rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
.mf-badge-prod { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.mf-badge-test { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

/* DB install card */
.mf-db-card {
    border: 1px solid rgba(30,45,74,0.15);
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
}
.mf-db-header {
    background: linear-gradient(135deg, #1a8754 0%, #157347 100%);
    color: #fff;
    padding: 1rem 1.3rem;
    border-radius: 10px 10px 0 0;
    font-weight: 700;
    font-size: 1rem;
}
.mf-db-body { padding: 1.3rem; }
CSS);

/* ── Hero section ── */
$oPage->container->addItem('<div class="mf-install-hero">
  <h1><i class="bi bi-box-arrow-in-down"></i> '._('Install MultiFlexi').'</h1>
  <p>'._('Set up the MultiFlexi automation platform on your Debian or Ubuntu system').'</p>
</div>');

/* ── Step 1: Prepare system ── */
$prepCard = new \Ease\Html\DivTag(null, ['class' => 'mf-db-card']);
$prepCard->addItem('<div class="mf-db-header"><i class="bi bi-terminal"></i> '._('Step 1: Prepare your system').'</div>');
$prepBody = $prepCard->addItem(new \Ease\Html\DivTag(null, ['class' => 'mf-db-body']));
$prepBody->addItem('<p class="text-muted mb-3">'._('Install prerequisites needed for secure APT repository access.').'</p>');
$prepBody->addItem('<div style="position:relative"><pre class="mf-pre" id="prepCmd"><code>sudo apt update &amp;&amp; sudo apt install -y lsb-release apt-transport-https bzip2 ca-certificates curl</code></pre><button class="mf-copy-btn" data-copy-target="prepCmd"><i class="bi bi-clipboard"></i> Copy</button></div>');
$oPage->container->addItem($prepCard);

/* ── Repository configs (production + testing) ── */
$repos = [
    [
        'id' => 'prod',
        'title' => _('Production Repository'),
        'subtitle' => _('Stable releases — recommended for production systems'),
        'badge' => '<span class="mf-repo-badge mf-badge-prod"><i class="bi bi-shield-check"></i> '._('Stable').'</span>',
        'baseUrl' => 'https://repo.multiflexi.eu',
        'keyword' => 'multiflexi',
        'distros' => ['bookworm', 'trixie', 'forky', 'jammy', 'noble', 'resolute'],
        'components' => ['main', 'paid'],
    ],
    [
        'id' => 'test',
        'title' => _('Testing Repository'),
        'subtitle' => _('Development builds — latest features, may be unstable'),
        'badge' => '<span class="mf-repo-badge mf-badge-test"><i class="bi bi-lightning"></i> '._('Testing').'</span>',
        'baseUrl' => 'https://repo.vitexsoftware.com',
        'keyword' => 'vitexsoftware',
        'distros' => ['bookworm', 'trixie', 'forky', 'jammy', 'noble', 'resolute'],
        'components' => ['main', 'backports', 'borrowed', 'games', 'paid'],
    ],
];

$oPage->container->addItem('<h2 class="mt-4 mb-3"><i class="bi bi-gear"></i> '._('Step 2: Configure APT Repository').'</h2>');
$oPage->container->addItem('<p class="text-muted mb-4">'._('Choose your preferred repository channel and configure it interactively.').'</p>');

$repoRow = new \Ease\TWB5\Row();

foreach ($repos as $repo) {
    $id = $repo['id'];
    $distroOptions = '<option value="">-- '._('Choose distribution').' --</option>';

    foreach ($repo['distros'] as $d) {
        $distroOptions .= '<option value="'.htmlspecialchars($d).'">'.ucfirst($d).'</option>';
    }

    $compCheckboxes = '';

    foreach ($repo['components'] as $comp) {
        $checked = ($comp === 'main') ? ' checked' : '';
        $disabled = ($comp === 'main') ? ' disabled' : '';
        $badge = ($comp === 'main') ? ' <span class="badge bg-success ms-1">'._('Required').'</span>' : '';
        $compCheckboxes .= '<div class="col-6"><div class="form-check">'.
            '<input class="form-check-input mf-comp-cb" type="checkbox" id="comp_'.$id.'_'.$comp.'" value="'.$comp.'" data-repo="'.$id.'"'.$checked.$disabled.'>'.
            '<label class="form-check-label" for="comp_'.$id.'_'.$comp.'">'.$comp.$badge.'</label>'.
            '</div></div>';
    }

    $html = '
<div class="card mf-setup-card h-100">
  <div class="card-header mf-setup-header">
    <h2><i class="bi bi-box-seam"></i> '.htmlspecialchars($repo['title']).' '.$repo['badge'].'</h2>
    <p>'.htmlspecialchars($repo['subtitle']).'</p>
  </div>
  <div class="card-body mf-setup-body">

    <div class="mb-3">
      <label for="distro_'.$id.'" class="form-label mf-label-teal"><i class="bi bi-hdd"></i> '._('Select your distribution:').'</label>
      <select id="distro_'.$id.'" class="form-select mf-select-teal" data-repo="'.$id.'">'.$distroOptions.'</select>
    </div>
    <div class="mb-3">
      <label for="format_'.$id.'" class="form-label mf-label-teal"><i class="bi bi-file-text"></i> '._('Configuration format:').'</label>
      <select id="format_'.$id.'" class="form-select mf-select-teal" data-repo="'.$id.'">
        <option value="deb822">'._('DEB822 format (.sources) — Recommended').'</option>
        <option value="legacy">'._('Legacy format (sources.list)').'</option>
      </select>
    </div>

    <div class="mb-3 d-none" id="compSection_'.$id.'">
      <label class="form-label mf-label-teal"><i class="bi bi-folder"></i> '._('Select components:').'</label>
      <div class="mf-comp-card"><div class="row g-2">'.$compCheckboxes.'</div>
        <small class="text-muted mt-2 d-block"><i class="bi bi-info-circle"></i> '._('The <strong>main</strong> component is always required.').'</small>
      </div>
    </div>

    <div id="configPreview_'.$id.'" class="d-none">
      <h3 class="mf-step-h3"><i class="bi bi-key"></i> '._('Import the GPG Key').'</h3>
      <div style="position:relative">
        <pre class="mf-pre" id="gpgCmd_'.$id.'"><code></code></pre>
        <button class="mf-copy-btn" data-copy-target="gpgCmd_'.$id.'"><i class="bi bi-clipboard"></i> Copy</button>
      </div>

      <h3 class="mf-step-h3"><i class="bi bi-file-code"></i> <span id="cfgTitle_'.$id.'">'._('Create Configuration File').'</span></h3>
      <p class="text-muted mb-2">'._('File path:').' <code id="cfgPath_'.$id.'" class="text-primary"></code></p>
      <div style="position:relative">
        <pre class="mf-pre-config" id="aptCfg_'.$id.'"><code></code></pre>
        <button class="mf-copy-btn" data-copy-target="aptCfg_'.$id.'"><i class="bi bi-clipboard"></i> Copy</button>
      </div>

      <div class="mf-quick-alert">
        <i class="bi bi-info-circle"></i> <strong>'._('Quick install command:').'</strong> '._('Copy and paste this into your terminal:').'
        <pre class="mf-quick-cmd" id="quickCmd_'.$id.'" data-copy-target="quickCmd_'.$id.'"><code></code></pre>
      </div>
    </div>

    <div id="noDistro_'.$id.'" class="mf-no-distro">
      <i class="bi bi-arrow-up-circle"></i>
      <p>'._('Select your distribution above to generate customized configuration').'</p>
    </div>
  </div>
</div>';

    $repoRow->addColumn(6, $html);
}

$oPage->container->addItem($repoRow);

/* ── Step 3: Update & install ── */
$oPage->container->addItem('<h2 class="mt-4 mb-3"><i class="bi bi-arrow-repeat"></i> '._('Step 3: Update Sources').'</h2>');
$oPage->container->addItem('<div style="position:relative"><pre class="mf-pre" id="updateCmd"><code>sudo apt update</code></pre><button class="mf-copy-btn" data-copy-target="updateCmd"><i class="bi bi-clipboard"></i> Copy</button></div>');

/* ── Step 4: Choose database ── */
$dbCard = new \Ease\Html\DivTag(null, ['class' => 'mf-db-card']);
$dbCard->addItem('<div class="mf-db-header"><i class="bi bi-database"></i> '._('Step 4: Install for your chosen database').'</div>');
$dbBody = $dbCard->addItem(new \Ease\Html\DivTag(null, ['class' => 'mf-db-body']));

$dbTabs = new \Ease\TWB5\Tabs();
$dbTabs->addTab(
    '<i class="bi bi-database"></i> MySQL',
    '<div style="position:relative"><pre class="mf-pre" id="dbMysql"><code>sudo apt install multiflexi-mysql</code></pre><button class="mf-copy-btn" data-copy-target="dbMysql"><i class="bi bi-clipboard"></i> Copy</button></div>',
);
$dbTabs->addTab(
    '<i class="bi bi-database"></i> PostgreSQL',
    '<div style="position:relative"><pre class="mf-pre" id="dbPgsql"><code>sudo apt install multiflexi-pgsql</code></pre><button class="mf-copy-btn" data-copy-target="dbPgsql"><i class="bi bi-clipboard"></i> Copy</button></div>',
);
$dbTabs->addTab(
    '<i class="bi bi-database"></i> SQLite',
    '<div style="position:relative"><pre class="mf-pre" id="dbSqlite"><code>sudo apt install multiflexi-sqlite</code></pre><button class="mf-copy-btn" data-copy-target="dbSqlite"><i class="bi bi-clipboard"></i> Copy</button></div>',
);
$dbBody->addItem($dbTabs);
$oPage->container->addItem($dbCard);

/* ── Step 5: Discover apps ── */
$oPage->container->addItem('<h2 class="mt-4 mb-3"><i class="bi bi-search"></i> '._('Step 5: Discover available applications').'</h2>');
$oPage->container->addItem('<p class="text-muted mb-2">'._('Search the repository for MultiFlexi application packages:').'</p>');
$oPage->container->addItem('<div style="position:relative"><pre class="mf-pre" id="searchCmd"><code>apt search multiflexi</code></pre><button class="mf-copy-btn" data-copy-target="searchCmd"><i class="bi bi-clipboard"></i> Copy</button></div>');

/* ── Repo config JSON data (read by JS via embedded <script type=application/json>) ── */
$repoConfigs = [];

foreach ($repos as $repo) {
    $repoConfigs[$repo['id']] = [
        'baseUrl' => $repo['baseUrl'],
        'keyword' => $repo['keyword'],
        'components' => $repo['components'],
    ];
}

$oPage->addItem('<script type="application/json" id="installRepoConfig">'.json_encode($repoConfigs, \JSON_UNESCAPED_SLASHES).'</script>');

$oPage->addJavaScript(<<<'JS'
(function(){
  'use strict';
  var repos = JSON.parse(document.getElementById('installRepoConfig').textContent);

  function getComponents(repoId) {
    var cbs = document.querySelectorAll('.mf-comp-cb[data-repo="'+repoId+'"]');
    var comps = [];
    cbs.forEach(function(cb){ if(cb.checked) comps.push(cb.value); });
    if(comps.indexOf('main')===-1) comps.unshift('main');
    return comps;
  }

  function updatePreview(repoId) {
    var cfg     = repos[repoId];
    var distro  = document.getElementById('distro_'+repoId).value;
    var format  = document.getElementById('format_'+repoId).value;
    var preview = document.getElementById('configPreview_'+repoId);
    var noDist  = document.getElementById('noDistro_'+repoId);
    var compSec = document.getElementById('compSection_'+repoId);

    if(!distro){ preview.classList.add('d-none'); compSec.classList.add('d-none'); noDist.classList.remove('d-none'); return; }

    preview.classList.remove('d-none'); compSec.classList.remove('d-none'); noDist.classList.add('d-none');

    var keyringPath='/usr/share/keyrings/'+cfg.keyword+'-archive-keyring.gpg';
    var comps = getComponents(repoId);
    var compsStr = comps.join(' ');

    document.getElementById('gpgCmd_'+repoId).querySelector('code').textContent =
      'sudo curl -fsSL '+cfg.baseUrl+'/KEY.gpg -o '+keyringPath;

    var cfgPath = document.getElementById('cfgPath_'+repoId);
    var aptCfg  = document.getElementById('aptCfg_'+repoId).querySelector('code');
    var quick   = document.getElementById('quickCmd_'+repoId).querySelector('code');
    var title   = document.getElementById('cfgTitle_'+repoId);

    if(format==='deb822'){
      var fname = cfg.keyword+'.sources';
      cfgPath.textContent = '/etc/apt/sources.list.d/'+fname;
      title.textContent   = 'Create .sources file (DEB822 format)';
      var content = 'Types: deb\nURIs: '+cfg.baseUrl+'/\nSuites: '+distro+'\nComponents: '+compsStr+'\nSigned-By: '+keyringPath;
      aptCfg.textContent  = content;
      quick.textContent   = '# Quick setup for '+distro+'\nsudo curl -fsSL '+cfg.baseUrl+'/KEY.gpg -o '+keyringPath+' && \\\n'
        + 'echo "'+content+'" | sudo tee /etc/apt/sources.list.d/'+fname;
    } else {
      var fname = cfg.keyword+'.list';
      cfgPath.textContent = '/etc/apt/sources.list.d/'+fname;
      title.textContent   = 'Create .list file (Legacy format)';
      var line = 'deb [signed-by='+keyringPath+'] '+cfg.baseUrl+'/ '+distro+' '+compsStr;
      aptCfg.textContent  = line;
      quick.textContent   = '# Quick setup for '+distro+'\nsudo curl -fsSL '+cfg.baseUrl+'/KEY.gpg -o '+keyringPath+' && \\\n'
        + 'echo "'+line+'" | sudo tee /etc/apt/sources.list.d/'+fname;
    }
  }

  function copyToClipboard(elementId) {
    var el   = document.getElementById(elementId);
    var code = el.querySelector('code');
    var text = code ? code.textContent : el.textContent;
    navigator.clipboard.writeText(text).then(function(){
      var btn = el.nextElementSibling || el.parentElement.querySelector('.mf-copy-btn');
      if(btn && btn.classList.contains('mf-copy-btn')){
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Copied!';
        btn.style.color = '#39ff14';
        setTimeout(function(){ btn.innerHTML = orig; btn.style.color = ''; }, 2000);
      }
    }).catch(function(){ alert('Failed to copy. Please select and copy manually.'); });
  }

  /* Wire up selectors and checkboxes (DOM is already ready via Ease/jQuery) */
  document.querySelectorAll('.mf-select-teal[data-repo], .mf-comp-cb').forEach(function(el){
    el.addEventListener('change', function(){ updatePreview(el.dataset.repo); });
  });
  /* Wire up all copy buttons */
  document.querySelectorAll('[data-copy-target]').forEach(function(el){
    el.addEventListener('click', function(){ copyToClipboard(el.dataset.copyTarget); });
  });
}());
JS);

$oPage->addItem(new PageBottom());

$oPage->draw();
