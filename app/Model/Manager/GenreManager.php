<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class GenreManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getGenreList(): Selection
    {
        return $this->database->table("genre");
    }


    public function findGenreById(?int $id): ?ActiveRow
    {
        return $this->database->table("genre")->get($id);
    }


    public function saveGenre(array $values): ActiveRow
    {
        return $this->database->table("genre")->insert($values);
    }

}