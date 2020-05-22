<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\GenreManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IGenreFormFactory
{
    public function create(int $genreId = null): GenreForm;
}

class GenreForm extends Control
{

    /** @var GenreManager */
    private $genreManager;

    /** @var int|null */
    private $genreId;

    /** @var ActiveRow|null */
    private $genre;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        GenreManager $genreManager,
        int $genreId = null
    )
    {
        $this->genreManager = $genreManager;
        $this->genreId = $genreId;
        $this->genre = $genreManager->findGenreById($genreId);
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("name", "Žánr")
            ->setRequired();
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->genre) {
            $form->setDefaults([
                "name" => $this->genre->name,
            ]);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "name" => $values["name"],
        ];

        if ($this->genre) {
            $this->genre->update($valuesToSave);
        } else {
            $this->genreManager->saveGenre($valuesToSave);
        }

        $this->onSuccess();
    }

}