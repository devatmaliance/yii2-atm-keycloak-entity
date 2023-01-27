<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\dto;

final class KeycloakEntityManagerConfigurationDTO
{
    private string $username;
    private string $password;
    private string $baseUrl;
    private string $clientId;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $realm
     * @param string $baseUrl
     */
    public function __construct(
        string $clientSecret,
        string $realm,
        string $baseUrl,
        string $clientId = 'admin-cli'
    ) {
        $this->username = $clientSecret;
        $this->password = $realm;
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}