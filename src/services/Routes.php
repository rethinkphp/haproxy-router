<?php

namespace rethink\hrouter\services;

use Illuminate\Database\Eloquent\Builder;
use rethink\hrouter\models\Route;
use rethink\hrouter\support\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class Routes
 *
 * @package rethink\hrouter\services
 */
class Routes extends ModelService
{
    public $modelClass = Route::class;

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection|Route[]
     */
    public function queryAll(array $params = [])
    {
        $query = Route::query();

        foreach ($params as $param => $value) {
            $query->where($param, $value);
        }

        return $query->get();
    }

    /**
     * @param $serviceId
     * @param $idOrName
     * @return Route
     */
    public function loadInService($serviceId, $idOrName)
    {
        $service = services()->loadOrFail($serviceId);

        $query = Route::query()->where('service_id', $service->id);

        $query->where(function (Builder $query) use ($idOrName) {
            $query
                ->orWhere('id', $idOrName)
                ->orWhere('name', $idOrName)
            ;
        });

        return $query->first();
    }


    public function loadInServiceOrFail($serviceId, $idOrName)
    {
        $route = $this->loadInService($serviceId, $idOrName);

        if (!$route) {
            throw new ModelNotFoundException('The requested resource does not found');
        }

        return $route;
    }

    /**
     * @param array $attributes
     * @return Route
     * @throws ValidationException
     */
    public function create(array $attributes)
    {
        $attributes['id'] = uniqid();

        $validator = validate($attributes, [
            'service_id' => 'required',
            'name' => 'required|unique:routes,name,0,id,service_id,' . $attributes['service_id'] ?? '',
        ]);

        $validator->setCustomMessages([
            'name.unique' => "The route '{$attributes['name']}' is already exists",
        ]);

        if ($validator->fails()) {
            throw ValidationException::fromValidator($validator);
        }

        $route = new Route();
        $route->fill($attributes);
        $route->save();

        $this->createDomainIfNeeded($route);

        return $route;
    }

    public function update($route, array $attributes)
    {
        $route = $this->loadOrFail($route);

        $route->fill($attributes);
        $route->save();

        $this->createDomainIfNeeded($route);

        return $route;
    }

    protected function createDomainIfNeeded(Route $route)
    {
        $domains = domains();

        if ($domains->has($route->host))  {
            return;
        }

        $domains->create([
            'name' => $route->host,
        ]);
    }

    public function delete($route)
    {
        $route = $this->loadOrFail($route);

        return $route->delete();
    }
}