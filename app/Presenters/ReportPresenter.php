<?php

namespace App\Presenters;

use App\Model\Manager\ReadBookManager;
use App\Model\Manager\ReportManager;

class ReportPresenter extends SecuredPresenter
{

    /** @var ReportManager */
    private $reportManager;

    /** @var ReadBookManager */
    private $readBookManager;


    public function __construct(
        ReportManager $reportManager,
        ReadBookManager $readBookManager
    )
    {
        $this->reportManager = $reportManager;
        $this->readBookManager = $readBookManager;
    }


    public function renderDefault(): void
    {
        $newestYear = intval((new \DateTime("now"))->format("Y"));
        $oldestYear = $this->readBookManager->findReadBookOldestYear();

        $this->template->oldestYear = $oldestYear ?? $newestYear;
        $this->template->newestYear = $newestYear;

        $readBookCount = [];
        $pagesCount = [];
        $duration = [];
        $rating = [];
        for ($i = $newestYear; $i >= $oldestYear; $i--) {
            $readBookCount[$i] = $this->reportManager->getReadBookCountByYear($i);
            $allPagesCounts = $this->reportManager->getReadBookPagesCountsByYear($i);
            $allDurations = $this->reportManager->getReadBookDurationsByYear($i);
            $allRatings = $this->reportManager->getReadBookRatingsByYear($i);

            $pagesCount[$i] = $allPagesCounts["sumPages"];
            $duration[$i] = $allDurations["sumDuration"];
            $rating[$i] = $allRatings["averageRating"];
        }
        $this->template->readBookYearCount = $readBookCount;
        $this->template->pagesCount = $pagesCount;
        $this->template->duration = $duration;
        $this->template->rating = $rating;
    }


    public function renderDetail(int $year): void
    {
        $this->template->year = $year;
        $this->template->readBookYearCount = $this->reportManager->getReadBookCountByYear($year);
        $this->template->authorsCounts = $this->reportManager->getReadBookAuthorsCountsByYear($year);
        $this->template->nationalitiesCounts = $this->reportManager->getReadBookAuthorNationalitiesByYear($year);
        $this->template->translatorsCounts = $this->reportManager->getReadBookTranslatorsCountsByYear($year);
        $this->template->genresCounts = $this->reportManager->getReadBookGenresCountsByYear($year);
        $this->template->tagsCounts = $this->reportManager->getReadBookTagsCountsByYear($year);
        $this->template->typesCounts = $this->reportManager->getReadBookTypesCountsByYear($year);
        $this->template->sourcesCounts = $this->reportManager->getReadBookSourcesCountsByYear($year);
        $this->template->pagesCounts = $this->reportManager->getReadBookPagesCountsByYear($year);
        $this->template->durations = $this->reportManager->getReadBookDurationsByYear($year);
        $this->template->bookReadersCounts = $this->reportManager->getReadBookBookReadersCountsByYear($year);
        $this->template->ratings = $this->reportManager->getReadBookRatingsByYear($year);
        $this->template->readingClubCounts = $this->reportManager->getReadBookReadingClubCountByYear($year);
        $this->template->languages = $this->reportManager->getReadBookLanguageCountsByYear($year);

        $readBookYearAndMonthCount = [];
        for ($month = 1; $month <= 12; $month++) {
            $readBookYearAndMonthCount[$month] = $this->reportManager->getReadBookCountByYearAndMonth($year, $month);
        }
        $this->template->readBookYearAndMonthCount = $readBookYearAndMonthCount;
    }
}