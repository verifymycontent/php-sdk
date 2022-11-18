<?php

namespace VerifyMyContent\SDK\Core;

use InvalidArgumentException;

abstract class DTO
{
    protected $fillable = [];
    protected $required = [];
    protected $casts = [];
    protected $attributes = [];

    public function __construct($data) {
        $fillable = array_merge($this->fillable, $this->required);
        foreach ($this->required as $required) {
            if (!isset($data[$required])) {
                throw new InvalidArgumentException("Missing required field: " . $required);
            }
        }

        foreach ($fillable as $key) {
            if (isset($data[$key])) {
                $value = $data[$key];
                if (isset($this->casts[$key])) {
                    if (!is_a($value, $this->casts[$key], true)) {
                        $value = new $this->casts[$key]($value);
                    }
                }

                $this->attributes[$key] = $value;
            }
        }
    }

    public function getAttributes(): array {
        return $this->attributes;
    }

    public function __get($name) {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        throw new InvalidArgumentException("Invalid property: " . $name);
    }

    public function __set($name, $value) {
        throw new \Exception("Cannot set property: " . $name);
    }

    public function toArray(): array {
        $arr = [];
        foreach ($this->attributes as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $value = $value->toArray();
            }

            $arr[$key] = $value;
        }

        return $arr;
    }
}
