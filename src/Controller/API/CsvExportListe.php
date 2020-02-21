<?php

namespace App\Controller\API;

use Closure;
use DateTime;
use Exception;
use App\Application\Command\Action\SendCSVExportMail;
use App\Application\Query\Issue as IssueDto;
use App\Application\Query\User as UserDto;
use App\Repository\Issues as IssuesRepository;
use App\Repository\User as UserRepository;
use App\Services\File\CsvFile;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Mail\Recipient\Main;
use App\Services\Mail\Recipient\Recipient;
use App\Services\Printer\SimpleRecipientPrinter;
use App\Services\Request\CustomRequest\Api\Get\CsvExportListe as CsvExportListeRequest;
use App\Services\Response\JsonApi\JsonApiResponse;
use App\Services\Response\JsonApi\Error as JsonApiError;
use App\Services\Response\JsonApi\ErrorsEnum;
use App\Services\Templating\TemplateEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * Csv Export Liste
 */
final class CsvExportListe
{
    /** @var Mailer **/
    private $baseMailer;
    /** @var IssuesRepository **/
    private $issuesRepository;
    /** @var TemplateEngine **/
    private $templateEngine;
    /** @var UserRepository **/
    private $userRepository;

    /**
     * @param IssuesRepository $issuesRepository
     * @param UserRepository   $userRepository
     * @param Mailer           $mailer
     * @param TemplateEngine   $templateEngine
     */
    public function __construct(
        IssuesRepository $issuesRepository,
        UserRepository $userRepository,
        Mailer $mailer,
        TemplateEngine $templateEngine
    ) {
        $this->baseMailer       = $mailer;
        $this->issuesRepository = $issuesRepository;
        $this->templateEngine   = $templateEngine;
        $this->userRepository   = $userRepository;
    }

    /**
     * @param CsvExportListeRequest $request
     *
     * @return Response
     */
    public function process(CsvExportListeRequest $request): Response
    {
        // fetch the issues and convert them to the expected format
        $csvData = $this->issuesToCsvData($this->issuesRepository->findBy($request->filters(), 10000));

        // create the csv file
        try {
            $name = sprintf('export_liste_%s.csv', date('Ymd_His'));

            $file = (new CsvFile(
                $this->csvHeaders(),
                $csvData,
                sprintf('/tmp/%s', $name),
                $name
            ))->write();
        } catch (Exception $exception) {
            // returns a "Failure" response
            return new JsonApiResponse(
                null,
                [
                    new JsonApiError(
                        'csv_' . (new DateTime())->getTimestamp(),
                        ErrorsEnum::CANNOT_SAVE_CSV_FILE,
                        'impossible d\'écrire le fichier d\'extract CSV dans le dossier de destination'
                    )
                ],
                500
            );
        }

        return $this->userRepository
            ->findById($request->userId())
            ->map($this->createEmailRecipient())
            ->then(function ($target) use ($file) {
                return (new SendCSVExportMail($this->baseMailer, $this->templateEngine, $file))->handle($target);
            })
            ->map($this->generateSuccessfulResponse())
            ->getValueOrExecOnFailure($this->generateFailureResponse());
    }

    /**
     * Returns a function which takes an User and returns a "main recipient" result.
     *
     * @return Closure the function as follows: f(UserDto) -> Result<Recipient>
     */
    private function createEmailRecipient(): Closure
    {
        return function (UserDto $user) {
            return [
                new Main(
                    $user->email()
                )
            ];
        };
    }

    /**
     * Returns the CSV headers
     *
     * @return array
     */
    private function csvHeaders(): array
    {
        return [
            'titre',
            'date_ouverture',
            'date_fermeture',
            'id',
            'id_etude',
            'priorite',
            'categorie',
            'statut',
            'type',
            'origine',
            'assignea',
            'perimetre',
            'temps_actif',
            'temps_pause',
        ];
    }

    /**
     * Returns a function which generates a HTTP#500 error response
     *
     * @return Closure the function as follows: f() -> Response
     */
    private function generateFailureResponse(): Closure
    {
        return function () {
            return new JsonApiResponse(
                null,
                [
                    new JsonApiError(
                        'csv_' . (new DateTime())->getTimestamp(),
                        ErrorsEnum::CANNOT_SEND_CSV_FILE,
                        'impossible d\'envoyer l\'extract CSV par mail'
                    )
                ],
                500
            );
        };
    }

    /**
     * Returns a function which generates an "export sent" response, using the
     *   e-mail recipients as parameters.
     *
     * @return Closure the function as follows: f(Recipient[]]) -> Result<Response>
     */
    private function generateSuccessfulResponse(): Closure
    {
        return function(array $recipients) {
            $printer = new SimpleRecipientPrinter();

            array_map(
                function(Recipient $recipient) use ($printer) { $recipient->print($printer); },
                $recipients
            );

            return new JsonApiResponse([
                'message' => sprintf('le fichier a été envoyé par e-mail à %s', $printer->mainAsString())
            ]);
        };
    }

    /**
     * Converts issues to "export CSV" raw data.
     *
     * @param IssueDto[] $issues
     *
     * @return array
     */
    private function issuesToCsvData(array $issues): array
    {
        $data = [];

        foreach ($issues as $issue) {
            $data[] = [
                $issue->title()->value(),
                $issue->lifeCycle()->started(),
                $issue->lifeCycle()->ended()->getOrElse(''),
                $issue->id()->value(),
                $issue->office()->value(),
                ucfirst($issue->priority()->name()->value()),
                $issue->category()->name()->value(),
                $this->formatStatus($issue->status()->value()),
                $issue->type()->value(),
                $issue->origin()->label()->value(),
                $issue->agent()->initials()->value(),
                $issue->context()->label()->value(),
                $issue->lifeCycle()->readTime()->value(),
                $issue->lifeCycle()->pauseTime()->value(),
            ];
        }

        return $data;
    }

    /**
     * Returns the formatted status.
     *
     * @param string $name
     *
     * @return string
     */
    private function formatStatus(string $name): string
    {
        switch ($name) {
            case 'nouveau':
                return 'Nouveau';
            case 'attente':
                return 'Attente info';
            case 'traite':
                return 'Traité';
            default:
                return 'INCONNU';
        }
    }
}
