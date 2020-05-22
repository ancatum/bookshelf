<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IPublisherFormFactory;
use App\Model\Manager\PublisherManager;

class PublisherPresenter extends SecuredPresenter
{

    /** @var PublisherManager */
    private $publisherManager;

    /** @var IPublisherFormFactory */
    private $publisherFormFactory;

    /** @var int|null */
    private $publisherId;


    public function __construct(
        PublisherManager $publisherManager,
        IPublisherFormFactory $publisherFormFactory
    )
    {
        $this->publisherManager = $publisherManager;
        $this->publisherFormFactory = $publisherFormFactory;
    }


    public function renderDefault(): void
    {
        $this->template->publishers = $this->publisherManager->getPublisherListOrderedByName();
    }


    public function createComponentPublisherForm(): BaseForm
    {
        $control = $this->publisherFormFactory->create($this->publisherId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->publisherId = null;
    }


    public function actionEdit(int $publisherId): void
    {
        $this->publisherId = $publisherId;
        $publisher = $this->publisherManager->findPublisherById($publisherId);
        if (!$publisher) {
            $this->error("Nakladatel nebyl nalezen");
        }
    }

}