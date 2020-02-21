<?php

namespace App\Repository;

use DateTime;
use App\Services\Database\Database;
use App\Services\Text\Windows1252Text;

final class History
{

    /** @var Database **/
    private $dataSource;

    /**
     * @param Database $dataSource
     */
    public function __construct(Database $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Creates a new history entry
     *
     * @param int    $issueId
     * @param int    $userId
     * @param string $content
     */
    public function add(int $issueId, int $userId, string $content): void
    {
        $query = 'INSERT INTO incident_histo (`incident_id`, `utilisateur`, `texte`, `date`)'
            . ' VALUES (:issueId, :userId, :content, :createdAt);';

        $this->dataSource->exec(
            $query,
            [
                'issueId'   => $issueId,
                'userId'    => $userId,
                'content'   => (new Windows1252Text($content))->toString(),
                'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
    }

}
