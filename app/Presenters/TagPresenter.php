<?php

namespace App\Presenters;

use App\Controls\Base\BaseForm;
use App\Controls\ITagFormFactory;
use App\Model\Manager\TagManager;

class TagPresenter extends SecuredPresenter
{

    /** @var TagManager */
    private $tagManager;

    /** @var ITagFormFactory */
    private $tagFormFactory;

    /** @var int|null */
    private $tagId;


    public function __construct(
        TagManager $tagManager,
        ITagFormFactory $tagFormFactory
    )
    {
        $this->tagManager = $tagManager;
        $this->tagFormFactory = $tagFormFactory;
    }


    public function renderDefault(): void
    {
        $this->template->tags = $this->tagManager->getTagListOrdered();
    }


    public function createComponentTagForm(): BaseForm
    {
        $control = $this->tagFormFactory->create($this->tagId);
        $control->onSuccess[] = function () {
            $this->redirect("default");
        };

        return $control->getForm();
    }


    public function actionAdd(): void
    {
        $this->tagId = null;
    }


    public function actionEdit(int $tagId): void
    {
        $this->tagId = $tagId;
        $tag = $this->tagManager->findTagById($tagId);
        if (!$tag) {
            $this->error("Štítek nebyl nalezen");
        }
    }

}