<?php

namespace App\Controls\Base;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

class StarRate extends TextInput
{

    private $rate;


    public function __construct($label = null)
    {
        parent::__construct($label);
    }


    public function setValue($value)
    {
        if (!$value) {
            return;
        } else {
            $this->rate = $value["rate"];
        }
    }


    public function getValue(): string
    {
        return $this->rate;
    }


    public function loadHttpData(): void
    {
        $this->rate = 1;
    }


    public function getControl(): Html
    {
        $thumbDown = Html::el("i")->addAttributes(["class" => "fa fa-thumbs-down"]);
        $star = Html::el("i")->addAttributes(["class" => "fa fa-star"]);
        $list = Html::el("ul")->addAttributes(["class" => "star-rating-list"]);
        $list->addHtml(Html::el("li")->addAttributes(["data-value" => 0, "class" => "book-rating book-rating-thumb-down"])->setHtml($thumbDown));
        $reset = Html::el("li")->addAttributes(["class" => "book-rating-remove"])->setHtml(Html::el("i")->addAttributes(["class" => "fa fa-times"]));

        for ($i = 1; $i <= 5; $i++) {
            $list->addHtml(Html::el("li")->addAttributes([
                "data-value" => $i,
                "class" => "book-rating book-rating-star",
            ])->setHtml($star));
        }
        $list->addHtml($reset);
        $div = Html::el("div")->addAttributes(["class" => "star-rating-component"]);
        $div->addHtml($list);

        return $div;
    }


}