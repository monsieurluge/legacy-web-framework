<?php

namespace App\Controller\Pages;

use App\Repository\Issues as Repository;
use App\Services\Request\CustomRequest\Page\Get\Issue as IssueRequest;
use App\Services\Templating\TemplateEngine;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issue page controller
 */
final class Issue
{

    /** @var [type] **/
    private $notFoundPage;
    /** @var Repository **/
    private $repository;
    /** @var TemplateEngine **/
    private $templateEngine;

    /**
     * @param TemplateEngine $templateEngine
     * @param Repository     $repository
     * @param [type]         $notFoundPage
     */
    public function __construct(
        TemplateEngine $templateEngine,
        Repository $repository,
        $notFoundPage
    ) {
        $this->notFoundPage   = $notFoundPage;
        $this->repository     = $repository;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param IssueRequest $request
     *
     * @return Response
     */
    public function process($request): Response
    {
        return $this->repository
            ->findById($request->issueId())
            ->map(function() use ($request) {
                return new Response(
                    $this->templateEngine
                        ->setTemplateDir(DOSSIER_TPL)
                        ->assign('userId', $request->userId())
                        ->assign('username', $request->userLastName())
                        ->assign('version', PROJECT_VERSION)
                        ->fetch($this->template())
                );
            })
            ->getValueOrExecOnFailure(function() use ($request) {
                return $this->notFoundPage->process($request);
            });
    }

    /**
     * Returns the template
     *
     * @return string
     */
    private function template(): string
    {
        return 'index.tpl';
    }

}
