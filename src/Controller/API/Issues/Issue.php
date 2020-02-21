<?php

namespace App\Controller\API\Issues;

use PDO;
use App\Services\Request\CustomRequest\Api\Get\Issue as IssueRequest;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\DatabaseFactory;
use Legacy\Incident;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issue
 */
final class Issue
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
     * @param IssueRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $issueId  = $request->issueId();
        $incident = new Incident($issueId, $this->logger, $this->dbFactory);
        $result   = $incident->toArray();

        $result['ssii']          = $incident->getSSII();
        $result['ssii_contacts'] = $incident->getContactsSSII();
        $result['play']          = $incident->estEnLecture();
        $result['email']         = $this->getEmail($result['email_id']);
        $result['pjs']           = $this->getIncidentPJ($issueId);
        $result['intervenants']  = $this->getIncidentIntervenant($issueId);

        if (!empty($result['id_etude'])) {
            $result['etude'] = $this->getEtude($result['id_etude']);
        }

        // context
        $result['context'] = $result['context_id'];
        unset($result['context_id']);

        return new Response(json_encode($result), 200, ['ContentType' => 'application/json']);
    }

    /**
     * Returns the e-mail.
     *
     * @param [type] $emailId
     *
     * @return [type]
     */
    private function getEmail($emailId)
    {
        $query  = 'select * from email where id = :id';
        $result = $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, array('id' => $emailId));

        if (empty($result)) {
            return null;
        }

        return (object) $result[0];
    }

    /**
     * Returns the office.
     *
     * @param [type] $officeId
     *
     * @return [type]
     */
    private function getEtude($officeId)
    {
        $query   = 'select * from etudes where code_unique_hj = :etude';
        $results = $this->dbFactory
            ->createDatabase(DB_GLOBAL['base'], true)
            ->query($query, PDO::FETCH_ASSOC, array('etude' => intval($officeId)));

        return nvl($results[0], array());
    }

    /**
     * Returns the issue operator.
     *
     * @param [type] $issueId
     *
     * @return [type]
     */
    private function getIncidentIntervenant($issueId)
    {
        $query = 'select * from incident_intervenant where incident_id = :id order by id asc';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, array('id' => $issueId));
    }

    /**
     * Returns the issue attachments.
     *
     * @param [type] $issueId
     *
     * @return [type]
     */
    private function getIncidentPj($issueId)
    {
        $query = 'SELECT `id`, `pj` as name FROM incident_pj WHERE incident_id = :id ORDER BY id ASC';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, array('id' => $issueId));
    }

}
