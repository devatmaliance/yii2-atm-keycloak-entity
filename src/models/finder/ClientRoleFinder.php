<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\Client;
use atmaliance\yii2_keycloak_entity\models\entity\ClientRole;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakClientRoleFinderException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use Throwable;
use Yii;

final class ClientRoleFinder
{
    private ?Client $client = null;
    private ?int $offset = null;
    private ?int $limit = null;
    private ?string $uuid = null;
    private ?string $roleName = null;

    /**
     * @param int $offset
     * @return $this
     * Pagination offset
     */
    public function whereOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     * Maximum results size (defaults to 20)
     */
    public function whereLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string $uuid
     * @return $this
     * User UUID
     */
    public function whereUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @param string $roleName
     * @return $this
     * Client role name
     */
    public function whereRoleName(string $roleName): self
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * @param Client $keycloakClient
     * @return $this
     * Client
     */
    public function whereClient(Client $keycloakClient): self
    {
        $this->client = $keycloakClient;

        return $this;
    }

    /**
     * @return ClientRole|null
     * Returns a specific client role based on uuid or role name
     */
    public function one(): ?ClientRole
    {
        try {
            $response = null;

            if (!empty($this->uuid)) {
                $response = KeycloakApi::getInstance()->getManager()->getRealmRoleById([
                    'role-id' => $this->uuid,
                ]);
            } elseif (!empty($this->roleName)) {
                $response = KeycloakApi::getInstance()->getManager()->getClientRole([
                    'id' => $this->client->getId(),
                    'role-name' => $this->roleName,
                ]);
            }

            if (null === $response) {
                return null;
            }

            if (isset($response['error'])) {
                throw new KeycloakClientRoleFinderException($response['error']);
            }

            /* @var ClientRole $keycloakClientRole */
            $keycloakClientRole = (new Normalizer())->denormalize($response, ClientRole::class);

            if (!$keycloakClientRole->isClientRole()) {
                throw new KeycloakClientRoleFinderException("Something is wrong. Found role is not a client role. UUID [$this->uuid]");
            }

            return $keycloakClientRole;
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return null;
    }

    /**
     * @return ClientRole[]
     * Returns all client roles as an array.
     */
    public function all(): array
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->getFilteredClientRoles(
                array_filter([
                    'id' => $this->client->getId(),
                    'first' => $this->offset,
                    'max' => $this->limit,
                    'search' => $this->roleName,
                ])
            );

            if (isset($response['error'])) {
                throw new KeycloakClientRoleFinderException($response['error']);
            }

            return (new Normalizer())->denormalize($response, sprintf('%s[]', ClientRole::class));
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return [];
    }
}