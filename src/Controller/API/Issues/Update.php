<?php

namespace App\Controller\API\Issues;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Application\Command\Action\UpdateIssue;
use App\Repository\Incident as IncidentRepository;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Request\CustomRequest\Api\Post\UpdateIssue as UpdateIssueRequest;
use App\Services\Response\JsonApi\JsonApiResponse;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use Legacy\Incident;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Update an Issue" API Controller (immutable object)
 */
final class Update
{

    /** @var IncidentRepository **/
    private $issuesRepository;
    /** @var LoggerInterface **/
    private $logger;

    /**
     * @param IncidentRepository $issuesRepository
     * @param LoggerInterface    $logger
     */
    public function __construct(IncidentRepository $issuesRepository, LoggerInterface $logger)
    {
        $this->issuesRepository = $issuesRepository;
        $this->logger           = $logger;
    }

    /**
     * @param UpdateIssueRequest $request
     *
     * @return Response
     */
    public function process($request): Response {
        return $this->issuesRepository
            ->findById($request->issueId())
            ->then(function ($target) use ($request) {
                return (new UpdateIssue($request->body(), $this->logger, $request->user()))->handle($target);
            })
            ->map(function(Incident $issue) { return new JsonApiResponse([ 'id' => $issue->id ]); })
            ->getValueOrExecOnFailure(function(Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [ new JsonApiError(
                        'updateissue_' . (new DateTime())->getTimestamp(),
                        ErrorsEnum::CANNOT_UPDATE_ISSUE,
                        sprintf(
                            'failed to update the issue #%s: %s',
                            $request->issueId(),
                            $error->message()
                        )
                    ) ]
                );
            });
    }

}
