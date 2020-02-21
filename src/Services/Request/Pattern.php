<?php

namespace App\Services\Request;

use Closure;
use App\ServiceInterfaces\Requests\PatternInterface;
use Symfony\Component\HttpFoundation\Request as RawRequest;

/**
 * Pattern
 */
final class Pattern implements PatternInterface
{
    /** @var string **/
    private $pattern;
    /** @var array **/
    private $structure;

    /**
     * @codeCoverageIgnore
     *
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern   = $pattern;
        $this->structure = null;
    }

    /**
     * @inheritDoc
     */
    public function pattern(): string
    {
        return $this->pattern;
    }

    /**
     * @inheritDoc
     */
    public function matches(RawRequest $request): bool
    {
        $matches = array_map(
            $this->patternItemMatchesPathItem(),
            $this->structure(),
            explode('/', $request->getPathInfo())
        );

        return !in_array(false, $matches, true);
    }

    /**
     * Returns a Closure which tells if the two items provided matches.
     * The first item is a pattern
     *
     * @return Closure the function as follows: f(string,string) -> bool
     */
    private function patternItemMatchesPathItem(): Closure
    {
        return function ($patternItem, $pathItem): bool
        {
            return isset($patternItem['parameter'])
                ? (false === empty($pathItem)) // a parameter must not be empty
                : $patternItem['path'] === $pathItem; // the paths must match
        };
    }

    /**
     * Returns either a parameter or a path string:
     *  - parameter -> [ 'parameter' => parameter's name ]
     *  - path      -> [ 'path'      => path's name ]
     * Ex: '{id}' -> [ 'parameter' => 'id' ], 'foo' -> [ 'path' => 'foo' ]
     *
     * @param string $part
     *
     * @return array a key-value pair
     */
    private function parameterOrPath(string $part): array
    {
        $matches = [];
        $match   = preg_match('#{([a-zA-Z0-9-_]+)}#', $part, $matches);

        return 1 === $match
            ? [ 'parameter' => $matches[1] ]
            : [ 'path' => $part ];
    }

    /**
     * Returns the pattern's structure.
     * Ex, using the pattern '/foo/{id}/bar/{test}/baz':
     *   [ ['path'=>'foo'],['parameter'=>'id'],['path'=>'bar'],['parameter'=>'test'],['path'=>'baz'] ]
     *
     * @return array
     */
    private function structure(): array
    {
        if (is_null($this->structure)) {
            $this->structure = array_reduce(
                explode('/', $this->pattern),
                function ($result, $part) {
                    return array_merge(
                        $result,
                        [ $this->parameterOrPath($part) ]
                    );
                },
                []
            );
        }

        return $this->structure;
    }
}
