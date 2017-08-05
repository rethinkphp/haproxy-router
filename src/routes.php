<?php
return [
    // RESTFull API for HAProxy management
    [
        '/api/v1',
        [
            ['GET', '/services', 'ServiceController@index'],
            ['POST', '/services', 'ServiceController@create'],
            ['GET', '/services/{id}', 'ServiceController@view'],
            ['PUT', '/services/{id}', 'ServiceController@update'],
            ['DELETE', '/services/{id}', 'ServiceController@delete'],

            ['GET', '/services/{id}/nodes', 'NodeController@index'],
            ['POST', '/services/{id}/nodes', 'NodeController@create'],
            ['GET', '/services/{id}/nodes/{nodeId}', 'NodeController@view'],
            ['PUT', '/services/{id}/nodes/{nodeId}', 'NodeController@update'],
            ['DELETE', '/services/{id}/nodes/{nodeId}', 'NodeController@delete'],

            ['GET', '/services/{id}/routes', 'RouteController@index'],
            ['POST', '/services/{id}/routes', 'RouteController@create'],
            ['GET', '/services/{id}/routes/{routeId}', 'RouteController@view'],
            ['PUT', '/services/{id}/routes/{routeId}', 'RouteController@update'],
            ['DELETE', '/services/{id}/routes/{routeId}', 'RouteController@delete'],

            ['GET', '/domains', 'DomainController@index'],
            ['POST', '/domains', 'DomainController@create'],
            ['GET', '/domains/{id}', 'DomainController@view'],
            ['PUT', '/domains/{id}', 'DomainController@update'],
            ['DELETE', '/domains/{id}', 'DomainController@delete'],

            ['PUT', '/stats', 'StatsController@update'],

            ['POST', '/transactions', 'TransactionController@create'],
            ['PUT', '/transactions', 'TransactionController@update'],

        ]
    ],

    ['GET', '/.well-known/acme-challenge/{id}', 'IndexController@renderChallenge'],

    // Endpoints for frontend APPs
    ['GET', '/', 'IndexController@renderAssets'],
    ['GET', '/{p1}[/{p2}[/{p3}[/{p4}]]]', 'IndexController@renderAssets'],
];
