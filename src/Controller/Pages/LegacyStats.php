<?php

namespace App\Controller\Pages;

use App\Infrastructure\DataSource\GlobalData;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\Database;
use App\Services\Request\CustomRequest\Legacy as LegacyRequest;
use App\Services\Templating\TemplateEngine;
use Legacy\GestionIncidentStats;
use Legacy\Maintenance;
use Symfony\Component\HttpFoundation\Response;

/**
 * Legacy Page
 */
final class LegacyStats
{

    /** @var [type] **/
    private $dbFactory;
    /** @var Database **/
    private $dbIncident;
    /** @var Database **/
    private $dbSsii;
    /** @var LoggerInterface **/
    private $logger;
    /** @var [type] **/
    private $statsService;
    /** @var TemplateEngine **/
    private $templateEngine;

    /**
     * @param TemplateEngine  $templateEngine
     * @param LoggerInterface $logger
     * @param [type]          $dbFactory
     * @param Database        $dbIncident
     * @param Database        $dbSsii
     * @param [type]          $service
     */
    public function __construct(
        TemplateEngine $templateEngine,
        LoggerInterface $logger,
        $dbFactory,
        Database $dbIncident,
        Database $dbSsii,
        $service
    ) {
        $this->dbFactory      = $dbFactory;
        $this->dbIncident     = $dbIncident;
        $this->dbSsii         = $dbSsii;
        $this->logger         = $logger;
        $this->statsService   = $service;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param LegacyRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $headers = [];
        $gestion = new GestionIncidentStats(
            $this->templateEngine,
            $this->logger,
            $this->dbFactory,
            $this->statsService,
            new Maintenance(),
            $request->user(),
            new GlobalData($this->dbIncident, $this->dbSsii, $request->user())
        );

        switch($request->route()) {
            case 'csv':
                $method = 'csv' . $request->method();
                break;
            case 'json':
                $method = 'json' . $request->method();
                $headers['Content-Type'] = 'application/json';
                break;
            case 'enreg':
                $method = 'enreg' . $request->method();
                break;
            default:
                $method = 'main';
                break;
        }

        return new Response($gestion->$method(), 200, $headers);
    }

}
