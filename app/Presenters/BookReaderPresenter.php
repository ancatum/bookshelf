<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IBookReaderFormFactory;
use App\Model\Manager\BookReaderManager;

class BookReaderPresenter extends SecuredPresenter
{

    /** @var BookReaderManager */
    private $bookReaderManager;

    /** @var IBookReaderFormFactory */
    private $bookReaderFormFactory;

    /** @var int|null */
    private $bookReaderId;

    /** @var int */
    private $perPage;


    public function __construct(
        int $perPage,
        BookReaderManager $bookReaderManager,
        IBookReaderFormFactory $bookReaderFormFactory
    )
    {
        parent::__construct();
        $this->perPage = $perPage;
        $this->bookReaderManager = $bookReaderManager;
        $this->bookReaderFormFactory = $bookReaderFormFactory;
    }


    public function renderDefault(int $page = 1, string $sortBy = null, string $direction = null): void
    {
        $interpreters = $this->bookReaderManager->getBookReaderListSorted($sortBy, $direction);
        $lastPage = 0;
        $this->template->interpreters = $interpreters->page($page, $this->perPage, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
        $this->template->sortBy = $sortBy;
        $this->template->direction = $direction;
        $this->template->defaultSort = "{$sortBy}-{$direction}";
    }


    public function createComponentBookReaderForm(): BaseForm
    {
        $control = $this->bookReaderFormFactory->create($this->bookReaderId);
        $control->onSuccess[] = function () {
            $this->flashMessage("něco");
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->bookReaderId = null;
    }


    public function actionEdit(int $bookReaderId): void
    {
        $this->bookReaderId = $bookReaderId;
        $bookReader = $this->bookReaderManager->findBookReaderById($bookReaderId);
        if (!$bookReader) {
            $this->error("Interpret audioknih nebyl nalezen");
        }
    }

}