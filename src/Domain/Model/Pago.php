<?php

namespace Src\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Src\Domain\Model\Traits\Timestampable;
use OpenApi\Attributes as OA;

class Pago implements ModelInterface
{
    use Timestampable;

    private ?int $id = null;
    private int $tarjetaId;
    private string $monto;

    public function __construct(
        ?int $id,
        int $tarjetaId,
        string $monto,
        DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->tarjetaId = $tarjetaId;
        $this->monto = $monto;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
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

    public function toReturnArray(): array
    {
        $array[self::KEY_RETURN_TO_ARRAY] = [
            'id' => $this->getId(),
            'tarjeta_id' => $this->getTarjetaId(),
            'monto' => $this->getMonto(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()
                ? $this->getUpdatedAt()->format('Y-m-d H:i:s')
                : '',
        ];

        return $array;
    }

    public function toArray(): array
    {
        if (null !== $this->getId()) {
            return [
                //'id' => $this->getId(),
                'tarjeta_id' => $this->getTarjetaId(),
                'monto' => $this->getMonto(),
                'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'tarjeta_id' => $this->getTarjetaId(),
            'monto' => $this->getMonto(),
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'updated_at' => null,
        ];
    }

    /**
     * Get the value of tarjetaId
     */ 
    public function getTarjetaId()
    {
        return $this->tarjetaId;
    }

    /**
     * Set the value of tarjetaId
     *
     * @return  self
     */ 
    public function setTarjetaId($tarjetaId)
    {
        $this->tarjetaId = $tarjetaId;

        return $this;
    }

    /**
     * Get the value of monto
     */ 
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set the value of monto
     *
     * @return  self
     */ 
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }
}
