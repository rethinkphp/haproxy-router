# RESTFul API

## Service Management API

Service is backend.

Service:

```
{
    name: 'rethinkphp', 
    mode: 'http',
    rewrites: [
        '/path/to/a' => '/path/to/b',
    ],
    fullconn: 1000,
    hosts: [
        {
            pattern: 'rethinkphp.com',
            protocol: 'https'
        } 
    ],
    routes: [
        'rethinkphp.com/users/.*',
        ...
    ],
}
```

### GET /services 

Get all services

### GET /services/:id

Get service by name

### POST /services

Create a service

### PUT /services/:id

Update a service

### DELETE /services/:id

Delete a service

## Route Management API ?

How?

## Server Management API

Server:

```
{
    service: 'passport',
    name: 'passport-1',
    check: true,
    backup: 1,
    ...
}
```

### GET /services/:id/servers

Get all servers of a service

### POST /services/:id/servers

Create a server

### GET /services/:id/servers/:sid

Get a server

### PUT /services/:id/servers/:sid

Update a server

### DELETE /services/:id/servers/:sid

Delete a server


## Transaction API

### POST /transactions

Create a transaction

### PUT /transactions/:id

Commit or cannel a transaction
