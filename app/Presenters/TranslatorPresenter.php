<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\ITranslatorFormFactory;
use App\Model\Manager\TranslatorManager;

class TranslatorPresenter extends SecuredPresenter
{

    /** @var TranslatorManager */
    private $translatorManager;

    /** @var ITranslatorFormFactory */
    private $translatorFormFactory;

    /** @var int|null */
    private $translatorId;

    /** @var int */
    private $perPage;


    public function __construct(
        int $perPage,
        TranslatorManager $translatorManager,
        ITranslatorFormFactory $translatorFormFactory
    )
    {
        parent::__construct();
        $this->perPage = $perPage;
        $this->translatorManager = $translatorManager;
        $this->translatorFormFactory = $translatorFormFactory;
    }


    public function renderDefault(int $page = 1, string $sortBy = null, string $direction = null): void
    {
        $translators = $this->translatorManager->getTranslatorListSorted($sortBy, $direction);
        $lastPage = 0;
        $this->template->translators = $translators->page($page, $this->perPage, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
        $this->template->sortBy = $sortBy;
        $this->template->direction = $direction;
        $this->template->defaultSort = "{$sortBy}-{$direction}";
    }


    public function createComponentTranslatorForm(): BaseForm
    {
        $control = $this->translatorFormFactory->create($this->translatorId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->translatorId = null;
    }


    public function actionEdit(int $translatorId): void
    {
        $this->translatorId = $translatorId;
        $translator = $this->translatorManager->findTranslatorById($translatorId);
        if (!$translator) {
            $this->error("Překladatel nebyl nalezen");
        }
    }

}