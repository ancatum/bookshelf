<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\NationalityManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface INationalityFormFactory
{
    public function create(int $nationalityId = null): NationalityForm;
}

class NationalityForm extends Control
{

    /** @var NationalityManager */
    private $nationalityManager;

    /** @var int|null */
    private $nationalityId;

    /** @var ActiveRow|null */
    private $nationality;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        NationalityManager $nationalityManager,
        int $nationalityId = null
    )
    {
        $this->nationalityManager = $nationalityManager;
        $this->nationalityId = $nationalityId;
        $this->nationality = $nationalityManager->findNationalityById($nationalityId);
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("name", "Národnost")
            ->setRequired();
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->nationality) {
            $form->setDefaults([
                "name" => $this->nationality->name,
            ]);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "name" => $values["name"],
        ];

        if ($this->nationality) {
            $this->nationality->update($valuesToSave);
        } else {
            $this->nationalityManager->saveNationality($valuesToSave);
        }

        $this->onSuccess();
    }

}