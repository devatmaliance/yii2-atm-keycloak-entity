<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\entity;

class KeycloakRole extends BaseEntity
{
    protected string $id;
    protected string $name;
    protected string $containerId;
    protected ?string $description;
    protected bool $clientRole;

    /**
     * @param string $id
     * @param string $containerId
     * @param string $name
     * @param bool $clientRole
     * @param string|null $description
     */
    public function __construct(
        string $id,
        string $containerId,
        string $name,
        bool $clientRole,
        ?string $description = null
    ) {
        $this->id = $id;
        $this->containerId = $containerId;
        $this->name = $name;
        $this->clientRole = $clientRole;
        $this->description = $description;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getContainerId(): string
    {
        return $this->containerId;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function isClientRole(): bool
    {
        return $this->clientRole;
    }
}