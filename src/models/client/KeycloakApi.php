<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\client;

use atmaliance\yii2_keycloak_entity\KeycloakManagerConiguration;
use Keycloak\Admin\KeycloakClient;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;

final class KeycloakApi
{
    private KeycloakClient $manager;
    private static ?KeycloakApi $instance = null;

    /**
     * @throws InvalidConfigException
     */
    private function __construct()
    {
        /* @var KeycloakManagerConiguration $keycloakManager */
        $keycloakManager = Yii::$app->get('keycloakManagerConfiguration');

        if (!$keycloakManager) {
            throw new RuntimeException("Component «keycloakEntityManager» is not initialized");
        }

        $configuration = $keycloakManager->getConfiguration();
        $this->manager = KeycloakClient::factory([
            'grant_type' => 'client_credentials',
            'realm' => $configuration->getRealm(),
            'baseUri' => $configuration->getBaseUrl(),
            'client_id' => $configuration->getClientId(),
            'client_secret' => $configuration->getClientSecret(),
            'custom_operations' => [
                'getFilteredClientRoles' => [
                    'uri' => 'admin/realms/{realm}/clients/{id}/roles',
                    'description' => 'Get all roles for the realm or client (Client Specific)',
                    'httpMethod' => 'GET',
                    'parameters' => [
                        'realm' => [
                            'location' => 'uri',
                            'description' => 'realm name (not id!)',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'id' => [
                            'location' => 'uri',
                            'description' => 'id of client (not client-id)',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'first' => [
                            'location' => 'query',
                            'description' => 'Paging offset',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'max' => [
                            'location' => 'query',
                            'description' => 'Maximum results size (defaults to 100)',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'search' => [
                            'location' => 'query',
                            'description' => 'search string',
                            'type' => 'string',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return KeycloakApi
     */
    public static function getInstance(): KeycloakApi
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return KeycloakClient
     */
    public function getManager(): KeycloakClient
    {
        return $this->manager;
    }
}