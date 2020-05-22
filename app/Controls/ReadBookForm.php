<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Model\Manager\BookManager;
use App\Model\Manager\EditionManager;
use App\Model\Enum\SourceEnum;
use App\Model\Manager\ReadBookManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IReadBookFormFactory
{
    public function create(int $bookId = null, int $editionId = null, int $readBookId = null): ReadBookForm;
}

class ReadBookForm extends Control
{

    /** @var ReadBookManager */
    private $readBookManager;

    /** @var BookManager */
    private $bookManager;

    /** @var EditionManager */
    private $editionManager;

    /** @var int|null */
    private $bookId;

    /** @var ActiveRow|null */
    private $book;

    /** @var int|null */
    private $editionId;

    /** @var int|null */
    private $readBookId;

    /** @var ActiveRow|null */
    private $readBook;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        ReadBookManager $readBookManager,
        BookManager $bookManager,
        EditionManager $editionManager,
        int $bookId = null,
        int $editionId = null,
        int $readBookId = null
    )
    {
        $this->readBookManager = $readBookManager;
        $this->bookManager = $bookManager;
        $this->editionManager = $editionManager;
        $this->bookId = $bookId;
        $this->book = $bookManager->findBookById($bookId);
        $this->editionId = $editionId;
        $this->readBookId = $readBookId;
        $this->readBook = $readBookManager->findReadBookById($readBookId);
    }


    public function getForm(): BaseForm
    {
        $bookList = [];
        foreach ($this->bookManager->getBookListSorted()->order("name") as $book) {
            $bookAuthors = [];
            foreach ($book->related("author_book") as $author_book) {
                $bookAuthors[] = $author_book->author->name . " " . $author_book->author->surname;
            }
            $bookList[$book->id] = $book->name . " (" . implode(", ", $bookAuthors) . ")";
        }

        $form = new BaseForm();
        if ($this->book) {
            $bookList = [];
            $bookAuthors = [];
            foreach ($this->book->related("author_book") as $author_book) {
                $bookAuthors[] = $author_book->author->name . " " . $author_book->author->surname;
            }
            $bookList[$this->book->id] = $this->book->name . " (" . implode(", ", $bookAuthors) . ")";

            $editionList = [];
            foreach ($this->book->related("edition") as $edition) {
                $editionList[$edition->id] = $edition["publisher"]["name"] . " (" . $edition->year . "), " . $edition->type;
            }

            $form->addSelect("book", "Kniha", $bookList)
                ->setHtmlAttribute("id", "bookSelect");
            $form->addSelect("edition", "Vydání", $editionList)
                ->setHtmlAttribute("id", "editions")
                ->setDefaultValue($this->editionId);
        } elseif ($this->readBook) {
            $book = $this->readBook->book;
            $editionList = [];
            foreach ($book->related("edition") as $edition) {
                $editionList[$edition->id] = $edition["publisher"]["name"] . " (" . $edition->year . "), " . $edition->type;
            }

            $form->addSelect("book", "Kniha", $bookList)
                ->setHtmlAttribute("id", "bookSelect")
                ->setPrompt("---");
            $form->addSelect("edition", "Vydání", $editionList)
                ->setHtmlAttribute("id", "editions")
                ->setPrompt("---");
        } else {
            $form->addSelect("book", "Kniha", $bookList)
                ->setHtmlAttribute("id", "bookSelect")
                ->setPrompt("---");
            $form->addSelect("edition", "Vydání", [])
                ->setHtmlAttribute("id", "editions")
                ->setPrompt("---");
        }

        $form->addText("start", "Začátek čtení")
            ->setHtmlType("date");
        $form->addText("end", "Dočteno")
            ->setHtmlType("date");
        $form->addCheckbox("readingClub", "Čtenářský klub ČBDB");
        $form->addSelect("source", "Zdroj", SourceEnum::OPTIONS)
            ->setPrompt("---")
            ->setRequired();
        $form->addHidden("rating")
            ->setHtmlAttribute("id", "rating-input");
        $form->addRating("ratingStar", "Hodnocení");

        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->readBook) {
            $defaults = [
                "book" => $this->readBook["book"]["id"],
                "edition" => $this->readBook["edition"]["id"],
                "start" => $this->readBook->start ? (new \DateTime($this->readBook->start))->format("Y-m-d") : "",
                "end" => $this->readBook->end ? (new \DateTime($this->readBook->end))->format("Y-m-d") : "",
                "readingClub" => $this->readBook->reading_club,
                "source" => $this->readBook->source,
                "rating" => $this->readBook->rating,
            ];
            $form->setDefaults($defaults);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $valuesToSave = [
            "book_id" => $values["book"],
            "edition_id" => $values["edition"],
            "start" => ($values["start"] == "") ? null : new \DateTime($values["start"]),
            "end" => ($values["end"] == "") ? null : new \DateTime($values["end"]),
            "reading_club" => $values["readingClub"],
            "source" => $values["source"],
            "rating" => $values["rating"],
        ];
        if ($this->readBook) {
            $this->readBook->update($valuesToSave);
        } else {
            $this->readBookManager->saveReadBook($valuesToSave);
        }

        $this->onSuccess();
    }

}