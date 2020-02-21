<?php

namespace App\Controller\API;

use \DateTime;
use App\Infrastructure\DataSource\Report;
use App\Services\Request\CustomRequest\Api\Post\CreateReport as CreateReportRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * "Create a report" API controller
 * @var [type]
 */
final class CreateReport
{
    /**
     * @param CreateReportRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $report = new Report(
            new DateTime('now'),
            'website',
            $_SESSION['user'],
            $request->trace(),
            $request->description()
        );

        $report->save();

        return new Response('', 201);
    }
}
