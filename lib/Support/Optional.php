<?php

namespace Lib\Support;

class Optional
{
    /**
     * @var mixed The object to be wrapped.
     */
    protected $object;

    /**
     * Construct of the class.
     * 
     * @param mixed $object The object to be wrapped.
     **/
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * __get
     *
     * @param  mixed $property
     * @return void
     */
    public function __get($property)
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
    public function __call($method, $arguments)
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
