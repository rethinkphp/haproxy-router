<?php
return [
    ['GET', '/', 'IndexController@sayHello'],

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

            ['PUT', '/stats', 'StatsController@update'],

            ['POST', '/transactions', 'TransactionController@create'],
            ['PUT', '/transactions', 'TransactionController@update'],

        ]
    ],
];
