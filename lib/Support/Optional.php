<?php

namespace Lib\Support;

/**
 * Class Optional
 * 
 * Provides optional functionality for an object.
 */
class Optional
{
    /**
     * The object to be wrapped.
     * 
     * @var mixed The object to be wrapped.
     */
    protected $object;

    /**
     * Construct of the class.
     * 
     * @param mixed $object The object to be wrapped.
     **/
    public function __construct(mixed $object)
    {
        $this->object = $object;
    }

    /**
     * __get
     *
     * @param  mixed $property
     * @return void
     */
    public function __get(mixed $property)
    {
        if ($this->object === null) {
            return null;
        }

        if (is_callable([$this->object, $property])) {
            return $this->object->{$property}();
        }

        return $this->object->{$property} ?? null;
    }

    /**
     * __call
     *
     * @param  mixed $method
     * @param  mixed $arguments
     * @return void
     */
    public function __call(mixed $method, mixed $arguments)
    {
        if ($this->object === null) {
            return null;
        }

        if (is_callable([$this->object, $method])) {
            return call_user_func_array([$this->object, $method], $arguments);
        }

        return null;
    }
}
