<?php

declare(strict_types=1);

namespace MyParcelNL\PrestaShop\Migration\Pdk;

use DbQuery;
use MyParcelNL\Pdk\Base\Support\Collection;
use MyParcelNL\Pdk\Carrier\Model\Carrier;
use MyParcelNL\Pdk\Facade\Config;
use MyParcelNL\Pdk\Facade\Logger;
use MyParcelNL\Pdk\Facade\Platform;
use MyParcelNL\PrestaShop\Contract\PsCarrierServiceInterface;
use MyParcelNL\PrestaShop\Entity\MyparcelnlCarrierMapping;
use MyParcelNL\PrestaShop\Facade\EntityManager;
use MyParcelNL\PrestaShop\Repository\PsCarrierMappingRepository;
use MyParcelNL\Sdk\src\Support\Str;

final class PdkCarrierMigration extends AbstractPsPdkMigration
{
    private const SETTING_PREFIX     = 'MYPARCELNL_';
    private const LEGACY_CARRIER_MAP = [
        Carrier::CARRIER_POSTNL_NAME => 'POSTNL',
        Carrier::CARRIER_DHL_NAME    => 'DHL',
    ];

    /**
     * @var \MyParcelNL\PrestaShop\Repository\PsCarrierMappingRepository
     */
    private $psCarrierMappingRepository;

    /**
     * @var \MyParcelNL\PrestaShop\Contract\PsCarrierServiceInterface
     */
    private $psCarrierService;

    /**
     * @param  \MyParcelNL\PrestaShop\Repository\PsCarrierMappingRepository $psCarrierMappingRepository
     * @param  \MyParcelNL\PrestaShop\Contract\PsCarrierServiceInterface    $psCarrierService
     */
    public function __construct(
        PsCarrierMappingRepository $psCarrierMappingRepository,
        PsCarrierServiceInterface  $psCarrierService
    ) {
        $this->psCarrierMappingRepository = $psCarrierMappingRepository;
        parent::__construct();
        $this->psCarrierService = $psCarrierService;
    }

    /**
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \PrestaShopDatabaseException
     */
    public function up(): void
    {
        $carrierRows = $this->getLegacyCarrierRows();

        if ($carrierRows->isEmpty()) {
            Logger::debug('No legacy carriers found to migrate.');

            return;
        }

        $oldCarriers = $this->getCarriersToMigrate($carrierRows);

        if ($oldCarriers->isEmpty()) {
            return;
        }

        $oldCarriers->each(function (array $item) {
            Logger::debug("Migrating carrier \"{$item['myparcelCarrier']}\" with id \"{$item['carrierId']}\"");
            $this->psCarrierMappingRepository->create($item);
        });

        EntityManager::flush();
    }

    /**
     * @param  \MyParcelNL\Pdk\Base\Support\Collection $carrierRows
     *
     * @return \MyParcelNL\Pdk\Base\Support\Collection
     */
    private function getCarriersToMigrate(Collection $carrierRows): Collection
    {
        $mappings = $this->psCarrierMappingRepository->all();
        $carriers = new Collection(Config::get('carriers'));

        return $carrierRows->reduce(function (Collection $carry, array $item) use ($carriers, $mappings) {
            $oldCarrier = Str::after($item['name'], self::SETTING_PREFIX);

            $name = strtolower($oldCarrier);
            $id   = (int) $item['value'];

            if (! $this->psCarrierService->exists($id)) {
                Logger::debug("Carrier with id \"$id\" does not exist.");

                return $carry;
            }

            if (! $carriers->containsStrict('name', $name)) {
                Logger::debug("Carrier \"$oldCarrier\" not found in carriers.");

                return $carry;
            }

            $existing = $mappings->first(function (MyparcelnlCarrierMapping $mapping) use ($name) {
                return $mapping->getMyparcelCarrier() === $name;
            });

            if ($existing) {
                $existingId = $existing->getCarrierId();
                Logger::debug("A carrier for \"$name\" already exists with id $existingId.");

                return $carry;
            }

            return $carry->push(['myparcelCarrier' => $name, 'carrierId' => $id]);
        }, new Collection());
    }

    /**
     * @return \MyParcelNL\Pdk\Base\Support\Collection
     * @throws \PrestaShopDatabaseException
     */
    private function getLegacyCarrierRows(): Collection
    {
        $settingNames = (new Collection(Platform::getCarriers()))->map(static function ($carrier) {
            $name = self::LEGACY_CARRIER_MAP[$carrier['name']] ?? strtoupper($carrier['name']);

            return self::SETTING_PREFIX . $name;
        });

        return $this->getAllRows('configuration', function (DbQuery $query) use ($settingNames) {
            $query->where(sprintf('name IN ("%s")', implode('", "', $settingNames->toArray())));
        });
    }
}
