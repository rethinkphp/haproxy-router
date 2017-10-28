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
 * @property string|null $certificate2 The field is used when $tls_provider == manual
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
    protected $fillable = ['id', 'name', 'description', 'tls_only', 'tls_provider', 'key_pair', 'certificate', 'certificate2', 'dist_names'];
    protected $hidden = ['key_pair', 'certificate', 'dist_names'];
    protected $casts = [
        'tls_only' => 'bool',
        'dist_names' => 'array',
    ];

    public $incrementing = false;

    /**
     * @return \AcmePhp\Ssl\KeyPair
     */
    public function getKeyPair()
    {
        return acme()->deserializeKeyPair($this->key_pair);
    }

    public function hasCertificate()
    {
        return ($this->tls_provider == self::TLS_PROVIDER_ACME && $this->certificate)
            || ($this->tls_provider == self::TLS_PROVIDER_MANUAL && $this->certificate2)
        ;
    }

    public function isAcme()
    {
        return $this->tls_provider == self::TLS_PROVIDER_ACME;
    }

    public function isCertificateRequested()
    {
        return (boolean)$this->certificate;
    }
}