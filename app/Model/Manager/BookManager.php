<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class BookManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getBookListSorted(string $sortBy = null, string $direction = null): Selection
    {
        $books = $this->database->table("book");
        if ($sortBy) {
            $books->order($sortBy . " " . strtoupper($direction) ?: "ASC");
        }

        return $books;
    }


    public function findBookById(?int $id): ?ActiveRow
    {
        return $this->database->table("book")->get($id);
    }


    public function saveBook(array $values): ActiveRow
    {
        return $this->database->table("book")->insert($values);
    }


    public function saveGenresToBook(array $genresIds, int $bookId): void
    {
        $this->database->table("book_genre")->where("book_id = ?", $bookId)->delete();
        foreach ($genresIds as $genreId) {
            $this->database->table("book_genre")->insert(["book_id" => $bookId, "genre_id" => $genreId]);
        }
    }


    public function saveTagsToBook(array $tagsIds, int $bookId): void
    {
        $this->database->table("book_tag")->where("book_id = ?", $bookId)->delete();
        foreach ($tagsIds as $tagId) {
            $this->database->table("book_tag")->insert(["book_id" => $bookId, "tag_id" => $tagId]);
        }
    }

}