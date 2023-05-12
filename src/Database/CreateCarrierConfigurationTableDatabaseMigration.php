<?php

declare(strict_types=1);

namespace MyParcelNL\PrestaShop\Database;

class CreateCarrierConfigurationTableDatabaseMigration extends AbstractDatabaseMigration
{
    public function down(): void
    {
        $table = Table::withPrefix(Table::TABLE_CARRIER_CONFIGURATION);
        $this->execute("DROP TABLE IF EXISTS `$table`");
    }

    public function up(): void
    {
        $table = Table::withPrefix(Table::TABLE_CARRIER_CONFIGURATION);
        $sql   = <<<SQL
            CREATE TABLE IF NOT EXISTS `$table` (
                `id`               INT AUTO_INCREMENT                                             NOT NULL,
                `id_carrier`       INT                                                            NOT NULL,
                `myparcel_carrier` VARCHAR(32)                                                    NOT NULL,
                `created`          DATETIME DEFAULT CURRENT_TIMESTAMP                             NOT NULL,
                `updated`          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                UNIQUE INDEX UNIQ_B515571199A4586C (`id_carrier`), 
                UNIQUE INDEX UNIQ_B5155711A4D607B2 (`myparcel_carrier`),
                PRIMARY KEY (`id`)
            ) ENGINE={ENGINE} DEFAULT CHARSET=utf8;
SQL;

        $this->execute($sql);
    }
}
