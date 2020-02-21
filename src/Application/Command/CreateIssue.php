<?php

namespace App\Application\Command;

use DateTime;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use App\Repository\Incident as IssueRepository;
use App\Services\Error\CannotCreateIssue;
use App\Services\Security\User\User;

/**
 * Creates an issue.
 */
final class CreateIssue
{
    /** @var IssueRepository **/
    private $repository;
    /** @var User **/
    private $user;

    /**
     * @param IssueRepository $repository
     * @param User            $user
     */
    public function __construct(IssueRepository $repository, User $user)
    {
        $this->repository = $repository;
        $this->user       = $user;
    }

    /**
     * [fromForm description]
     *
     * @param [type] $body
     *
     * @return Result
     */
    public function fromForm($body): Result
    {
        $postData = json_decode($body, true);

        if (false === $postData) {
            return new Failure(
                new CannotCreateIssue('failed to decode the POST data')
            );
        }

        return $this->repository->create([
            'actions'        => $this->getOrDefault($postData, 'actions', ''),
            'categorie'      => $this->getOrDefault($postData, 'categorie', null),
            'conditions'     => $this->getOrDefault($postData, 'conditions', ''),
            'context_id'     => $this->getOrDefault($postData, 'context_id', 1), // front-office
            'date_ouverture' => (new DateTime())->format('Y-m-d H:i'),
            'date_modif'     => (new DateTime())->format('Y-m-d H:i'),
            'description'    => $this->getOrDefault($postData, 'description', ''),
            'email_id'       => $this->getOrDefault($postData, 'email_id', null),
            'id_assigne'     => $this->getOrDefault($postData, 'id_assigne', $this->user->toArray()['id']),
            'id_createur'    => $this->user->toArray()['id'],
            'id_etude'       => $this->getOrDefault($postData, 'id_etude', null),
            'id_ssii'        => $this->getOrDefault($postData, 'id_ssii', null),
            'non_lu'         => 1,
            'origine'        => $this->getOrDefault($postData, 'origine', 'tel'),
            'priorite'       => $this->getOrDefault($postData, 'priorite', 2), // normale
            'statut'         => 'nouveau',
            'temps'          => 0,
            'titre'          => $this->getOrDefault($postData, 'titre', ''),
            'type'           => $this->getOrDefault($postData, 'type', 'incident'),
        ]);
    }

    /**
     * [getOrDefault description]
     *
     * @param array  $parameters
     * @param string $parameter
     * @param [type] $default
     *
     * @return [type]
     */
    private function getOrDefault(array $parameters, string $parameter, $default)
    {
        return isset($parameters[$parameter])
            ? $parameters[$parameter]
            : $default;
    }
}
