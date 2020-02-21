<?php

namespace App\Controller\API\Offices;

use monsieurluge\result\Error\Error;
use App\Domain\ValueObject\ID;
use App\Repository\Offices as OfficesRepository;
use App\Services\Date\Timestamp;
use App\Services\Request\CustomRequest\Api\Get\Office as OfficeRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Offices list" API controller
 */
final class Office
{
    /** @var OfficesRepository **/
    private $officesRepository;
    /** @var Timestamp **/
    private $timestamp;

    /**
     * @codeCoverageIgnore
     * @param OfficesRepository $officesRepository
     * @param Timestamp         $timestamp
     */
    public function __construct(OfficesRepository $officesRepository, Timestamp $timestamp)
    {
        $this->officesRepository = $officesRepository;
        $this->timestamp         = $timestamp;
    }

    /**
     * @param OfficeRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->officesRepository
            ->findById(new ID($request->officeId()))
            ->map(function(array $informations) {
                return new JsonApiResponse([
                    'id'      => $informations['code_unique_hj'],
                    'name'    => $informations['raison_sociale'],
                    'phone'   => $informations['telephone'],
                    'email'   => $this->fullEmail($informations),
                    'address' => $this->fullAddress($informations),
                ]);
            })
            ->getValueOrExecOnFailure(function(Error $error) {
                return new JsonApiResponse(
                    '',
                    [
                        new JsonApiError(
                            'getoffice_' . $this->timestamp->value(),
                            ErrorsEnum::CANNOT_GET_OFFICE,
                            $error->message()
                        )
                    ],
                    404
                );
            });
    }

    /**
     * Returns the office's full address.
     *
     * @codeCoverageIgnore
     * @param  array  $office
     * @return string
     */
    private function fullAddress(array $office): string
    {
        return implode(
            ', ',
            $this->keepOnlyFilledValues(
                $this->keepOnlyAddressValues($office)
            )
        );
    }

    /**
     * Returns the office's full email ; can be multiple.
     *
     * @codeCoverageIgnore
     * @param  array  $office
     * @return string
     */
    private function fullEmail(array $office): string
    {
        return implode(
            '; ',
            $this->keepOnlyFilledValues(
                $this->keepOnlyEmailValues($office)
            )
        );
    }

    /**
     * Returns only the entries which are part of an office's address.
     * The address keys are "adresse1", "adresse2", "adresse3", "code_postal" and "ville".
     *
     * @codeCoverageIgnore
     * @param  array $office
     * @return array
     */
    private function keepOnlyAddressValues(array $office): array
    {
        return $this->keepOnlyTheseKeys(
            $office,
            [ 'adresse1', 'adresse2', 'adresse3', 'code_postal', 'ville' ]
        );
    }

    /**
     * Returns only the entries which are part of an office's e-mail.
     * The address keys are "e_mail1" and "e_mail2".
     *
     * @codeCoverageIgnore
     * @param  array $office
     * @return array
     */
    private function keepOnlyEmailValues(array $office): array
    {
        return $this->keepOnlyTheseKeys(
            $office,
            [ 'e_mail1', 'e_mail2' ]
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
