<?php

namespace App\Services\Templating;

use App\Services\Templating\TemplateEngine;
use Smarty;

/**
 * Smarty Template Engine wrapper
 */
final class SmartyEngine implements TemplateEngine
{

    /** @var Smarty **/
    private $origin;

    /**
     * @param Smarty $origin
     */
    public function __construct(Smarty $origin)
    {
        $this->origin = $origin;
    }

    /**
     * @inheritDoc
     */
    public function assign($name, $value = null, $noCache = false): TemplateEngine
    {
        return new self($this->origin->assign($name, $value, $noCache));
    }

    /**
     * @inheritDoc
     */
    public function fetch($template = null, $cacheId = null, $compileId = null, $parent = null): string
    {
        return $this->origin->fetch($template, $cacheId, $compileId, $parent);
    }

    /**
     * @inheritDoc
     */
    public function setTemplateDir($templateDirectory, $isConfig = false): TemplateEngine
    {
        return new self($this->origin->setTemplateDir($templateDirectory, $isConfig));
    }

}
