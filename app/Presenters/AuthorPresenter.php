<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\IAuthorFormFactory;
use App\Model\Manager\AuthorManager;

class AuthorPresenter extends SecuredPresenter
{

    /** @var AuthorManager */
    private $authorManager;

    /** @var IAuthorFormFactory */
    private $authorFormFactory;

    /** @var int|null */
    private $authorId;

    /** @var int */
    private $perPage;

    public function __construct(
        int $perPage,
        AuthorManager $authorManager,
        IAuthorFormFactory $authorFormFactory
    )
    {
        parent::__construct();
        $this->perPage = $perPage;
        $this->authorFormFactory = $authorFormFactory;
        $this->authorManager = $authorManager;
    }


    public function renderDefault(int $page = 1, string $sortBy = null, string $direction = null): void
    {
        $authors = $this->authorManager->getAuthorListSorted($sortBy, $direction);
        $lastPage = 0;
        $this->template->authors = $authors->page($page, $this->perPage, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
        $this->template->sortBy = $sortBy;
        $this->template->direction = $direction;
        $this->template->defaultSort = "{$sortBy}-{$direction}";
    }


    protected function createComponentAuthorForm(): BaseForm
    {
        $control = $this->authorFormFactory->create($this->authorId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->authorId = null;
    }


    public function actionEdit(int $authorId): void
    {
        $this->authorId = $authorId;
        $author = $this->authorManager->findAuthorById($authorId);
        if (!$author) {
            $this->error("Autor nebyl nalezen");
        }
    }

}