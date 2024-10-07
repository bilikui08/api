<?php

namespace Src\Infrastructure\Persistence\Migration;

use Faker\Factory;

class TarjetaMigration extends AbstractMigration
{
    public function up(): void
    {
        $this
            ->addSql('
                CREATE TABLE IF NOT EXISTS `tarjeta` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `dni` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `nombre_entidad_bancaria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `numero` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `limite` decimal(15,2) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `created_at` datetime NOT NULL,
                    `updated_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
    }

    public function down(): void
    {
        $this->addSql('DROP TABLE IF EXISTS tarjeta');
    }
}
