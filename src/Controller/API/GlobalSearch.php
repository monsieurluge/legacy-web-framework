<?php

namespace App\Controller\API;

use App\Repository\Incident as IncidentRepository;
use App\Repository\BaseOffices as OfficeRepository;
use App\Services\Response\JsonApi\JsonApiResponse;
use App\Services\Request\CustomRequest\Api\Get\GlobalSearch as GlobalSearchRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Global search" API controller
 */
final class GlobalSearch
{

    /** @var IncidentRepository **/
    private $issuesRepository;
    /** @var OfficeRepository **/
    private $officesRepository;

    /**
     * @param IncidentRepository $issuesRepository
     * @param OfficeRepository   $officesRepository
     */
    public function __construct(IncidentRepository $issuesRepository, OfficeRepository $officesRepository)
    {
        $this->issuesRepository = $issuesRepository;
        $this->officesRepository = $officesRepository;
    }

    /**
     * @inheritDoc
     */
    public function process(GlobalSearchRequest $request): Response
    {
        $result = [
            'issues' => [],
            'offices' => []
        ];

        // issue(s)

        $issue = $this->issuesRepository
            ->findById(intval($request->term()))
            ->getValueOrExecOnFailure(function() { return null; });

        if ($issue) {
            $result['issues'][] = [
                'id'    => $issue->id,
                'title' => $issue->titre
            ];
        }

        // offices

        $offices = $this->officesRepository
            ->findByTerm($request->term());

        foreach ($offices as $office) {
            $result['offices'][] = [
                'id'    => $office['code_unique_hj'],
                'label' => $office['raison_sociale'],
                'phone' => $office['telephone'],
                'city'  => $office['ville']
            ];
        }

        // result

        return new JsonApiResponse($result);
    }

}
