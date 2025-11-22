<?php

namespace LMS_Website\Models;

use LMS_Website\Containers\DB;

class BaseModel
{
    protected static $pdo;

    public function __construct()
    {
        self::$pdo = DB::connectToDatabase();
    }

    public static function prepareDatabase(): void
    {
        if (self::$pdo === null) {
            self::$pdo = DB::connectToDatabase();
        }
    }

    public static function fromArray(array $data): BaseModel
    {
        $class = get_called_class();
        $obj = new $class;

        $reflection = new \ReflectionClass($class);

        foreach ($data as $key => $value) {

            // Convert database column to property name
            $cleanKey = self::toPropertyName($key);

            // Skip if the property does not exist
            if (!$reflection->hasProperty($cleanKey)) {
                continue;
            }

            $property = $reflection->getProperty($cleanKey);
            $type = $property->getType();

            // If no declared type → assign normally
            if (!$type) {
                $obj->{$cleanKey} = $value;
                continue;
            }

            $typeName = $type->getName();

            // If the property is another model AND the value is array → hydrate it
            if (class_exists($typeName) && is_subclass_of($typeName, BaseModel::class) && is_array($value)) {
                $obj->{$cleanKey} = $typeName::fromArray($value);
                continue;
            }

            // Otherwise assign normally
            $obj->{$cleanKey} = $value;
        }

        return $obj;
    }


    private static function toPropertyName(string $key): string
    {
        // Example: last_name → lastName
        return preg_replace_callback('/_([a-z])/', function ($m) {
            return strtoupper($m[1]);
        }, $key);
    }

    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $props = $reflection->getProperties();

        $result = [];

        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $name  = $prop->getName();
            $value = $prop->getValue($this);
            if ($value instanceof \PDO || $name === 'table') {
                continue;
            }

            $result[$name] = $this->serializeValue($value);
        }

        return $result;
    }

    /**
     * Recursively serialize a value (scalar, object, array)
     */
    protected function serializeValue($value)
    {
        // Null
        if ($value === null) {
            return null;
        }

        // Scalar values
        if (is_scalar($value)) {
            return $value;
        }

        // DateTime → string
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        // Enum → value
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        // Model instance → use its toArray()
        if ($value instanceof BaseModel) {
            return $value->toArray();
        }

        // Arrays (possibly containing nested models)
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                $out[$k] = $this->serializeValue($v);
            }
            return $out;
        }

        // Fallback: convert to string
        return $value;
    }



}