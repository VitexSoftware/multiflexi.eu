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
 * Description of WebPage.
 *
 * @author vitex
 */
class WebPage extends \Ease\TWB5\WebPage
{
    /**
     * Put page contents here.
     */
    public \Ease\TWB5\Container $container;

    /**
     * @param string $pageTitle
     */
    public function __construct($pageTitle = '')
    {
        parent::__construct((string) $pageTitle);
        \Ease\TWB5\Part::jQueryze();
        $this->container = $this->addItem(new \Ease\TWB5\Container());
        $this->container->setTagClass('container-fluid');
        $this->includeCss('css/lightbox.min.css');
        $this->includeJavaScript('js/lightbox.js');
        $this->addCSS(<<<'CSS'
            body { background-color: #f0f2f5; color: #343a40; font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
            .card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border-radius: 8px; border: none; }
            .card:hover { box-shadow: 0 14px 28px rgba(0,0,0,0.1), 0 10px 10px rgba(0,0,0,0.1) !important; }
            .img-thumbnail { border-radius: 12px; transition: transform 0.3s ease; }
            .img-thumbnail:hover { transform: scale(1.05); }
            .nav-tabs { border-bottom: 2px solid #dee2e6; margin-bottom: 20px; }
            .nav-link { font-weight: 500; color: #6c757d; border: none !important; padding: 12px 20px; }
            .nav-link.active { color: #007bff !important; border-bottom: 3px solid #007bff !important; background: transparent !important; }
            .btn { border-radius: 6px; font-weight: 500; transition: all 0.2s; }
            .btn-outline-danger:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2); }
            .application-metadata h3 { color: #343a40; font-weight: 700; }
            .table thead th { border-top: none; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #8898aa; border-bottom: 1px solid #e9ecef; }
            .table td { vertical-align: middle; }
            .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }

            /* ── Main navbar ─────────────────────────────────────────── */
            .mf-navbar {
                background: linear-gradient(135deg, #1a1f36 0%, #1e2d4a 60%, #0f3460 100%) !important;
                box-shadow: 0 2px 16px rgba(0,0,0,0.35);
                border-bottom: 1px solid rgba(255,255,255,0.07);
                padding: 0.15rem 1rem;
                z-index: 1030;
            }
            /* Keep brand + menu on a single row (the navbar-nav width:100% used for
               right-aligning the language switcher otherwise wraps the menu below
               the brand, doubling the navbar height). Also cancel the page
               content-area padding that the global .container-fluid rule adds. */
            .mf-navbar > .container-fluid { flex-wrap: nowrap; padding-top: 0; padding-bottom: 0; }
            /* The search form is a direct child of <nav>; align it with the menu row */
            .mf-navbar > form { align-self: center; }
            .mf-navbar .navbar-brand { padding: 0; }
            .mf-navbar .navbar-brand img { height: 36px; width: 36px; transition: transform 0.2s ease, filter 0.2s ease; filter: drop-shadow(0 1px 4px rgba(0,0,0,0.4)); }
            .mf-navbar .navbar-brand:hover img { transform: scale(1.08); filter: drop-shadow(0 2px 8px rgba(80,160,255,0.5)); }
            /* Compact menu icons/images */
            .mf-navbar .navbar-nav .nav-link img { height: 22px !important; width: auto !important; vertical-align: middle; }

            /* Nav links */
            .mf-navbar .navbar-nav .nav-link {
                color: rgba(255,255,255,0.78) !important;
                font-size: 0.875rem;
                font-weight: 500;
                padding: 0.45rem 0.7rem !important;
                border-radius: 6px;
                margin: 0 1px;
                transition: color 0.15s ease, background 0.15s ease;
                border: none !important;
                white-space: nowrap;
            }
            .mf-navbar .navbar-nav .nav-link:hover,
            .mf-navbar .navbar-nav .nav-link:focus {
                color: #fff !important;
                background: rgba(255,255,255,0.11);
            }
            .mf-navbar .navbar-nav .nav-link.active,
            .mf-navbar .navbar-nav .show > .nav-link {
                color: #fff !important;
                background: rgba(255,255,255,0.15);
            }

            /* Dropdowns */
            .mf-navbar .dropdown-menu {
                background: #1e2d4a;
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 10px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.45);
                padding: 0.4rem 0.35rem;
                min-width: 200px;
            }
            .mf-navbar .dropdown-item {
                color: rgba(255,255,255,0.78);
                border-radius: 6px;
                padding: 0.4rem 0.9rem;
                font-size: 0.875rem;
                transition: background 0.12s ease, color 0.12s ease;
            }
            .mf-navbar .dropdown-item:hover, .mf-navbar .dropdown-item:focus {
                background: rgba(255,255,255,0.12);
                color: #fff;
            }
            .mf-navbar .dropdown-divider { border-color: rgba(255,255,255,0.12); margin: 0.3rem 0; }

            /* Toggler */
            .mf-navbar .navbar-toggler { border-color: rgba(255,255,255,0.25); padding: 0.3rem 0.6rem; }
            .mf-navbar .navbar-toggler-icon { filter: invert(1) opacity(0.8); }

            /* Language selector — always the last nav-item; push it to the far right */
            /* Make the nav fill the collapse width so the last item's margin-left:auto
               can push the language switcher to the far right (works even when there
               are only a couple of items, e.g. the logged-out login form). */
            .mf-navbar .navbar-collapse > .navbar-nav { flex-grow: 1; width: 100%; }
            .mf-navbar .navbar-nav > li.nav-item:last-child { margin-left: auto; }

            /* Language button (LangSelect renders btn-secondary) */
            .mf-navbar .btn-secondary, .mf-navbar .btn-secondary:focus {
                background: rgba(255,255,255,0.1) !important;
                border-color: rgba(255,255,255,0.22) !important;
                color: rgba(255,255,255,0.82) !important;
                font-size: 0.82rem;
                padding: 0.35rem 0.65rem;
                border-radius: 6px;
                box-shadow: none !important;
                white-space: nowrap;
            }
            .mf-navbar .btn-secondary:hover { background: rgba(255,255,255,0.18) !important; color: #fff !important; }
            .mf-navbar .dropdown-menu[aria-labelledby$="-button"] {
                background: #1e2d4a;
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 10px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.45);
                padding: 0.4rem 0.35rem;
            }

            /* Search input-group */
            .mf-search-group {
                flex-wrap: nowrap !important; /* Bootstrap 5 input-group defaults to wrap; force single row */
                box-shadow: 0 1px 8px rgba(0,0,0,0.3);
            }
            .mf-search-group .mf-search-input {
                background: rgba(255,255,255,0.10);
                border-color: rgba(255,255,255,0.22);
                color: #fff;
                font-size: 0.82rem;
                flex: 0 0 68px;
                min-width: 0;
            }
            .mf-search-group .mf-search-input::placeholder { color: rgba(255,255,255,0.4); }
            .mf-search-group .mf-search-input:focus {
                background: rgba(255,255,255,0.16);
                border-color: rgba(255,255,255,0.42);
                color: #fff;
                box-shadow: none;
            }
            .mf-search-group .mf-search-select {
                /* Remove OS default arrow before adding our own */
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                background-color: rgba(255,255,255,0.08);
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='rgba(255,255,255,.7)' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.45rem center;
                background-size: 12px 9px;
                border-color: rgba(255,255,255,0.22);
                color: rgba(255,255,255,0.88);
                font-size: 0.82rem;
                padding-right: 1.6rem;
                flex: 0 0 122px;
                min-width: 0;
            }
            .mf-search-group .mf-search-select:focus { box-shadow: none; border-color: rgba(255,255,255,0.42); }
            .mf-search-group .mf-search-select option { background: #1a2540; color: #fff; }
            .mf-search-group #mainmenusearchbutton {
                background: rgba(255,255,255,0.12);
                border-color: rgba(255,255,255,0.22);
                color: rgba(255,255,255,0.9);
                font-size: 1rem;
                padding: 0 0.65rem;
                flex-shrink: 0;
                transition: background 0.15s ease;
            }
            .mf-search-group #mainmenusearchbutton:hover {
                background: rgba(255,255,255,0.22);
                color: #fff;
            }

            /* Status messages strip */
            #status-messages { background: rgba(255,248,220,0.96); border-radius: 0 0 6px 6px; padding: 6px 16px; font-size: 0.85rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

            /* ── Content area ─────────────────────────── */
            .container-fluid { padding-top: 1rem; padding-bottom: 2rem; }

            /* ── Typography ───────────────────────────── */
            h1, h2 { color: #1e2d4a; font-weight: 700; margin-bottom: 0.75rem; }
            h3 { color: #243a5e; font-weight: 600; }
            h4, h5 { color: #344055; font-weight: 600; }

            /* ── Cards ────────────────────────────────── */
            .card {
                box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                border-radius: 10px !important;
                margin-bottom: 1.25rem;
                border: 1px solid rgba(0,0,0,0.06) !important;
            }
            .card-header {
                background: linear-gradient(135deg, #1e2d4a 0%, #243a5e 100%);
                color: #fff;
                font-weight: 600;
                font-size: 0.9rem;
                letter-spacing: 0.02em;
                border-radius: 10px 10px 0 0 !important;
                padding: 0.7rem 1.1rem;
                border-bottom: none;
            }
            .card-header a { color: rgba(255,255,255,0.88); }
            .card-header a:hover { color: #fff; }
            .card-body { padding: 1.1rem; }
            .card-footer { background: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 10px 10px !important; padding: 0.6rem 1.1rem; font-size: 0.85rem; }

            /* Panel (uses .card) header fix */
            .card > .card-header h1,
            .card > .card-header h2,
            .card > .card-header h3,
            .card > .card-header h4 { color: #fff; margin: 0; }

            /* ── DataTables ───────────────────────────── */
            /* Horizontal scroll for wide tables at any width (replaces scrollX,
               which created a duplicate header row). */
            .dataTables_wrapper, .dt-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }

            /* Bootstrap 5 .table paints cell backgrounds via --bs-table-bg AND an
               inset box-shadow, which hid the navy header. Override all three. */
            table.dataTable thead th, table.dataTable thead td {
                --bs-table-bg: #1e2d4a;
                --bs-table-color: #ffffff;
                background-color: #1e2d4a !important;
                box-shadow: none !important;
                color: #ffffff !important;
                border-color: rgba(255,255,255,0.10);
                font-size: 0.74rem;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                font-weight: 600;
                padding: 0.65rem 0.8rem;
                white-space: nowrap;
            }
            table.dataTable thead th:hover { background-color: #28406a !important; }
            table.dataTable thead th.sorting:after,
            table.dataTable thead th.sorting_asc:after,
            table.dataTable thead th.sorting_desc:after,
            table.dataTable thead th.dt-orderable-asc span.dt-column-order:before,
            table.dataTable thead th.dt-orderable-desc span.dt-column-order:after { color: rgba(255,255,255,0.55) !important; opacity: 1; }
            table.dataTable thead th.dt-ordering-asc span.dt-column-order:before,
            table.dataTable thead th.dt-ordering-desc span.dt-column-order:after { color: #ffffff !important; }

            /* Body rows — stronger text contrast + visible zebra striping */
            table.dataTable tbody tr { transition: background 0.1s ease; }
            table.dataTable tbody td {
                --bs-table-bg: transparent;
                vertical-align: middle;
                padding: 0.5rem 0.8rem;
                font-size: 0.875rem;
                color: #25303f;
                border-color: #e3e7ee;
            }
            table.dataTable tbody td a { color: #15539e; }
            table.dataTable tbody td a:hover { color: #0d3b75; }
            table.dataTable.table-striped > tbody > tr:nth-of-type(odd) > td,
            table.dataTable tbody tr.odd > td { background-color: #f2f5fb !important; box-shadow: none !important; }
            table.dataTable tbody tr:hover > td { background-color: #e1eaff !important; box-shadow: none !important; }
            table.dataTable { border-radius: 8px; overflow: hidden; }

            /* Exit code indicator — dark, high-contrast semantic text colours
               that stay readable on the tinted job rows */
            .mf-exit { font-weight: 700; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; white-space: nowrap; }
            .mf-exit-success   { color: #146c43; }  /* dark green  */
            .mf-exit-secondary { color: #0a4275; }  /* dark blue   */
            .mf-exit-danger    { color: #b02a37; }  /* dark red    */
            .mf-exit-warning   { color: #985700; }  /* dark orange */
            .mf-exit-info      { color: #087990; }  /* dark cyan   */

            /* DataTable toolbar buttons */
            .dt-buttons { margin-bottom: 0.6rem; display: flex; flex-wrap: wrap; gap: 4px; }
            .dt-buttons .btn {
                border-radius: 6px !important;
                font-size: 0.78rem !important;
                padding: 0.28rem 0.65rem !important;
                background: #fff;
                border: 1px solid #d0d7e3;
                color: #344055;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
                font-weight: 500;
                transition: all 0.15s ease;
            }
            .dt-buttons .btn:hover { background: #eef2ff; border-color: #8ba3cc; color: #1e2d4a; }
            .dt-buttons .btn.active, .dt-buttons .btn:active { background: #1e2d4a !important; color: #fff !important; border-color: #1e2d4a !important; }
            div.dt-button-collection { border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; }

            /* DT search + info bar */
            .dataTables_wrapper .dataTables_filter { margin-bottom: 0.4rem; }
            .dataTables_wrapper .dataTables_filter input {
                border-radius: 6px;
                border: 1px solid #ced4da;
                padding: 0.3rem 0.65rem;
                font-size: 0.875rem;
                margin-left: 0.4rem;
            }
            .dataTables_wrapper .dataTables_info { font-size: 0.8rem; color: #6c757d; padding-top: 0.5rem; }
            .dataTables_wrapper .dataTables_paginate { padding-top: 0.4rem; }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                border-radius: 6px !important;
                font-size: 0.82rem;
                padding: 0.2rem 0.55rem;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current,
            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background: #1e2d4a !important;
                border-color: #1e2d4a !important;
                color: #fff !important;
            }
            .dataTables_processing {
                background: rgba(30,45,74,0.9) !important;
                color: #fff !important;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            }

            /* ── General tables (non-DT) ──────────────── */
            .table:not(.dataTable) thead th {
                --bs-table-bg: #1e2d4a;
                --bs-table-color: #ffffff;
                background-color: #1e2d4a !important;
                box-shadow: none !important;
                color: #ffffff !important;
                text-transform: uppercase;
                font-size: 0.73rem;
                letter-spacing: 0.07em;
                border: none;
                padding: 0.6rem 0.8rem;
                font-weight: 600;
            }
            .table:not(.dataTable) tbody tr:hover td { background: #eef2ff; }
            .table:not(.dataTable) td { vertical-align: middle; }

            /* ── Buttons (page body) ──────────────────── */
            .btn-primary { background: #1e2d4a; border-color: #1a2640; }
            .btn-primary:hover, .btn-primary:focus { background: #0f3460; border-color: #0a2a52; }
            .btn-success { background: #1a8754; border-color: #157347; }
            .btn-info { background: #0e6fa8; border-color: #0b5f90; color: #fff; }
            .btn-info:hover { background: #0a5a8a; color: #fff; }

            /* ── Dropdowns (non-navbar) ───────────────── */
            .dropdown-menu:not(.mf-navbar .dropdown-menu) {
                border-radius: 10px;
                box-shadow: 0 6px 24px rgba(0,0,0,0.10);
                border: 1px solid #e2e8f0;
                padding: 0.4rem;
            }
            .dropdown-menu:not(.mf-navbar .dropdown-menu) .dropdown-item {
                border-radius: 6px;
                padding: 0.4rem 0.8rem;
                font-size: 0.875rem;
                color: #344055;
                transition: background 0.1s ease;
            }
            .dropdown-menu:not(.mf-navbar .dropdown-menu) .dropdown-item:hover { background: #eef2ff; color: #1e2d4a; }

            /* ── Forms ────────────────────────────────── */
            .form-control { border-radius: 6px; border-color: #d0d7e3; }
            .form-control:focus { border-color: #1e6fbf; box-shadow: 0 0 0 0.2rem rgba(30,111,191,0.15); }
            .form-select { border-radius: 6px; border-color: #d0d7e3; }
            .form-label { font-weight: 500; font-size: 0.875rem; color: #344055; }
            .input-group-text { border-radius: 6px; background: #f0f2f5; border-color: #d0d7e3; }

            /* ── Badges ───────────────────────────────── */
            .badge { font-size: 0.73rem; font-weight: 600; letter-spacing: 0.03em; }

            /* ── Nav tabs (filter dialogs) ────────────── */
            .nav-tabs { border-bottom: 2px solid #e2e8f0; }
            .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border: none !important; padding: 0.5rem 1rem; }
            .nav-tabs .nav-link.active { color: #1e2d4a !important; border-bottom: 3px solid #1e2d4a !important; background: transparent !important; }
            .nav-tabs .nav-link:hover { color: #243a5e !important; background: #f0f4ff; border-radius: 6px 6px 0 0; }

            /* ── Images in table cells ────────────────── */
            td img { max-height: 44px; max-width: 120px; object-fit: contain; }

            /* ── Footer ───────────────────────────────── */
            #footer {
                background: #f8f9fa;
                border-top: 1px solid #e2e8f0;
                color: #6c757d;
                font-size: 0.82rem;
                margin-top: 2rem;
                padding: 1.5rem 0;
            }
            #footer a { color: #1e6fbf; text-decoration: none; }
            #footer a:hover { color: #0f3460; text-decoration: underline; }
            #footer hr { display: none; }

            /* ── Login / Logout splash pages ─────────── */
            body:has(#LoginFace),
            body:has(#LogoutFace) {
                background: linear-gradient(140deg, #09111f 0%, #121c32 40%, #0f3060 100%) !important;
                min-height: 100vh;
            }
            /* Large watermark logo centred behind the card */
            #LoginFace, #LogoutFace {
                position: relative;
                min-height: calc(100vh - 120px);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }
            #LoginFace::before, #LogoutFace::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 99vmin;
                height: 99vmin;
                background: url('images/project-logo.svg') center / contain no-repeat;
                opacity: 0.10;
                pointer-events: none;
                z-index: 0;
                filter: drop-shadow(0 0 80px rgba(80,160,255,0.25));
            }
            /* Card floats above the watermark */
            .mf-login-card {
                position: relative;
                z-index: 1;
                background: rgba(255,255,255,0.97);
                border-radius: 18px;
                box-shadow: 0 24px 72px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.08);
                max-width: 420px;
                width: 100%;
                padding: 2.5rem;
                backdrop-filter: blur(12px);
            }
            .mf-login-card .mf-login-logo { text-align: center; margin-bottom: 1.5rem; }
            .mf-login-card .mf-login-logo img { height: 80px; }
            .mf-login-card h2 { text-align: center; font-size: 1.4rem; margin-bottom: 1.5rem; color: #1e2d4a; }
            /* Status messages adapt on auth pages */
            body:has(#LoginFace) #status-messages,
            body:has(#LogoutFace) #status-messages {
                background: rgba(255,240,180,0.92);
                border-left: 4px solid #e0a800;
                color: #4a3600;
            }

            /* ── btn-inverse (BS5 removed it, used by CompanyLinkButton etc.) ── */
            .btn-inverse {
                background: #1e2d4a; border-color: #182540; color: #fff;
            }
            .btn-inverse:hover, .btn-inverse:focus {
                background: #0f3460; border-color: #0a2a52; color: #fff;
            }

            /* ── Images inside DataTable cells & link-buttons ── */
            table.dataTable td img,
            table.dataTable td a img,
            table.dataTable td .btn img {
                height: 36px !important;
                width: auto !important;
                max-width: 90px;
                object-fit: contain;
                vertical-align: middle;
            }
            /* Constrain inline link-buttons inside table cells */
            table.dataTable td a.btn {
                font-size: 0.8rem;
                padding: 0.2rem 0.5rem;
                white-space: nowrap;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            /* ── Scrollbars ───────────────────────────── */
            ::-webkit-scrollbar { width: 7px; height: 7px; }
            ::-webkit-scrollbar-track { background: #f1f3f5; }
            ::-webkit-scrollbar-thumb { background: #adb5bd; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #6c757d; }

            /* ══════════════════════════════════════════════
               MOBILE  ≤ 767px
            ══════════════════════════════════════════════ */
            @media (max-width: 767.98px) {

                /* ── Navbar ─────────────────────────────── */
                .mf-navbar { padding: 0.3rem 0.6rem; }
                /* Search bar: full-width below the toggler */
                .mf-navbar form.my-2 { width: 100%; margin-top: 0.4rem !important; }
                .mf-search-group { width: 100%; }
                .mf-search-group .mf-search-input { flex: 1 1 60px; }
                .mf-search-group .mf-search-select { flex: 1 1 110px; }

                /* ── Status messages ─────────────────────── */
                #status-messages { font-size: 0.75rem; padding: 4px 10px; line-height: 1.4; }

                /* ── Content padding ─────────────────────── */
                .container-fluid { padding-left: 10px; padding-right: 10px; padding-top: 0.6rem; }

                /* ── Typography ──────────────────────────── */
                h1 { font-size: 1.5rem; }
                h2 { font-size: 1.25rem; }
                h3 { font-size: 1.1rem; }

                /* ── Cards ───────────────────────────────── */
                .card { margin-bottom: 0.9rem; border-radius: 8px !important; }
                .card-header { font-size: 0.82rem; padding: 0.5rem 0.8rem; }
                .card-body { padding: 0.75rem; }

                /* ── DataTable wrapper — horizontal scroll ── */
                .dataTables_wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
                table.dataTable { min-width: 500px; }

                /* ── DT toolbar ──────────────────────────── */
                .dt-buttons { gap: 3px; }
                .dt-buttons .btn {
                    font-size: 0.7rem !important;
                    padding: 0.2rem 0.4rem !important;
                    border-radius: 5px !important;
                }
                /* DT search input full-width */
                .dataTables_wrapper .dataTables_filter { float: none; text-align: left; }
                .dataTables_wrapper .dataTables_filter input { width: 100%; margin-left: 0; box-sizing: border-box; }
                /* DT info + paging stack */
                .dataTables_wrapper .dataTables_info,
                .dataTables_wrapper .dataTables_paginate { float: none; text-align: center; }
                .dataTables_wrapper .dataTables_paginate { margin-top: 0.5rem; }

                /* ── Tables (non-DT) ─────────────────────── */
                .table-responsive-mobile { overflow-x: auto; -webkit-overflow-scrolling: touch; }

                /* ── Buttons ─────────────────────────────── */
                .btn { font-size: 0.85rem; }
                .btn-lg { font-size: 0.9rem; padding: 0.5rem 1rem; }
                /* Stack row-of-buttons vertically */
                .row > [class*="col-"] > .btn { margin-bottom: 0.3rem; }

                /* ── Company / panel logos ───────────────── */
                img.img-fluid { max-height: 80px; object-fit: contain; }
                .card-header img, .panel-heading img { max-height: 50px !important; max-width: 50%; }

                /* ── Footer ──────────────────────────────── */
                #footer .row > div { text-align: center; margin-bottom: 0.6rem; }
                #footer { padding: 1rem 0; }

                /* ── Login card ──────────────────────────── */
                .mf-login-card { padding: 1.5rem; margin: 0 0.5rem; border-radius: 12px; }
                .mf-login-card .mf-login-logo img { height: 60px; }
                .mf-login-card h2 { font-size: 1.2rem; }

                /* ── Hide smdrag handle on mobile ────────── */
                #smdrag { display: none; }
            }

            /* ══════════════════════════════════════════════
               TABLET  768 – 991px
            ══════════════════════════════════════════════ */
            @media (min-width: 768px) and (max-width: 991.98px) {

                /* ── DT toolbar compact ──────────────────── */
                .dt-buttons .btn {
                    font-size: 0.75rem !important;
                    padding: 0.25rem 0.5rem !important;
                }

                /* ── Content padding ─────────────────────── */
                .container-fluid { padding-left: 14px; padding-right: 14px; }

                /* ── DT horizontal scroll ────────────────── */
                .dataTables_wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
                table.dataTable { min-width: 600px; }
            }
CSS);
    }
}
