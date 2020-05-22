<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class PublisherManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getPublisherListOrderedByName(): Selection
    {
        return $this->database->table("publisher")->order("name");
    }


    public function findPublisherById(?int $id): ?ActiveRow
    {
        return $this->database->table("publisher")->get($id);
    }


    public function savePublisher(array $values): ActiveRow
    {
        return $this->database->table("publisher")->insert($values);
    }

}