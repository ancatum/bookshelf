<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IBookFormFactory;
use App\Model\Manager\BookManager;
use Nette\Utils\Paginator;

class BookPresenter extends SecuredPresenter
{

    /** @var BookManager */
    private $bookManager;

    /** @var IBookFormFactory */
    private $bookFormFactory;

    /** @var int|null */
    private $bookId;

    /** @var int */
    private $perPage;


    public function __construct(
        int $perPage,
        BookManager $bookManager,
        IBookFormFactory $bookFormFactory
    )
    {
        parent::__construct();
        $this->perPage = $perPage;
        $this->bookManager = $bookManager;
        $this->bookFormFactory = $bookFormFactory;
    }


    public function renderDefault(int $page = 1, string $sortBy = null, string $direction = null): void
    {
        $books = $this->bookManager->getBookListSorted($sortBy, $direction);
        $lastPage = 0;
        $this->template->books = $books->page($page, $this->perPage, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
        $this->template->sortBy = $sortBy;
        $this->template->direction = $direction;
        $this->template->defaultSort = "{$sortBy}-{$direction}";
    }


    public function createComponentBookForm(): BaseForm
    {
        $control = $this->bookFormFactory->create($this->bookId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->bookId = null;
    }


    public function actionEdit(int $bookId): void
    {
        $this->bookId = $bookId;
        $book = $this->bookManager->findBookById($bookId);
        if (!$book) {
            $this->error("Kniha nebyla nalezena");
        }
    }


    public function renderDetail(int $bookId): void
    {
        $book = $this->bookManager->findBookById($bookId);
        if (!$book) {
            $this->error("Kniha nebyla nalezena");
        }
        $this->template->book = $book;
    }

}