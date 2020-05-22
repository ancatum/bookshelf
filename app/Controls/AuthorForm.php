<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\AuthorManager;
use App\Model\Enum\GenderEnum;
use App\Model\Manager\NationalityManager;
use DateTime;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IAuthorFormFactory
{
    public function create(int $authorId = null): AuthorForm;
}

class AuthorForm extends Control
{

    /** @var AuthorManager */
    private $authorManager;

    /** @var NationalityManager */
    private $nationalityManager;

    /** @var int|null */
    private $authorId;

    /** @var ActiveRow|null */
    private $author;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        AuthorManager $authorManager,
        NationalityManager $nationalityManager,
        int $authorId = null
    )
    {
        $this->authorManager = $authorManager;
        $this->nationalityManager = $nationalityManager;
        $this->authorId = $authorId;
        $this->author = $authorManager->findAuthorById($authorId);
    }


    public function getForm(): BaseForm
    {
        $nationalities = [];
        foreach ($this->nationalityManager->getNationalityListOrderedByName() as $nationality) {
            $nationalities[$nationality->id] = $nationality->name;
        }
        $form = new BaseForm();
        $form->addText("name", "Jméno")
            ->setRequired();
        $form->addText("surname", "Příjmení");
        $form->addText("date_of_birth", "Datum narození")
            ->setHtmlType("date");
        $form->addText("date_of_death", "Datum úmrtí")
            ->setHtmlType("date");
        $form->addSelect("nationality", "Národnost", $nationalities)
            ->setPrompt("---");
        $form->addRadioList("sex", "Pohlaví", GenderEnum::OPTIONS);
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->author) {
            $defaults = [
                "name" => $this->author->name,
                "surname" => $this->author->surname,
                "date_of_birth" => $this->author->date_of_birth ? (new DateTime($this->author->date_of_birth))->format("Y-m-d") : "",
                "date_of_death" => $this->author->date_of_death ? (new DateTime($this->author->date_of_death))->format("Y-m-d") : "",
                "nationality" => $this->author->nationality,
                "sex" => $this->author->sex,
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
            "date_of_birth" => ($values["date_of_birth"] == "") ? null : new DateTime($values["date_of_birth"]),
            "date_of_death" => ($values["date_of_death"] == "") ? null : new DateTime($values["date_of_death"]),
            "nationality_id" => $values["nationality"],
            "sex" => $values["sex"],
        ];

        if ($this->author) {
            $this->author->update($valuesToSave);
        } else {
            $this->authorManager->saveAuthor($valuesToSave);
        }

        $this->onSuccess();
    }

}