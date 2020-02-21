<?php

namespace App\Controller\API;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Repository\Emails as Repository;
use App\Services\Request\CustomRequest\Api\Get\Email as EmailRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Note Email API
 * Returns a specific note email
 */
final class Email
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
     * @param EmailRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->findById($request->emailId())
            ->map(function($email) { return new JsonApiResponse($email); })
            ->getValueOrExecOnFailure(function (Error $error) use ($request) {
                return new JsonApiResponse(
                    null,
                    [
                        new JsonApiError(
                            'get_note_email_api_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::CANNOT_GET_EMAIL,
                            sprintf(
                                'erreur lors de la récupération de l\'e-mail #%s : %s',
                                $request->emailId(),
                                $error->message()
                            )
                        )
                    ]
                );
            });
    }

}
