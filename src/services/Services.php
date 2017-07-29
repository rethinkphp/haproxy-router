<?php

namespace rethink\hrouter\services;

use rethink\hrouter\models\Service;
use Illuminate\Database\Eloquent\Builder;
use rethink\hrouter\support\ValidationException;

/**
 * Class Services
 *
 * @package rethink\hrouter\services
 */
class Services extends ModelService
{
    public $modelClass = Service::class;

    public function load($id, array $options = [])
    {
        if ($id instanceof Service) {
            return $id;
        }

        return Service::query()
            ->where(function (Builder $query) use ($id) {
                $query
                    ->orWhere('id', $id)
                    ->orWhere('name', $id)
                ;
            })
            ->first();
    }

    public function queryAll(array $params = [])
    {
        return Service::query()->get();
    }

    /**
     * Create a new service.
     *
     * @param array $attributes
     * @return Service
     * @throws ValidationException
     */
    public function create(array $attributes = [])
    {
        $attributes['id'] = uniqid();

        $validator = validate($attributes, [
            'name' => 'required|unique:services',
        ]);

        if ($validator->fails()) {
            throw ValidationException::fromValidator($validator);
        }

        $service = new Service($attributes);
        $service->save();

        return $service;
    }

    public function update($service, array $attributes)
    {
        $service = $this->loadOrFail($service);

        $service->fill($attributes);
        $service->save();

        return $service;
    }

    public function delete($service)
    {
        $service = $this->loadOrFail($service);

        foreach ($service->nodes as $node) {
            $node->delete();
        }

        foreach ($service->routes as $route) {
            $route->delete();
        }

        return $service->delete();
    }

}