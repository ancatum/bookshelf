<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\INationalityFormFactory;
use App\Model\Manager\NationalityManager;

class NationalityPresenter extends SecuredPresenter
{

    /** @var NationalityManager */
    private $nationalityManager;

    /** @var INationalityFormFactory */
    private $nationalityFormFactory;

    /** @var int|null */
    private $nationalityId;


    public function __construct(
        NationalityManager $nationalityManager,
        INationalityFormFactory $nationalityFormFactory
    )
    {
        $this->nationalityManager = $nationalityManager;
        $this->nationalityFormFactory = $nationalityFormFactory;
    }


    public function renderDefault(): void
    {
        $this->template->nationalities = $this->nationalityManager->getNationalityListOrderedByName();
    }


    protected function createComponentNationalityForm(): BaseForm
    {
        $control = $this->nationalityFormFactory->create($this->nationalityId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->nationalityId = null;
    }


    public function actionEdit(int $nationalityId): void
    {
        $this->nationalityId = $nationalityId;
        $nationality = $this->nationalityManager->findNationalityById($nationalityId);
        if (!$nationality) {
            $this->error("Národnost nebyla nalezena");
        }
    }

}