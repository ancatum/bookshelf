<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Helpers\DurationHelper;
use App\Model\Manager\AuthorManager;
use App\Model\Manager\BookManager;
use App\Model\Manager\EditionManager;
use App\Model\Enum\BookTypeEnum;
use App\Model\Manager\GenreManager;
use App\Model\Manager\BookReaderManager;
use App\Model\Manager\PublisherManager;
use App\Model\Manager\SeriesManager;
use App\Model\Manager\TagManager;
use App\Model\Manager\TranslatorManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IBookFormFactory
{
    public function create(int $bookId = null): BookForm;
}

class BookForm extends Control
{

    /** @var BookManager */
    private $bookManager;

    /** @var AuthorManager */
    private $authorManager;

    /** @var GenreManager */
    private $genreManager;

    /** @var TagManager */
    private $tagManager;

    /** @var SeriesManager */
    private $seriesManager;

    /** @var PublisherManager */
    private $publisherManager;

    /** @var TranslatorManager */
    private $translatorManager;

    /** @var BookReaderManager */
    private $bookReaderManager;

    /** @var EditionManager */
    private $editionManager;

    /** @var int|null */
    private $bookId;

    /** @var ActiveRow|null */
    private $book;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        BookManager $bookManager,
        AuthorManager $authorManager,
        GenreManager $genreManager,
        TagManager $tagManager,
        SeriesManager $seriesManager,
        PublisherManager $publisherManager,
        TranslatorManager $translatorManager,
        BookReaderManager $bookReaderManager,
        EditionManager $editionManager,
        int $bookId = null
    )
    {
        $this->bookManager = $bookManager;
        $this->authorManager = $authorManager;
        $this->genreManager = $genreManager;
        $this->tagManager = $tagManager;
        $this->seriesManager = $seriesManager;
        $this->publisherManager = $publisherManager;
        $this->translatorManager = $translatorManager;
        $this->bookReaderManager = $bookReaderManager;
        $this->editionManager = $editionManager;
        $this->bookId = $bookId;
        $this->book = $bookManager->findBookById($bookId);
    }


    public function getForm(): BaseForm
    {
        $authorList = [];
        foreach ($this->authorManager->getAuthorListSorted() as $author) {
            $authorList[$author->id] = $author->name . " " . $author->surname;
        }
        $genreList = [];
        foreach ($this->genreManager->getGenreList() as $genre) {
            $genreList[$genre->id] = $genre->name;
        }
        $tagList = [];
        foreach ($this->tagManager->getTagListOrdered() as $tag) {
            $tagList[$tag->id] = $tag->name;
        }
        $seriesList = [];
        foreach ($this->seriesManager->getSeriesList() as $series) {
            $seriesList[$series->id] = $series->name;
        }

        $form = new BaseForm();
        $form->addGroup();
        $form->addText("name", "Název knihy")
            ->setRequired();
        $form->addMultiSelect("authors", "Autoři", $authorList);
        $form->addMultiSelect("genres", "Žánry", $genreList);
        $form->addMultiSelect("tags", "Štítky", $tagList);
        $form->addSelect("series", "Série", $seriesList)
            ->setPrompt("---")
            ->setRequired(false);
        $form->addText("part_of_series", "Číslo dílu v sérii");

        if (!$this->book) {
            $publisherList = [];
            foreach ($this->publisherManager->getPublisherListOrderedByName() as $publisher) {
                $publisherList[$publisher->id] = $publisher->name;
            }
            $translatorList = [];
            foreach ($this->translatorManager->getTranslatorListSorted() as $translator) {
                $translatorList[$translator->id] = $translator->name . " " . $translator->surname;
            }
            $bookReaderList = [];
            foreach ($this->bookReaderManager->getBookReaderListSorted() as $bookReader) {
                $bookReaderList[$bookReader->id] = $bookReader->name . " " . $bookReader->surname;
            }
            $form->addGroup("Vydání");
            $form->addSelect("publisher", "Nakladatelství", $publisherList)
                ->setPrompt("---");
            $form->addText("year", "Rok vydání");
            $form->addText("language", "Jazyk")
                ->setDefaultValue("cs");
            $form->addText("number_of_pages", "Počet stran");
            $form->addText("duration", "Doba trvání")
                ->setHtmlAttribute("id", "duration");
            $form->addRadioList("type", "Typ", BookTypeEnum::OPTIONS);
            $form->addMultiSelect("translators", "Překladatelé", $translatorList);
            $form->addMultiSelect("bookReaders", "Interpreti audioknihy", $bookReaderList);
        }

        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->book) {
            $defaults = [
                "name" => $this->book->name,
                "series_id" => $this->book->series,
                "part_of_series" => $this->book->part_of_series,
            ];
            foreach ($this->book->related("author_book") as $author_book) {
                $defaults["authors"][$author_book->author->id] = $author_book->author->id;
            }
            foreach ($this->book->related("book_genre") as $book_genre) {
                $defaults["genres"][$book_genre->genre->id] = $book_genre->genre->id;
            }
            foreach ($this->book->related("book_tag") as $book_tag) {
                $defaults["tags"][$book_tag->tag->id] = $book_tag->tag->id;
            }
            $form->setDefaults($defaults);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $bookValuesToSave = [
            "name" => $values["name"],
            "series_id" => $this->seriesManager->findSeriesById($values["series"]),
            "part_of_series" => $values["part_of_series"],
        ];
        if ($this->book) {
            $this->book->update($bookValuesToSave);
            $bookId = $this->book->id;
        } else {
            $book = $this->bookManager->saveBook($bookValuesToSave);
            $bookId = $book->id;
            $duration = $values["duration"] ? explode(":", $values["duration"]) : null;
            $editionValuesToSave = [
                "book_id" => $bookId,
                "publisher_id" => $values["publisher"],
                "year" => $values["year"],
                "language" => $values["language"],
                "number_of_pages" => $values["number_of_pages"],
                "duration" => DurationHelper::durationToSeconds($duration),
                "type" => $values["type"],
            ];
            $edition = $this->editionManager->saveEdition($editionValuesToSave);
            if ($values["translators"]) {
                $this->editionManager->saveTranslatorsToEdition($values["translators"], $edition->id);
            }
            if ($values["bookReaders"]) {
                $this->editionManager->saveBookReadersToEdition($values["bookReaders"], $edition->id);
            }
        }

        $this->authorManager->saveAuthorsToBook($values["authors"], $bookId);
        $this->bookManager->saveGenresToBook($values["genres"], $bookId);
        $this->bookManager->saveTagsToBook($values["tags"], $bookId);

        $this->onSuccess();
    }

}