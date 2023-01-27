<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity;

use atmaliance\yii2_keycloak_entity\models\dto\KeycloakEntityManagerConfigurationDTO;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use yii\base\Configurable;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class KeycloakEntityManager implements Configurable
{
    private KeycloakEntityManagerConfigurationDTO $configuration;

    /**
     * @param array $config
     * @throws ExceptionInterface
     */
    public function __construct(array $config = [])
    {
        $this->configuration = (new Normalizer())->denormalize($config, KeycloakEntityManagerConfigurationDTO::class);
    }

    /**
     * @return KeycloakEntityManagerConfigurationDTO
     */
    public function getConfiguration(): KeycloakEntityManagerConfigurationDTO
    {
        return $this->configuration;
    }
}