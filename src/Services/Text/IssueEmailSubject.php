<?php

namespace App\Services\Text;

use App\Domain\DTO\Email;
use App\Services\Text\Text;
use Legacy\Incident;

final class IssueEmailSubject implements Text
{

    /** @var Email **/
    private $email;
    /** @var Incident **/
    private $issue;

    /**
     * @param Incident $issue
     * @param Email    $email
     */
    public function __construct(Incident $issue, Email $email)
    {
        $this->email = $email;
        $this->issue = $issue;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return true === $this->issue->hasIdEtude()
            ? $this->subjectWithOffice()
            : $this->simpleSubject();
    }

    /**
     * Returns the subject
     *
     * @return string
     */
    private function simpleSubject(): string
    {
        return sprintf(
            '[LEGACY#%s] %s',
            $this->issue->id,
            $this->email->subject()->toString()
        );
    }

    /**
    * Returns the subject
    *
    * @return string
    */
    private function subjectWithOffice(): string
    {
        return sprintf(
            '[LEGACY#%s] Etude %s - %s',
            $this->issue->id,
            $this->issue->getRefLegacy(),
            $this->email->subject()->toString()
        );
    }

}
