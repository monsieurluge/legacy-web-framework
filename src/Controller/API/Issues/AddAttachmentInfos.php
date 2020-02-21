<?php

namespace App\Controller\API\Issues;

use DateTime;
use monsieurluge\result\Error\Error;
use Legacy\Incident;
use App\Application\Command\Action\AddAttachment as AddAttachmentAction;
use App\Repository\Incident as IssueRepository;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Request\CustomRequest\Api\Post\AddAttachmentInfos as AddAttachmentInfosRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Add Attachment informations to an Issue" API Controller (immutable object)
 */
final class AddAttachmentInfos
{

    /** @var LoggerInterface **/
    private $logger;
    /** @var IssueRepository **/
    private $repository;

    /**
     * @param IssueRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(IssueRepository $repository, LoggerInterface $logger)
    {
        $this->logger     = $logger;
        $this->repository = $repository;
    }

    /**
     * @param AddAttachmentInfosRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->findById($request->issueId())
            ->then(function ($target) use ($request) {
                return (new AddAttachmentAction($request->body(), $this->logger, $request->user()))->handle($target);
            })
            ->map(function(Incident $issue) use ($request) {
                return new JsonApiResponse([
                    'id'   => $issue->id,
                    'file' => json_decode($request->body(), true)['file']
                ]);
            })
            ->getValueOrExecOnFailure(function(Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [ new JsonApiError(
                        'add_attachment_' . (new DateTime())->getTimestamp(),
                        ErrorsEnum::CANNOT_ADD_ATTACHMENT,
                        sprintf(
                            'failed to add an attachment to the issue #%s: %s',
                            $request->issueId(),
                            $error->message()
                        )
                    ) ]
                );
            });
    }

}
