<?php

namespace App\Controller\API\Files;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Application\Command\Action\File\AddFileToIssue;
use App\Application\Command\Action\File\FindIssueFileId;
use App\Application\Command\Action\History\CreateFileAddedHistoryEntry;
use App\Domain\DTO\FileDescription;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;
use App\Repository\Incident as IncidentRepository;
use App\Repository\History as HistoryRepository;
use App\Services\Database\Database;
use App\Services\Date\Timestamp;
use App\Services\File\PostFormInformations;
use App\Services\File\PostFormFile;
use App\Services\Request\CustomRequest\Api\Post\AddFiles as AddFilesRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a file.
 */
final class Add
{

    /** @var Database **/
    private $incidentDatabase;
    /** @var HistoryRepository **/
    private $historyRepository;
    /** @var IncidentRepository **/
    private $repository;
    /** @var Timestamp **/
    private $timestamp;

    /**
     * @param IncidentRepository $repository
     * @param HistoryRepository  $historyRepository
     * @param Timestamp          $timestamp
     * @param Database           $incidentDatabase
     */
    public function __construct(IncidentRepository $repository, HistoryRepository $historyRepository, Timestamp $timestamp, Database $incidentDatabase)
    {
        $this->incidentDatabase  = $incidentDatabase;
        $this->historyRepository = $historyRepository;
        $this->repository        = $repository;
        $this->timestamp         = $timestamp;
    }

    /**
     * @param AddFilesRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $file = new PostFormFile(
            new PostFormInformations(
                sprintf('%s/%s/', DOSSIER_UPLOAD, $request->issueId()),
                $_FILES['files']
            ),
            $_FILES['files']
        );

        return $this->repository
            ->findById($request->issueId())
            ->then(function ($target) use ($file, $request) {
                return (new AddFileToIssue($file, $request->user()))->handle($target);
            })
            ->then(function ($target) use ($file) {
                return (new FindIssueFileId($this->incidentDatabase, $file))->handle($target);
            })
            ->map(function(int $fileId) use ($file, $request) {
                return new FileDescription(
                    new ID($fileId),
                    new Label($file->name()),
                    new DateTime(),
                    new ID($request->userId()),
                    new ID($request->issueId())
                );
            })
            ->then(function ($target) use ($request) {
                return (new CreateFileAddedHistoryEntry($this->historyRepository, $request->user()))->handle($target);
            })
            ->map(function(FileDescription $fileDescription) use ($request) {
                return new JsonApiResponse([
                    'file'  => $fileDescription->identifier()->value(),
                    'name'  => $fileDescription->name()->value(),
                    'issue' => $request->issueId(),
                ]);
            })
            ->getValueOrExecOnFailure(function(Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [
                        new JsonApiError(
                            'add_file_to_issue_api_' . $this->timestamp->value(),
                            ErrorsEnum::CANNOT_GET_EMAIL,
                            sprintf(
                                'erreur lors de l\'ajout d\'un fichier à l\'incident #%s : %s',
                                $request->issueId(),
                                $error->message()
                            )
                        )
                    ]
                );
            });
    }

}
