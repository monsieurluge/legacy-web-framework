<?php

namespace App\Application\Query;

use DateTime;
use App\Domain\Aggregate\Agent;
use App\Domain\Aggregate\Category;
use App\Domain\Aggregate\Context\Context;
use App\Domain\Aggregate\LifeCycle;
use App\Domain\Aggregate\Origin\Origin;
use App\Domain\Aggregate\Priority;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Office;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Type;

/**
 * Issue object.
 */
final class Issue
{
    /** @var Agent **/
    private $agent;
    /** @var Category **/
    private $category;
    /** @var Context **/
    private $context;
    /** @var DateTime **/
    private $created;
    /** @var ID **/
    private $identifier;
    /** @var LifeCycle **/
    private $lifeCycle;
    /** @var Priority **/
    private $priority;
    /** @var Office **/
    private $office;
    /** @var Origin **/
    private $origin;
    /** @var Status **/
    private $status;
    /** @var Title **/
    private $title;
    /** @var Type **/
    private $type;

    /**
     * @param ID        $identifier
     * @param Priority  $priority
     * @param Office    $office
     * @param Category  $category
     * @param Title     $title
     * @param DateTime  $created
     * @param Type      $type
     * @param Status    $status
     * @param Agent     $agent
     * @param Context   $context
     * @param Origin    $origin
     * @param LifeCycle $lifeCycle
     */
    public function __construct(
        ID $identifier,
        Priority $priority,
        Office $office,
        Category $category,
        Title $title,
        DateTime $created,
        Type $type,
        Status $status,
        Agent $agent,
        Context $context,
        Origin $origin,
        LifeCycle $lifeCycle
    ) {
        $this->agent      = $agent;
        $this->category   = $category;
        $this->context    = $context;
        $this->created    = $created;
        $this->identifier = $identifier;
        $this->lifeCycle  = $lifeCycle;
        $this->office     = $office;
        $this->origin     = $origin;
        $this->priority   = $priority;
        $this->status     = $status;
        $this->title      = $title;
        $this->type       = $type;
    }

    /**
     * Returns the agent who owns the issue.
     *
     * @return Agent
     */
    public function agent(): Agent
    {
        return $this->agent;
    }

    /**
     * Returns the category.
     *
     * @return Category
     */
    public function category(): Category
    {
        return $this->category;
    }

    /**
     * Returns the context.
     *
     * @return Context
     */
    public function context(): Context
    {
        return $this->context;
    }

    /**
     * Returns the creation date.
     *
     * @return DateTime
     */
    public function created(): DateTime
    {
        return $this->created;
    }

    /**
     * Returns the ID.
     *
     * @return ID
     */
    public function id(): ID
    {
        return $this->identifier;
    }

    /**
     * Returns the Issue life cycle.
     *
     * @return LifeCycle
     */
    public function lifeCycle(): LifeCycle
    {
        return $this->lifeCycle;
    }

    /**
     * Returns the office.
     *
     * @return Office
     */
    public function office(): Office
    {
        return $this->office;
    }

    /**
     * Returns the origin.
     *
     * @return Origin
     */
    public function origin(): Origin
    {
        return $this->origin;
    }

    /**
     * Returns the priority.
     *
     * @return Priority
     */
    public function priority(): Priority
    {
        return $this->priority;
    }

    /**
     * Returns the status.
     *
     * @return Status
     */
    public function status(): Status
    {
        return $this->status;
    }

    /**
     * Returns the title.
     *
     * @return Title
     */
    public function title(): Title
    {
        return $this->title;
    }

    /**
     * Returns the type.
     *
     * @return Type
     */
    public function type(): Type
    {
        return $this->type;
    }
}
