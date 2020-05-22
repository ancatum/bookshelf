<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IGenreFormFactory;
use App\Model\Manager\GenreManager;

class GenrePresenter extends SecuredPresenter
{

    /** @var GenreManager */
    private $genreManager;

    /** @var IGenreFormFactory */
    private $genreFormFactory;

    /** @var int|null */
    private $genreId;


    public function __construct(
        GenreManager $genreManager,
        IGenreFormFactory $genreFormFactory
    )
    {
        $this->genreManager = $genreManager;
        $this->genreFormFactory = $genreFormFactory;
    }


    public function renderDefault(): void
    {
        $this->template->genres = $this->genreManager->getGenreList();
    }


    public function createComponentGenreForm(): BaseForm
    {
        $control = $this->genreFormFactory->create($this->genreId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->genreId = null;
    }


    public function actionEdit(int $genreId): void
    {
        $this->genreId = $genreId;
        $genre = $this->genreManager->findGenreById($genreId);
        if (!$genre) {
            $this->error("Žánr nebyl nalezen");
        }
    }

}