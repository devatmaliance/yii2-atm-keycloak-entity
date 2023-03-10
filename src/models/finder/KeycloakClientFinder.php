<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\KeycloakClient;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakClientFinderException;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakUserException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use Throwable;
use Yii;

final class KeycloakClientFinder
{
    private ?string $clientId = null;

    /**
     * @param string $clientId
     * @return $this
     * Filter by clientId
     */
    public function whereClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return KeycloakClient|null
     * Returns a client based on the client id parameter
     */
    public function one(): ?KeycloakClient
    {
        try {
            if (empty($this->clientId)) {
                return null;
            }

            $keycloakClients = $this->all();

            if (count($keycloakClients) !== 1) {
                throw new KeycloakUserException("Number of found clients with clientId [{$this->clientId}] !== 1.");
            }

            $keycloakClient = current($keycloakClients);

            if ($keycloakClient->getClientId() !== $this->clientId) {
                throw new KeycloakUserException(
                    "Something is wrong. Specified clientId [{$this->clientId}] not equal keycloak clientId [{$keycloakClient->getClientId()}]"
                );
            }

            return $keycloakClient;
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return null;
    }

    /**
     * @return KeycloakClient[]
     * Returns all clients as an array.
     */
    public function all(): array
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->getClients(
                array_filter([
                    'clientId' => $this->clientId,
                ])
            );

            if (isset($response['error'])) {
                throw new KeycloakClientFinderException($response['error']);
            }

            return (new Normalizer())->denormalize($response, sprintf('%s[]', KeycloakClient::class));
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return [];
    }
}