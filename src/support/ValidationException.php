<?php

namespace rethink\hrouter\support;

use blink\core\Exception;
use blink\support\Json;
use blink\core\InvalidParamException;
use Illuminate\Validation\Validator;

/**
 * Class ValidationException
 *
 * @package chalk\base
 */
class ValidationException extends Exception
{
    public $errors;

    public function __construct($errors, $code = 0, $previous = null)
    {
        $this->errors = $errors;

        parent::__construct('Validation Error: ' . Json::encode($errors, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $code, $previous);
    }

    /**
     * Create a ValidationException from a Validator.
     *
     * @param Validator $validator
     * @return static
     */
    public static function fromValidator(Validator $validator)
    {
        $results = [];

        foreach ($validator->errors()->getMessages() as $filed => $errors) {
            foreach ($errors as $error) {
                $results[] = [
                    'field' => $filed,
                    'message' => $error,
                ];
            }
        }

        return new static($results);
    }

    /**
     * Create a ValidationException from the arguments list.
     *
     * @param $field
     * @param $error
     * @return static
     */
    public static function fromArgs($field, $error)
    {
        $args = func_get_args();

        if (count($args) % 2 !== 0) {
            throw new InvalidParamException('The total arguments must be multiples of 2');
        }

        $errors = [];

        foreach (array_chunk($args, 2) as list($key, $error)) {
            $error = static::normalizeError($error);
            $error['field'] = $key;

            $errors[] = $error;
        }

        return new static($errors);
    }

    protected static function normalizeError($error)
    {
        if (!is_array($error)) {
            $error = ['message' => (string)$error];
        }

        return $error;
    }

    /**
     * Create a ValidationException from raw array.
     *
     * @param array $errors
     * @return static
     */
    public static function fromArray(array $errors)
    {
        return new static($errors);
    }
}
