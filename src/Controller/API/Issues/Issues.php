<?php

namespace App\Controller\API\Issues;

use App\Repository\Issues as IssuesRepository;
use App\Application\Command\IssuesToJson;
use App\Services\Request\CustomRequest\Api\Get\Issues as IssuesRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issues API
 */
final class Issues
{
    /** @var IssuesRepository **/
    private $repository;

    /**
     * @param IssuesRepository $repository
     */
    public function __construct(IssuesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param IssuesRequest $request
     *
     * @return Response
     */
    public function process(IssuesRequest $request): Response
    {
        $issues = $this->repository->findBy(
            $request->filters(),
            $request->maxResults(),
            $request->order()
        );

        return new Response((new IssuesToJson($issues))->toJson(), 200);
    }
}
