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
 * Page TOP.
 */
class PageTop extends \Ease\Html\DivTag
{
    /**
     * Titulek stránky.
     */
    public string $pageTitle;

    public \Ease\Html\DivTag $status;

    /**
     * Nastavuje titulek.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = null)
    {
        parent::__construct();

        if (null !== $pageTitle) {
            WebPage::singleton()->setPageTitle($pageTitle);
        }

        $this->status = WebPage::singleton()->body->addAsFirst(new \Ease\Html\DivTag('', ['id' => 'status']));
        WebPage::singleton()->body->addAsFirst(new MainMenu());
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv.
     */
    public function finalize(): void
    {
        //        if (\Ease\Shared::user()->isLogged()) { //Authenticated user
        //            $this->addItem(new Breadcrumb());
        //        }
        if (!empty(\Ease\Shared::logger()->getMessages())) {
            WebPage::singleton()->addCss(<<<'EOD'

#smdrag { height: 8px;
          background-image:  url( images/slidehandle.png );
          background-color: #ccc;
          background-repeat: no-repeat;
          background-position: top center;
          cursor: ns-resize;
}
#smdrag:hover { background-color: #f5ad66; }


EOD);
            $this->status->addItem(WebPage::singleton()->getStatusMessagesBlock(['id' => 'status-messages', 'title' => _('Click to hide messages')]));
            $this->status->addItem(new \Ease\Html\DivTag(null, ['id' => 'smdrag', 'style' => 'margin-bottom: 5px']));
            \Ease\Shared::logger()->cleanMessages();
            WebPage::singleton()->addCss('.dropdown-menu { overflow-y: auto } ');
            WebPage::singleton()->addJavaScript(
                "$('.dropdown-menu').css('max-height',$(window).height()-100);",
                null,
                true,
            );
            \Ease\Part::jQueryze();
            WebPage::singleton()->includeJavaScript('js/slideupmessages.js');
        }

        parent::finalize();
    }
}
