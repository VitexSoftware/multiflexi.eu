<?php

/**
 * Multi Flexi  - New Company registration form
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2015-2020 Vitex Software
 */

namespace MultiFlexi\Ui;

use MultiFlexi\Engine;
use Ease\Html\DivTag;
use Ease\Html\InputHiddenTag;
use Ease\Html\InputSubmitTag;
use Ease\TWB5\Col;
use Ease\TWB5\Form;
use Ease\TWB5\Row;
use Ease\TWB5\SubmitButton;

class ColumnsForm extends Form
{
    /**
     * Šířka sloupce.
     *
     * @var int
     */
    public $colsize = 4;

    /**
     * Řádek.
     *
     * @var Row
     */
    public $row = null;

    /**
     * Počet položek na řádek.
     *
     * @var int
     */
    public $itemsPerRow = 1;

    /**
     * @var \Ease\Engine
     */
    public $engine = null;

    /**
     * Submit button
     *
     * @var \Ease\Html\DivTag
     */
    public $savers = null;

    /**
     * Formulář Bootstrapu.
     *
     * @param Engine $engine        jméno formuláře
     * @param mixed  $formContents  prvky uvnitř formuláře
     * @param array  $tagProperties vlastnosti tagu například:
     *                                 array('enctype' => 'multipart/form-data')
     */
    public function __construct(
        $engine,
        $formContents = null,
        $tagProperties = []
    ) {
        $this->engine = $engine;
        $tagProperties['method'] = 'post';
        $tagProperties['name'] = get_class($engine);
        parent::__construct($tagProperties, [], $formContents);
        $this->newRow();
        $this->savers = new DivTag(
            null,
            ['style' => 'text-align: right']
        );
    }

    /**
     * Přidá další řadu formuláře.
     *
     * @return Row Nově vložený řádek formuláře
     */
    public function newRow()
    {
        return $this->row = $this->addItem(new Row());
    }

    /**
     * Vloží prvek do sloupce formuláře.
     *
     * @param mixed  $input       Vstupní prvek
     * @param string $caption     Popisek
     * @param string $placeholder předvysvětlující text
     * @param string $helptext    Dodatečná nápověda
     * @param string $addTagClass CSS třída kterou má být oskiován vložený prvek
     *
     * @return Row New item
     */
    public function addInput(
        $input,
        $caption = null,
        $placeholder = null,
        $helptext = null,
        $addTagClass = 'form-control'
    ) {
        if ($this->row->getItemsCount() > $this->itemsPerRow) {
            $this->row = $this->addItem(new Row());
        }

        $input->addTagClass($addTagClass);
        return $this->row->addItem(new Col(
            $this->colsize,
            new \Ease\TWB5\InputGroup($caption, $input, $helptext)
        ));
    }

    /**
     * Přidá do formuláře tlačítko "Uložit".
     */
    public function addSubmitSave()
    {
        $this->savers->addItem(
            new SubmitButton(_('Save'), 'default'),
            ['style' => 'text-align: right']
        );
    }

    /**
     * Přidá do formuláře tlačítko "Uložit a zpět na přehled".
     */
    public function addSubmitSaveAndList()
    {
        $this->savers->addItem(new InputSubmitTag(
            'gotolist',
            _('Save and back'),
            ['class' => 'btn btn-info']
        ));
    }

    /**
     * Add to form button  "Save next ext".
     */
    public function addSubmitSaveAndNext()
    {
        $this->savers->addItem(new InputSubmitTag(
            'gotonew',
            _('Save and next'),
            ['class' => 'btn btn-success']
        ));
    }

    /**
     * Add Submit buttons
     * @return boolean
     */
    public function finalize()
    {
        $recordID = $this->engine->getMyKey();
        $this->addItem(new InputHiddenTag(
            'class',
            get_class($this->engine)
        ));
        if (!is_null($recordID)) {
            $this->addItem(new InputHiddenTag(
                $this->engine->getKeyColumn(),
                $recordID
            ));
        }

        $this->newRow();
        $this->addItem($this->savers);
        return parent::finalize();
    }
}
