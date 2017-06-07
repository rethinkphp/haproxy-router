<?php

namespace rethink\hrouter\entities;

/**
 * Class ServiceEntity
 *
 * @package rethink\hrouter\entities
 */
class ServiceEntity extends BaseEntity
{
    public function defaults()
    {
        return [
            'nodes' => [],
        ];
    }
}