<?php

namespace App\Model\Manager;

use App\Model\Enum\BookTypeEnum;
use App\Model\Enum\GenderEnum;
use App\Model\Enum\SourceEnum;
use Nette\Database\Context;
use Nette\SmartObject;

class ReportManager
{

    use SmartObject;

    /** @var Context */
    private $database;


    public function __construct(Context $database)
    {
        $this->database = $database;
    }


    public function getReadBookCountByYear(int $year): int
    {
        return $this->database->table("read_book")->where("YEAR(end)", $year)->count();
    }


    public function getReadBookCountByYearAndMonth(int $year, int $month): int
    {
        return $this->database->table("read_book")->where("YEAR(end)", $year)->where("MONTH(end)", $month)->count();
    }


    public function getReadBookAuthorsCountsByYear(int $year): array
    {
        $authorsCounts = [];
        $allAuthorsCounts = $this->database->fetch(
            "SELECT COUNT(DISTINCT(author.id)) AS author_count FROM read_book
                INNER JOIN book ON read_book.book_id = book.id
                INNER JOIN author_book ON author_book.book_id = book.id 
                INNER JOIN author ON author.id = author_book.author_id
                WHERE YEAR(read_book.end) = ?", $year
        );
        $authorsCounts["allAuthorsCount"] = $allAuthorsCounts["author_count"];

        foreach (GenderEnum::OPTIONS as $key => $value) {
            $countByGender = $this->database->fetch(
                "SELECT COUNT(DISTINCT(author.id)) AS author_$key FROM read_book
                INNER JOIN book ON read_book.book_id = book.id
                INNER JOIN author_book ON author_book.book_id = book.id 
                INNER JOIN author ON author.id = author_book.author_id
                WHERE author.sex = ? AND YEAR(read_book.end) = ?", $key, $year
            );
            $authorsCounts["author_$key"] = $countByGender["author_$key"];
        }

        $deadAuthors = $this->database->fetch(
            "SELECT COUNT(DISTINCT(author.id)) AS deadAuthor FROM read_book
            INNER JOIN book ON read_book.book_id = book.id
            INNER JOIN author_book ON author_book.book_id = book.id 
            INNER JOIN author ON author.id = author_book.author_id
            WHERE author.date_of_death <= read_book.end AND YEAR(read_book.end) = ?", $year
        );
        $authorsCounts["dead_authors"] = $deadAuthors["deadAuthor"];
        $authorsCounts["alive_authors"] = $allAuthorsCounts["author_count"] - $deadAuthors["deadAuthor"];

        return $authorsCounts;
    }


    public function getReadBookAuthorNationalitiesByYear(int $year): array
    {
        $nationalities = [];
        $czech = $this->database->fetch(
            "SELECT COUNT(DISTINCT(author.nationality_id)) AS czech FROM read_book
                INNER JOIN book ON read_book.book_id = book.id
                INNER JOIN author_book ON author_book.book_id = book.id
                INNER JOIN author ON author.id = author_book.author_id
                INNER JOIN nationality ON nationality.id = author.nationality_id
                WHERE nationality.name = ? AND YEAR(read_book.end) = ?", "česká", $year
        );
        $nationalities["česká"] = $czech["czech"];
        $other = $this->database->fetchAll(
            "SELECT DISTINCT(nationality.name) AS other_name, COUNT(DISTINCT(author.id)) AS other_count FROM read_book
                INNER JOIN book ON read_book.book_id = book.id
                INNER JOIN author_book ON author_book.book_id = book.id
                INNER JOIN author ON author.id = author_book.author_id
                INNER JOIN nationality ON nationality.id = author.nationality_id
                WHERE NOT nationality.name = ? AND YEAR(read_book.end) = ? 
                GROUP BY nationality.name 
                ORDER BY other_count DESC, other_name", "česká", $year
        );
        foreach ($other as $item) {
            $nationalities[$item["other_name"]] = $item["other_count"];
        }

        return $nationalities;
    }


    public function getReadBookTranslatorsCountsByYear(int $year): array
    {
        $translatorsCounts = [];
        $allTranslatorsCounts = $this->database->fetch(
            "SELECT COUNT(DISTINCT(translator.id)) AS translator_count FROM read_book
            INNER JOIN book ON read_book.book_id = book.id
            INNER JOIN edition ON edition.book_id = book.id
            INNER JOIN edition_translator ON edition_translator.edition_id = edition.id
            INNER JOIN translator ON translator.id = edition_translator.translator_id
            WHERE YEAR(read_book.end) = ?", $year
        );
        $translatorsCounts["allTranslatorsCount"] = $allTranslatorsCounts["translator_count"];

        foreach (GenderEnum::OPTIONS as $key => $value) {
            $countByGender = $this->database->fetch(
                "SELECT COUNT(DISTINCT(translator.id)) AS translator_$key FROM read_book
            INNER JOIN book ON read_book.book_id = book.id
            INNER JOIN edition ON edition.book_id = book.id
            INNER JOIN edition_translator ON edition_translator.edition_id = edition.id
            INNER JOIN translator ON translator.id = edition_translator.translator_id
            WHERE translator.sex = ? AND YEAR(read_book.end) = ?", $key, $year
            );
            $translatorsCounts["translator_$key"] = $countByGender["translator_$key"];
        }

        return $translatorsCounts;
    }


    public function getReadBookGenresCountsByYear(int $year): array
    {
        $genresCounts = [];
        $genres = $this->database->fetchAll(
            "SELECT genre.name AS genre_name, COUNT(genre.id) AS genre_count FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN book_genre ON book_genre.book_id = book.id
            INNER JOIN genre ON genre.id = book_genre.genre_id
            WHERE YEAR(read_book.end) = ?
            GROUP BY genre.name
            ORDER BY genre_count DESC, genre.name
            LIMIT 3", $year
        );
        foreach ($genres as $genre) {
            $genresCounts[$genre["genre_name"]] = $genre["genre_count"];
        }

        return $genresCounts;
    }


    public function getReadBookTagsCountsByYear(int $year): array
    {
        $tagsCounts = [];
        $tags = $this->database->fetchAll(
            "SELECT tag.name AS tag_name, COUNT(tag.id) AS tag_count FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN book_tag ON book_tag.book_id = book.id
            INNER JOIN tag ON tag.id = book_tag.tag_id
            WHERE YEAR(read_book.end) = ?
            GROUP BY tag.name
            ORDER BY tag_count DESC, tag.name
            LIMIT 10", $year
        );
        foreach ($tags as $tag) {
            $tagsCounts[$tag["tag_name"]] = $tag["tag_count"];
        }

        return $tagsCounts;
    }


    public function getReadBookTypesCountsByYear(int $year): array
    {
        $typesCounts = [];
        foreach (BookTypeEnum::OPTIONS as $key => $value) {
            $countByType = $this->database->fetch(
                "SELECT edition.type AS type_$key, COUNT(edition.type) as type_count FROM read_book
                INNER JOIN edition ON edition.id = read_book.edition_id
                WHERE edition.type = ? AND YEAR(read_book.end) = ?", $key, $year
            );
            $typesCounts[$value] = $countByType["type_count"];
        }

        return $typesCounts;
    }


    public function getReadBookSourcesCountsByYear(int $year): array
    {
        $sourcesCounts = [];
        foreach (SourceEnum::OPTIONS as $key => $value) {
            $countBySource = $this->database->fetch(
                "SELECT read_book.source AS source_$key, COUNT(read_book.source) AS source_count FROM read_book
                WHERE read_book.source = ? AND YEAR(read_book.end) = ?", $key, $year
            );
            $sourcesCounts[$value] = $countBySource["source_count"];
        }

        return $sourcesCounts;
    }


    public function getReadBookPagesCountsByYear(int $year): array
    {
        $counts = [];
        $isLeap = boolval((\DateTime::createFromFormat("Y", $year)->format("L")));

        $sumPages = $this->database->fetch(
            "SELECT SUM(edition.number_of_pages) AS sum_pages FROM read_book
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE NOT edition.type = ? AND YEAR(read_book.end) = ?", "audio", $year
        );
        $maxPages = $this->database->fetch(
            "SELECT edition.number_of_pages AS max_pages, book.name FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE NOT edition.type = ? AND YEAR(read_book.end) = ? ORDER BY max_pages DESC LIMIT 1", "audio", $year
        );
        $minPages = $this->database->fetch(
            "SELECT edition.number_of_pages AS min_pages, book.name FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE NOT edition.type = ? AND YEAR(read_book.end) = ? ORDER BY min_pages LIMIT 1", "audio", $year
        );
        $averagePages = $this->database->fetch(
            "SELECT AVG(edition.number_of_pages) AS average_pages FROM read_book
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE NOT edition.type = ? AND YEAR(read_book.end) = ?", "audio", $year
        );

        $counts["sumPages"] = $sumPages["sum_pages"];
        $counts["maxPages"]["pages"] = $maxPages["max_pages"];
        $counts["maxPages"]["name"] = $maxPages["name"];
        $counts["minPages"]["pages"] = $minPages["min_pages"];
        $counts["minPages"]["name"] = $minPages["name"];
        $counts["averagePages"] = $averagePages["average_pages"];
        $counts["averagePagesPerDay"] = $isLeap ? ($sumPages["sum_pages"] / 366) : ($sumPages["sum_pages"] / 365);

        return $counts;
    }


    public function getReadBookDurationsByYear(int $year): array
    {
        $durations = [];
        $isLeap = boolval(\DateTime::createFromFormat("Y", $year)->format("L"));

        $sumDuration = $this->database->fetch(
            "SELECT SUM(edition.duration) AS duration FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE edition.type = ? AND YEAR(read_book.end) = ?", "audio", $year
        );
        $maxDuration = $this->database->fetch(
            "SELECT edition.duration AS duration, book.name FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE edition.type = ? AND YEAR(read_book.end) = ? ORDER BY duration DESC LIMIT 1", "audio", $year
        );
        $minDuration = $this->database->fetch(
            "SELECT edition.duration AS duration, book.name FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE edition.type = ? AND YEAR(read_book.end) = ? ORDER BY duration LIMIT 1", "audio", $year
        );
        $averageDuration = $this->database->fetch(
            "SELECT AVG(edition.duration) AS duration FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE edition.type = ? AND YEAR(read_book.end) = ?", "audio", $year
        );

        $durations["sumDuration"] = $sumDuration["duration"];
        $durations["maxDuration"]["duration"] = $maxDuration["duration"];
        $durations["maxDuration"]["name"] = $maxDuration["name"];
        $durations["minDuration"]["duration"] = $minDuration["duration"];
        $durations["minDuration"]["name"] = $minDuration["name"];
        $durations["averageDuration"] = $averageDuration["duration"];
        $durations["averageDurationPerDay"] = $isLeap ? ($sumDuration["duration"] / 366) : ($sumDuration["duration"] / 365);

        return $durations;
    }


    public function getReadBookBookReadersCountsByYear(int $year): array
    {
        $bookReadersCounts = [];
        $allBookReadersCounts = $this->database->fetch(
            "SELECT COUNT(DISTINCT(book_reader.id)) AS book_reader_count FROM read_book
            INNER JOIN book ON read_book.book_id = book.id
            INNER JOIN edition ON edition.book_id = book.id
            INNER JOIN edition_book_reader ON edition_book_reader.edition_id = edition.id
            INNER JOIN book_reader ON book_reader.id = edition_book_reader.book_reader_id
            WHERE YEAR(read_book.end) = ?", $year
        );
        $bookReadersCounts["allBookReadersCount"] = $allBookReadersCounts["book_reader_count"];

        foreach (GenderEnum::OPTIONS as $key => $value) {
            $countByGender = $this->database->fetch(
                "SELECT COUNT(DISTINCT(book_reader.id)) AS book_reader_$key FROM read_book
            INNER JOIN book ON read_book.book_id = book.id
            INNER JOIN edition ON edition.book_id = book.id
            INNER JOIN edition_book_reader ON edition_book_reader.edition_id = edition.id
            INNER JOIN book_reader ON book_reader.id = edition_book_reader.book_reader_id
            WHERE book_reader.sex = ? AND YEAR(read_book.end) = ?", $key, $year
            );
            $bookReadersCounts["book_reader_$key"] = $countByGender["book_reader_$key"];
        }

        return $bookReadersCounts;
    }


    public function getReadBookRatingsByYear(int $year): array
    {
        $rating = [];

        $sumRating = $this->database->fetch(
            "SELECT SUM(rating) AS sum_rating FROM read_book
            WHERE YEAR(read_book.end) = ?", $year
        );
        $averageRating = $this->database->fetch(
            "SELECT AVG(rating) AS average_rating FROM read_book
            WHERE YEAR(read_book.end) = ?", $year
        );

        $rating["sumRating"] = $sumRating["sum_rating"];
        $rating["averageRating"] = $averageRating["average_rating"];

        return $rating;
    }


    public function getReadBookReadingClubCountByYear(int $year): array
    {
        $readingClub = [];

        $readingClubCount = $this->database->fetch(
            "SELECT COUNT(reading_club) as reading_club_count FROM read_book
            WHERE YEAR(read_book.end) = ? AND read_book.reading_club = ?", $year, true
        );
        $readingClubBooks = $this->database->fetchAll(
            "SELECT MONTH(read_book.end) as reading_club_month, book.name as reading_club_book FROM read_book
            INNER JOIN book ON book.id = read_book.book_id
            WHERE YEAR(read_book.end) = ? AND read_book.reading_club = ? ORDER BY MONTH(read_book.end)", $year, true
        );
        $readingClub["count"] = $readingClubCount["reading_club_count"];
        foreach ($readingClubBooks as $readingClubBook) {
            $readingClub[$readingClubBook["reading_club_month"]] = $readingClubBook["reading_club_book"];
        }

        return $readingClub;
    }


    public function getReadBookLanguageCountsByYear(int $year): array
    {
        $languages = [];

        $languagesSum = $this->database->fetch(
            "SELECT COUNT(DISTINCT(edition.language)) as languages_sum FROM read_book
            INNER JOIN edition ON edition.id = read_book.edition_id
            WHERE YEAR(read_book.end) = ? ", $year
        );
        $countByLanguage = $this->database->fetchAll(
            "SELECT COUNT(edition.language) AS lang_count, edition.language AS lang_name FROM read_book
                INNER JOIN edition ON edition.id = read_book.edition_id
                WHERE YEAR(read_book.end) = ? GROUP BY edition.language", $year
        );
        $languages["sum"] = $languagesSum["languages_sum"];
        foreach ($countByLanguage as $language) {
            $languages[$language["lang_name"]] = $language["lang_count"];
        }

        return $languages;
    }
}