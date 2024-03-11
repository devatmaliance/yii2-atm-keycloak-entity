<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\entity;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakUserException;
use atmaliance\yii2_keycloak_entity\models\finder\KeycloakClientFinder;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use Throwable;
use Yii;

final class KeycloakClient extends BaseEntity
{
    private string $id;
    private string $clientId;

    /**
     * @param string $id
     * @param string $clientId
     */
    public function __construct(string $id, string $clientId)
    {
        $this->id = $id;
        $this->clientId = $clientId;
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
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return KeycloakClientRole[]
     */
    public function getRoles(): array
    {
        return KeycloakClientRole::find()->whereClient($this)->all();
    }

    /**
     * @return KeycloakClientFinder
     */
    public static function find(): KeycloakClientFinder
    {
        return new KeycloakClientFinder();
    }

    /**
     * @param KeycloakUser $keycloakUser
     * @return KeycloakClientRole[]
     */
    public function getUserRoles(KeycloakUser $keycloakUser): array
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->getUserClientRoleMappings([
                'client' => $this->id,
                'id' => $keycloakUser->getId(),
            ]);

            if (isset($response['error'])) {
                throw new KeycloakUserException($response['error']);
            }

            return (new Normalizer())->denormalize($response, sprintf('%s[]', KeycloakClientRole::class));
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return [];
    }

    /**
     * @param KeycloakUser $keycloakUser
     * @param KeycloakClientRole[] $roles
     * @return bool
     */
    public function addRolesToUser(KeycloakUser $keycloakUser, array $roles): bool
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->addUserClientRoleMappings([
                'id' => $keycloakUser->getId(),
                'client' => $this->getId(),
                'roles' => array_map(static function (KeycloakClientRole $role) {
                    return [
                        'id' => $role->getId(),
                        'name' => $role->getName(),
                        'description' => $role->getDescription(),
                        'containerId' => $role->getContainerId(),
                        'clientRole' => $role->isClientRole(),
                    ];
                }, $roles),
            ]);

            if (isset($response['error'])) {
                throw new KeycloakUserException($response['error']);
            }

            return true;
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return false;
    }

    /**
     * @param KeycloakUser $keycloakUser
     * @param KeycloakClientRole[] $roles
     * @return bool
     */
    public function removeRolesToUser(KeycloakUser $keycloakUser, array $roles): bool
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->deleteUserClientRoleMappings([
                'id' => $keycloakUser->getId(),
                'client' => $this->getId(),
                'roles' => array_map(static function (KeycloakClientRole $role) {
                    return [
                        'id' => $role->getId(),
                        'name' => $role->getName(),
                        'description' => $role->getDescription(),
                        'containerId' => $role->getContainerId(),
                        'clientRole' => $role->isClientRole(),
                    ];
                }, $roles),
            ]);

            if (isset($response['error'])) {
                throw new KeycloakUserException($response['error']);
            }

            return true;
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return false;
    }
}