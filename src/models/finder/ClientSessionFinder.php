<?php

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\KeycloakClient;
use atmaliance\yii2_keycloak_entity\models\entity\ClientSession;
use atmaliance\yii2_keycloak_entity\models\exception\ClientSessionException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use DateTime;
use DateTimeZone;

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
     * @param DateTime $startDateTime
     * @param DateTime $endDateTime
     * @return array
     * @throws ClientSessionException
     */
    public function whereBetweenStart(DateTime $startDateTime, DateTime $endDateTime): array
    {
        $filteredSessions = [];

        foreach ($this->all() as $session) {
            $startTime = $session->getStart() / 1000;  // переводим миллисекунды в секунды
            $startTime = DateTime::createFromFormat('U', (string)$startTime);
            $startTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

            if ($startTime >= $startDateTime && $startTime <= $endDateTime) {
                $filteredSessions[] = $session;
            }
        }

        return $filteredSessions;
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