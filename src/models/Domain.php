<?php

namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Domain
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string|null $certificate
 * @property bool $tls_only
 *
 * @package rethink\hrouter\models
 */
class Domain extends Model
{
    protected $table = 'domains';
    protected $fillable = ['id', 'name', 'description', 'tls_only', 'certificate'];
    protected $casts = [
        'tls_only' => 'bool',
    ];

    public $incrementing = false;
}