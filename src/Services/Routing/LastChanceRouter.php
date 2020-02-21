<?php

namespace App\Services\Routing;

use DateTime;
use Exception;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Routing\Route;
use App\Services\Request\CustomRequest\Page\CriticalError as CriticalErrorRequest;
use App\Services\Request\NoPathParameters;
use App\Services\Request\QueryString;
use App\Services\Routing\Router;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Last Chance Router (Router decorator).
 * Use it to catch any exception throwed when handling a HTTP request, and to display a pleasant error page.
 */
final class LastChanceRouter implements Router
{

    /** @var [type] **/
    private $http500Page;
    /** @var LoggerInterface **/
    private $logger;
    /** @var Router **/
    private $origin;

    /**
     * @codeCoverageIgnore
     * @param Router          $origin
     * @param [type]          $http500Page
     * @param LoggerInterface $logger
     */
    public function __construct(Router $origin, $http500Page, LoggerInterface $logger)
    {
        $this->http500Page = $http500Page;
        $this->logger      = $logger;
        $this->origin      = $origin;
    }

    /**
     * @inheritDoc
     */
    public function add(Route $route): Router
    {
        $this->origin->add($route);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(Request $request, User $user): Result
    {
        try {
            return $this->origin->dispatch($request, $user);
        } catch (Exception $exception) {
            $errorMessage = sprintf(
                '(%s) %s',
                (new DateTime())->getTimestamp(),
                $exception->getMessage()
            );

            $this->logger->log(
                LogLevel::ERROR,
                $errorMessage . PHP_EOL . print_r($exception->getTrace(), true)
            );

            return new Success(
                $this->http500Page->process(
                    new CriticalErrorRequest($errorMessage)
                )
            );
        }
    }

}
