<?php

namespace App\Services\Mail\Action;

use Exception;
use PDO;
use App\Core\Exceptions\NotFoundException;
use App\Domain\DTO\Email as BaseEmailDTO;
use App\Domain\ValueObject\EmailAddress;
use App\Services\Database\DatabaseTools;
use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Mail\Recipient\Main as MainRecipient;
use App\Services\Output\Output;
use App\Services\Templating\TemplateEngine;
use App\Services\Text\SimpleText;
use App\Services\Text\Windows1252Text;
use Legacy\Incident;

/**
 * "Create Issue" Action (immutable object)
 */
final class CreateIssue extends AbstractAction implements Action
{
    /** @var [type] **/
    private $dbFactory;
    /** @var [type] **/
    private $logger;
    /** @var Mailer **/
    private $mailer;
    /** @var TemplateEngine **/
    private $templateEngine;
    /** @var Output **/
    private $output;

    /**
     * @param [type]         $logger
     * @param [type]         $dbFactory
     * @param TemplateEngine $templateEngine
     * @param Output         $output
     * @param Mailer         $mailer         [description]
     */
    public function __construct($logger, $dbFactory, TemplateEngine $templateEngine, Output $output, Mailer $mailer)
    {
        $this->dbFactory      = $dbFactory;
        $this->logger         = $logger;
        $this->mailer         = $mailer;
        $this->output         = $output;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @inheritDoc
     */
    public function handle(Mailbox $mailbox, EMail $email): Action
    {
        try { // TODO grosse refacto à prévoir dans cette classe -> SRP violation
            $emailID = $this->enregistreEmail($email);

            if (false === $emailID || is_null($emailID)) {
                (new Move($mailbox, 'A corriger', $this->output))->handle($email);

                return new Failure(
                    $this,
                    sprintf('enregistrement de l\'e-mail "%s" impossible', $email->subject())
                );
            }

            try {
                // update the existing issue
                $issueID = $this->issueIDFromEmail($email);

                $this->updateIncidentParEmail($email, $issueID, $emailID);
            } catch (NotFoundException $exception) {
                // create a new issue
                $issueID = $this->nouvelIncidentParEmail($email, $emailID);

                if (in_array(strtolower($email->from()), MAIL_BLACKLIST) === false) {
                    $this->envoiEmailReponseAuto($issueID, $email->from());
                } else {
                    $this->output->write(' > pas de réponse auto : e-mail blacklisté');
                }
            }
        } catch (Exception $exception) {
            return new Failure(
                $this,
                sprintf(
                    'enregistrement de l\'e-mail "%s" impossible : %s',
                    $email->subject(),
                    $exception->getMessage()
                )
            );
        }

        return new Success($this, $issueID);
    }

    /**
     * TODO [enregistreEmail description]
     * @param EMail $email
     * @return int
     */
    private function enregistreEmail(EMail $email): int
    {
        $data = array(
            'expediteur' => $email->from(),
            'objet'      => (new Windows1252Text($email->subject()))->toString(),
            'corps'      => (new Windows1252Text($email->body()))->toString(),
            'date'       => date('Y-m-d H:i:s')
        );

        return DatabaseTools::insert(DB_INCIDENT['base'], $this->logger, 'email', $data, true, true);
    }

    /**
     * TODO [issueIDFromEmail description]
     * @param EMail $email
     * @return int
     * @throws Exception
     */
    private function issueIDFromEmail(EMail $email): int
    {
        $this->output->write(' > recherche de "[LEGACY#xxxx]" dans le sujet : ' . $email->subject());

        $preg = preg_match('/\[LEGACY\#([0-9]+)\]/', $email->subject(), $match);

        if ($preg) {
            $issueId = $match[1];

            if ($this->existeIncident($issueId)) {
                $this->output->write(sprintf(' > ticket #%s trouvé en bdd', $issueId));

                return $issueId;
            }

            $this->output->write(sprintf(' > ticket #%s introuvable en bdd', $issueId));

            throw new NotFoundException(sprintf('issue #%s not found', $issueId));
        }

        $this->output->write(' > aucun résultat');

        throw new NotFoundException(sprintf('no issue ID found in the mail subject : %s', $email->subject()));;
    }

    /**
     * TODO [updateIncidentParEmail description]
     * @param EMail $email
     * @param int   $issueID
     * @param int   $emailID
     */
    private function updateIncidentParEmail(EMail $email, int $issueID, int $emailID)
    {
        $this->output->write(' > mise à jour de l\'incident #' . $issueID);

        $note_array = array(
            'incident_id' => $issueID,
            'date'        => date('Y-m-d H:i:s'),
            'texte'       => 'note créée suite à une réponse par mail',
            'from'        => $email->from(),
            'email_id'    => $emailID
        );

        $noteAdded = DatabaseTools::insert(
            DB_INCIDENT['base'],
            $this->logger,
            'incident_note',
            $note_array,
            true,
            true
        );

        if (!$noteAdded) {
            throw new Exception('Enregistrement de la note impossible');
        }

        $incident = new Incident($issueID, $this->logger, $this->dbFactory);

        $incident->majNonLu();

        $incident->setByArray([ 'statut' => 'nouveau' ]);

        $incident->addHisto(null, 'Ajout de note par import email');

        $incident->enregistrer();
    }

    /**
     * TODO [nouvelIncidentParEmail description]
     * @param EMail $email
     * @param int   $emailID
     * @return int
     * @throws Exception
     */
    private function nouvelIncidentParEmail(EMail $email, int $emailID)
    {
        $this->output->write(' > création d\'un nouvel incident');

        $incident = new Incident(null, $this->logger, $this->dbFactory);

        $incident->setByEmail($email, $emailID);
        $incident->enregistrer();

        if (!$incident->id) {
            throw new Exception('A ce stade, l\incident doit avoir un ID. L\'enregistrement n\'a pas fonctionné');
        }

        $this->output->write(' > incident #' . $incident->id);

        $incident->addHisto(null, 'Création par import email');

        return $incident->id;
    }

    /**
     * TODO [envoiEmailReponseAuto description]
     * @param int    $issueId
     * @param string $destinataire
     */
    private function envoiEmailReponseAuto($issueId, $destinataire)
    {
        $this->output->write(sprintf(' > envoi de la réponse auto, incident %s', $issueId));

        $incident = new Incident($issueId, $this->logger, $this->dbFactory);

        $contenu = $this->templateEngine
            ->setTemplateDir(DOSSIER_TPL)
            ->assign($incident->toArray())
            ->fetch('import_reponse_email.tpl');

        $this->mailer->sendEmailTo(
            new BaseEmailDTO(
                new SimpleText('[LEGACY#' . $issueId . '] ' . stripslashes($incident->titre)),
                new SimpleText($contenu),
                []
            ),
            [
                new MainRecipient(
                    new EmailAddress($destinataire)
                )
            ]
        );
    }

    /**
     * TODO [existeIncident description]
     * @param  string $issueId
     * @return bool
     */
    private function existeIncident($issueId)
    {
        $query    = 'select * from incident where id = :id';
        $database = $this->dbFactory->createDatabase(DB_INCIDENT['base'], true);
        $result   = $database->query($query, PDO::FETCH_ASSOC, array('id' => $issueId));

        return !empty($result);
    }

}
