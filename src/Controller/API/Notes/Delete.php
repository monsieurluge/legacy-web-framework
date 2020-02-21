<?php

namespace App\Controller\API\Notes;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Repository\NotesRepository;
use App\Services\Request\CustomRequest\Api\Delete\Note as DeleteNoteRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Delete a Note" API controller
 */
final class Delete
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
     * @param DeleteNoteRequest $request
     *
     * @return Response
     */
    public function process($request): Response {
        return $this->repository
            ->delete($request->noteId())
            ->map(function() use ($request) { return new JsonApiResponse([ 'id' => $request->noteId() ]); })
            ->getValueOrExecOnFailure(function(Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [
                        new JsonApiError(
                            'delete_note_api_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::CANNOT_DELETE_NOTE,
                            sprintf(
                                'erreur lors de la suppression de la note #%s : %s',
                                $request->noteId(),
                                $error->message()
                            )
                        )
                    ]
                );
            });
    }

}
