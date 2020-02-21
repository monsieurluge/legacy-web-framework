<?php

namespace App\Controller\Pages;

use App\Services\Request\CustomRequest\Page\Unauthorized as UnauthorizedRequest;
use App\Services\Templating\TemplateEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unauthorized page
 */
final class Unauthorized
{

    /** @var TemplateEngine **/
    private $templateEngine;

    /**
     * @param TemplateEngine $templateEngine
     */
    public function __construct(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @inheritDoc
     * @param UnauthorizedRequest $request
     */
    public function process($request): Response
    {
        return new Response(
            $this->templateEngine
                ->setTemplateDir(DOSSIER_TPL)
                ->assign('destination', $request->destination())
                ->fetch($this->template()),
            200
        );
    }

    /**
     * Returns the template
     * @return string
     */
    private function template(): string
    {
        return 'unauthorized.tpl';
    }

}
