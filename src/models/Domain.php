<?php

namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Domain
 *
 * @property string $id
 * @property string $name
 * @property string $description
 *
 * @property string|null $key_pair
 * @property string|null $certificate
 *
 * @property bool $tls_only
 * @property string $tls_provider
 * @property array $dist_names
 *
 * @package rethink\hrouter\models
 */
class Domain extends Model
{
    const TLS_PROVIDER_MANUAL = 'manual';
    const TLS_PROVIDER_ACME   = 'acme';

    protected $table = 'domains';
    protected $fillable = ['id', 'name', 'description', 'tls_only', 'tls_provider', 'key_pair', 'certificate', 'dist_names'];
    protected $casts = [
        'tls_only' => 'bool',
        'dist_names' => 'array',
    ];

    public $incrementing = false;
}