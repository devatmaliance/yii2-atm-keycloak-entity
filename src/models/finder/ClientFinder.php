<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\Client;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakClientFinderException;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakUserException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use Throwable;
use Yii;

final class ClientFinder
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
     * @return Client
     * Returns a client based on the client id parameter
     */
    public function one(): Client
    {
        if (empty($this->clientId)) {
            throw new KeycloakUserException("Not found clientId!");
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
    }

    /**
     * @return Client[]
     * Returns all clients as an array.
     */
    public function all(): array
    {
        $response = KeycloakApi::getInstance()->getManager()->getClients(
            array_filter([
                'clientId' => $this->clientId,
            ])
        );

        if (isset($response['error'])) {
            throw new KeycloakClientFinderException($response['error']);
        }

        return (new Normalizer())->denormalize($response, sprintf('%s[]', Client::class));

    }
}