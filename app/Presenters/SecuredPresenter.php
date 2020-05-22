<?php

namespace App\Presenters;

class SecuredPresenter extends BasePresenter
{

    protected function startup()
    {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            $this->redirect("Sign:in");
        }
    }

}