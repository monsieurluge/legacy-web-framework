<?php

namespace App\Repository;

use monsieurluge\result\Result\Result;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\DatabaseFactoryInterface;
use App\Services\Error\NoIssueFound;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;
use Legacy\Incident as LegacyIncident;

/**
 * Incident Repository (immutable object)
 */
final class Incident
{
    /** @var DatabaseFactoryInterface **/
    private $dbFactory;
    /** @var LoggerInterface **/
    private $logger;

    /**
     * @param LoggerInterface          $logger
     * @param DatabaseFactoryInterface $dbFactory
     */
    public function __construct(LoggerInterface $logger, DatabaseFactoryInterface $dbFactory)
    {
        $this->dbFactory = $dbFactory;
        $this->logger    = $logger;
    }

    /**
     * Returns a Result<Incident>
     *
     * @param int $issueId
     *
     * @return Result
     */
    public function findById(int $issueId): Result
    {
        $issue = new LegacyIncident($issueId, $this->logger, $this->dbFactory);

        return $issue->date_ouverture && $issue->id
            ? new Success($issue)
            : new Failure(new NoIssueFound($issueId));
    }

    /**
     * Creates an issue
     *
     * @param array $data
     *
     * @return Result Result<Incident>
     */
    public function create(array $data): Result
    {
        $issue = new LegacyIncident(null, $this->logger, $this->dbFactory);

        $issue->setByArray($data);

        $issue->enregistrer();

        return new Success($issue);
    }
}
