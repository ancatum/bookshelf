<?php

namespace App\Model\Manager;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class EditionManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getEditionList(): Selection
    {
        return $this->database->table("edition");
    }


    public function findEditionById(?int $id): ?ActiveRow
    {
        return $this->database->table("edition")->get($id);
    }


    public function findEditionListByBookId(int $bookId): ?Selection
    {
        return $this->database->table("edition")->where("book_id = ?", $bookId);
    }


    public function saveEdition(array $values): ActiveRow
    {
        return $this->database->table("edition")->insert($values);
    }


    public function saveTranslatorsToEdition(array $translatorsIds, int $editionId): void
    {
        $this->database->table("edition_translator")->where("edition_id = ?", $editionId)->delete();
        foreach ($translatorsIds as $translatorId) {
            $this->database->table("edition_translator")->insert(["edition_id" => $editionId, "translator_id" => $translatorId]);
        }
    }


    public function saveBookReadersToEdition(array $bookReadersIds, int $editionId): void
    {
        $this->database->table("edition_book_reader")->where("edition_id = ?", $editionId)->delete();
        foreach ($bookReadersIds as $bookReaderId) {
            $this->database->table("edition_book_reader")->insert(["edition_id" => $editionId, "book_reader_id" => $bookReaderId]);
        }
    }

}