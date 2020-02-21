<?php

namespace App\Services\Mail\Action;

use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;
use App\Services\Output\Output;

/**
 * "Print E-Mail Infos" Action (immutable object)
 */
final class PrintInfos extends AbstractAction implements Action
{

    /** @var Output **/
    private $output;

    /**
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function handle(Mailbox $mailbox, EMail $email): Action
    {
        $this->output->write(sprintf(
            'UID #%s, envoyé par "%s", objet : "%s"',
            $email->uid(),
            $email->from(),
            $email->subject()
        ));

        return new Success($this, null);
    }

}
