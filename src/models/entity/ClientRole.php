<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\entity;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\dto\KeycloakRoleCreationDTO;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakClientRoleException;
use atmaliance\yii2_keycloak_entity\models\finder\ClientRoleFinder;
use Throwable;
use Yii;

final class ClientRole extends Role
{
    /**
     * @return void
     */
    public function update(): void
    {
        $response = KeycloakApi::getInstance()->getManager()->updateClientRole([
            'id' => $this->containerId,
            'role-name' => $this->getInitialProperty('name'),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        ]);

        if (isset($response['error'])) {
            throw new KeycloakClientRoleException($response['error']);
        }
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $response = KeycloakApi::getInstance()->getManager()->deleteClientRole([
            'id' => $this->getContainerId(),
            'role-name' => $this->getName(),
        ]);

        if (isset($response['error'])) {
            throw new KeycloakClientRoleException($response['error']);
        }

    }

    /**
     * @return ClientRoleFinder
     */
    public static function find(): ClientRoleFinder
    {
        return new ClientRoleFinder();
    }

    /**
     * @param KeycloakRoleCreationDTO $keycloakRoleCreationDTO
     * @return static
     */
    public static function create(KeycloakRoleCreationDTO $keycloakRoleCreationDTO): self
    {
        KeycloakApi::getInstance()->getManager()->createClientRole(
            array_filter([
                'id' => $keycloakRoleCreationDTO->getClient()->getId(),
                'name' => $keycloakRoleCreationDTO->getName(),
                'description' => $keycloakRoleCreationDTO->getDescription(),
            ])
        );

        return self::find()->whereRoleName($keycloakRoleCreationDTO->getName())->whereClient($keycloakRoleCreationDTO->getClient())->one();
    }
}