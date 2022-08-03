<?php

declare(strict_types=1);

namespace MyParcelNL\PrestaShop\Database;

/**
 * @see \MyParcelNL\PrestaShop\Entity\MyparcelnlCartDeliveryOptions
 */
class CreateCartDeliveryOptionsTableMigration extends AbstractMigration
{
    public function down(): bool
    {
        $table = Table::withPrefix(Table::TABLE_CART_DELIVERY_OPTIONS);
        return $this->execute("DROP TABLE IF EXISTS `$table`");
    }

    public function up(): bool
    {
        $table = Table::withPrefix(Table::TABLE_CART_DELIVERY_OPTIONS);
        $sql   = <<<SQL
            CREATE TABLE IF NOT EXISTS `$table` (
                `id`      INT AUTO_INCREMENT                                             NOT NULL,
                `id_cart` INT                                                            NOT NULL,
                `data`    TEXT                                                           NOT NULL,
                `created` DATETIME DEFAULT CURRENT_TIMESTAMP                             NOT NULL,
                `updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                UNIQUE INDEX UNIQ_8CA4157F808394B5 (`id_cart`),
                PRIMARY KEY (`id`)
            ) ENGINE={ENGINE} DEFAULT CHARSET=utf8;
SQL;

        return $this->execute($sql);
    }
}
