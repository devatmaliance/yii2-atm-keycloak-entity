<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity;

use atmaliance\yii2_keycloak_entity\models\dto\KeycloakManagerConfigurationDTO;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use yii\base\Configurable;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class KeycloakManagerConiguration implements Configurable
{
    private KeycloakManagerConfigurationDTO $configuration;

    /**
     * @param array $config
     * @throws ExceptionInterface
     */
    public function __construct(array $config = [])
    {
        $this->configuration = (new Normalizer())->denormalize($config, KeycloakManagerConfigurationDTO::class);
    }

    /**
     * @return KeycloakManagerConfigurationDTO
     */
    public function getConfiguration(): KeycloakManagerConfigurationDTO
    {
        return $this->configuration;
    }
}