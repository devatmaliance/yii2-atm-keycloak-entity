ATM Yii2 Keycloak Entity
=====================

## Что нужно сделать?

Необходимо добавить код в следующих местах:

### Файл `common/config/main.php`

```php
return [
    'components' => [
        'keycloakEntityManager' => [
            'class' => \atmaliance\yii2_keycloak_entity\KeycloakEntityManager::class,
        ],
    ],
];
```

### Файл `common/config/main-local.php`

```php
return [
    'components' => [
        'keycloakEntityManager' => [
            'client_id' => 'admin-cli',
            'baseUrl' => 'http://localhost:8180',
            'realm' => 'master',
            'username' => 'admin',
            'password' => '1234',
        ],
    ],
];
```