<?php

namespace App\Controller\API\Notes;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Domain\DTO\Note;
use App\Domain\ValueObject\Label;
use App\Domain\ValueObject\Lastname;
use App\Repository\Notes as Repository;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Request\CustomRequest\Api\Get\Notes as NotesRequest;

/**
 * Notes API
 * Returns the issue's notes
 */
final class Notes
{

    /** @var Repository **/
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NotesRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->findByIssue($request->issueId())
            ->map(function(array $notes) {
                return new JsonApiResponse(array_map(
                    function(Note $note) { return $this->noteToResponseArray($note); },
                    $notes
                ));
            })
            ->getValueOrExecOnFailure(function(Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [
                        new JsonApiError(
                            'get_notes_api_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::CANNOT_GET_NOTES,
                            sprintf(
                                'erreur lors de la récupération des notes pour l\'incident #%s : %s',
                                $request->issueId(),
                                $error->message()
                            )
                        )
                    ]
                );
            });
    }

    /**
     * Converts a Note DTO to a raw Note response
     *
     * @param  Note  $note
     * @return array
     */
    private function noteToResponseArray(Note $note): array
    {
        return [
            'id'        => $note->identifier()->value(),
            'content'   => $note->content()->toString(),
            'createdAt' => $note->createdAt()->format('Y-m-d H:i:s'),
            'createdBy' => $note->createdBy()->value(),
            'from'      => $note->from()->getContentOrDefaultOnFailure(new Lastname(''))->value(),
            'emailId'   => $note->emailId()->getContentOrDefaultOnFailure(new Label(''))->value()
        ];
    }

}
