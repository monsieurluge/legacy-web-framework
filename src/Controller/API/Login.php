<?php

namespace App\Controller\API;

use App\Services\Security\UserFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Login API
 */
final class Login
{

    /** @var UserFactory **/
    private $userFactory;

    /**
     * @param UserFactory $userFactory
     */
    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    /**
     * @inheritDoc
     */
    public function process($request): Response
    {
        return $this->userFactory
            ->createFromLogin(
                filter_input(INPUT_POST, 'login'),
                filter_input(INPUT_POST,'password')
            )
            ->map(function() { return new Response(); })
            ->getValueOrExecOnFailure(function() {
                return new Response('', 400);
            });
    }

}
