<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\CustomRequest\Api\Get\AbstractIssuesRequest;
use App\Services\Request\Request;

/**
 * Issues custom API HTTP request.
 */
final class Issues extends AbstractIssuesRequest
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
     * Returns the maximum desired results (20 by default).
     *
     * @return int
     */
    public function maxResults(): int
    {
        return intval($this->request->queryParameter('max', 20));
    }

    /**
     * Returns the result's order (desc by default).
     *
     * @return string
     */
    public function order(): string
    {
        return $this->request->queryParameter('order', 'desc');
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
            $this->filterIfPresent('office', 'office'),
            $this->filterIfPresent('owner', 'owner'),
            $this->filterIfPresent('origin', 'origin'),
            $this->filterIfPresent('createdBy', 'created-by'),
            $this->filterIfPresent('description', 'description'),
            $this->filterIfPresent('priority', 'priority'),
            $this->filterIfPresent('ssii', 'ssii'),
            $this->filterIfPresent('title', 'title'),
            $this->filterIfPresent('status', 'status'),
            $this->filterIfPresent('type', 'type'),
            $this->filterIfPresent('category', 'category'),
            $this->filterIfPresent('context', 'context'),
            $this->filterIfPresent('read', 'read')
        );
    }

    /**
     * @inheritDoc
     */
    protected function request(): Request
    {
        return $this->request;
    }
}
