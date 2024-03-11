<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\entity;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakUserException;
use atmaliance\yii2_keycloak_entity\models\finder\ClientFinder;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use Throwable;
use Yii;

final class Client extends BaseEntity
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
     * @return ClientRole[]
     */
    public function getRoles(): array
    {
        return ClientRole::find()->whereClient($this)->all();
    }

    /**
     * @return ClientFinder
     */
    public static function find(): ClientFinder
    {
        return new ClientFinder();
    }

    /**
     * @param User $keycloakUser
     * @return ClientRole[]
     */
    public function getUserRoles(User $keycloakUser): array
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->getUserClientRoleMappings([
                'client' => $this->id,
                'id' => $keycloakUser->getId(),
            ]);

            if (isset($response['error'])) {
                throw new KeycloakUserException($response['error']);
            }

            return (new Normalizer())->denormalize($response, sprintf('%s[]', ClientRole::class));
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return [];
    }

    /**
     * @param User $keycloakUser
     * @param ClientRole[] $roles
     * @return bool
     */
    public function addRolesToUser(User $keycloakUser, array $roles): bool
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->addUserClientRoleMappings([
                'id' => $keycloakUser->getId(),
                'client' => $this->getId(),
                'roles' => array_map(static function (ClientRole $role) {
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
     * @param User $keycloakUser
     * @param ClientRole[] $roles
     * @return bool
     */
    public function removeRolesToUser(User $keycloakUser, array $roles): bool
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->deleteUserClientRoleMappings([
                'id' => $keycloakUser->getId(),
                'client' => $this->getId(),
                'roles' => array_map(static function (ClientRole $role) {
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