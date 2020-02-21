<?php

namespace App\Application\Command;

use App\Application\Query\Issue;

/**
 * Helps to convert an Issue to a flat array.
 */
final class IssueToArray
{
    /** @var Issue **/
    private $issue;

    /**
     * @param Issue $issue
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * Returns the Issue as a flat array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'category'      => $this->issue->category()->id()->value(),
            'categoryName'  => $this->issue->category()->name()->value(),
            'context'       => $this->issue->context()->identifier()->value(),
            'created'       => $this->issue->created()->format('Y-m-d H:i:s'),
            'id'            => $this->issue->id()->value(),
            'office'        => $this->issue->office()->value(),
            'owner'         => $this->issue->agent()->identifier()->value(),
            'ownerInitials' => $this->issue->agent()->initials()->value(),
            'priority'      => $this->issue->priority()->id()->value(),
            'priorityName'  => $this->issue->priority()->name()->value(),
            'status'        => $this->issue->status()->value(),
            'title'         => $this->issue->title()->value(),
            'type'          => $this->issue->type()->value()
        ];
    }
}
