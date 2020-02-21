<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\CustomRequest\Api\Get\AbstractIssuesRequest;
use App\Services\Request\Request;

/**
 * The "GET csv export liste" custom request.
 */
final class CsvExportListe extends AbstractIssuesRequest
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
     * Returns a list of valid Export filters
     *
     * @return array as follows: [ filterName:string => filterValue:string, ... ]
     */
    public function filters(): array
    {
        return array_merge(
            $this->searchDates(),
            $this->filterIfPresent('office', 'filtre_id_etude'),
            $this->filterIfPresent('owner', 'filtre_id_assigne'),
            $this->filterIfPresent('origin', 'filtre_origine'),
            $this->filterIfPresent('created-by', 'filtre_id_createur'),
            $this->filterIfPresent('description', 'filtre_title_desc'),
            $this->filterIfPresent('priority', 'filtre_priorite'),
            $this->filterIfPresent('ssii', 'filtre_id_ssii'),
            $this->filterIfPresent('title', 'filtre_title_desc'),
            $this->filterIfPresent('status', 'filtre_statut'),
            $this->filterIfPresent('type', 'filtre_type'),
            $this->filterIfPresent('category', 'filtre_categorie'),
            $this->filterIfPresent('context', 'filtre_context_id')
        );
    }

    /**
     * Returns the user's ID.
     *
     * @return int
     */
    public function userId(): int
    {
        return $this->request->user()->toArray()['id'];
    }

    /**
     * @inheritDoc
     */
    protected function request(): Request
    {
        return $this->request;
    }
}
