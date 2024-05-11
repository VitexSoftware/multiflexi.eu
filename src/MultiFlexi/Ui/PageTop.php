<?php

/**
 * Multi Flexi  - Shared page top class
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2024 Vitex Software
 */

namespace MultiFlexi\Ui;

use MultiFlexi\Ui\WebPage;

/**
 * Page TOP.
 */
class PageTop extends \Ease\Html\DivTag {

    /**
     * Titulek stránky.
     *
     * @var string
     */
    public $pageTitle = null;

    /**
     * 
     * @var \Ease\Html\DivTag
     */
    public $status;

    /**
     * Nastavuje titulek.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = null) {
        parent::__construct();
        if (!is_null($pageTitle)) {
            WebPage::singleton()->setPageTitle($pageTitle);
        }
        $this->status = WebPage::singleton()->body->addAsFirst(new \Ease\Html\DivTag('', ['id' => 'status']));
        WebPage::singleton()->body->addAsFirst(new MainMenu());
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv.
     */
    public function finalize() {
//        if (\Ease\Shared::user()->isLogged()) { //Authenticated user
//            $this->addItem(new Breadcrumb());
//        }
        if (!empty(\Ease\Shared::logger()->getMessages())) {
            WebPage::singleton()->addCss('
#smdrag { height: 8px; 
          background-image:  url( images/slidehandle.png ); 
          background-color: #ccc; 
          background-repeat: no-repeat; 
          background-position: top center; 
          cursor: ns-resize;
}
#smdrag:hover { background-color: #f5ad66; }

');
            $this->status->addItem(WebPage::singleton()->getStatusMessagesBlock(['id' => 'status-messages', 'title' => _('Click to hide messages')]));
            $this->status->addItem(new \Ease\Html\DivTag(null, ['id' => 'smdrag', 'style' => 'margin-bottom: 5px']));
            \Ease\Shared::logger()->cleanMessages();
            WebPage::singleton()->addCss('.dropdown-menu { overflow-y: auto } ');
            WebPage::singleton()->addJavaScript(
                    "$('.dropdown-menu').css('max-height',$(window).height()-100);",
                    null,
                    true
            );
            \Ease\Part::jQueryze();
            WebPage::singleton()->includeJavaScript('js/slideupmessages.js');
        }
        parent::finalize();
    }
}
