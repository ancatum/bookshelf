<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\SeriesManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface ISeriesFormFactory
{
    public function create(int $seriesId = null): SeriesForm;
}

class SeriesForm extends Control
{

    /** @var SeriesManager */
    private $seriesManager;

    /** @var int|null */
    private $seriesId;

    /** @var ActiveRow|null */
    private $series;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        SeriesManager $seriesManager,
        int $seriesId = null
    )
    {
        $this->seriesManager = $seriesManager;
        $this->seriesId = $seriesId;
        $this->series = $seriesManager->findSeriesById($seriesId);
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("name", "Série")
            ->setRequired();
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->series) {
            $form->setDefaults([
                "name" => $this->series->name,
            ]);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "name" => $values["name"],
        ];

        if ($this->series) {
            $this->series->update($valuesToSave);
        } else {
            $this->seriesManager->saveSeries($valuesToSave);
        }

        $this->onSuccess();
    }

}