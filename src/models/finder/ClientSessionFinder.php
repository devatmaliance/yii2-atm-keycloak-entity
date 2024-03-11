<?php

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\KeycloakClient;
use atmaliance\yii2_keycloak_entity\models\entity\ClientSession;
use atmaliance\yii2_keycloak_entity\models\exception\ClientSessionException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;

class ClientSessionFinder
{
    private KeycloakClient $client;

    /**
     * @param KeycloakClient $keycloakClient
     * @return $this
     * Client
     */
    public function whereClient(KeycloakClient $keycloakClient): self
    {
        $this->client = $keycloakClient;

        return $this;
    }

    /**
     * @return ClientSession[]
     */
    public function all(): array
    {
        $response = KeycloakApi::getInstance()->getManager()->getClientSessions([
            'id' => $this->client->getId(),
        ]);

        if (isset($response['error'])) {
            throw new ClientSessionException($response['error']);
        }

        return (new Normalizer())->denormalize($response, sprintf('%s[]', ClientSession::class));
    }
}