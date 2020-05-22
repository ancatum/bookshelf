<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class BookReaderManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getBookReaderListSorted(string $sortBy = null, string $direction = null): Selection
    {
        $bookReaders = $this->database->table("book_reader");
        if ($sortBy) {
            $bookReaders->order($sortBy . " " . strtoupper($direction) ?: "ASC");
        }

        return $bookReaders;
    }


    public function findBookReaderById(?int $id): ?ActiveRow
    {
        return $this->database->table("book_reader")->get($id);
    }


    public function saveBookReader(array $values): ActiveRow
    {
        return $this->database->table("book_reader")->insert($values);
    }

}