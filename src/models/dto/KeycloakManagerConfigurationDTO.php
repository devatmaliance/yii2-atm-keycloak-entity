<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\dto;

final class KeycloakManagerConfigurationDTO
{
    private string $realm;
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;

    /**
     * @param string $realm
     * @param string $baseUrl
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(
        string $realm,
        string $baseUrl,
        string $clientId,
        string $clientSecret
    ) {
        $this->realm = $realm;
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
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
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}