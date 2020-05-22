<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\ISeriesFormFactory;
use App\Model\Manager\SeriesManager;

class SeriesPresenter extends SecuredPresenter
{

    /** @var SeriesManager */
    private $seriesManager;

    /** @var ISeriesFormFactory */
    private $seriesFormFactory;

    /** @var int|null */
    private $seriesId;


    public function __construct(
        SeriesManager $seriesManager,
        ISeriesFormFactory $seriesFormFactory
    )
    {
        $this->seriesManager = $seriesManager;
        $this->seriesFormFactory = $seriesFormFactory;
    }


    public function renderDefault(): void
    {
        $this->template->seriesList = $this->seriesManager->getSeriesList();
    }


    public function createComponentSeriesForm(): BaseForm
    {
        $control = $this->seriesFormFactory->create($this->seriesId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->seriesId = null;
    }


    public function actionEdit(int $seriesId): void
    {
        $this->seriesId = $seriesId;
        $series = $this->seriesManager->findSeriesById($seriesId);
        if (!$series) {
            $this->error("Série nebyla nalezena");
        }
    }


    public function renderDetail(int $seriesId): void
    {
        $series = $this->seriesManager->findSeriesById($seriesId);
        if (!$series) {
            $this->error("Série nebyla nalezena");
        }
        $this->template->series = $series;
    }

}