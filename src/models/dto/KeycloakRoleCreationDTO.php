<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\dto;

use atmaliance\yii2_keycloak_entity\models\entity\Client;

final class KeycloakRoleCreationDTO
{
    private Client $client;
    private string $name;
    private ?string $description;

    /**
     * @param Client $client
     * @param string $name
     * @param string|null $description
     */
    public function __construct(Client $client, string $name, ?string $description = null)
    {
        $this->client = $client;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
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