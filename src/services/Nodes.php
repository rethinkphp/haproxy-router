<?php

namespace rethink\hrouter\services;

use Illuminate\Database\Eloquent\Builder;
use rethink\hrouter\models\Node;
use rethink\hrouter\support\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class Nodes
 *
 * @package rethink\hrouter\services
 */
class Nodes extends ModelService
{
    public $modelClass = Node::class;

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection|Node[]
     */
    public function queryAll(array $params = [])
    {
        $query = Node::query();

        foreach ($params as $param => $value) {
            $query->where($param, $value);
        }

        return $query->get();
    }

    /**
     * @param $serviceId
     * @param $idOrName
     * @return Node
     */
    public function loadInService($serviceId, $idOrName)
    {
        $service = services()->loadOrFail($serviceId);

        $query = Node::query()->where('service_id', $service->id);

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
        $node = $this->loadInService($serviceId, $idOrName);

        if (!$node) {
            throw new ModelNotFoundException('The requested resource does not found');
        }

        return $node;
    }

    /**
     * @param array $attributes
     * @return Node
     * @throws ValidationException
     */
    public function create(array $attributes)
    {
        $attributes['id'] = uniqid();

        $validator = validate($attributes, [
            'service_id' => 'required',
            'name' => 'required|unique:nodes,name,0,id,service_id,' . $attributes['service_id'] ?? '',
        ]);

        $validator->setCustomMessages([
            'name.unique' => "The node '{$attributes['name']}' is already exists",
        ]);

        if ($validator->fails()) {
            throw ValidationException::fromValidator($validator);
        }

        $node = new Node();
        $node->fill($attributes);
        $node->save();

        return $node;
    }

    public function update($node, array $attributes)
    {
        $node = $this->loadOrFail($node);

        $node->fill($attributes);
        $node->save();

        return $node;
    }

    public function delete($node)
    {
        $node = $this->loadOrFail($node);

        return $node->delete();
    }
}