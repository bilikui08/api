<?php

namespace Src\Infrastructure\Persistence\Repository;

use DateTimeImmutable;
use PDO;
use PDOException;
use ArrayObject;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Model\User;
use Src\Domain\Repository\RepositoryInterface;

class UserRepository extends AbstractRepository implements RepositoryInterface
{
    public function getTableName(): string
    {
        return 'user';
    }

    public function getFields(): array
    {
        return ['id', 'username', 'password', 'token', 'latitud', 'longitud', 'created_at', 'updated_at'];
    }

    public function auth(string $username, string $password): User|null
    {
        $hashedPassword = md5($password);
        $sql = $this->getQueryBuilder()
            ->select($this->getFields())
            ->from($this->getTableName())
            ->where('username', '=', $username)
            ->where('password', '=', $hashedPassword)
            ->getQuery();

        $values = $this->getQueryBuilder()->getValues();

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $values[0]);
        $stmt->bindValue(2, $values[1]);

        $this->logger->debug($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = null;
        if ($row) {
            return $this->fillArrayResultToModel($row);
        }

        return $user;
    }

    public function fillArrayResultToModel(array $row): ModelInterface|null
    {
        if (empty($row)) {
            return null;
        }

        $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['created_at']);
        $updatedAt = isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null;
        return new User(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['token'] ?? '',
            $row['latitud'],
            $row['longitud'],
            $createdAt,
            $updatedAt
        );
    }
}
