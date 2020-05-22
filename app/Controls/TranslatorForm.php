<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\TranslatorManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface ITranslatorFormFactory
{
    public function create(int $translatorId = null): TranslatorForm;
}

class TranslatorForm extends Control
{


    /** @var TranslatorManager */
    private $translatorManager;

    /** @var int|null */
    private $translatorId;

    /** @var ActiveRow|null */
    private $translator;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        TranslatorManager $translatorManager,
        int $translatorId = null
    )
    {
        $this->translatorManager = $translatorManager;
        $this->translatorId = $translatorId;
        $this->translator = $translatorManager->findTranslatorById($translatorId);
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("name", "Jméno")
            ->setRequired();
        $form->addText("surname", "Příjmení");
        $form->addRadioList("sex", "Pohlaví", ["man" => "Muž", "woman" => "Žena"]);
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->translator) {
            $defaults = [
                "name" => $this->translator->name,
                "surname" => $this->translator->surname,
                "sex" => $this->translator->sex,
            ];
            $form->setDefaults($defaults);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "name" => $values["name"],
            "surname" => $values["surname"],
            "sex" => $values["sex"],
        ];

        if ($this->translator) {
            $this->translator->update($valuesToSave);
        } else {
            $this->translatorManager->saveTranslator($valuesToSave);
        }

        $this->onSuccess();
    }

}