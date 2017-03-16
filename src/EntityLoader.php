<?php

namespace Arachne\EntityLoader;

use Arachne\EntityLoader\Exception\UnexpectedValueException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class EntityLoader
{
    /**
     * @var callable
     */
    private $filterInResolver;

    public function __construct(callable $filterInResolver)
    {
        $this->filterInResolver = $filterInResolver;
    }

    /**
     * @param string $type
     * @param mixed  $parameter
     *
     * @return mixed
     */
    public function filterIn(string $type, $parameter)
    {
        if ($this->isType($type, $parameter)) {
            return $parameter;
        }
        $value = $this->getFilter($type)->filterIn($parameter);
        if (!$this->isType($type, $value)) {
            throw new UnexpectedValueException("FilterIn did not return an instance of '$type'.");
        }

        return $value;
    }

    private function getFilter(string $type): FilterInInterface
    {
        $filter = call_user_func($this->filterInResolver, $type);
        if (!$filter) {
            throw new UnexpectedValueException("No filter in found for type '$type'.");
        }

        return $filter;
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return bool
     */
    private function isType(string $type, $value): bool
    {
        switch ($type) {
            case 'int':
                return is_int($value);
            case 'float':
                return is_float($value);
            case 'bool':
                return is_bool($value);
            case 'string':
                return is_string($value);
            case 'array':
                return is_array($value);
            case 'object':
                return is_object($value);
            case 'resource':
                return is_resource($value);
            case 'callable':
                return is_callable($value);
            case 'mixed':
                return true;
            default:
                return $value instanceof $type;
        }
    }
}
