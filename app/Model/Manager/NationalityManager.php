<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class NationalityManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getNationalityListOrderedByName(): Selection
    {
        return $this->database->table("nationality")->order("name");
    }


    public function findNationalityById(?int $id): ?ActiveRow
    {
        return $this->database->table("nationality")->get($id);
    }


    public function saveNationality(array $values): ActiveRow
    {
        return $this->database->table("nationality")->insert($values);
    }

}