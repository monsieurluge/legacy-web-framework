<?php

namespace App\Application\Command\Action;

use Exception;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Error\CannotSaveIssue;
use App\Services\Security\User\User;
use App\Services\Text\Windows1252Text;
use Legacy\Incident;

/**
 * Update an issue using a POST form content. (immutable object)
 */
final class UpdateIssue implements Action
{
    /** @var string **/
    private $formBody;
    /** @var LoggerInterface **/
    private $logger;
    /** @var User **/
    private $user;

    /**
     * @param string          $formBody the form content
     * @param LoggerInterface $logger
     * @param User            $user
     */
    public function __construct(string $formBody, LoggerInterface $logger, User $user)
    {
        $this->formBody = $formBody;
        $this->logger   = $logger;
        $this->user     = $user;
    }

    /**
     * @inheritDoc
     * @param Incident $target
     */
    public function handle($target): Result
    {
        // update the issue and collect the differences
        $diffs = $target->setByArray(
            $this->mapPropertiesToIssue(
                json_decode($this->formBody, true)
            )
        );

        // save the issue
        try {
            $target->enregistrer();
        } catch (Exception $exception) {
            $this->logger->error('issue save failure: ' . $exception->getMessage());

            return new Failure(new CannotSaveIssue());
        }

        // update the history
        foreach ($diffs as $name => $value) {
            $target->addHisto(
                $this->user->toArray()['id'],
                sprintf(
                    '%s mis(e) à jour : %s',
                    $name,
                    $value
                )
            );
        }

        return new Success($target);
    }

    /**
     * Returns the "POST properties / Incident properties" mapping
     *
     * @return array
     */
    private function propertiesMapping(): array
    {
        return [
            'actions'                 => 'actions',
            'categorie'               => 'categorie',
            'conditions'              => 'conditions',
            'context'                 => 'context_id',
            'description'             => 'description',
            'email_id'                => 'email_id',
            'id_assigne'              => 'id_assigne',
            'id_etude'                => 'id_etude',
            'id_ssii'                 => 'id_ssii',
            'interlocuteur'           => 'interlocuteur',
            'interlocuteur_email'     => 'interlocuteur_email',
            'interlocuteur_telephone' => 'interlocuteur_telephone',
            'non_lu'                  => 'non_lu',
            'origine'                 => 'origine',
            'priority'                => 'priorite',
            'statut'                  => 'statut',
            'titre'                   => 'titre',
            'type'                    => 'type'
        ];
    }

    /**
     * Map the POST properties to the Incident ones
     *
     * @param array $properties
     *
     * @return array
     */
    private function mapPropertiesToIssue(array $properties): array
    {
        $result = [];

        foreach ($properties as $property => $value) {
            if (isset($this->propertiesMapping()[$property])) {
                $result[$this->propertiesMapping()[$property]] = (new Windows1252Text($value))->toString();
            };
        }

        return $result;
    }
}
