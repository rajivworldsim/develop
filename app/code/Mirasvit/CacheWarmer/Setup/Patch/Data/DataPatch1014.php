<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.7.7
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\CacheWarmer\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;
use Psr\Log\LoggerInterface;

class DataPatch1014 implements DataPatchInterface, PatchVersionInterface
{
    private $setup;
    private $pageRepository;
    private $logger;

    public function __construct(
        ModuleDataSetupInterface $setup,
        PageRepositoryInterface $pageRepository,
        LoggerInterface $logger
    ) {
        $this->setup = $setup;
        $this->pageRepository = $pageRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->setup->getConnection()->startSetup();
        $setup = $this->setup;

        $pageCollection = $this->pageRepository->getCollection();
        $size = $pageCollection->getSize();

        if (!$size) {
            return;
        }

        $connection = $setup->getConnection();
        //remove possible duplicates
        $this->logger->info("Removing possible duplicates...");

        $connection = $setup->getConnection();

        $indexName = "mst_cache_warmer_uri_vary_index";
        $connection->query("ALTER TABLE `{$setup->getTable(PageInterface::TABLE_NAME)}` ADD INDEX `$indexName` (`uri` (600), `vary_data` (100));");

        $connection->query("DELETE t1 FROM `{$setup->getTable(PageInterface::TABLE_NAME)}` t1
            INNER JOIN
            `{$setup->getTable(PageInterface::TABLE_NAME)}` t2
            WHERE t1.page_id > t2.page_id AND t1.uri = t2.uri AND t1.vary_data = t2.vary_data;");

        $connection->query("ALTER TABLE `{$setup->getTable(PageInterface::TABLE_NAME)}` DROP INDEX `$indexName`;");


        $this->setup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '1.0.14';
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [DataPatch109::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}
