<?php

require 'init.php';

use monsieurluge\result\Error\Error;
use App\Services\BaseProvider as ServiceProvider;
use App\Services\Routing\RoutesGroup;
use App\Services\ServicesGroup;
use Config\Routing\ApiDelete as ApiDeleteRoutes;
use Config\Routing\ApiGet as ApiGetRoutes;
use Config\Routing\ApiPost as ApiPostRoutes;
use Config\Routing\PagesGet as PagesGetRoutes;
use Config\Routing\PagesPost as PagesPostRoutes;
use Config\Services\Database as DatabaseServices;
use Config\Services\Generic as GenericServices;
use Config\Services\Repository as RepositoryServices;
use Config\Services\Routing as RoutingServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// create the service provider
$serviceProvider = new ServiceProvider();

// declare the services to the service provider
array_map(
    function (ServicesGroup $services) use ($serviceProvider) { $services->addTo($serviceProvider); },
    [
        new DatabaseServices(),
        new GenericServices(),
        new RepositoryServices(),
        new RoutingServices(),
    ]
);

// create the router
$router = $serviceProvider->named('router');

// attach the routes groups to the router
array_map(
    function (RoutesGroup $routes) use ($router) { $routes->addTo($router); },
    [
        new ApiDeleteRoutes($serviceProvider),
        new ApiGetRoutes($serviceProvider),
        new ApiPostRoutes($serviceProvider),
        new PagesGetRoutes($serviceProvider),
        new PagesPostRoutes($serviceProvider),
    ]
);

// dispatch the request, then send the response to the client
$router
    ->dispatch(Request::createFromGlobals(), $serviceProvider->named('user from session'))
    ->getValueOrExecOnFailure(function (Error $error) { return new Response($error->message(), 422); })
    ->send();
