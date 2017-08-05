<?php


namespace rethink\hrouter\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Challenge
 *
 * @property integer $id
 * @property string $domain
 * @property string $type
 * @property string $url
 * @property string $token
 * @property string $payload
 *
 * @package rethink\hrouter\models
 */
class Challenge extends Model
{
    protected $table = 'challenges';
    protected $fillable = ['domain', 'type', 'url', 'token', 'payload'];
}