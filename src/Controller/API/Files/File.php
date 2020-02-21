<?php

namespace App\Controller\API\Files;

use monsieurluge\result\Error\Error;
use App\Application\Command\Action\CreateFileAttachmentResponse;
use App\Application\Command\Action\CreateFileNotFoundResponse;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;
use App\Repository\Files as FilesRepository;
use App\Services\Request\CustomRequest\Api\Get\File as FileRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * File API
 * @codeCoverageIgnore
 */
final class File
{
    /** @var FilesRepository **/
    private $repository;

    /**
     * @param FilesRepository $repository
     */
    public function __construct(FilesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param FileRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->fileById(new ID($request->fileId()))
            ->then(function ($target) {
                return (new CreateFileAttachmentResponse())->handle($target);
            })
            ->else(function ($target) use ($request) {
                return (new CreateFileNotFoundResponse(new Label(strval($request->fileId()))))->handle($target);
            })
            ->getValueOrExecOnFailure(function(Error $error) {
                return new Response($error->message(), 500);
            });
    }
}
