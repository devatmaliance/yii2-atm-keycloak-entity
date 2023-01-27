<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\entity;

use ReflectionClass;

class BaseEntity
{
    protected array $initialProperties = [];

    public function __construct()
    {
        $this->initialProperties = $this->getProperties();
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        $properties = [];

        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            $property->setAccessible(true);
            $properties[$property->getName()] = $property->getValue($this);
        }

        if (isset($properties['initialProperties'])) {
            unset($properties['initialProperties']);
        }

        return $properties;
    }

    /**
     * @return array
     */
    public function getInitialProperties(): array
    {
        return $this->initialProperties;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getInitialProperty(string $name)
    {
        return $this->initialProperties[$name] ?? null;
    }
}