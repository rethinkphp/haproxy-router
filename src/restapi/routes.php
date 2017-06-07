<?php
return [
    ['GET', '/', 'IndexController@sayHello'],

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

    ['POST', '/transactions', 'TransactionController@create'],
    ['PUT', '/transactions', 'TransactionController@update'],
];
