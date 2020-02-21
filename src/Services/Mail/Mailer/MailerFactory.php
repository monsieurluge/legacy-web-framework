<?php

namespace App\Services\Mail\Mailer;

use App\Domain\ValueObject\EmailAddress;
use App\Services\Mail\Builder\MailBuilder;
use App\Services\Mail\Mailer\DebugMailer;
use App\Services\Mail\Mailer\SwiftMailer;
use App\Services\Mail\Mailer\Mailer;
use Swift_SmtpTransport;

/**
 * Mailer Factory (immutable object)
 * Helps to create a Mailer object
 */
final class MailerFactory
{

    /** @var MailBuilder **/
    private $mailBuilder;

    /**
     * @param MailBuilder $mailBuilder
     */
    public function __construct(MailBuilder $mailBuilder)
    {
        $this->mailBuilder = $mailBuilder;
    }

    /**
     * Returns a Mailer depending on the configured and global ENVIRONMENT variable
     *
     * @return Mailer
     */
    public function createFromEnvironment(): Mailer
    {
        switch (strtolower(ENVIRONMENT)) {
            case 'prod':
            case 'production':
                return $this->createSwiftMailer();
                break;
            case 'local':
            case 'dev':
            case 'development':
            case 'test':
            default:
                return new DebugMailer($this->createSwiftMailer());
                break;
        }
    }

    /**
     * Returns a Swift Mailer object
     *
     * @return Mailer
     */
    private function createSwiftMailer(): Mailer
    {
        return new SwiftMailer(
            $this->mailBuilder,
            (new Swift_SmtpTransport(MAIL_SMTP, MAIL_SMTP_PORT, MAIL_SMTP_SECURITY))
                ->setUsername(MAIL_SMTP_USER)
                ->setPassword(MAIL_SMTP_PASSWORD),
            new EmailAddress(MAIL_SENDER_EMAIL),
            MAIL_SENDER_NAME
        );
    }

}
