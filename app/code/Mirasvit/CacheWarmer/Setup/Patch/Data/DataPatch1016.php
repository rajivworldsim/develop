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

class DataPatch1016 implements DataPatchInterface, PatchVersionInterface
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

        $connection = $setup->getConnection();
        $this->logger->info("Updating URLs ...");
        $connection = $setup->getConnection();
        $connection->query(
            "UPDATE `{$setup->getTable(PageInterface::TABLE_NAME)}` SET uri_hash = sha1(uri);"
        );

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
        return [DataPatch1014::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}
