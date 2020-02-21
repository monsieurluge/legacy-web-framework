<?php

namespace App\Services\Request;

use Exception;
use App\Services\Request\PathParameters;
use App\Services\Request\QueryString;
use App\Services\Security\User\User;

/**
 * HTTP Request.
 */
final class Request
{
    /** @var mixed **/
    private $body;
    /** @var PathParameters **/
    private $parameters;
    /** @var QueryString **/
    private $query;
    /** @var User **/
    private $user;

    /**
     * @codeCoverageIgnore
     *
     * @param PathParameters $parameters
     * @param QueryString    $query
     * @param [type]         $body
     * @param User           $user
     */
    public function __construct(PathParameters $parameters, QueryString $query, $body, User $user)
    {
        $this->body       = $body;
        $this->parameters = $parameters;
        $this->query      = $query;
        $this->user       = $user;
    }

    /**
     * Returns the body.
     *
     * @return mixed
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * Returns the desired path parameter's value.
     *
     * @param string $name
     *
     * @throws Exception if the named parameter does not exist
     * @return string
     */
    public function pathParameter(string $name): string
    {
        return $this->parameters->valueFor($name);
    }

    /**
    * Returns the desired query parameter's value or the default one if the
    *   parameter is not part of the query string.
    *
    * @param string $name
    * @param string $default
    *
    * @return string
    */
    public function queryParameter(string $name, $default): string
    {
        return $this->query->valueForOr($name, $default);
    }

    /**
     * Returns the user.
     *
     * @return User
     */
    public function user(): User
    {
        return $this->user;
    }
}
