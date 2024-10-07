<?php

namespace Src\Infrastructure\Persistence\Migration;

use Faker\Factory;

class UserMigration extends AbstractMigration
{
    public function up(): void
    {
        $this
            ->addSql('
                CREATE TABLE IF NOT EXISTS `user` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `token` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `latitud` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `longitud` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `created_at` datetime NOT NULL,
                    `updated_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `UNIQ_USERNAME` (`username`)
                ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ')
            ->addSql("
                INSERT INTO user (username, password, latitud, longitud, created_at) 
                VALUES ('admin', MD5('verifarmaApi'), '-34.913351', '-58.385837', NOW())
            ");

        $faker = Factory::create('es_ES');
        for ($i = 0; $i < 52; $i++) {
            $sql = "INSERT INTO user (username, password, latitud, longitud, created_at) ";
            $sql .= "VALUES ('" . $faker->userName . "', '" . $faker->password . "', ";
            $sql .= "'" . $faker->latitude . "', '" . $faker->longitude . "', NOW())";

            $this->addSql($sql);
        }
    }

    public function down(): void
    {
        $this->addSql('DROP TABLE IF EXISTS user');
    }
}
