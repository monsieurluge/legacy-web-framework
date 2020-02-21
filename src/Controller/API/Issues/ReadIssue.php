<?php

namespace App\Controller\API\Issues;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\DatabaseFactory;
use App\Services\Request\CustomRequest\Api\Post\ReadIssue as ReadIssueRequest;
use Legacy\Incident;
use Symfony\Component\HttpFoundation\Response;

/**
 * Read-Unread an Issue
 */
final class ReadIssue
{

    /** @var DatabaseFactory **/
    private $dbFactory;
    /** @var LoggerInterface **/
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param DatabaseFactory $dbFactory
     */
    public function __construct(LoggerInterface $logger, DatabaseFactory $dbFactory)
    {
        $this->dbFactory = $dbFactory;
        $this->logger    = $logger;
    }

    /**
     * @param ReadIssueRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $incident = new Incident(
            $request->issueId(),
            $this->logger,
            $this->dbFactory
        );

        'read' === $request->newState()
            ? $incident->majLu()
            : $incident->majNonLu();

        $result = $incident->enregistrer();

        if (true == $result) {
            return new Response('', 204);
        }

        return new Response(
            sprintf(
                'Enregistrement de l\'incident n°%s KO',
                $request->issueId()
            ),
            500
        );
    }

}
