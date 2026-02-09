<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/DataGrouper.php';

class DashboardData {
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function build() {
        $rows = $this->database->getClauses();
        $metrics = $this->database->getMetrics();
        $observations = $this->database->getObservations();
        $values = $this->database->getValues();
        $preuves = $this->database->getPreuves();

        $rowsByDomain = DataGrouper::groupClauses($rows);
        $metricsByClause = DataGrouper::groupMetrics($metrics);
        $observationsByClause = DataGrouper::groupObservations($observations);
        $valuesByMetric = DataGrouper::groupValues($values);
        $preuvesByClause = DataGrouper::groupPreuves($preuves);
        $allYearValues = $this->database->getAllValuesLastYear();

        $domainAverages = $this->calculateDomainAverages($rowsByDomain, $observationsByClause);
        $globalStats = $this->calculateGlobalStats($rowsByDomain, $observationsByClause);

        return [
            'rowsByDomain' => $rowsByDomain,
            'metricsByClause' => $metricsByClause,
            'observationsByClause' => $observationsByClause,
            'valuesByMetric' => $valuesByMetric,
            'preuvesByClause' => $preuvesByClause,
            'allYearValues' => $allYearValues,
            'domainAverages' => $domainAverages,
            'totalClauses' => $globalStats['totalClauses'],
            'clausesWithNotes' => $globalStats['clausesWithNotes'],
            'notesLow' => $globalStats['notesLow'],
            'notesMid' => $globalStats['notesMid'],
            'notesHigh' => $globalStats['notesHigh'],
            'globalAverage' => $globalStats['globalAverage'],
            'progressPercent' => $globalStats['progressPercent'],
        ];
    }

    private function calculateDomainAverages($rowsByDomain, $observationsByClause) {
        $domainAverages = [];

        foreach ($rowsByDomain as $domainId => $domainData) {
            $totalNotes = 0;
            $countNotes = 0;

            foreach ($domainData['clauses'] as $clause) {
                if (isset($observationsByClause[$clause['id']]) && !empty($observationsByClause[$clause['id']])) {
                    $lastObs = end($observationsByClause[$clause['id']]);
                    if ($lastObs['note'] !== null) {
                        $totalNotes += (int)$lastObs['note'];
                        $countNotes++;
                    }
                }
            }

            $domainAverages[$domainId] = $countNotes > 0 ? round($totalNotes / $countNotes, 1) : null;
        }

        return $domainAverages;
    }

    private function calculateGlobalStats($rowsByDomain, $observationsByClause) {
        $totalClauses = 0;
        $clausesWithNotes = 0;
        $notesLow = 0;
        $notesMid = 0;
        $notesHigh = 0;
        $totalNotesSum = 0;

        foreach ($rowsByDomain as $domainData) {
            foreach ($domainData['clauses'] as $clause) {
                $totalClauses++;
                if (isset($observationsByClause[$clause['id']]) && !empty($observationsByClause[$clause['id']])) {
                    $lastObs = end($observationsByClause[$clause['id']]);
                    if ($lastObs['note'] !== null) {
                        $clausesWithNotes++;
                        $note = (int)$lastObs['note'];
                        $totalNotesSum += $note;
                        if ($note === 0) {
                            $notesLow++;
                        } elseif ($note === 1) {
                            $notesMid++;
                        } else {
                            $notesHigh++;
                        }
                    }
                }
            }
        }

        return [
            'totalClauses' => $totalClauses,
            'clausesWithNotes' => $clausesWithNotes,
            'notesLow' => $notesLow,
            'notesMid' => $notesMid,
            'notesHigh' => $notesHigh,
            'globalAverage' => $clausesWithNotes > 0 ? round($totalNotesSum / $clausesWithNotes, 1) : null,
            'progressPercent' => $totalClauses > 0 ? round(($clausesWithNotes / $totalClauses) * 100) : 0,
        ];
    }
}

?>
