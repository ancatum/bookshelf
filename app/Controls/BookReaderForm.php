<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\BookReaderManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IBookReaderFormFactory
{
    public function create(int $bookReaderId = null): BookReaderForm;
}

class BookReaderForm extends Control
{

    /** @var BookReaderManager */
    private $bookReaderManager;

    /** @var int|null */
    private $bookReaderId;

    /** @var ActiveRow|null */
    private $bookReader;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        BookReaderManager $bookReaderManager,
        int $bookReaderId = null
    )
    {
        $this->bookReaderManager = $bookReaderManager;
        $this->bookReaderId = $bookReaderId;
        $this->bookReader = $bookReaderManager->findBookReaderById($bookReaderId);
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

        if ($this->bookReader) {
            $defaults = [
                "name" => $this->bookReader->name,
                "surname" => $this->bookReader->surname,
                "sex" => $this->bookReader->sex,
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

        if ($this->bookReader) {
            $this->bookReader->update($valuesToSave);
        } else {
            $this->bookReaderManager->saveBookReader($valuesToSave);
        }

        $this->onSuccess();
    }

}