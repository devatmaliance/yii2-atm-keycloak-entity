<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\client;

use atmaliance\yii2_keycloak_entity\KeycloakEntityManager;
use Keycloak\Admin\KeycloakClient;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;

final class KeycloakApi
{
    private KeycloakClient $manager;
    private static array $instances = [];

    /**
     * @throws InvalidConfigException
     */
    private function __construct()
    {
        /* @var KeycloakEntityManager $keycloakEntityManager */
        $keycloakEntityManager = Yii::$app->get('keycloakEntityManager');

        if (null === $keycloakEntityManager) {
            throw new RuntimeException("Component «keycloakEntityManager» is not initialized");
        }

        $this->manager = KeycloakClient::factory([
            'username' => $keycloakEntityManager->getConfiguration()->getUsername(),
            'password' => $keycloakEntityManager->getConfiguration()->getPassword(),
            'client_id' => $keycloakEntityManager->getConfiguration()->getClientId(),
            'baseUri' => $keycloakEntityManager->getConfiguration()->getBaseUrl(),
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

    /**
     * @return mixed
     * @throws RuntimeException
     */
    public function __wakeup()
    {
        throw new RuntimeException("Cannot unserialize a singleton.");
    }

    /**
     * @return KeycloakApi
     */
    public static function getInstance(): KeycloakApi
    {
        $currentClass = self::class;

        if (!isset(self::$instances[$currentClass])) {
            self::$instances[$currentClass] = new self();
        }

        return self::$instances[$currentClass];
    }

    /**
     * @return KeycloakClient
     */
    public function getManager(): KeycloakClient
    {
        return $this->manager;
    }
}