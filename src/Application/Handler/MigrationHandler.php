<?php

namespace Src\Application\Handler;

use PDO;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDOException;
use Src\Infrastructure\Persistence\Migration\UserMigration;
use Src\Infrastructure\Persistence\Migration\TarjetaMigration;
use Src\Infrastructure\Persistence\PdoPersistence;

class MigrationHandler
{
    private PDO $connection;
    private Logger $logger;

    private bool $runSqlDown = false;

    // Put migrations here:
    private array $migrations = [
        UserMigration::class,
        TarjetaMigration::class,
    ];

    public function __construct()
    {
        $this->connection = PdoPersistence::getInstance();
        $appName = getenv('APP_NAME');
        $logPath = getenv('LOG_PATH');
        $logFile = getenv('LOG_FILE');
        $routerFile = getenv('ROUTES_FILE');

        $this->logger = new Logger($appName, [new StreamHandler($logPath . $logFile)]);
    }

    public function run(): void
    {
        foreach ($this->migrations as $migration) {
            $migrationObject = new $migration();

            $querys = !$this->getRunSqlDown()
                ? $migrationObject->getSql()
                : $migrationObject->getDownSql();

            foreach ($querys as $sql) {
                try {
                    $stmt = $this->connection->prepare($sql);
                    $this->logger->debug("----Run SQL Migration----");
                    $this->logger->debug($sql);
                    $this->logger->debug('--------');
                    $stmt->execute();
                } catch (PDOException $e) {
                    $this->logger->error($e->getMessage());
                    $this->logger->error($sql);
                }
            }
        }
    }

    /**
     * Get the value of runSqlDown
     */
    public function getRunSqlDown()
    {
        return $this->runSqlDown;
    }

    /**
     * Set the value of runSqlDown
     *
     * @return  self
     */
    public function setRunSqlDown(bool $runSqlDown): self
    {
        $this->runSqlDown = $runSqlDown;

        return $this;
    }
}
