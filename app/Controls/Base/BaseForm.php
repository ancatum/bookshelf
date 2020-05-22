<?php

namespace App\Controls\Base;

use Nette\Application\UI\Form;

class BaseForm extends Form
{

    public function __construct()
    {
        $this->setRenderer(new BaseRenderer());
    }


    /**
     * This control need input with type hidden with id rating-input
     * @param string $name
     * @param string|null $label
     * @return StarRate
     */
    public function addRating(string $name, string $label = null): StarRate
    {
        $control = new StarRate($label);

        return $this[$name] = $control;
    }

}