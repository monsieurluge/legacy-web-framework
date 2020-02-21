<?php

namespace App\Services\Mail\Action;

use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;
use App\Services\Output\Output;

/**
 * Move E-Mail Action (immutable object)
 */
final class Move extends AbstractAction implements Action
{

    /** @var Output **/
    private $output;
    /** @var string **/
    private $to;

    /**
     * @param string $to
     * @param Output $output
     */
    public function __construct(string $to, Output $output)
    {
        $this->output = $output;
        $this->to     = $to;
    }

    /**
     * @inheritDoc
     */
    public function handle(Mailbox $mailbox, EMail $email): Action
    {
        $mailbox->move(
            $email,
            $this->to,
            function() use ($email) {
                $this->output->write(sprintf(
                    ' > déplacement de l\'e-mail %s ("%s") vers "%s" effectué',
                    $email->uid(),
                    $email->subject(),
                    $this->to
                ));
            },
            function($errorMessage) use ($email) { $this->output->write($errorMessage); }
        );

        return new Success($this, null);
    }

}
