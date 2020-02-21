<?php

namespace App\Application\Command;

use Closure;
use App\Application\Command\IssueToArray;

final class IssuesToDatatableJson
{
    /** @var int **/
    private $searchTotal;
    /** @var array **/
    private $issues;
    /** @var int **/
    private $total;

    /**
     * @param array $issues
     */
    public function __construct(array $issues, int $total, int $searchTotal)
    {
        $this->searchTotal = $searchTotal;
        $this->issues      = $issues;
        $this->total       = $total;
    }

    /**
     * Returns the issues as "Datatable JSON" format.
     *
     * @return string
     */
    public function toJson(): string
    {
        $formattedIssues = array_map($this->formatIssue(), $this->issues);

        return json_encode([
            'iTotalRecords'        => $this->total,
            'iTotalDisplayRecords' => $this->searchTotal,
            'iDisplayLength'       => count($this->issues),
            'aaData'               => $formattedIssues
        ]);
    }

    /**
     * Returns a function which formats the issue.
     *
     * @return Closure the function as follows: issue -> array
     */
    private function formatIssue(): Closure
    {
        return function ($issue) {
            $raw = (new IssueToArray($issue))->toArray();

            return [
                'DT_RowId'       => 'row_' . $raw['id'],
                'DT_RowClass'    => $raw['status'],
                'inc_id'         => $raw['id'],
                'categorie'      => $raw['category'],
                'categorielib'   => $raw['categoryName'],
                'priorite'       => $raw['priority'],
                'prioritelib'    => $raw['priorityName'],
                'id_etude'       => $raw['office'],
                'titre'          => $raw['title'],
                'date_ouverture' => $raw['created'],
                'type'           => $raw['type'],
                'statutlib'      => $raw['status'],
                'assignea'       => $raw['ownerInitials'],
            ];
        };
    }
}
