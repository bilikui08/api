<?php

namespace Src\Domain\Repository;

use ArrayObject;
use Src\Domain\Model\ModelInterface;

interface RepositoryInterface
{
    public function findAll(): ArrayObject;
    public function findById(int $id): ModelInterface|null;
    public function insertOrUpdate(ModelInterface $model): bool;
    public function delete(int $id): bool;
    public function getTableName(): string;
    public function getFields(): array;
    public function fillArrayResultToModel(array $row): ModelInterface|null;
}
