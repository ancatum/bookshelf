<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class SeriesManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getSeriesList(): Selection
    {
        return $this->database->table("series");
    }


    public function findSeriesById(?int $id): ?ActiveRow
    {
        return $this->database->table("series")->get($id);
    }


    public function saveSeries(array $values): ActiveRow
    {
        return $this->database->table("series")->insert($values);
    }

}