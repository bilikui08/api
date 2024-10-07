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

    public function getByNumero(string $numero): ModelInterface|null
    {
        $this->queryBuilder->clear();
        $sql = $this
            ->queryBuilder
            ->select($this->getFields())
            ->from($this->getTableName())
            ->where('numero', '=', $numero)
            ->getQuery();

        $values = $this->queryBuilder->getValues();

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $values[0]);
        $this->logger->debug($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $model = null;
        if ($result) {
            $model = $this->fillArrayResultToModel($result);
        }

        return $model;
    }
}
