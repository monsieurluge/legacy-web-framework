<?php

namespace App\Controller\Pages;

use App\Services\Templating\TemplateEngine;
use App\Services\Request\CustomRequest\Page\CriticalError as CriticalErrorRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * "HTTP 500 Server error" Page
 */
final class CriticalError
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
     * @param CriticalErrorRequest $request
     */
    public function process($request): Response
    {
        return new Response(
            $this->templateEngine
                ->setTemplateDir(DOSSIER_TPL)
                ->assign('report', $request->trace())
                ->fetch($this->template()),
            500
        );
    }

    /**
     * Returns the template
     * @return string
     */
    private function template(): string
    {
        return '500.tpl';
    }

}
