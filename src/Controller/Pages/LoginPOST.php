<?php

namespace App\Controller\Pages;

use App\Services\Templating\TemplateEngine;
use App\Services\Security\UserFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Login page, for POST requests
 */
final class LoginPOST
{

    /** @var TemplateEngine **/
    private $templateEngine;
    /** @var UserFactory **/
    private $userFactory;

    /**
     * @param TemplateEngine $templateEngine
     * @param UserFactory    $userFactory
     */
    public function __construct(TemplateEngine $templateEngine, UserFactory $userFactory)
    {
        $this->templateEngine = $templateEngine;
        $this->userFactory    = $userFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        return $this->userFactory
            ->createFromLogin(
                filter_input(INPUT_POST, 'login'),
                filter_input(INPUT_POST,'password')
            )
            ->map(function() {
                return new Response(
                    '',
                    301,
                    [ 'Location' => '/' ]
                );
            })
            ->getValueOrExecOnFailure(function() {
                return new Response(
                    '',
                    301,
                    [ 'Location' => '/login' ]
                );
            });
    }

}
