<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;
use App\Domain\DTO\FileDescription;
use App\Services\Error\FileNotFound;
use Symfony\Component\HttpFoundation\Response;

/**
 * Represents the action which creates an "attachment" HTTP Response from a
 *   file description.
 */
final class CreateFileAttachmentResponse implements Action
{
    /**
     * @param FileDescription $target
     *
     * @return Result a Result<Response>
     */
    public function handle($target): Result
    {
        return $this->fileContent($target->name(), $target->issueId())
            ->map(function(string $rawContent) use ($target) {
                return new Success(
                    new Response(
                        $rawContent,
                        200,
                        [
                            'Cache-Control'       => 'private',
                            'Content-Disposition' => 'attachment; filename="' . $target->name()->value() . '";'
                        ]
                    )
                );
            })
            ->getValueOrExecOnFailure(function() {
                return new Success(
                    new Response('', 404)
                );
            });
    }

    /**
     * Retrieve the file content or a Failure if the file was not found.
     *
     * @param Label $name    the file name (without any path)
     * @param ID    $issueId the issue ID
     *
     * @return Result a Result<string>
     */
    private function fileContent(Label $name, ID $issueId): Result
    {
        $path = file_exists(sprintf('upload/files/%s/%s', $issueId->value(), $name->value()))
            ? sprintf('upload/files/%s/%s', $issueId->value(), $name->value())
            : sprintf('upload/files/%s', $name->value());

        return false === file_exists($path)
            ? new Failure(
                new FileNotFound(),
                sprintf('the file %s was not found', $path)
            )
            : new Success(file_get_contents($path));
    }
}
