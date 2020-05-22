<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\PublisherManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IPublisherFormFactory
{
    public function create(int $publisherId = null): PublisherForm;
}

class PublisherForm extends Control
{

    /** @var PublisherManager */
    private $publisherManager;

    /** @var int|null */
    private $publisherId;

    /** @var ActiveRow|null */
    private $publisher;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        PublisherManager $publisherManager,
        int $publisherId = null
    )
    {
        $this->publisherManager = $publisherManager;
        $this->publisherId = $publisherId;
        $this->publisher = $publisherManager->findPublisherById($publisherId);
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("name", "Název");
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->publisher) {
            $form->setDefaults([
                "name" => $this->publisher->name,
            ]);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "name" => $values["name"],
        ];

        if ($this->publisher) {
            $this->publisher->update($valuesToSave);
        } else {
            $this->publisherManager->savePublisher($valuesToSave);
        }

        $this->onSuccess();
    }

}