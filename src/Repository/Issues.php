<?php

namespace App\Repository;

use PDO;
use DateTime;
use monsieurluge\optional\Optional\FromNullable;
use monsieurluge\result\Result\Result;
use App\Application\Query\Issue;
use App\Domain\Aggregate\Agent;
use App\Domain\Aggregate\Category;
use App\Domain\Aggregate\Context\BackOffice;
use App\Domain\Aggregate\Context\FrontOffice;
use App\Domain\Aggregate\Context\MiddleOffice;
use App\Domain\Aggregate\LifeCycle;
use App\Domain\Aggregate\Origin\File;
use App\Domain\Aggregate\Origin\Mail;
use App\Domain\Aggregate\Origin\Phone;
use App\Domain\Aggregate\Priority;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Firstname;
use App\Domain\ValueObject\Initials;
use App\Domain\ValueObject\Label;
use App\Domain\ValueObject\Lastname;
use App\Domain\ValueObject\Office;
use App\Domain\ValueObject\Status;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Type;
use App\Domain\ValueObject\SecondsFromCallback;
use App\Services\Database\Database;
use App\Services\Error\NoIssueFound;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;

/**
 * Issues Repository
 */
final class Issues
{
    /** @var string **/
    const ASC = 'asc';
    /** @var string **/
    const DESC = 'desc';

    /** @var Database **/
    private $dataSource;

    /**
     * @param Database $dataSource
     */
    public function __construct(Database $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Find the issues corresponding to the provided filters.
     * Available filters:
     *  - createdStart (DateTime, "date de création min", obligatoire)
     *  - createdEnd (DateTime, "date de création max", obligatoire)
     *  - createdBy (int, "id de l'agent qui a ouvert le ticket")
     *  - office (int, "id étude")
     *  - status (int)
     *  - category (int)
     *  - owner (int, "id de l'agent assigné à l'incident")
     *  - ssii (int)
     *  - read (bool, "ticket lu ou non lu")
     *  - type (int)
     *  - priority (int)
     *  - title (string)
     *  - origin (int)
     *  - context (int, "périmètre")
     *
     * @param array  $filters
     * @param int    $max
     * @param string $order
     * @param int    $start
     *
     * @return Issue[]
     */
    public function findBy(array $filters = [], int $max = 20, string $order = self::DESC, int $start = 0): array
    {
        $validFilters = $this->keepOnlyValidFilters($filters);

        $query = $this->commonSelectAndJoinSentence()
            . ' WHERE (date_ouverture BETWEEN :createdStart AND :createdEnd)'
            . $this->andWhere($validFilters, 'category', 'categorie')
            . $this->andWhereIn($validFilters, 'context', 'context_id')
            . $this->andWhere($validFilters, 'createdBy', 'id_createur')
            . $this->andWhere($validFilters, 'office', 'id_etude')
            . $this->andWhere($validFilters, 'origin', 'origine')
            . $this->andWhereOrIsNullWhen($validFilters, 'owner', 'id_assigne', '-1')
            . $this->andWhere($validFilters, 'priority', 'priorite')
            . $this->andWhere($validFilters, 'read', 'non_lu')
            . $this->andWhere($validFilters, 'ssii', 'id_ssii')
            . $this->andWhereIn($validFilters, 'status', 'statut')
            . $this->andWhere($validFilters, 'type', 'inc.type')
            . $this->andWhereOr([
                $this->whereContains($validFilters, 'title', 'titre'),
                $this->whereContains($validFilters, 'description', 'inc.description')
            ])
            . ' ORDER BY id ' . $order . ' LIMIT ' . $max . ' OFFSET ' . $start;

        unset($validFilters['status']); // TODO quick fix: remove the "andWhereIn" filter
        unset($validFilters['context']); // TODO quick fix: remove the "andWhereIn" filter

        $finalFilters = $this->removeOwnerIfUnassigned($validFilters);

        return array_map(
            function($dbIssue) { return $this->createIssue($dbIssue); },
            $this->dataSource->query($query, PDO::FETCH_ASSOC, $finalFilters)
        );
    }

    /**
     * Returns the corresponding issue if it exists.
     *
     * @param int $identifier
     *
     * @return Result a Result<Issue>
     */
    public function findById(int $identifier): Result
    {
        $query = $this->commonSelectAndJoinSentence() . ' WHERE inc.id=:identifier';

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC, [ 'identifier' => $identifier ]);

        return empty($result)
            ? new Failure(
                new NoIssueFound(),
                sprintf('the issue #%s does not exist', $identifier)
            )
            : new Success(
                $this->createIssue($result[0])
            );
    }

    /**
     * Returns the total "pause time", in seconds.
     *
     * @param ID $identifier the target Issue ID
     *
     * @return int
     */
    public function pauseTime(ID $identifier): int
    {
        $query = 'select * from incident_temps where incident_id = :identifier order by id asc';

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC, [ 'identifier' => $identifier->value() ]);

        if (count($result) <= 1) {
            return 0;
        }

        $total = 0;

        for ($index = 1, $leni = count($result); $index < $leni; $index++) {
            $result1 = $result[$index - 1];
            $result2 = $result[$index];

            if (isset($result1['arret']) && $result1['arret'] == 0) {
                $total1 = date_mysql_to_timestamp($result1['fin']);
                $total2 = date_mysql_to_timestamp($result2['debut']);

                $total += $total2 - $total1;
            }
        }

        return $total;
    }

    /**
     * Returns the total "read time", in seconds.
     *
     * @param ID $identifier the target Issue ID
     *
     * @return int
     */
    public function readTime(ID $identifier): int
    {
        $query = 'select SUM(TIMESTAMPDIFF(SECOND, debut, IFNULL(fin, now()))) total from incident_temps where incident_id = :identifier';

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC, [ 'identifier' => $identifier->value() ]);

        return intval($result[0]['total']);
    }

    /**
     * Returns the total issues.
     *
     * @return int
     */
    public function total(): int
    {
        $query = 'SELECT COUNT(id) as total FROM incident';

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC);

        return intval($result[0]['total']);
    }

    /**
     * Returns the total issues which matches the provided filters.
     *
     * @param array $filters
     *
     * @return int
     */
    public function totalBy(array $filters = []): int
    {
        $validFilters = $this->keepOnlyValidFilters($filters);

        $query = 'SELECT COUNT(inc.id) as total'
            . ' FROM incident inc'
            . ' LEFT JOIN priorite prio ON inc.priorite = prio.id'
            . ' LEFT JOIN categorie cat ON inc.categorie = cat.id'
            . ' LEFT JOIN categorie cat_parent ON cat.parent = cat_parent.id'
            . ' LEFT JOIN utilisateur owner ON owner.id = inc.id_assigne'
            . ' WHERE (date_ouverture BETWEEN :createdStart AND :createdEnd)'
            . $this->andWhere($validFilters, 'category', 'categorie')
            . $this->andWhereIn($validFilters, 'context', 'context_id')
            . $this->andWhere($validFilters, 'createdBy', 'id_createur')
            . $this->andWhere($validFilters, 'office', 'id_etude')
            . $this->andWhere($validFilters, 'origin', 'origine')
            . $this->andWhereOrIsNullWhen($validFilters, 'owner', 'id_assigne', '-1')
            . $this->andWhere($validFilters, 'priority', 'priorite')
            . $this->andWhere($validFilters, 'read', 'non_lu')
            . $this->andWhere($validFilters, 'ssii', 'id_ssii')
            . $this->andWhereIn($validFilters, 'status', 'statut')
            . $this->andWhere($validFilters, 'type', 'inc.type')
            . $this->andWhereOr([
                $this->whereContains($validFilters, 'title', 'titre'),
                $this->whereContains($validFilters, 'description', 'inc.description')
            ]);

        unset($validFilters['status']); // TODO quick fix: remove the "andWhereIn" filter
        unset($validFilters['context']); // TODO quick fix: remove the "andWhereIn" filter

        $finalFilters = $this->removeOwnerIfUnassigned($validFilters);

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC, $finalFilters);

        return intval($result[0]['total']);
    }

    /**
     * Returns the common "findBy" select & join sentence.
     *
     * @return string
     */
    private function commonSelectAndJoinSentence(): string
    {
        return 'SELECT inc.categorie category, cat.nom category_label, cat_parent.nom category_parent_label, inc.date_fermeture closedDate, inc.context_id context, inc.id_createur createdBy, inc.date_ouverture createdDate, inc.id id, inc.non_lu isRead, inc.id_etude office, inc.origine origin, inc.id_assigne ownerId, owner.initiale ownerInitials, inc.priorite priority, prio.libelle priority_label, inc.statut status, inc.titre title, inc.type type'
            . ' FROM incident inc'
            . ' LEFT JOIN priorite prio ON inc.priorite = prio.id'
            . ' LEFT JOIN categorie cat ON inc.categorie = cat.id'
            . ' LEFT JOIN categorie cat_parent ON cat.parent = cat_parent.id'
            . ' LEFT JOIN utilisateur owner ON owner.id = inc.id_assigne';
    }

    /**
     * Creates and returns an Issue DTO, using a database Issue hash
     *
     * @param array $dbIssue the hash as follows: [id, priority, office, category, category_label, category_parent_label, title, createdDate, type, status, ownerId, context]
     *
     * @return Issue
     */
    private function createIssue(array $dbIssue): Issue
    {
        switch (intval($dbIssue['context'])) {
            case 1:
                $context = new FrontOffice();
                break;
            case 2:
                $context = new MiddleOffice();
                break;
            case 3:
                $context = new BackOffice();
                break;
            default:
                $context = new FrontOffice();
                break;
        }

        switch ($dbIssue['origin']) {
            case 'tel':
                $origin = new Phone();
                break;
            case 'email':
                $origin = new Mail();
                break;
            case 'fichier':
                $origin = new File();
                break;
            default:
                $origin = new Phone();
                break;
        }

        return new Issue(
            new ID($dbIssue['id']),
            new Priority(new ID($dbIssue['priority']), new Label($dbIssue['priority_label'])),
            new Office($dbIssue['office'] ? $dbIssue['office'] : ''),
            Category::for($dbIssue['category'], $dbIssue['category_parent_label'], $dbIssue['category_label']),
            new Title($dbIssue['title'] ? $dbIssue['title'] : ''),
            new DateTime($dbIssue['createdDate']),
            new Type($dbIssue['type'] ? $dbIssue['type'] : ''),
            new Status($dbIssue['status'] ? $dbIssue['status'] : ''),
            is_null($dbIssue['ownerId'])
                ? new Agent(new ID(0), new Firstname('inconnu'), new Lastname('inconnu'), new Initials('--'))
                : new Agent(new ID(intval($dbIssue['ownerId'])), new Firstname('---'), new Lastname('---'), new Initials($dbIssue['ownerInitials'])),
            $context,
            $origin,
            new LifeCycle(
                new DateTime($dbIssue['createdDate']),
                (new FromNullable($dbIssue['closedDate']))->map(function ($closedDate) { return new DateTime($closedDate); }),
                new SecondsFromCallback(function () use ($dbIssue) { return $this->readTime(new ID($dbIssue['id'])); }),
                new SecondsFromCallback(function () use ($dbIssue) { return $this->pauseTime(new ID($dbIssue['id'])); })
            )
        );
    }

    /**
     * Returns a "AND x=y" sentence.
     *
     * @param array  $filter
     * @param string $param
     * @param string $dbColName
     *
     * @return string
     */
    private function andWhere(array $filter, string $param, string $dbColName): string
    {
        if (false === isset($filter[$param])) {
            return '';
        }

        return sprintf(
            ' AND %s=:%s',
            $dbColName,
            $param
        );
    }

    /**
     * Returns a "AND INSTR(x, y)" sentence.
     *
     * @param array  $filter
     * @param string $param
     * @param string $dbColName
     *
     * @return string
     */
    private function andWhereContains(array $filter, string $param, string $dbColName): string
    {
        if (false === isset($filter[$param])) {
            return '';
        }

        return sprintf(
            ' AND INSTR(%s, :%s) > 0',
            $dbColName,
            $param
        );
    }

    /**
     * Returns a "AND x IN (y, z)" sentence.
     *
     * @param array  $filter
     * @param string $param
     * @param string $dbColName
     *
     * @return string
     */
    private function andWhereIn(array $filter, string $param, string $dbColName): string
    {
        if (false === isset($filter[$param])) {
            return '';
        }

        $values = array_map(
            function($value) { return '"' . $value . '"'; },
            explode(',', $filter[$param])
        );

        return sprintf(
            ' AND %s IN (%s)',
            $dbColName,
            implode(',', $values)
        );
    }

    /**
     * Returns a "AND x=y" sentence or "AND x IS NULL" if the condition is met.
     *
     * @param array  $filter
     * @param string $param
     * @param string $dbColName
     * @param string $condition
     *
     * @return string
     */
    private function andWhereOrIsNullWhen(array $filter, string $param, string $dbColName, string $condition): string
    {
        if (isset($filter[$param]) && $condition === $filter[$param]) {
            return sprintf(
                ' AND %s IS NULL',
                $dbColName
            );
        }

        return $this->andWhere($filter, $param, $dbColName);
    }

    /**
     * Returns the filters which can be use in the DB query.
     *
     * @param array $filters
     *
     * @return array
     */
    private function keepOnlyValidFilters(array $filters = [])
    {
        $valid = [
            'category',
            'context',
            'createdBy',
            'createdEnd',
            'createdStart',
            'description',
            'office',
            'origin',
            'owner',
            'priority',
            'read',
            'ssii',
            'status',
            'title',
            'type'
        ];

        return array_filter(
            $filters,
            function($name) use ($valid) { return in_array($name, $valid); },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Returns a "AND (x OR y)" sentence where x, y are other query expressions/sentences.
     *
     * @param array $expressions
     *
     * @return string
     */
    private function andWhereOr(array $expressions): string
    {
        // remove the empty expressions
        $filteredExpressions = array_filter(
            $expressions,
            function (string $expression) { return false === empty($expression); }
        );

        if (0 === count($filteredExpressions)) {
            return '';
        }

        return sprintf(
            ' AND (%s)',
            implode(' OR ', $filteredExpressions)
        );
    }

    /**
     * Remove the 'owner' filter if its value is "ALL" (-1)
     *
     * @param array $filters
     *
     * @return array
     */
    private function removeOwnerIfUnassigned(array $filters): array
    {
        return array_filter(
            $filters,
            function($value, $key) { return !($key === 'owner' && '-1' === $value); },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Returns a "INSTR (x, y) > 0" sentence.
     *
     * @param array  $filter
     * @param string $param
     * @param string $dbColName
     *
     * @return string
     */
    private function whereContains(array $filter, string $param, string $dbColName): string
    {
        if (false === isset($filter[$param])) {
            return '';
        }

        return sprintf(
            ' INSTR(%s, :%s) > 0',
            $dbColName,
            $param
        );
    }
}
