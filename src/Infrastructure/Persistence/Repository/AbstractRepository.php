<?php

namespace Src\Infrastructure\Persistence\Repository;

use ArrayObject;
use PDO;
use PDOException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Src\Domain\Model\ModelInterface;
use Src\Domain\Repository\RepositoryInterface;
use Src\Infrastructure\Persistence\PdoPersistence;
use Src\Infrastructure\Persistence\QueryBuilder;

class AbstractRepository implements RepositoryInterface
{
    protected ?Logger $logger = null;
    protected ?PDO $connection = null;
    protected QueryBuilder $queryBuilder;

    public static function create($modelClass): RepositoryInterface
    {
        $appName = getenv('APP_NAME');
        $logPath = getenv('LOG_PATH');
        $logFile = getenv('LOG_FILE');
        $logger = new Logger($appName, [new StreamHandler($logPath . $logFile)]);
        return new $modelClass($logger);
    }

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->connection = PdoPersistence::getInstance();
        $this->queryBuilder = new QueryBuilder();
    }

    public function findById(int $id): ModelInterface|null
    {
        $row = $this->getById($id);
        $model = null;
        if ($row) {
            $model = $this->fillArrayResultToModel($row);
        }

        return $model;
    }

    public function findAll(): ArrayObject
    {
        $rows = $this->getAll();
        $models = new ArrayObject([]);
        foreach ($rows as $row) {
            $model = $this->fillArrayResultToModel($row);
            $models->append($model);
        }

        return $models;
    }

    public function insertOrUpdate(ModelInterface $model): bool
    {
        $data = $model->toArray();
        $countData = count($data);
        $this->queryBuilder->clear();
        try {
            if (null === $model->getId()) {
                $sql = $this->queryBuilder->insert($this->getTableName(), $data);
                $values = $this->queryBuilder->getValues();
                $stmt = $this->connection->prepare($sql);
                $bindIndex = 1;

                for ($i = 0; $i < $countData; $i++) {
                    $stmt->bindValue($bindIndex, $values[$i]);
                    $bindIndex++;
                }
            } else {
                $sql = $this
                    ->queryBuilder
                    ->update($this->getTableName(), $data)
                    ->where('id', '=', $model->getId())
                    ->getQuery();

                $values = $this->queryBuilder->getValues();
                $stmt = $this->connection->prepare($sql);
                $bindIndex = 1;

                for ($i = 0; $i < $countData; $i++) {
                    $stmt->bindValue($bindIndex, $values[$i]);
                    $bindIndex++;
                }

                $stmt->bindValue($bindIndex, $values[$i]);
            }

            $this->logger->debug($sql);
            $result = $stmt->execute();

            if (null !== $model->getId()) {
                return $result;
            }

            $stmt = $this->connection->prepare('SELECT LAST_INSERT_ID() as id');
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $model->setId($row['id']);
            return $result;

        } catch (PDOException $e) {
            $this->logger->error('Error en la query: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $this->queryBuilder->clear();
        $sql = $this
            ->queryBuilder
            ->delete($this->getTableName())
            ->where('id', '=', $id)
            ->getQuery();

        $values = $this->queryBuilder->getValues();

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $values[0]);
        $this->logger->debug($sql);
        return $stmt->execute();
    }

    public function fillArrayResultToModel(array $row): ModelInterface|null
    {
        return null;
    }

    public function getTableName(): string
    {
        return 'table_name';
    }

    public function getFields(): array
    {
        return ['*'];
    }

    protected function getById(int $id): array
    {
        $this->queryBuilder->clear();
        $sql = $this
            ->queryBuilder
            ->select($this->getFields())
            ->from($this->getTableName())
            ->where('id', '=', $id)
            ->getQuery();

        $values = $this->queryBuilder->getValues();

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $values[0]);
        $this->logger->debug($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return [];
        }

        return $result;
    }

    protected function getAll(): array
    {
        $this->queryBuilder->clear();
        $sql = $this
            ->queryBuilder
            ->select($this->getFields())
            ->from($this->getTableName())
            ->getQuery();

        $stmt = $this->connection->prepare($sql);
        $this->logger->debug($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$result) {
            return [];
        }

        return $result;
    }


    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}
