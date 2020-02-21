<?php

namespace App\Services\Templating;

/**
 * Template Engine interface
 */
interface TemplateEngine
{

    /**
     * Assigns a variable
     * @param array|string $name    the template variable name(s)
     * @param mixed        $value   the value to assign
     * @param bool         $noCache if true any output of this variable will be not cached
     *
     * @return TemplateEngine
     */
    public function assign($name, $value = null, $noCache = false): TemplateEngine;

    /**
     * Fetches a rendered template
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cacheId    cache ID to be used with this template
     * @param mixed  $compileId  compile ID to be used with this template
     * @param object $parent     next higher level of variables
     *
     * @return string rendered template output
     */
    public function fetch($template = null, $cacheId = null, $compileId = null, $parent = null): string;

    /**
     * Set template directory
     * @param string|array $templateDirectory directory(s) of template sources
     * @param bool         $isConfig          true for config_dir
     *
     * @return TemplateEngine
     */
    public function setTemplateDir($templateDirectory, $isConfig = false): TemplateEngine;

}
