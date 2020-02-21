<?php

namespace App\Controller\API;

use App\Infrastructure\DataSource\GlobalData as DataSource;
use App\Services\Request\CustomRequest\Api\Get\GlobalData as GlobalDataRequest;
use App\Services\Database\Database;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Legacy" Global Data
 */
final class GlobalData
{

    /** @var Database **/
    private $dbIncident;
    /** @var Database **/
    private $dbSsii;

    /**
     * @param Database $dbIncident
     * @param Database $dbSsii
     */
    public function __construct(Database $dbIncident, Database $dbSsii)
    {
        $this->dbIncident = $dbIncident;
        $this->dbSsii     = $dbSsii;
    }

    /**
     * @param GlobalDataRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $data = new DataSource($this->dbIncident, $this->dbSsii, $request->user());

        return new Response(
            json_encode($data->toArray()),
            200,
            [ 'ContentType' => 'application/json' ]
        );
    }

}
