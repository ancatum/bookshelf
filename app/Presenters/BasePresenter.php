<?php

namespace App\Presenters;

use App\Helpers\Latte\BookTypeFilter;
use App\Helpers\Latte\DurationFilter;
use App\Helpers\Latte\GenderFilter;
use App\Helpers\Latte\MonthNameFilter;
use App\Helpers\Latte\RatingFilter;
use App\Helpers\Latte\SourceFilter;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->addFilter("duration", new DurationFilter());
        $this->template->addFilter("gender", new GenderFilter());
        $this->template->addFilter("type", new BookTypeFilter());
        $this->template->addFilter("source", new SourceFilter());
        $this->template->addFilter("rating", new RatingFilter());
        $this->template->addFilter("czechMonth", new MonthNameFilter());
        $this->template->now = new \DateTime();
    }

}