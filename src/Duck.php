<?php
namespace AlexeyYashin\Ducktyping;

/**
 * Class Duck
 * @package AlexeyYashin\Ducktyping
 */
class Duck
{
    const STATIC = 1;
    const NON_STATIC = 2;

    protected $duck = null;
    protected $name = null;
    protected $methods = null;
    protected $properties = null;

    public function __construct($duck)
    {
        $this->duck = $duck;

        if (is_string($this->duck)) {
            $this->name = $this->duck;
        } elseif (is_object($this->duck)) {
            $this->name = get_class($this->duck);
        }
    }

    protected static function exists($name)
    {
        if ( ! $name) {
            return false;
        }

        return class_exists($name) || interface_exists($name);
    }

    protected function getMethods()
    {
        if ($this->methods === null) {
            $this->methods = static::getMethodsList($this->name);
        }

        return $this->methods;
    }

    protected static function getMethodsList($class)
    {
        $methods = [];

        if (static::exists($class)) {
            $reflected = new \ReflectionClass($class);

            foreach ($reflected->getMethods() as $method) {
                if ( ! $method->isPublic()) {
                    continue;
                }

                $methods[$method->name] = $method->isStatic() ? static::STATIC : static::NON_STATIC;
            }
        }

        return $methods;
    }

    protected function getProperties()
    {
        if ($this->properties === null) {
            $this->properties = static::getPropertiesList($this->name);
        }

        return $this->properties;
    }

    protected static function getPropertiesList($class)
    {
        $properties = [];

        if (static::exists($class)) {
            $reflected = new \ReflectionClass($class);

            foreach ($reflected->getProperties() as $property) {
                if ( ! $property->isPublic()) {
                    continue;
                }

                $properties[$property->name] = $property->isStatic() ? static::STATIC : static::NON_STATIC;
            }
        }

        return $properties;
    }

    /**
     * Checks implementation in duck-type way
     *
     * @param object|string $class_name
     *
     * @return bool `true` if duck instantiates/extends/implements $class_name or has all public methods and properties
     *              from $class_name (including "static" modifier coincidence)
     */
    public function implementing($class_name)
    {
        if (
            ! $this->duck
            || ! $class_name
            || ! (
                is_string($class_name)
                || is_object($class_name)
            )
        ) {
            return false;
        }

        if (is_object($class_name)) {
            $class_name = get_class($class_name);
        }

        if (is_a($this->duck, $class_name, true)) {
            return true;
        }

        if ( ! static::exists($class_name)) {
            return false;
        }

        return (
            ! array_diff_assoc(
                static::getMethodsList($class_name),
                $this->getMethods()
            )
            // todo me@alexey-yashin.ru. mb we don't need this
            /*
            && ! array_diff_assoc(
                static::getPropertiesList($class_name),
                $this->getProperties()
            )
            //*/
        );
    }

    /**
     * Checks method existence in current duck by method name, case-sensitive
     *
     * @param string $method_name
     * @param int    $check_static Check "static" modifier,  must be self::STATIC or self::NON_STATIC or `0`
     *
     * @return bool `true` if method exists and "static" modifiers coincided
     */
    public function hasMethod(string $method_name, int $check_static = 0)
    {
        if (array_key_exists($method_name, $this->getMethods())) {
            return $check_static === 0 || $this->methods[$method_name] === $check_static;
        }

        return false;
    }
}
