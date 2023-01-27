<?php

declare(strict_types=1);

namespace atmaliance\yii2_keycloak_entity\models\finder;

use atmaliance\yii2_keycloak_entity\models\client\KeycloakApi;
use atmaliance\yii2_keycloak_entity\models\entity\KeycloakUser;
use atmaliance\yii2_keycloak_entity\models\exception\KeycloakUserFinderException;
use atmaliance\yii2_keycloak_entity\models\serializer\Normalizer;
use Throwable;
use Yii;

final class KeycloakUserFinder
{
    private ?int $offset = null;
    private ?int $limit = null;
    private ?array $attributes = null;
    private ?string $uuid = null;
    private ?string $name = null;
    private ?string $surname = null;
    private ?string $email = null;
    private ?string $username = null;
    private ?string $search = null;
    private ?string $exact = null;

    /**
     * @param int $offset
     * @return $this
     * Pagination offset
     */
    public function whereOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     * Maximum results size (defaults to 100)
     */
    public function whereLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     * A query to search for custom attributes, in the format \'key1:value2 key2:value2\
     */
    public function whereAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $uuid
     * @return $this
     * User UUID
     */
    public function whereUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * A String contained in firstName, or the complete firstName, if param "exact" is true
     */
    public function whereName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $surname
     * @return $this
     * A String contained in lastName, or the complete lastName, if param "exact" is true
     */
    public function whereSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @param string $email
     * @return $this
     * A String contained in email, or the complete email, if param "exact" is true
     */
    public function whereEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $username
     * @return $this
     * A String contained in username, or the complete username, if param "exact" is true
     */
    public function whereUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param bool $exact
     * @return $this
     * Boolean which defines whether the params "last", "first", "email" and "username" must match exactly
     */
    public function whereExact(bool $exact): self
    {
        /* This format is required by the library mohammad-waleed/keycloak-admin-client */
        $this->exact = $exact ? 'true' : 'false';

        return $this;
    }

    /**
     * @param string $search
     * @return $this
     * A String contained in username, first or last name, or email
     */
    public function whereSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return KeycloakUser|null
     * Returns a specific user based on uuid or email
     */
    public function one(): ?KeycloakUser
    {
        if (!empty($this->uuid)) {
            try {
                $response = KeycloakApi::getInstance()->getManager()->getUser([
                    'id' => $this->uuid,
                ]);

                if (isset($response['error'])) {
                    throw new KeycloakUserFinderException($response['error']);
                }

                return (new Normalizer())->denormalize($response, KeycloakUser::class);
            } catch (Throwable $exception) {
                Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
            }

            return null;
        }

        if (!empty($this->email)) {
            try {
                $keycloakUsers = $this->whereExact(true)->all();

                if (count($keycloakUsers) !== 1) {
                    throw new KeycloakUserFinderException("Number of found users with email [$this->email] !== 1");
                }

                /* @var KeycloakUser $keycloakUser */
                $keycloakUser = current($keycloakUsers);

                if (mb_strtolower($keycloakUser->getEmail(), 'UTF-8') !== mb_strtolower($this->email, 'UTF-8')) {
                    throw new KeycloakUserFinderException(
                        "Something is wrong. Specified email [$this->email] not equal keycloak email [{$keycloakUser->getEmail()}]"
                    );
                }

                return $keycloakUser;
            } catch (Throwable $exception) {
                Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
            }

            return null;
        }

        return null;
    }

    /**
     * @return KeycloakUser[]
     * Returns all users as an array.
     */
    public function all(): array
    {
        try {
            $response = KeycloakApi::getInstance()->getManager()->getUsers(
                array_filter([
                    'firstName' => $this->name,
                    'lastName' => $this->surname,
                    'email' => $this->email,
                    'username' => $this->username,
                    'q' => $this->attributes,
                    'search' => $this->search,
                    'first' => $this->offset,
                    'exact' => $this->exact,
                    'max' => $this->limit,
                ])
            );

            if (isset($response['error'])) {
                throw new KeycloakUserFinderException($response['error']);
            }

            return (new Normalizer())->denormalize($response, sprintf('%s[]', KeycloakUser::class));
        } catch (Throwable $exception) {
            Yii::error(sprintf('%s: %s', __METHOD__, $exception->getMessage()));
        }

        return [];
    }
}