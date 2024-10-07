<?php

namespace Src\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Src\Domain\Model\Traits\Timestampable;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Tarjeta",
    type: "object",
    description: "Modelo de Tarjeta",
    properties: [
        new OA\Property(property: "id", type: "integer", description: "ID de la tarjeta"),
        new OA\Property(property: "dni", type: "string", description: "Dni del cliente"),
        new OA\Property(property: "nombre", type: "string", description: "Nombre del cliente"),
        new OA\Property(property: "apellido", type: "string", description: "apellido del cliente"),
        new OA\Property(property: "nombreEntidadBancaria", type: "string", description: "Nombre de la Entidad Bancaria de la tarjeta"),
        new OA\Property(property: "limite", type: "string", description: "Limite de la Entidad Bancaria de la tarjeta"),
        new OA\Property(property: "created_at", type: "datetime", description: "Fecha de creación de la tarjeta"),
        new OA\Property(property: "updated_at", type: "datetime", description: "Fecha de actualización de la tarjeta"),
    ]
)]
class Tarjeta implements ModelInterface
{
    use Timestampable;

    private ?int $id = null;
    private string $dni;
    private string $nombre;
    private string $apellido;
    private string $nombreEntidadBancaria;
    private string $numero;
    private string $limite;

    public function __construct(
        ?int $id,
        string $dni,
        string $nombre,
        string $apellido,
        string $nombreEntidadBancaria,
        string $numero,
        string $limite,
        DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->nombreEntidadBancaria = $nombreEntidadBancaria;
        $this->numero = $numero;
        $this->limite = $limite;
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
            'dni' => $this->getDni(),
            'nombre' => $this->getNombre(),
            'apellido' => $this->getApellido(),
            'nombre_entidad_bancaria' => $this->getNombreEntidadBancaria(),
            'limite' => $this->getLimite(),
            'numero' => $this->getNumero(),
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
                'dni' => $this->getDni(),
                'nombre' => $this->getNombre(),
                'apellido' => $this->getApellido(),
                'nombre_entidad_bancaria' => $this->getNombreEntidadBancaria(),
                'limite' => $this->getLimite(),
                'numero' => $this->getNumero(),
                'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'dni' => $this->getDni(),
            'nombre' => $this->getNombre(),
            'apellido' => $this->getApellido(),
            'nombre_entidad_bancaria' => $this->getNombreEntidadBancaria(),
            'limite' => $this->getLimite(),
            'numero' => $this->getNumero(),
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'updated_at' => null,
        ];
    }

    /**
     * Get the value of dni
     */ 
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set the value of dni
     *
     * @return  self
     */ 
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of apellido
     */ 
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set the value of apellido
     *
     * @return  self
     */ 
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get the value of nombreEntidadBancaria
     */ 
    public function getNombreEntidadBancaria()
    {
        return $this->nombreEntidadBancaria;
    }

    /**
     * Set the value of nombreEntidadBancaria
     *
     * @return  self
     */ 
    public function setNombreEntidadBancaria($nombreEntidadBancaria)
    {
        $this->nombreEntidadBancaria = $nombreEntidadBancaria;

        return $this;
    }

    /**
     * Get the value of numero
     */ 
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set the value of numero
     *
     * @return  self
     */ 
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get the value of limite
     */ 
    public function getLimite()
    {
        return $this->limite;
    }

    /**
     * Set the value of limite
     *
     * @return  self
     */ 
    public function setLimite($limite)
    {
        $this->limite = $limite;

        return $this;
    }
}
