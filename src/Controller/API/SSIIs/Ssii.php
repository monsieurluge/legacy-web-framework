<?php

namespace App\Controller\API\SSIIs;

use monsieurluge\result\Error\Error;
use App\Domain\ValueObject\ID;
use App\Repository\SSIIs as SSIIsRepository;
use App\Services\Date\Timestamp;
use App\Services\Request\CustomRequest\Api\Get\Ssii as SsiiRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * "SSII list" API controller
 */
final class Ssii
{
    /** @var SSIIsRepository **/
    private $repository;
    /** @var Timestamp **/
    private $timestamp;

    /**
     * @codeCoverageIgnore
     * @param SSIIsRepository $repository
     * @param Timestamp       $timestamp
     */
    public function __construct(SSIIsRepository $repository, Timestamp $timestamp)
    {
        $this->repository = $repository;
        $this->timestamp  = $timestamp;
    }

    /**
     * @param SsiiRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->findById(new ID($request->ssiiId()))
            ->map(function(array $informations) {
                return new JsonApiResponse(
                    [
                        'id'      => $informations['id'],
                        'name'    => $informations['name'],
                        'email'   => $this->fullEmail($informations),
                        'address' => $this->fullAddress($informations),
                        'phone'   => $informations['phone'],
                        'office'  => $informations['office'],
                    ]
                );
            })
            ->getValueOrExecOnFailure(function(Error $error) {
                return new JsonApiResponse(
                    '',
                    [
                        new JsonApiError(
                            'getssii_' . $this->timestamp->value(),
                            ErrorsEnum::CANNOT_GET_SSII,
                            $error->message()
                        )
                    ],
                    Response::HTTP_NOT_FOUND
                );
            });
    }

    /**
     * Returns the ssii's full address.
     *
     * @codeCoverageIgnore
     * @param  array  $ssii
     * @return string
     */
    private function fullAddress(array $ssii): string
    {
        return implode(
            ', ',
            $this->keepOnlyFilledValues(
                $this->keepOnlyAddressValues($ssii)
            )
        );
    }

    /**
     * Returns the ssii's full email address ; can be multiple.
     *
     * @codeCoverageIgnore
     * @param  array  $ssii
     * @return string
     */
    private function fullEmail(array $ssii): string
    {
        return implode(
            ';',
            $this->keepOnlyFilledValues(
                $this->keepOnlyEmailValues($ssii)
            )
        );
    }

    /**
     * Returns only the entries which are part of an ssii's address.
     * The address keys are "addressLine1", "addressLine2", "addressLine3", "postalCode" and "city".
     *
     * @codeCoverageIgnore
     * @param  array $ssii
     * @return array
     */
    private function keepOnlyAddressValues(array $ssii): array
    {
        return $this->keepOnlyTheseKeys(
            $ssii,
            [ 'addressLine1', 'addressLine2', 'addressLine3', 'postalCode', 'city' ]
        );
    }

    /**
     * Returns only the entries which are part of an ssii's e-mail.
     * The address keys are "email1" and "email2".
     *
     * @codeCoverageIgnore
     * @param  array $ssii
     * @return array
     */
    private function keepOnlyEmailValues(array $ssii): array
    {
        return $this->keepOnlyTheseKeys(
            $ssii,
            [ 'email1', 'email2' ]
        );
    }

    /**
     * Returns only the entries for which the key is in the provided key list.
     *
     * @codeCoverageIgnore
     * @param  array $data
     * @param  array $keys
     * @return array
     */
    private function keepOnlyTheseKeys(array $data, array $keys): array
    {
        return array_filter(
            $data,
            function($key) use ($keys) { return in_array($key, $keys); },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Returns only the entries which are not empty.
     *   ex: [ 'foo' => 1234, 'bar' => '', 'baz' => 'uh' ] -> [ 'foo' => 1234, 'baz' => 'uh' ]
     *
     * @codeCoverageIgnore
     * @param  array $data
     * @return array
     */
    private function keepOnlyFilledValues(array $data): array
    {
        return array_filter(
            $data,
            function($value) { return false === empty($value); }
        );
    }
}
