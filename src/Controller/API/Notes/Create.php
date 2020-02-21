<?php

namespace App\Controller\API\Notes;

use DateTime;
use App\Domain\ValueObject\ID;
use App\Repository\NotesRepository;
use App\Services\Request\CustomRequest\Api\Post\CreateNote as CreateNoteRequest;
use App\Services\Response\JsonApi\JsonApiResponse;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Create Note" API controller
 */
final class Create
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
     * @param CreateNoteRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $content = $request->content();

        if (empty($content)) {
            return new JsonApiResponse(
                null,
                [
                    new JsonApiError(
                        'createnote_' . (new DateTime())->getTimestamp(),
                        ErrorsEnum::EMPTY_NOTE_CONTENT,
                        'le contenu de la note ne doit pas être vide'
                    )
                ]
            );
        }

        return $this->repository
            ->create(new ID($request->issueId()), $request->user(), $content)
            ->map(function (ID $noteId) use ($content, $request) {
                return new JsonApiResponse(
                    [
                        'id'        => $noteId->value(),
                        'content'   => $content,
                        'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
                        'createdBy' => $request->userId(),
                        'emailId'   => null
                    ]
                );
            })
            ->getValueOrExecOnFailure(function() {
                return new JsonApiResponse(
                    null,
                    [
                        new JsonApiError(
                            'createnote_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::EMPTY_NOTE_CONTENT,
                            'impossible de créer la note'
                        )
                    ]
                );
            });
    }

}
