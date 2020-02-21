<?php

namespace App\Application\Command\Action;

use Exception;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Error\CannotUpdateIssue;
use App\Services\Security\User\User;
use Legacy\Incident;

/**
 * Add an attachment to the issue using POST data. (immutable object)
 */
final class AddAttachment implements Action
{
    /** @var string **/
    private $postBody;
    /** @var LoggerInterface **/
    private $logger;
    /** @var User **/
    private $user;

    /**
     * @param string $postBody the POST data
     */
    /**
     * @param string          $postBody the POST data
     * @param LoggerInterface $logger
     * @param User            $user
     */
    public function __construct(string $postBody, LoggerInterface $logger, User $user)
    {
        $this->postBody = $postBody;
        $this->logger   = $logger;
        $this->user     = $user;
    }

    /**
     * @inheritDoc
     * @param Incident $target
     */
    public function handle($target): Result
    {
        try {
            $target->addPJ(
                $this->user->toArray()['id'],
                json_decode($this->postBody, true)['file']
            );
        } catch (Exception $exception) {
            $this->logger->error('add attachment failure: ' . $exception->getMessage());

            return new Failure(new CannotUpdateIssue());
        }

        try {
            $target->enregistrer();
        } catch (Exception $exception) {
            $this->logger->error('issue save failure: ' . $exception->getMessage());

            return new Failure(new CannotUpdateIssue());
        }

        return new Success($target);
    }
}
