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
     * Bump when css/theme.css, css/hub.css or js/theme.js change, so browsers fetch the new version.
     */
    public const ASSET_VERSION = '1.0.0';

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
        // Light/dark mode before the first paint, so the page does not flash (dark is the default).
        $this->head->addItem('<script>(function(){var t="dark";try{t=localStorage.getItem("vsTheme")||t}catch(e){}var d=document.documentElement;d.classList.add("js");d.setAttribute("data-theme",t);d.setAttribute("data-bs-theme",t)})()</script>');
        $this->head->addItem('<meta name="theme-color" content="#1d1440">');
        $this->head->addItem('<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>');
        $this->includeCss('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=Caveat:wght@400;500&display=swap');
        $this->includeCss('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css');
        $this->includeCss('css/theme.css?v='.self::ASSET_VERSION);
        $this->includeCss('css/hub.css?v='.self::ASSET_VERSION);
        $this->includeJavaScript('js/theme.js?v='.self::ASSET_VERSION);
        $this->container->addTagClass('page-content');
    }

    /**
     * Locale for this request: ?locale= → session → browser language → English.
     *
     * Ease\Locale::langToLocale() compares "cs_CZ" from the browser with "cs", so it never matches.
     */
    public static function preferredLocale(string $i18n, string $domain): string
    {
        $available = array_map(
            static fn (string $mo): string => basename(\dirname($mo, 2)),
            glob($i18n.'/*/LC_MESSAGES/'.$domain.'.mo') ?: [],
        );

        foreach ([$_REQUEST['locale'] ?? null, $_SESSION['locale'] ?? null] as $candidate) {
            if (\is_string($candidate) && \in_array($candidate, $available, true)) {
                return $candidate;
            }
        }

        $browser = \function_exists('locale_accept_from_http') ? (string) locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') : '';

        foreach ($available as $code) {
            if ($browser !== '' && strncmp($browser, $code, 2) === 0) {
                return $code;
            }
        }

        return 'en_US';
    }
}
