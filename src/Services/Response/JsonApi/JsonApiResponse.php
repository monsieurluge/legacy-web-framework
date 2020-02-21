<?php

namespace App\Services\Response\JsonApi;

use App\Services\Response\JsonApi\Error as JsonApiError;
use Symfony\Component\HttpFoundation\Response;

final class JsonApiResponse extends Response
{
    /** @var mixed */
    private $data;
    /** @var Error[] **/
    private $errors;

    /**
     * @param mixed   $data
     * @param Error[] $errors
     * @param int     $status
     */
    public function __construct($data, array $errors = [], int $status = 200)
    {
        $this->data   = $data;
        $this->errors = $errors;

        parent::__construct('', $status);
    }

    /**
     * @inheritDoc
     */
    public function send()
    {
        $this->setJsonApiHeaders();

        $this->setContent( // TODO add meta data, if any
            json_encode([
                'data'   => $this->errors ? [] : $this->data,
                'errors' => $this->errorsToFlatArray()
            ])
        );

        return parent::send();
    }

    /**
     * Sets the right HTTP headers
     */
    private function setJsonApiHeaders()
    {
        $this->headers->set('Content-Type', 'application/vnd.api+json');
    }

    /**
     * Flatten the errors: each Error object is converted to an array.
     *
     * @return array
     */
    private function errorsToFlatArray(): array
    {
        return array_map(
            function(JsonApiError $error) { return $error->toArray(); },
            $this->errors
        );
    }

}
