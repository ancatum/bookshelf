<?php

namespace App\Helpers\Latte;

use App\Model\Enum\RatingEnum;
use Nette\Utils\Html;

class RatingFilter
{

    /**
     * @param int|null $ratingKey
     * @return Html|string
     */
    public function __invoke(?int $ratingKey)
    {
        if (!in_array($ratingKey, RatingEnum::OPTIONS)) {
            return "nehodnoceno";
        } elseif (RatingEnum::getValue($ratingKey) == 0) {
            return Html::el("div")->addAttributes(["class" => "thumbDown"])->addHtml(Html::el("i")->addAttributes([
                "class" => "fa fa-thumbs-down",
                "aria-hidden" => "true",
            ]));
        } else {
            $fullStar = Html::el("div")->addAttributes(["class" => "fullStar"])->addHtml(Html::el("i")->addAttributes([
                "class" => "fa fa-star",
                "aria-hidden" => "true",
            ]));
            $emptyStar = Html::el("div")->addAttributes(["class" => "emptyStar"])->addHtml(Html::el("i")->addAttributes([
                "class" => "fa fa-star",
                "aria-hidden" => "true",
            ]));

            $maxRating = 5;
            $result = "";
            for ($i = 1; $i <= $ratingKey; $i++) {
                $result .= $fullStar;
            }
            $difference = $maxRating - $ratingKey;
            for ($i = 1; $i <= $difference; $i++) {
                $result .= $emptyStar;
            }

            return $result;
        }
    }

}