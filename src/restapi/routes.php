<?php
return [
    ['GET', '/', 'IndexController@sayHello'],

    ['GET', '/services', 'ServiceController@index'],
    ['POST', '/services', 'ServiceController@create'],
    ['GET', '/services/{id}', 'ServiceController@view'],
    ['PUT', '/services/{id}', 'ServiceController@update'],
    ['DELETE', '/services/{id}', 'ServiceController@delete'],

    ['GET', '/services/:id/servers', 'ServerController@index'],
    ['POST', '/services/:id/servers', 'ServerController@create'],
    ['GET', '/services/:id/servers/:serverId', 'ServerController@view'],
    ['PUT', '/services/:id/servers/:serverId', 'ServerController@update'],
    ['DELETE', '/services/:id/servers/:serverId', 'ServerController@delete'],

    ['POST', '/transactions', 'TransactionController@create'],
    ['PUT', '/transactions', 'TransactionController@update'],
];
