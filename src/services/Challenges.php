<?php

namespace rethink\hrouter\services;

use rethink\hrouter\models\Challenge;

/**
 * Class Challenges
 *
 * @package rethink\hrouter\services
 */
class Challenges extends ModelService
{
    public $modelClass = Challenge::class;

    /**
     * @param $token
     * @return Challenge|null
     */
    public function loadByToken(string $token)
    {
        return Challenge::query()->where('token', $token)->first();
    }

    /**
     * @param $domain
     * @return Challenge|null
     */
    public function loadByDomain(string $domain)
    {
        return Challenge::query()->where('domain', $domain)->first();
    }
}