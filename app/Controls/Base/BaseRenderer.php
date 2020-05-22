<?php

namespace App\Controls\Base;

use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextBase;
use Nette\Forms\Form;
use Nette\Forms\Rendering\DefaultFormRenderer;

class BaseRenderer extends DefaultFormRenderer
{

    public $wrappers = [
        "form" => [
            "container" => "",
        ],
        "error" => [
            "container" => "div class='alert alert-danger'",
            "item" => "p",
        ],
        "group" => [
            "container" => "fieldset",
            "label" => "legend",
            "description" => "p",
        ],
        "controls" => [
            "container" => "div",
        ],
        "pair" => [
            "container" => "div class='form-group row'",
            ".required" => "required",
            ".optional" => null,
            ".odd" => null,
            ".error" => "has-error",
        ],
        "control" => [
            "container" => "div class='col-sm-9'",
            ".odd" => null,
            "description" => "span class='help-block'",
            "requiredsuffix" => "",
            "errorcontainer" => "span class='help-block'",
            "erroritem" => "",
            ".required" => "required",
            ".text" => "text",
            ".password" => "text",
            ".file" => "text",
            ".submit" => "button",
            ".image" => "imagebutton",
            ".button" => "button",
        ],
        "label" => [
            "container" => "div class='col-sm-3 control-label'",
            "suffix" => null,
            "requiredsuffix" => "",
        ],
        "hidden" => [
            "container" => "div",
        ],
    ];


    /** @var bool */
    private $novalidate;

    public function __construct(bool $novalidate = true)
    {
        $this->novalidate = $novalidate;
    }


    public function render(Form $form, string $mode = null): string
    {
        if ($this->novalidate) {
            $form->getElementPrototype()->setNovalidate("novalidate");
        }

        foreach ($form->getControls() as $control) {
            if ($control instanceof Button) {
                if (strpos($control->getControlPrototype()->getClass(), "btn") === false) {
                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? "btn btn-info" : "btn btn-primary");
                    $usedPrimary = true;
                }
            } elseif ($control instanceof TextBase ||
                $control instanceof SelectBox ||
                $control instanceof MultiSelectBox) {
                $control->getControlPrototype()->setAttribute("class", "form-control");
            } elseif ($control instanceof Checkbox ||
                $control instanceof CheckboxList ||
                $control instanceof RadioList) {
                $control->getSeparatorPrototype()->setName("div")->setAttribute("class", $control->getControlPrototype()->type);
            }
        }

        return parent::render($form, $mode);
    }

}