<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\dto;

use atmaliance\yii2_keycloak_entity\models\entity\KeycloakClient;

final class KeycloakRoleCreationDTO
{
    private KeycloakClient $client;
    private string $name;
    private ?string $description;

    /**
     * @param KeycloakClient $client
     * @param string $name
     * @param string|null $description
     */
    public function __construct(KeycloakClient $client, string $name, ?string $description = null)
    {
        $this->client = $client;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return KeycloakClient
     */
    public function getClient(): KeycloakClient
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}