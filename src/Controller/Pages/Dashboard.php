<?php

namespace App\Controller\Pages;

use App\Infrastructure\DataSource\GlobalData;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\Database;
use App\Services\Request\CustomRequest\Page\Get\Dashboard as DashboardRequest;
use App\Services\Templating\TemplateEngine;
use Legacy\GestionIncident;
use Legacy\Maintenance;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dashboard Page
 */
final class Dashboard
{

    /** @var [type] **/
    private $dbFactory;
    /** @var Database **/
    private $dbIncident;
    /** @var Database **/
    private $dbSsii;
    /** @var LoggerInterface **/
    private $logger;
    /** @var TemplateEngine **/
    private $templateEngine;

    /**
     * @param TemplateEngine  $templateEngine
     * @param LoggerInterface $logger
     * @param [type]          $dbFactory
     * @param Database        $dbIncident
     * @param Database        $dbSsii
     */
    public function __construct(
        TemplateEngine $templateEngine,
        LoggerInterface $logger,
        $dbFactory,
        Database $dbIncident,
        Database $dbSsii
    ) {
        $this->dbFactory      = $dbFactory;
        $this->dbIncident     = $dbIncident;
        $this->dbSsii         = $dbSsii;
        $this->logger         = $logger;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param DashboardRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $gestion = new GestionIncident(
            $this->templateEngine,
            $this->logger,
            $this->dbFactory,
            new Maintenance(),
            $request->user(),
            new GlobalData($this->dbIncident, $this->dbSsii, $request->user())
        );

        return new Response($gestion->main(), 200);
    }

}
