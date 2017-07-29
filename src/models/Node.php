<?php
namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Node
 *
 * @package rethink\hrouter\models
 */
class Node extends Model
{
    protected $table = 'nodes';
    protected $fillable = ['id', 'service_id', 'name', 'host', 'check', 'backup'];

    public $incrementing = false;
}