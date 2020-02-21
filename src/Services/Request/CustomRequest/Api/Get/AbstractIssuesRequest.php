<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use DateInterval;
use DateTime;
use App\Services\Request\Request;

/**
 * Abstract Issues custom API HTTP request.
 */
abstract class AbstractIssuesRequest
{
    /**
     * Returns the native request.
     *
     * @return Request
     */
    abstract protected function request(): Request;

    /**
     * Returns the query filter value, if present.
     *
     * @param string $name
     * @param string $param
     *
     * @return array the filter as follows: [ name:string => value:string ]
     */
    protected function filterIfPresent(string $name, string $param): array
    {
        $value = $this->request()->queryParameter($param, '@nope@');

        return '@nope@' === $value || empty($value)
            ? []
            : [ $name => $value ];
    }

    /**
     * Returns the search dates, based on the following filters:
     *  - filtre_date1 -> createdStart
     *  - filtre_date2 -> createdEnd
     *
     * @return array the result as follows: [ 'createdStart' => value:string, 'createdEnd' => value:string ]
     */
    protected function searchDates(): array
    {
        $defaultDate1 = (new DateTime())->sub(new DateInterval('P10Y'))->format('Y-m-d'); // 10 years ago
        $defaultDate2 = (new DateTime())->add(new DateInterval('P1D'))->format('Y-m-d'); // tomorrow

        $createdStart = (new DateTime($this->queryParameterNotEmpty('filtre_date1', $defaultDate1)));
        $createdEnd = (new DateTime($this->queryParameterNotEmpty('filtre_date2', $defaultDate2)));

        return [
            'createdStart' => $createdStart->format('Y-m-d 00:00:00'),
            'createdEnd'   => $createdEnd->format('Y-m-d 23:59:59')
        ];
    }

    /**
     * Returns the named query parameter value, or a default one if not present or empty.
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    private function queryParameterNotEmpty(string $name, string $default): string
    {
        $value = $this->request()->queryParameter($name, $default);

        return empty($value)
            ? $default
            : $value;
    }
}
