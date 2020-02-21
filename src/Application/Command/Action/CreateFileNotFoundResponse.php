<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\ValueObject\Label;
use Symfony\Component\HttpFoundation\Response;

/**
 * Represents the action which creates a "file not found" HTTP Response.
 */
final class CreateFileNotFoundResponse implements Action
{
    /** @var Label **/
    private $name;

    /**
     * @param Label $name
     */
    public function __construct(Label $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     * @return Result a Result<Response>
     */
    public function handle($target): Result
    {
        return new Success(
            new Response(
                sprintf(
                    'le fichier %s n\'a pas été trouvé',
                    $this->name->value()
                ),
                Response::HTTP_NOT_FOUND
            )
        );
    }
}
