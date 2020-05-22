<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class TranslatorManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getTranslatorListSorted(string $sortBy = null, string $direction = null): Selection
    {
        $translators = $this->database->table("translator");
        if ($sortBy) {
            $translators->order($sortBy . " " . strtoupper($direction) ?: "ASC");
        }

        return $translators;
    }


    public function findTranslatorById(?int $id): ?ActiveRow
    {
        return $this->database->table("translator")->get($id);
    }


    public function saveTranslator(array $values): ActiveRow
    {
        return $this->database->table("translator")->insert($values);
    }

}