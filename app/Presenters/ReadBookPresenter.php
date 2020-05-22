<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IReadBookFormFactory;
use App\Model\Manager\BookManager;
use App\Model\Manager\ReadBookManager;

class ReadBookPresenter extends SecuredPresenter
{

    /** @var ReadBookManager */
    private $readBookManager;

    /** @var BookManager */
    private $bookManager;

    /** @var IReadBookFormFactory */
    private $readBookFormFactory;

    /** @var int|null */
    private $readBookId;

    /** @var int|null */
    private $bookId;

    /** @var int|null */
    private $editionId;

    /** @var int */
    private $perPage;


    public function __construct(
        int $perPage,
        ReadBookManager $readBookManager,
        BookManager $bookManager,
        IReadBookFormFactory $readBookFormFactory
    )
    {
        parent::__construct();
        $this->perPage = $perPage;
        $this->readBookManager = $readBookManager;
        $this->readBookFormFactory = $readBookFormFactory;
        $this->bookManager = $bookManager;
    }


    public function renderDefault(int $page = 1, string $sortBy = null, string $direction = null): void
    {
        $readBooks = $this->readBookManager->getReadBookListSorted($sortBy, $direction);
        $lastPage = 0;
        $this->template->readBooks = $readBooks->page($page, $this->perPage, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
        $this->template->sortBy = $sortBy;
        $this->template->direction = $direction;
        $this->template->defaultSort = "{$sortBy}-{$direction}";
    }


    public function createComponentReadBookForm(): BaseForm
    {
        $control = $this->readBookFormFactory->create($this->bookId, $this->editionId, $this->readBookId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(int $bookId = null, int $editionId = null): void
    {
        $this->bookId = $bookId;
        $this->editionId = $editionId;
        $this->readBookId = null;
    }


    public function actionEdit(int $readBookId): void
    {
        $this->readBookId = $readBookId;
        $readBook = $this->readBookManager->findReadBookById($this->readBookId);
        if (!$readBook) {
            $this->error("Přečtená kniha nebyla nalezena");
        }
    }


    public function renderDetail(int $readBookId): void
    {
        $readBook = $this->readBookManager->findReadBookById($readBookId);
        if (!$readBook) {
            $this->error("Přečtená kniha nebyla nalezena");
        }
        $this->template->readBook = $readBook;
        $this->template->edition = $readBook->edition;
        $book = $readBook->book;
        $this->template->readBookList = $book->related("read_book")->order("end");
    }


    public function handleEditions(string $bookId = null): void
    {
        $editionsOptions = [];
        if ($bookId != "") {
            $book = $this->bookManager->findBookById($bookId);
            if ($book->related("edition")->count() != 0) {
                foreach ($book->related("edition") as $edition) {
                    $editionsOptions[$edition->id] = $edition["publisher"]["name"] . " (" . $edition->year . "), " . $edition->type;
                }
            }
        }

        $this->payload->editionsOptions = $editionsOptions;
        $this->sendPayload();
    }

}