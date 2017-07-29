<?php

namespace rethink\hrouter\services;


use blink\core\Object;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ModelService
 *
 * @package rethink\hrouter\services
 */
class ModelService extends Object
{
    public $modelClass;

    protected function loadModel($modelClass, $id, array $options = [])
    {
        if (!$id instanceof $modelClass) {
            $id = $modelClass::query()->find($id);
        }

        return $id;
    }

    protected function loadModelOrFail($modelClass, $id, array $options = [])
    {
        $model = $this->loadModel($modelClass, $id, $options);

        if (!$model) {
            throw new ModelNotFoundException('The requested resource does not found');
        }

        return $model;
    }

    public function load($id, array $options = [])
    {
        return $this->loadModel($this->modelClass, $id, $options);
    }

    public function loadOrFail($id, array $options = [])
    {
        $model = $this->load($id, $options);

        if (!$model) {
            throw new ModelNotFoundException('The requested resource does not found');
        }

        return $model;
    }
}
