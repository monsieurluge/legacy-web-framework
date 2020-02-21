<?php

namespace App\Controller\API;

use DateTime;
use monsieurluge\result\Error\Error;
use App\Application\Command\Action\CreateMailSentNote;
use App\Application\Command\Action\CreateMailHistoryEntry;
use App\Application\Command\Action\SendIssueEmail;
use App\Repository\Incident as IssuesRepository;
use App\Repository\Notes as NotesRepository;
use App\Services\Database\Database;
use App\Services\Mail\EmailFromRequest;
use App\Services\Mail\RecipientsFromRequest;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Request\CustomRequest\Api\Post\SendEmail as SendEmailRequest;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Response\JsonApi\JsonApiResponse;
use App\Services\Templating\TemplateEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends an email.
 */
final class SendEmail
{
    /** @var Database **/
    private $issueDatabase;
    /** @var IssuesRepository **/
    private $issuesRepository;
    /** @var Mailer **/
    private $mailer;
    /** @var NotesRepository **/
    private $notesRepository;
    /** @var TemplateEngine **/
    private $templateEngine;

    /**
     * @param TemplateEngine   $templateEngine
     * @param Mailer           $mailer
     * @param IssuesRepository $issuesRepository
     * @param NotesRepository  $notesRepository
     * @param Database         $issueDatabase
     */
    public function __construct(
        TemplateEngine $templateEngine,
        Mailer $mailer,
        IssuesRepository $issuesRepository,
        NotesRepository $notesRepository,
        Database $issueDatabase
    ) {
        $this->issueDatabase     = $issueDatabase;
        $this->issuesRepository  = $issuesRepository;
        $this->mailer            = $mailer;
        $this->notesRepository   = $notesRepository;
        $this->templateEngine    = $templateEngine;
    }

    /**
     * @param SendEmailRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        $mainRecipients = isset($_POST['desti']) ? $this->notEmptyItems($_POST['desti']) : [];
        $carbonCopy     = isset($_POST['copie']) ? $this->notEmptyItems($_POST['copie']) : [];
        $attachments    = isset($_POST['pjs'])   ? $this->notEmptyItems($_POST['pjs'])   : [];
        $subject        = isset($_POST['objet']) ? $_POST['objet'] : '';
        $emailBody      = isset($_POST['texte']) ? $_POST['texte'] : '';

        if (empty($mainRecipients)) {
            return new JsonApiResponse(
                '',
                [
                    new JsonApiError(
                        'send_email_api_' . (new DateTime())->getTimestamp(),
                        ErrorsEnum::CANNOT_SEND_EMAIL,
                        'l\'e-mail doit être envoyé à au moins un destinataire'
                    )
                ]
            );
        }

        $email = (new EmailFromRequest(
            $request->issueId(),
            $attachments,
            $subject,
            $emailBody,
            $this->issueDatabase,
            $this->templateEngine
        ))->create();

        $recipients = (new RecipientsFromRequest(
            $mainRecipients,
            $carbonCopy
        ))->create();

        return $this->issuesRepository
            ->findById($request->issueId())
            ->then(function ($target) use ($email, $recipients) {
                return (new SendIssueEmail($this->mailer, $email, $recipients))->handle($target);
            })
            ->then(function ($target) use ($request, $recipients, $email, $emailBody) {
                return (new CreateMailSentNote($this->notesRepository, $request->user(), $recipients, $email, $emailBody))->handle($target);
            })
            ->then(function ($target) use ($request) {
                return (new CreateMailHistoryEntry($request->user()))->handle($target);
            })
            ->map(function() { return new JsonApiResponse(''); })
            ->getValueOrExecOnFailure(function(Error $error) {
                return new JsonApiResponse(
                    '',
                    [
                        new JsonApiError(
                            'send_email_api_' . (new DateTime())->getTimestamp(),
                            ErrorsEnum::CANNOT_SEND_EMAIL,
                            $error->message()
                        )
                    ]
                );
            });
    }

    /**
     * Returns a filtered list (removes the empty items)
     *
     * @param  array $from
     * @return array
     */
    private function notEmptyItems(array $from): array
    {
        return array_filter(
            $from,
            function($value) { return false === empty($value); }
        );
    }
}
