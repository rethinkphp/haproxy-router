<?php

namespace rethink\hrouter\entities;

use ArrayObject;

/**
 * Class BaseEntity
 *
 * @package rethink\hrouter\entities
 */
class BaseEntity extends ArrayObject
{
    public function defaults()
    {
        return [];
    }

    public function __construct(array $attributes = [])
    {
        $attributes += $this->defaults();

        parent::__construct($attributes, ArrayObject::ARRAY_AS_PROPS);
    }

    public function merge(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function fromArray(array $attributes)
    {
        return new static($attributes);
    }
}
