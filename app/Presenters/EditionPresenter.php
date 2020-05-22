<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IEditionFormFactory;
use App\Model\Manager\BookManager;
use App\Model\Manager\EditionManager;

class EditionPresenter extends SecuredPresenter
{

    /** @var EditionManager */
    private $editionManager;

    /** @var IEditionFormFactory */
    private $editionFormFactory;

    /** @var BookManager */
    private $bookManager;

    /** @var int */
    private $bookId;

    /** @var int|null */
    private $editionId;


    public function __construct(
        EditionManager $editionManager,
        IEditionFormFactory $editionFormFactory,
        BookManager $bookManager
    )
    {
        $this->editionManager = $editionManager;
        $this->editionFormFactory = $editionFormFactory;
        $this->bookManager = $bookManager;
    }


    public function createComponentEditionForm(): BaseForm
    {
        $control = $this->editionFormFactory->create($this->bookId, $this->editionId);
        $control->onSuccess[] = function () {
            $this->redirect("Book:detail", $this->bookId);
        };

        return $control->getForm();
    }


    public function actionAdd(int $bookId): void
    {
        $this->bookId = $bookId;
        $this->editionId = null;
        $book = $this->bookManager->findBookById($this->bookId);
        if (!$book) {
            $this->error("Kniha nebyla nalezena");
        }
    }


    public function actionEdit(int $bookId, int $editionId): void
    {
        $this->bookId = $bookId;
        $this->editionId = $editionId;
        $book = $this->bookManager->findBookById($this->bookId);
        if (!$book) {
            $this->error("Kniha nebyla nalezena");
        }
        $edition = $this->editionManager->findEditionById($this->editionId);
        if (!$edition) {
            $this->error("Vydání nebylo nalezeno");
        }
    }
}