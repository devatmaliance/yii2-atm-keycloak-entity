<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\dto;

final class KeycloakUserCreationDTO
{
    private string $email;
    private string $username;
    private array $attributes;
    private array $credentials;
    private array $requiredActions;
    private bool $enabled;
    private bool $emailVerified;

    /**
     * @param string $email
     * @param string $username
     * @param array $attributes
     * @param array $credentials
     * @param array $requiredActions
     * @param bool $enabled
     * @param bool $emailVerified
     */
    public function __construct(
        string $email,
        string $username = '',
        array $attributes = [],
        array $credentials = [],
        array $requiredActions = [],
        bool $enabled = true,
        bool $emailVerified = true
    ) {
        $this->email = $email;
        $this->username = $username;
        $this->attributes = $attributes;
        $this->credentials = $credentials;
        $this->requiredActions = $requiredActions;
        $this->enabled = $enabled;
        $this->emailVerified = $emailVerified;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }

    /**
     * @return array
     */
    public function getRequiredActions(): array
    {
        return $this->requiredActions;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }
}