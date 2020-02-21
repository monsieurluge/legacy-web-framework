<?php

namespace App\Services\Mail\Action;

use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\Action\Success;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;
use App\Services\Output\Output;

/**
 * Delete E-Mail Action (immutable object)
 */
final class Delete extends AbstractAction implements Action
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
        $mailbox->delete(
            $email,
            function() use ($email) {
                $this->output->write(sprintf(
                    ' > suppression de l\'e-mail %s effectuée',
                    $email->uid()
                ));
            }
        );

        return new Success($this, null);
    }

}
