<?php

namespace App\Controller\Pages;

use App\Services\Templating\TemplateEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * "404 Not Found" Page
 */
final class NotFound
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
     */
    public function process(): Response
    {
        return new Response(
            $this->templateEngine
                ->setTemplateDir(DOSSIER_TPL)
                ->fetch($this->template()),
            404
        );
    }

    /**
     * Returns the template
     * @return string
     */
    private function template(): string
    {
        return '404.tpl';
    }

}
