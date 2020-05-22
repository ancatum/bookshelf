<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class ReadBookManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getReadBookListSorted(string $sortBy = null, string $direction = null): Selection
    {
        $bookList = $this->database->table("read_book");
        if ($sortBy) {
            if ($sortBy == "book") {
                $bookList->joinWhere("book", "book.id = read_book.book_id");
                $bookList->order("book.name ASC");
            } else {
                $bookList->order($sortBy . " " . strtoupper($direction) ?: "ASC");
            }
        }

        return $bookList;
    }


    public function findReadBookById(?int $id): ?ActiveRow
    {
        return $this->database->table("read_book")->get($id);
    }


    public function saveReadBook(array $values): ActiveRow
    {
        return $this->database->table("read_book")->insert($values);
    }


    public function findReadBookOldestYear(): ?int
    {
        $result = $this->database->table("read_book")->select("MIN(YEAR(end)) AS year")->where("end NOT", NULL);

        /** @var ActiveRow $row */
        $row = $result[0];
        if ($row) {
            return $row->year;
        }
        return null;
    }

}