<?php

namespace atmaliance\yii2_keycloak_entity\models\entity;

class KeycloakClientSession extends BaseEntity
{
    private string $id;
    private string $username;
    private string $userId;
    private string $ipAddress;
    private string $start;
    private string $lastAccess;
    private bool $rememberMe;

    public function __construct(
        string $id,
        string $username,
        string $userId,
        string $ipAddress,
        string $start,
        string $lastAccess,
        bool $rememberMe
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->start = $start;
        $this->lastAccess = $lastAccess;
        $this->rememberMe = $rememberMe;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @return string
     */
    public function getLastAccess(): string
    {
        return $this->lastAccess;
    }

    /**
     * @return bool
     */
    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }
}