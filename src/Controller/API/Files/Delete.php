<?php

namespace App\Controller\API\Files;

use monsieurluge\result\Error\Error;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;
use App\Application\Command\Action\CreateFileNotFoundResponse;
use App\Application\Command\Action\History\CreateFileDeletedHistoryEntry;
use App\Application\Command\Action\File\DeleteFile;
use App\Application\Command\Action\File\DeleteFileFromDB;
use App\Repository\Files as FilesRepository;
use App\Repository\History as HistoryRepository;
use App\Services\Request\CustomRequest\Api\Delete\File as DeleteFileRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * DELETE a file.
 */
final class Delete
{
    /** @var HistoryRepository **/
    private $historyRepository;
    /** @var FilesRepository **/
    private $repository;

    /**
     * @param FilesRepository   $repository
     * @param HistoryRepository $historyRepository
     */
    public function __construct(FilesRepository $repository, HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
        $this->repository        = $repository;
    }

    /**
     * @param DeleteFileRequest $request
     *
     * @return Response
     */
    public function process($request): Response {
        return $this->repository
            ->fileById(new ID($request->fileId()))
            ->then(function ($target) {
                return (new DeleteFile())->handle($target);
            })
            ->then(function ($target) {
                return (new DeleteFileFromDB($this->repository))->handle($target);
            })
            ->then(function ($target) use ($request) {
                return (new CreateFileDeletedHistoryEntry($this->historyRepository, $request->user()))->handle($target);
            })
            ->map(function() { return new Response('', 201); })
            ->else(function ($target) use ($request) {
                return (new CreateFileNotFoundResponse(new Label('numéro #' . $request->fileId())))->handle($target);
            })
            ->getValueOrExecOnFailure(function(Error $error) {
                return new Response($error->message(), 500);
            });
    }
}
