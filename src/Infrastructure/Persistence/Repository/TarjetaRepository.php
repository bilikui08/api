<?php

namespace Src\Infrastructure\Persistence\Repository;

use DateTimeImmutable;
use PDO;
use PDOException;
use ArrayObject;
use Src\Domain\Repository\RepositoryInterface;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Model\Tarjeta;

class TarjetaRepository extends AbstractRepository
{
    public function getTableName(): string
    {
        return 'tarjeta';
    }

    public function getFields(): array
    {
        return ['id', 'dni', 'nombre', 'apellido', 'nombre_entidad_bancaria', 'numero', 'limite', 'created_at', 'updated_at'];
    }

    public function fillArrayResultToModel(array $row): ModelInterface|null
    {
        if (empty($row)) {
            return null;
        }

        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null;
        return new Tarjeta(
            $row['id'],
            $row['dni'],
            $row['nombre'],
            $row['apellido'],
            $row['nombre_entidad_bancaria'],
            $row['numero'],
            $row['limite'],
            $createdAt,
            $updatedAt
        );
    }
}
