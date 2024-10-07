<?php

namespace Src\Infrastructure\Persistence;

class QueryBuilder
{
    protected array $select = [];
    protected string $table;
    protected array $where = [];
    protected string $limit;
    protected array $orderBy = [];
    protected array $values = [];
    protected string $queryType;
    protected array $updateData = [];

    public function select($fields = ['*']): self
    {
        $this->queryType = 'SELECT';
        $this->select = is_array($fields) ? $fields : func_get_args();
        return $this;
    }

    public function from($table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, string $operator, string $value, bool $bindParams = true): self
    {
        if ($bindParams) {
            $this->where[] = "{$column} {$operator} ?";
        } else {
            $this->where[] = "{$column} {$operator} '{$value}'";
        }

        if ($bindParams) {
            $this->values[] = $value;
        }

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(string $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getQuery(): string
    {
        if ($this->queryType === 'SELECT') {
            $query = $this->buildSelect();
        }

        if ($this->queryType === 'UPDATE') {
            return $this->buildUpdate();
        }

        if ($this->queryType === 'DELETE') {
            return $this->buildDelete();
        }

        return $query;
    }

    protected function buildSelect(): string
    {
        $select = implode(', ', $this->select);
        $sql = "SELECT {$select} FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if (!empty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $sql;
    }

    protected function buildUpdate(): string
    {
        $setClause = implode(', ', array_map(function ($column) {
            return "{$column} = ?";
        }, array_keys($this->updateData)));

        $sql = "UPDATE {$this->table} SET {$setClause}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        return $sql;
    }

    protected function buildDelete(): string
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!empty($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $sql;
    }

    public function getValues(): array
    {
        return $this->values;
        //return array_merge(array_values($this->updateData), $this->values);
    }

    public function insert(string $table, array $data): string
    {
        $this->queryType = 'INSERT';
        $this->table = $table;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->values = array_values($data);

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        return $sql;
    }

    public function update($table, $data): self
    {
        $this->queryType = 'UPDATE';
        $this->table = $table;
        $this->updateData = $data;
        $this->values = array_values($data);
        return $this;
    }

    public function delete($table): self
    {
        $this->queryType = 'DELETE';
        $this->table = $table;
        return $this;
    }

    public function clear(): void
    {
        $this->select = [];
        $this->table = '';
        $this->where = [];
        $this->orderBy = [];
        $this->values = [];
        $this->limit = '';
        $this->queryType = '';
        $this->updateData = [];
    }
}
