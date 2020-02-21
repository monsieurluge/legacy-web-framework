<?php

namespace App\Controller\API\Notes;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Repository\NotesRepository;
use App\Services\Request\CustomRequest\Api\Post\UpdateNote as UpdateNoteRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Update a Note" API controller
 */
final class Update
{

    /** @var NotesRepository **/
    private $repository;

    /**
     * @param NotesRepository $repository
     */
    public function __construct(NotesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateNoteRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->updateContent(
                $request->issueId(),
                $request->content()
            )
            ->map(function($noteDTO) { return new JsonApiResponse($noteDTO); })
            ->getValueOrExecOnFailure(function(Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [
                        new JsonApiError(
                            'updatenote_api_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::CANNOT_UPDATE_NOTE,
                            sprintf(
                                'erreur lors de la mise à jour de la note #%s : %s',
                                $request->issueId(),
                                $error->message()
                            )
                        )
                    ]
                );
            });
    }

}
