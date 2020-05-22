<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\TagManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface ITagFormFactory
{
    public function create(int $tagId = null): TagForm;
}

class TagForm extends Control
{

    /** @var TagManager */
    private $tagManager;

    /** @var int|null */
    private $tagId;

    /** @var ActiveRow|null */
    private $tag;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        TagManager $tagManager,
        int $tagId = null
    )
    {
        $this->tagManager = $tagManager;
        $this->tagId = $tagId;
        $this->tag = $tagManager->findTagById($tagId);
    }


    public function getForm(): BaseForm
    {
        $form = new BaseForm();
        $form->addText("name", "Štítek")
            ->setRequired();
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->tag) {
            $form->setDefaults([
                "name" => $this->tag->name,
            ]);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "name" => $values["name"],
        ];

        if ($this->tag) {
            $this->tag->update($valuesToSave);
        } else {
            $this->tagManager->saveTag($valuesToSave);
        }

        $this->onSuccess();
    }

}