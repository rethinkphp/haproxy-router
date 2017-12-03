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
 * @property array $check
 *  + type: http-check
 *  + args: ['GET', '/', 'HTTP/1.0', '']
 *  + expect: []
 *  + disable_on_404: true
 *
 * @package rethink\hrouter\models
 */
class Service extends Model
{
    protected $table = 'services';
    protected $fillable = ['id', 'name', 'description', 'check'];
    protected $casts = ['check' => 'object'];

    public $incrementing = false;

    public function getCheckLine()
    {
        $check = (array)$this->check;

        if ($check['type'] ?? '' == 'http') {
            $result = 'option httpchk';
        } else {
            return false;
        }

        $args = $check['args'] ?? [];

        if (isset($args[2])) {
            $args[2] = strtr($args[2], [' ' => '\ ']);
        }

        return $result . ' ' . implode(' ', $args);
    }

    public function nodes()
    {
        return $this->hasMany(Node::class, 'service_id', 'id');
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'service_id', 'id');
    }
}