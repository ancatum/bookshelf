<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use Nette\Application\UI\Control;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

interface ISignInFormFactory
{
    public function create(): SignInForm;
}

class SignInForm extends Control
{

    /** @var User */
    private $user;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        User $user
    )
    {
        $this->user = $user;
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("username", "Uživatelské jméno")
            ->setRequired("Vyplňte uživatelské jméno");
        $form->addPassword("password", "Heslo")
            ->setRequired("Vyplňte heslo");
        $form->addSubmit("send", "Přihlásit");
        $form->onSuccess[] = [$this, "processForm"];

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        try {
            $this->user->login($values["username"], $values["password"]);
        } catch (AuthenticationException $e) {
            $form->addError("Nesprávné uživatelské jméno nebo heslo");
        }

        $this->onSuccess();
    }

}