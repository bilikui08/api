<?php

namespace Src\Infrastructure\Persistence\Migration;

use Faker\Factory;

class PagoMigration extends AbstractMigration
{
    public function up(): void
    {
        $this
            ->addSql('
                CREATE TABLE IF NOT EXISTS `pago` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `tarjeta_id` INT NOT NULL,
                    `monto` decimal(15,2) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `created_at` datetime NOT NULL,
                    `updated_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (tarjeta_id) REFERENCES tarjeta(id)
                ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
    }

    public function down(): void
    {
        $this->addSql('DROP TABLE IF EXISTS pago');
    }
}
