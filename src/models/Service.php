<?php

namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property array $nodes
 * @property array $routes
 *
 * @package rethink\hrouter\models
 */
class Service extends Model
{
    protected $table = 'services';
    protected $fillable = ['id', 'name', 'description'];

    public $incrementing = false;

    public function nodes()
    {
        return $this->hasMany(Node::class, 'service_id', 'id');
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'service_id', 'id');
    }
}