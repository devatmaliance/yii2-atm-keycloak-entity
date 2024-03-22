<?php

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\Client;
use atmaliance\yii2_keycloak_entity\models\entity\ClientSession;
use atmaliance\yii2_keycloak_entity\models\exception\ClientSessionException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;

class ClientSessionFinder
{
    private Client $client;

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
     * @return ClientSession[]
     */
    public function all(int $max = 100): array
    {
        $response = KeycloakApi::getInstance()->getManager()->getClientSessions([
            'id' => $this->client->getId(),
            'max' => $max
        ]);

        if (isset($response['error'])) {
            throw new ClientSessionException($response['error']);
        }

        return (new Normalizer())->denormalize($response, sprintf('%s[]', ClientSession::class));
    }
}