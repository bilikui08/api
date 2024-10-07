<?php

namespace Src\Domain\Model;

use DateTimeInterface;
use DateTimeImmutable;
use Src\Domain\Model\Traits\Timestampable;

class User implements ModelInterface
{
    use Timestampable;

    private int $id;
    private string $username;
    private string $password;
    private string $token;
    private string $latitud;
    private string $longitud;

    public function __construct(
        int $id,
        string $username,
        string $password,
        string $token,
        string $latitud,
        string $longitud,
        DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->token = $token;
        $this->latitud = $latitud;
        $this->longitud = $longitud;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of token
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of latitud
     */
    public function getLatitud(): string
    {
        return $this->latitud;
    }

    /**
     * Set the value of latitud
     *
     * @return  self
     */
    public function setLatitud(string $latitud): self
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Get the value of longitud
     */
    public function getLongitud(): string
    {
        return $this->longitud;
    }

    /**
     * Set the value of longitud
     *
     * @return  self
     */
    public function setLongitud(string $longitud): self
    {
        $this->longitud = $longitud;

        return $this;
    }

    public function toReturnArray(): array
    {
        $array[self::KEY_RETURN_TO_ARRAY] = [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'latitud' => $this->getLatitud(),
            'longitud' => $this->getLongitud(),
        ];

        return $array;
    }

    public function toArray(): array
    {
        if (null !== $this->getId()) {
            return [
                //'id' => $this->getId(),
                'username' => $this->getUsername(),
                'password' => $this->getPassword(),
                'token' => $this->getToken(),
                'latitud' => $this->getLatitud(),
                'longitud' => $this->getLongitud(),
                'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'token' => $this->getToken(),
            'latitud' => $this->getLatitud(),
            'longitud' => $this->getLongitud(),
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'updated_at' => null,
        ];
    }
}
