<?php

namespace App\Controls;

use App\Controls\Base\BaseForm;
use App\Helpers\DurationHelper;
use App\Model\Manager\BookReaderManager;
use App\Model\Manager\EditionManager;
use App\Model\Enum\BookTypeEnum;
use App\Model\Manager\PublisherManager;
use App\Model\Manager\TranslatorManager;
use Nette\Application\UI\Control;
use Nette\Database\Table\ActiveRow;

interface IEditionFormFactory
{
    public function create(int $bookId, int $editionId = null): EditionForm;
}

class EditionForm extends Control
{

    /** @var EditionManager */
    private $editionManager;

    /** @var PublisherManager */
    private $publisherManager;

    /** @var TranslatorManager */
    private $translatorManager;

    /** @var BookReaderManager */
    private $bookReaderManager;

    /** @var int */
    private $bookId;

    /** @var int|null */
    private $editionId;

    /** @var ActiveRow|null */
    private $edition;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        EditionManager $editionManager,
        PublisherManager $publisherManager,
        TranslatorManager $translatorManager,
        BookReaderManager $bookReaderManager,
        int $bookId,
        int $editionId = null
    )
    {
        $this->editionManager = $editionManager;
        $this->publisherManager = $publisherManager;
        $this->translatorManager = $translatorManager;
        $this->bookReaderManager = $bookReaderManager;
        $this->bookId = $bookId;
        $this->editionId = $editionId;
        $this->edition = $editionManager->findEditionById($editionId);
    }


    public function getForm(): BaseForm
    {
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

        $form = new BaseForm();
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

        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, "processForm"];

        if ($this->edition) {
            $duration = DurationHelper::secondsToDuration($this->edition->duration);
            $defaults = [
                "publisher" => $this->edition->publisher,
                "year" => $this->edition->year,
                "language" => $this->edition->language,
                "number_of_pages" => $this->edition->number_of_pages,
                "durationHours" => $duration["hours"],
                "durationMinutes" => $duration["minutes"],
                "durationSeconds" => $duration["seconds"],
                "type" => $this->edition->type,
            ];
            foreach ($this->edition->related("edition_translator") as $edition_translator) {
                $defaults["translators"][$edition_translator->translator->id] = $edition_translator->translator->id;
            }
            foreach ($this->edition->related("edition_book_reader") as $edition_book_reader) {
                $defaults["bookReaders"][$edition_book_reader->book_reader->id] = $edition_book_reader->book_reader->id;
            }
            $form->setDefaults($defaults);
        }

        return $form;
    }


    public function processForm(BaseForm $form, array $values): void
    {
        $duration = $values["duration"] ? explode(":", $values["duration"]) : null;
        $valuesToSave = [
            "book_id" => $this->bookId,
            "publisher_id" => $values["publisher"],
            "year" => $values["year"],
            "language" => $values["language"],
            "number_of_pages" => $values["number_of_pages"],
            "duration" => DurationHelper::durationToSeconds($duration),
            "type" => $values["type"],
        ];
        if ($this->edition) {
            $this->edition->update($valuesToSave);
            $editionId = $this->edition->id;
        } else {
            $edition = $this->editionManager->saveEdition($valuesToSave);
            $editionId = $edition->id;
        }
        $this->editionManager->saveTranslatorsToEdition($values["translators"], $editionId);
        $this->editionManager->saveBookReadersToEdition($values["bookReaders"], $editionId);

        $this->onSuccess();
    }

}