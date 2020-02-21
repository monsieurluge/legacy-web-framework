<?php

namespace App\Controller\API\Issues;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Application\Command\CloneIssue as CloneIssueCommand;
use App\Application\Command\CreateIssue as CreateIssueCommand;
use App\Repository\Incident as IssueRepository;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Request\CustomRequest\Api\Post\CreateIssue as CreateIssueRequest;
use App\Services\Response\JsonApi\JsonApiResponse;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Create Issue" API controller.
 *
 * If a source is given (/<route>?source=1234) the new issue acts as a fresh
 *   copy of the given issue's ID
 */
final class Create
{
    /** @var [type] */
    private $dbFactory;
    /** @var LoggerInterface **/
    private $logger;
    /** @var IssueRepository **/
    private $repository;

    /**
     * @param LoggerInterface $logger
     * @param [type]          $dbFactory
     * @param IssueRepository $repository
     */
    public function __construct(
        LoggerInterface $logger,
        $dbFactory,
        IssueRepository $repository
    ) {
        $this->dbFactory  = $dbFactory;
        $this->repository = $repository;
        $this->logger     = $logger;
    }

    /**
     * @param CreateIssueRequest $request
     *
     * @return Response
     */
    public function process($request): Response {
        $issueCreated = empty($request->source())
            ? (new CreateIssueCommand($this->repository, $request->user()))
                ->fromForm($request->body())
            : (new CloneIssueCommand($this->logger, $this->dbFactory, $request->user()))
                ->fromId((int) $request->source());

        return $issueCreated
            ->else(function(Error $error) {
                $this->logger->error(sprintf(
                    'erreur lors de la création d\'un incident : %s',
                    $error->message()
                ));

                return $error;
            })
            ->map(function($issue) { return new JsonApiResponse([ 'id' => $issue->id ]); })
            ->getValueOrExecOnFailure(function(Error $error) {
                return new JsonApiResponse(
                    '',
                    [
                        new JsonApiError(
                            'createissue_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::CANNOT_CREATE_ISSUE,
                            $error->message()
                        )
                    ]
                );
            });
    }
}
