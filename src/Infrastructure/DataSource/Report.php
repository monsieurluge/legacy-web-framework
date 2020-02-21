<?php

namespace App\Infrastructure\DataSource;

use \DateTime;
use App\Services\Log\LegacyEMailLogger;
use App\Services\Database\DatabaseTools;

/**
 * Report data source
 */
final class Report
{

    /** @var DateTime **/
    private $created;
    /** @var string **/
    private $description;
    /** @var string **/
    private $trace;
    /** @var string **/
    private $type;
    /** @var int **/
    private $userId;

    /**
     * @param DateTime $created
     * @param string   $type
     * @param int      $userId
     * @param string   $trace
     * @param string   $description
     */
    public function __construct(DateTime $created, string $type, int $userId, string $trace, string $description)
    {
        $this->created     = $created;
        $this->description = $description;
        $this->trace       = $trace;
        $this->type        = $type;
        $this->userId      = $userId;
    }

    /**
     * Saves the report
     */
    public function save()
    {
        DatabaseTools::insert(
            DB_INCIDENT['base'],
            new LegacyEMailLogger('DATABASE', MAIL_ERREUR, true),
            'report',
            [
                'created'     => $this->created->format('Y-m-d H:i:s'),
                'description' => $this->description,
                'trace'       => $this->trace,
                'type'        => $this->type,
                'user'        => $this->userId,
            ],
            false,
            true
        );
    }

}
