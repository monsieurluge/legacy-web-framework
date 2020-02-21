<?php

namespace App\Controller\API\Offices;

use App\Repository\BaseOffices as OfficeRepository;
use App\Repository\BaseSSIIs as SsiiRepository;
use App\Services\Request\CustomRequest\Api\Get\Offices as OfficesRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Offices API
 */
final class Offices
{

    /** @var OfficeRepository **/
    private $officeRepository;
    /** @var SsiiRepository **/
    private $ssiiRepository;

    /**
     * @codeCoverageIgnore
     * @param OfficeRepository $officeRepository
     * @param SsiiRepository   $ssiiRepository
     */
    public function __construct($officeRepository, $ssiiRepository)
    {
        $this->officeRepository = $officeRepository;
        $this->ssiiRepository   = $ssiiRepository;
    }

    /**
     * @param OfficesRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $offices = $this->officeRepository->findByTerm($request->term());
        $ssii    = $this->ssiiRepository->findByOffices($offices);

        return new Response(
            json_encode(
                array_map(
                    function ($office) use ($ssii) {
                        return $this->officeFormatter($office, $ssii);
                    },
                    $offices
                )
            ),
            200
        );
    }

    /**
     * Returns an office hash as follows:
     *   [ id:string, label:string, value:string, ssii:string, data:array ]
     *
     * @codeCoverageIgnore
     * @param  array $office
     * @param  array $ssii
     * @return array
     */
    private function officeFormatter(array $office, array $ssii): array
    {
        return [
            'id'    => $office['code_unique_hj'],
            'label' => str_pad($office['code_unique_hj'], 4, '0', STR_PAD_LEFT) . ' - ' . $office['raison_sociale'],
            'value' => str_pad($office['code_unique_hj'], 4, '0', STR_PAD_LEFT),
            'ssii'  => $this->ssiiForOffice($office, $ssii),
            'data'  => $office
        ];
    }

    /**
     * Returns the SSII for the given office, or an empty string
     *
     * @codeCoverageIgnore
     * @param  array  $office
     * @param  array  $ssiiList
     * @return string
     */
    private function ssiiForOffice($office, $ssiiList): string
    {
        return isset($ssiiList[intval($office['code_unique_hj'])])
            ? $ssiiList[intval($office['code_unique_hj'])]
            : '';
    }

}
