<?php

namespace App\Application\Command;

use DateTime;
use Exception;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\DatabaseFactory;
use App\Services\Error\CannotCreateIssue;
use App\Services\Security\User\User;
use Legacy\Incident;

/**
 * Clones an issue.
 */
final class CloneIssue
{
    /** @var DatabaseFactory **/
    private $dbFactory;
    /** @var LoggerInterface **/
    private $logger;
    /** @var User **/
    private $user;

    /**
     * @param LoggerInterface $logger
     * @param DatabaseFactory $dbFactory
     * @param User            $user
     */
    public function __construct(LoggerInterface $logger, DatabaseFactory $dbFactory, User $user)
    {
        $this->dbFactory = $dbFactory;
        $this->logger    = $logger;
        $this->user      = $user;
    }

    /**
     * [fromId description]
     *
     * @param int $issueId
     *
     * @return Result
     */
    public function fromId(int $issueId): Result
    {
        $origin      = new Incident($issueId, $this->logger, $this->dbFactory);
        $clone       = new Incident(null, $this->logger, $this->dbFactory);
        $originArray = $origin->toArray();

        try {
            $clone->setByArray([
                'actions'        => $originArray['actions'],
                'categorie'      => $originArray['categorie'],
                'conditions'     => $originArray['conditions'],
                'context_id'     => $originArray['context_id'],
                'date_ouverture' => (new DateTime())->format('Y-m-d H:i'),
                'date_modif'     => (new DateTime())->format('Y-m-d H:i'),
                'description'    => $originArray['description'],
                'email_id'       => $originArray['email_id'],
                'id_assigne'     => $originArray['id_assigne'],
                'id_createur'    => $this->user->toArray()['id'],
                'id_etude'       => $originArray['id_etude'],
                'id_ssii'        => $originArray['id_ssii'],
                'non_lu'         => 1,
                'origine'        => $originArray['origine'],
                'priorite'       => $originArray['priorite'],
                'statut'         => 'nouveau',
                'temps'          => 0,
                'titre'          => $originArray['titre'],
                'type'           => $originArray['type'],
            ]);

            $clone->enregistrer();
        } catch (Exception $exception) {
            return new Failure(
                new CannotCreateIssue(
                    sprintf(
                        'clonage de l\'incident %s impossible : %s',
                        $issueId,
                        $exception->getMessage()
                    )
                )
            );
        }

        return new Success($clone);
    }
}
