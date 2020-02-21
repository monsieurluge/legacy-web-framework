<?php

namespace App\Application\Command;

use App\Application\Command\IssueToArray;
use App\Application\Query\Issue;

/**
 * Helps to convert an Issue to JSON format.
 */
final class IssuesToJson
{
    /** @var Issue[] */
    private $issues;

    /**
     * @param Issue[] $issues
     */
    public function __construct(array $issues)
    {
        $this->issues = $issues;
    }

    /**
     * Returns the JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        $arrayResult = [];

        foreach ($this->issues as $issue) {
            $arrayResult[] = (new IssueToArray($issue))->toArray();
        }

        return json_encode($arrayResult);
    }
}
