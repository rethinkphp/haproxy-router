<?php

namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Route
 *
 * @property string $id
 * @property string $name
 * @property string $host
 * @property string $path
 *
 * @package rethink\hrouter\models
 */
class Route extends Model
{
    protected $table = 'routes';
    protected $fillable = ['id', 'service_id', 'name', 'host', 'path'];

    public $incrementing = false;

    public function domain()
    {
        return $this->hasOne(Domain::class, 'name', 'host');
    }
}