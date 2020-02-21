<?php

namespace App\Services\Request;

use \Exception;
use App\ServiceInterfaces\Requests\QueryStringInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A Query String made from a HTTP Request
 */
final class QueryString implements QueryStringInterface
{

    /** @var Request **/
    private $request;

    /**
     * @codeCoverageIgnore
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function valueFor(string $parameter): string
    {
        $value = $this->request->query->get($parameter);

        if (is_null($value)) {
            throw new Exception(sprintf(
                'the parameter "%s" is not part of the HTTP request query parameters',
                $parameter
            ));
        }

        if (is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function valueForOr(string $parameter, string $default): string
    {
        try {
            return $this->valueFor($parameter);
        } catch (Exception $exception) {
            return $default;
        }
    }

}
