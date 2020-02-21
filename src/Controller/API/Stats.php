<?php

namespace App\Controller\API;

use App\Services\Request\CustomRequest\Api\Get\Stats as StatsRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Stats" API controller
 */
final class Stats
{
    /** @var [type] */
    private $dbFactory;
    /** @var [type] */
    private $repository;
    /** @var [type] */
    private $statsService;

    /**
     * @param [type] $dbFactory
     * @param [type] $service
     * @param [type] $repository
     */
    public function __construct($dbFactory, $service, $repository)
    {
        $this->dbFactory    = $dbFactory;
        $this->repository   = $repository;
        $this->statsService = $service;
    }

    /**
     * @param StatsRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $date2 = $request->date2();

        $results = $this->repository->findFromFilters([
            'courbe'    => $request->courbe(),
            'echelle'   => $request->echelle(),
            'ech_champ' => $request->echChamp(),
            'donnee'    => $request->donnee(),
            'date1'     => $request->date1(),
            'date2'     => $date2
        ]);

        return new Response(
            json_encode($this->statsService->miseEnFormeStatsMain($results, $request->echelle(), $request->date1(), $date2)),
            200,
            [ 'ContentType' => 'application/json' ]
        );
    }
}
