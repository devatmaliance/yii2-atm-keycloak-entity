<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\dto;

final class KeycloakEntityManagerConfigurationDTO
{
    private string $realm;
    private string $baseUrl;
    private string $username;
    private string $password;

    /**
     * @param string $realm
     * @param string $baseUrl
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $realm,
        string $baseUrl,
        string $username,
        string $password
    ) {
        $this->realm = $realm;
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRealm(): string
    {
        return $this->realm;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
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
}