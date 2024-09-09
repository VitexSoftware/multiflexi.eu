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
    public function __construct($pageTitle = null)
    {
        parent::__construct($pageTitle);
        \Ease\TWB5\Part::jQueryze();
        $this->container = $this->addItem(new \Ease\TWB5\Container());
        $this->container->setTagClass('container-fluid');
        $this->includeCss('css/lightbox.min.css');
        $this->includeJavaScript('js/lightbox.js');
        $this->addCSS(<<<'EOD'


EOD);
    }
}
