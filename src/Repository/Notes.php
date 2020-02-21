<?php

namespace App\Repository;

use DateTime;
use PDO;
use monsieurluge\result\Result\Result;
use App\Domain\DTO\Note;
use App\Domain\ValueObject\EmailAddress;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Lastname;
use App\Repository\NotesRepository;
use App\Services\Database\Database;
use App\Services\Error\CannotCreateNote;
use App\Services\Error\CannotDeleteNote;
use App\Services\Error\CannotFetchNotes;
use App\Services\Error\CannotUpdateNote;
use monsieurluge\result\Result\Failure;
use App\Services\Result\None;
use App\Services\Result\Some;
use monsieurluge\result\Result\Success;
use App\Services\Security\User\User;
use App\Services\Text\SimpleText;
use App\Services\Text\Windows1252Text;

/**
 * Notes repository.
 */
final class Notes implements NotesRepository
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
     * @inheritDoc
     */
    public function create(ID $issueId, User $user, string $content): Result
    {
        $query = 'INSERT INTO `incident_note` (`incident_id`, `utilisateur`, `date`, `texte`, `temps`)'
            . ' VALUES (:issueId, :userId, :createdAt, :content, :duration)';

        $noteId = $this->dataSource
            ->exec(
                $query,
                [
                    'issueId'   => $issueId->value(),
                    'userId'    => $user->toArray()['id'],
                    'createdAt' => (new DateTime('now'))->format('Y-m-d H:i:s'),
                    'content'   => (new Windows1252Text($content))->toString(),
                    'duration'  => 0
                ],
                true
            );

        return $noteId > 0
            ? new Success(new ID($noteId))
            : new Failure(
                new CannotCreateNote(),
                'création de note impossible - raison inconnue'
            );
    }

    /**
     * @inheritDoc
     */
    public function delete(int $noteId): Result
    {
        $deleted = $this->dataSource
            ->exec(
                'DELETE FROM incident_note WHERE id=:noteId',
                [ 'noteId'  => $noteId ]
            );

        return true === $deleted
            ? new Success((object) [ 'id' => $noteId ])
            : new Failure(
                new CannotDeleteNote(),
                'suppression impossible - raison inconnue'
            );
    }

    /**
     * @inheritDoc
     */
    public function findById(int $noteId): Result
    {
        $query = 'SELECT a.id, texte as content, a.from, b.prenom as createdBy, a.date as createdAt, a.email_id as emailId, a.incident_id as issueId'
            . ' FROM incident_note a'
            . ' LEFT JOIN utilisateur b ON a.utilisateur = b.id'
            . ' WHERE a.id = :id';

        $note = $this->dataSource
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $noteId ]);

        return false === $note
            ? new Failure(
                new CannotFetchNotes(),
                sprintf('la note #%s n\'a pas été trouvée', $noteId)
            )
            : new Success((object) $note[0]);
    }

    /**
     * @inheritDoc
     */
    public function findByIssue(int $issueId): Result
    {
        $query = 'SELECT a.id, texte as content, a.from, b.prenom as createdBy, a.date as createdAt, a.email_id as emailId'
            . ' FROM incident_note a'
            . ' LEFT JOIN utilisateur b ON a.utilisateur = b.id'
            . ' WHERE incident_id = :id'
            . ' ORDER BY a.date asc';

        $notes = $this->dataSource
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $issueId ]);

        return false === $notes
            ? new Failure(
                new CannotFetchNotes(),
                'récupération impossible - raison inconnue'
            )
            : new Success(array_map(
                function(array $note) { return $this->databaseNoteToNoteDTO($note); },
                $notes
            ));
    }

    /**
     * @inheritDoc
     */
    public function updateContent(int $noteId, string $content): Result
    {
        if (empty($content)) {
            return new Failure(
                new CannotUpdateNote(),
                'le contenu de la note ne doit pas être vide'
            );
        }

        $updated = $this->dataSource
            ->exec(
                'UPDATE incident_note SET texte=:content WHERE id=:noteId',
                [
                    'noteId'  => $noteId,
                    'content' => (new Windows1252Text($content))->toString()
                ]
            );

        return true === $updated
            ? $this->findById($noteId)
            : new Failure(
                new CannotUpdateNote(),
                'mise à jour impossible - raison inconnue'
            );
    }

    /**
     * Converts a database note to a Note DTO
     *
     * @param  array $rawNote
     * @return Note
     */
    private function databaseNoteToNoteDTO(array $rawNote): Note
    {
        return new Note(
            new ID($rawNote['id']),
            new SimpleText($rawNote['content']),
            new DateTime($rawNote['createdAt']),
            new Lastname(empty($rawNote['createdBy']) ? 'inconnu' : $rawNote['createdBy']),
            true === empty($rawNote['from'])
                ? new None()
                : new Some(new EmailAddress($rawNote['from'])),
            true === empty($rawNote['emailId'])
                ? new None()
                : new Some(new ID($rawNote['emailId']))
        );
    }
}
