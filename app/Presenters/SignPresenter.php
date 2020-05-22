<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\ISignInFormFactory;

class SignPresenter extends BasePresenter
{

    /** @var ISignInFormFactory */
    private $signInFormFactory;


    public function __construct(
        ISignInFormFactory $signInFormFactory
    )
    {
        $this->signInFormFactory = $signInFormFactory;
    }


    protected function createComponentSignInForm(): BaseForm
    {
        $control = $this->signInFormFactory->create();
        $control->onSuccess[] = function () {
            $this->redirect(":Homepage:default");
        };

        return $control->getForm();
    }


    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->redirect(":Homepage:default");
    }

}