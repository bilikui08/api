<?php

namespace Src\Infrastructure\Persistence\Migration;

abstract class AbstractMigration
{
    private array $plannedSql = [];
    private array $nonPlannedSql = [];
    private bool $isUp = true;

    public function __construct()
    {
        $this->up();
        $this->isUp = false;
        $this->down();
    }

    abstract public function up(): void;
    abstract public function down(): void;

    public function addSql(string $sql): self
    {
        if ($this->isUp) {
            $this->plannedSql[] = $sql;
        } else {
            $this->nonPlannedSql[] = $sql;
        }

        return $this;
    }

    public function getSql(): array
    {
        return $this->plannedSql;
    }

    public function getDownSql(): array
    {
        return $this->nonPlannedSql;
    }
}
