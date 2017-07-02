<?php

namespace rethink\hrouter\entities;

use ArrayObject;
use blink\core\Configurable;
use blink\core\ObjectTrait;

/**
 * Class BaseEntity
 *
 * @package rethink\hrouter\entities
 */
class BaseEntity extends ArrayObject implements Configurable
{
    use ObjectTrait {
        __construct as objectConstruct;
    }

    public function defaults()
    {
        return [];
    }

    public function __construct(array $attributes = [], $config = [])
    {
        $attributes += $this->defaults();

        parent::__construct($attributes, ArrayObject::ARRAY_AS_PROPS);

        $this->objectConstruct($config);
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
