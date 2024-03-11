<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\entity;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\dto\KeycloakUserCreationDTO;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakUserException;
use atmaliance\yii2_keycloak_entity\models\finder\UserFinder;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;

final class User extends BaseEntity
{
    private string $id;
    private string $email;
    private string $username;
    private bool $enabled;
    private bool $emailVerified;
    private ?array $attributes;

    /**
     * @param string $id
     * @param string $email
     * @param string $username
     * @param bool $enabled
     * @param bool $emailVerified
     * @param array|null $attributes
     */
    public function __construct(
        string $id,
        string $email,
        string $username,
        bool   $enabled,
        bool   $emailVerified,
        ?array $attributes = null
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->enabled = $enabled;
        $this->emailVerified = $emailVerified;
        $this->attributes = $attributes;
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @param bool $canMergeWithExistingAttributes
     * @return $this
     */
    public function setAttributes(
        array $attributes,
        bool  $canMergeWithExistingAttributes = true
    ): self
    {
        $this->attributes = $canMergeWithExistingAttributes ? array_merge($this->attributes, $attributes) : $attributes;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * @param bool $emailVerified
     * @return $this
     */
    public function setEmailVerified(bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $response = KeycloakApi::getInstance()->getManager()->updateUser(array_filter((new Normalizer())->normalize($this)));

        if (isset($response['error'])) {
            throw new KeycloakUserException($response['error']);
        }
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $response = KeycloakApi::getInstance()->getManager()->deleteUser([
            'id' => $this->getId(),
        ]);

        if (isset($response['error'])) {
            throw new KeycloakUserException($response['error']);
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $response = KeycloakApi::getInstance()->getManager()->logoutUser([
            'id' => $this->getId(),
        ]);

        if (isset($response['error'])) {
            throw new KeycloakUserException($response['error']);
        }
    }

    /**
     * @param string $newPassword
     * @param bool $temporary
     * @return void
     */
    public function resetPassword(string $newPassword, bool $temporary = false): void
    {
        $response = KeycloakApi::getInstance()->getManager()->resetUserPassword([
            'id' => $this->getId(),
            'type' => 'password',
            'value' => $newPassword,
            'temporary' => $temporary,
        ]);

        if (isset($response['error'])) {
            throw new KeycloakUserException($response['error']);
        }
    }

    /**
     * @return UserFinder
     */
    public static function find(): UserFinder
    {
        return new UserFinder();
    }

    /**
     * @param KeycloakUserCreationDTO $keycloakUserCreationDTO
     * @return static
     */
    public static function create(KeycloakUserCreationDTO $keycloakUserCreationDTO): self
    {
        KeycloakApi::getInstance()->getManager()->createUser([
            'username' => $keycloakUserCreationDTO->getUsername(),
            'email' => $keycloakUserCreationDTO->getEmail(),
            'enabled' => $keycloakUserCreationDTO->isEnabled(),
            'emailVerified' => $keycloakUserCreationDTO->isEmailVerified(),
            'credentials' => $keycloakUserCreationDTO->getCredentials(),
            'attributes' => $keycloakUserCreationDTO->getAttributes(),
            'requiredActions' => $keycloakUserCreationDTO->getRequiredActions(),
        ]);

        return self::find()->whereEmail($keycloakUserCreationDTO->getEmail())->one();
    }
}