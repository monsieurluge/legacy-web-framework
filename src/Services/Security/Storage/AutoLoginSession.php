<?php

namespace App\Services\Security\Storage;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\UpdateSession;
use App\Services\Security\Session;

/**
 * Auto Login Session
 */
final class AutoLoginSession implements Session
{
    /** @var Session **/
    private $cookie;
    /** @var Session **/
    private $session;

    /**
     * @param Session $session
     * @param Session $cookie
     */
    public function __construct(Session $session, Session $cookie)
    {
        $this->cookie  = $cookie;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function retrieve(string $key): Result
    {
        return $this->session
            ->retrieve($key)
            ->then(function ($target) {
                return new Success((new UpdateSession($this))->handle($target));
            })
            ->getValueOrExecOnFailure(function () use ($key) {
                return $this->cookie->retrieve($key);
            });
    }

    /**
     * @inheritDoc
     */
    public function update(array $values): Session
    {
        $this->cookie->update($values);

        $this->session->update($values);

        return $this;
    }
}
