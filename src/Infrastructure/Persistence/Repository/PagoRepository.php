<?php

namespace Src\Infrastructure\Persistence\Repository;

use DateTimeImmutable;
use PDO;
use PDOException;
use ArrayObject;
use Src\Domain\Repository\RepositoryInterface;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Model\Pago;

class PagoRepository extends AbstractRepository
{
    public function getTableName(): string
    {
        return 'pago';
    }

    public function getFields(): array
    {
        return ['id', 'tarjeta_id', 'monto', 'created_at', 'updated_at'];
    }

    public function fillArrayResultToModel(array $row): ModelInterface|null
    {
        if (empty($row)) {
            return null;
        }

        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null;
        return new Pago(
            $row['id'],
            $row['tarjeta_id'],
            $row['monto'],
            $createdAt,
            $updatedAt
        );
    }
}
