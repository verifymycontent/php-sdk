<?php

namespace VerifyMyContent\SDK\Core;

use Exception;
use InvalidArgumentException;
use VerifyMyContent\SDK\Core\Validator\ValidationException;
use VerifyMyContent\SDK\Core\Validator\Validator;

abstract class DTO
{
    protected $fillable = [];
    protected $casts = [];
    protected $attributes = [];

    /**
     * @var array<Validator> $validate
     */
    protected $validate = [];

    /**
     * @throws ValidationException
     */
    public function __construct(array $data)
    {
        foreach ($this->fillable as $key) {
            $this->validateAttribute($key, $data[$key] ?? null);
            if (isset($data[$key])) {
                $this->attributes[$key] = $this->castAttribute($key, $data[$key]);
            }
        }
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        throw new InvalidArgumentException("Invalid property: " . $name);
    }

    public function __set($name, $value)
    {
        throw new Exception("Cannot set property: " . $name);
    }

    public function toArray(): array
    {
        $arr = [];
        foreach ($this->attributes as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $value = $value->toArray();
            }

            $arr[$key] = $value;
        }

        return $arr;
    }

    private function castAttribute($key, $value)
    {
        if (isset($this->casts[$key])) {
            $castTo = $this->casts[$key];

            if (is_a($value, $castTo, true)) {
                return $value;
            }

            return new $castTo($value);
        }

        return $value;
    }

    /**
     * @throws ValidationException
     */
    private function validateAttribute($key, $value)
    {
        $validator = $this->validate[$key] ?? null;
        if ($validator) {
            if (!is_array($validator)) {
                if (!is_string($validator)) {
                    throw new Exception("Invalid validator for {$key}");
                }

                $validator = [$validator];
            }

            foreach ($validator as $v) {
                $v::validate($value, $key);
            }
        }
    }
}
