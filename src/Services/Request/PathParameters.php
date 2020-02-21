<?php

namespace App\Services\Request;

use \Exception;
use App\ServiceInterfaces\Requests\PathParametersInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTTP Request Path Parameters
 */
final class PathParameters implements PathParametersInterface
{

    /** @var array **/
    private $cachedExtract;
    /** @var string **/
    private $pattern;
    /** @var Request **/
    private $request;

    /**
     * @codeCoverageIgnore
     * @param string  $pattern
     * @param Request $request
     */
    public function __construct(string $pattern, Request $request)
    {
        $this->cachedExtract = [];
        $this->pattern       = $pattern;
        $this->request       = $request;
    }

    /**
     * Returns the parameters that have been extracted from the request
     * @codeCoverageIgnore
     * @return array
     */
    private function cachedExtract(): array
    {
        $total = count($this->cachedExtract);

        if ($total > 0) {
            return $this->cachedExtract[$total - 1];
        }

        $this->cachedExtract[] = $this->extractParameters();

        return $this->cachedExtract();
    }

    /**
     * Extracts the request parameters, according to the URI pattern
     * @codeCoverageIgnore
     * @return array
     */
    private function extractParameters()
    {
        $pattern    = explode('/', $this->pattern);
        $path       = explode('/', $this->request->getPathInfo());
        $elementsNb = count($pattern);
        $parameters = [];

        for ($index = 0; $index < $elementsNb; $index++) {
            $matches = [];

            $matched = preg_match('#{([a-zA-Z0-9-_]+)}#', $pattern[$index], $matches);

            if (1 === $matched && true === isset($path[$index])) {
                $parameters[$matches[1]] = $path[$index];
            }
        }

        return $parameters;
    }

    /**
     * @inheritDoc
     */
    public function valueFor(string $parameter): string
    {
        if (false === isset($this->cachedExtract()[$parameter])) {
            throw new Exception(
                sprintf(
                    'the parameter "%s" is not part of the HTTP request path',
                    $parameter
                )
            );
        }

        return $this->cachedExtract()[$parameter];
    }

}
