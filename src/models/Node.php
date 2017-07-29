<?php
namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Node
 *
 * @property $id
 * @property $service_id
 * @property string $name
 * @property string $host
 * @property string $check
 * @property string $backup
 *
 * @package rethink\hrouter\models
 */
class Node extends Model
{
    protected $table = 'nodes';
    protected $fillable = ['id', 'service_id', 'name', 'host', 'check', 'backup'];
    protected $casts = [
        'check' => 'bool',
        'backup' => 'bool',
    ];

    public $incrementing = false;
}