<?php

namespace App\Controller\API\Issues;

use App\Application\Command\IssuesToDatatableJson;
use App\Repository\Issues as IssuesRepository;
use App\Services\Request\CustomRequest\Api\Get\IssuesDatatable as IssuesDatatableRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issues API
 */
final class IssuesDatatable
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
     * Processes the custom request.
     *
     * @param IssuesDatatableRequest $request
     *
     * @return Response
     */
    public function process(IssuesDatatableRequest $request): Response
    {
        $issues = $this->repository->findBy(
            $request->filters(),
            $request->length(),
            $request->order(),
            $request->start()
        );

        return new Response(
            (new IssuesToDatatableJson(
                $issues,
                $this->repository->total(),
                $this->repository->totalBy($request->filters())
            ))->toJson(),
            200
        );
    }
}
