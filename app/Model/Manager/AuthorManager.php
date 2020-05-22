<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class AuthorManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getAuthorListSorted(string $sortBy = null, string $direction = null): Selection
    {
        $authors = $this->database->table("author");
        if ($sortBy) {
            if ($sortBy == "nationality") {
                $authors->joinWhere("nationality", "nationality.id = author.nationality_id");
                $authors->order("nationality.name " . strtoupper($direction) ?: "ASC");
            } else {
                $authors->order($sortBy . " " . strtoupper($direction) ?: "ASC");
            }
        }

        return $authors;
    }


    public function findAuthorById(?int $id): ?ActiveRow
    {
        return $this->database->table("author")->get($id);
    }


    public function saveAuthor(array $values): ActiveRow
    {
        return $this->database->table("author")->insert($values);
    }


    public function saveAuthorsToBook(array $authorsIds, int $bookId): void
    {
        $this->database->table("author_book")->where("book_id = ?", $bookId)->delete();
        foreach ($authorsIds as $authorId) {
            $this->database->table("author_book")->insert(["author_id" => $authorId, "book_id" => $bookId]);
        }
    }

}