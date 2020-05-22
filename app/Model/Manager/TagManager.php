<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class TagManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getTagListOrdered(): Selection
    {
        return $this->database->table("tag")->order("name");
    }


    public function findTagById(?int $id): ?ActiveRow
    {
        return $this->database->table("tag")->get($id);
    }


    public function saveTag(array $values): ActiveRow
    {
        return $this->database->table("tag")->insert($values);
    }

}