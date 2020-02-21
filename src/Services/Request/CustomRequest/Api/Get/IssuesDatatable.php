<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\CustomRequest\Api\Get\AbstractIssuesRequest;
use App\Services\Request\Request;

/**
 * Issues Datatable custom API HTTP request.
 */
final class IssuesDatatable extends AbstractIssuesRequest
{
    /** @var Request **/
    private $request;

    /**
     * @codeCoverageIgnore
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return array_merge(
            $this->searchDates(),
            $this->filterIfPresent('office',      'filtre_id_etude'),
            $this->filterIfPresent('owner',       'filtre_id_assigne'),
            $this->filterIfPresent('origin',      'filtre_origine'),
            $this->filterIfPresent('createdBy',   'filtre_id_createur'),
            $this->filterIfPresent('description', 'filtre_title_desc'),
            $this->filterIfPresent('priority',    'filtre_priorite'),
            $this->filterIfPresent('ssii',        'filtre_id_ssii'),
            $this->filterIfPresent('title',       'filtre_title_desc'),
            $this->filterIfPresent('status',      'filtre_statut'),
            $this->filterIfPresent('type',        'filtre_type'),
            $this->filterIfPresent('category',    'filtre_categorie'),
            $this->filterIfPresent('context',     'filtre_context_id')
        );
    }

    /**
     * Returns the number of items to return (default = 20).
     *
     * @return int
     */
    public function length(): int
    {
        return intval($this->request->queryParameter('iDisplayLength', '20'));
    }

    /**
     * Returns the sorting order, ASC or DESC (default = DESC).
     *
     * @return string
     */
    public function order(): string
    {
        return 'asc' === $this->request->queryParameter('iSortDir_0', '')
            ? 'ASC'
            : 'DESC';
    }

    /**
     * Returns the sorting column (default = id).
     *
     * @return string
     */
    public function orderBy(): string
    {
        $sortColName = $this->request->queryParameter(
            sprintf('mDataProp_%s', $this->request->queryParameter('iSortCol_0', '0')),
            'inc_id'
        );

        return $this->propToDbColumn($sortColName);
    }

    /**
     * Returns the starting item search offset (default = 0).
     *
     * @return int
     */
    public function start(): int
    {
        return intval($this->request->queryParameter('iDisplayStart', '0'));
    }

    /**
     * @inheritDoc
     */
    protected function request(): Request
    {
        return $this->request;
    }

    /**
     * Converts the data prop to a valid DB column name (default = id).
     *
     * @param string $prop
     *
     * @return string
     */
    private function propToDbColumn(string $prop): string
    {
        $mapping = [
            'inc_id'         => 'id',
            'prioritelib'    => 'priority',
            'id_etude'       => 'office',
            'categorielib'   => 'category',
            'titre'          => 'title',
            'data_ouverture' => 'id',
            'type'           => 'type',
            'statutlib'      => 'status',
            'assignea'       => 'owner',
        ];

        return isset($mapping[$prop])
            ? $mapping[$prop]
            : 'id';
    }
}
