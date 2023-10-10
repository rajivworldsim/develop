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
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;
use Mirasvit\CacheWarmer\Helper\Serializer;
use Psr\Log\LoggerInterface;

class DataPatch108 implements DataPatchInterface, PatchVersionInterface
{
    private $setup;
    private $pageRepository;
    private $logger;
    private $serializer;

    public function __construct(
        ModuleDataSetupInterface $setup,
        PageRepositoryInterface $pageRepository,
        LoggerInterface $logger,
        Serializer $serializer
    ) {
        $this->setup = $setup;
        $this->pageRepository = $pageRepository;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->setup->getConnection()->startSetup();
        $installer = $this->setup;

        $offset = 0;
        $limit  = 1000;

        $pageCollection = $this->pageRepository->getCollection();
        $size = $pageCollection->getSize();
        $select = $pageCollection->getSelect();
        $select->limit($limit, $offset);
        if ($size) {
            $this->logger->info("Convert warmer URLs to a new format 1:");
        }
        while ($pageCollection->count()) {
            $this->logger->info($offset."/".$size);
            /** @var PageInterface $page */
            foreach ($pageCollection as $page) {
                $varyData = $page->getData(PageInterface::VARY_DATA);
                $varyData = $this->serializer->unserialize($varyData);
                if (is_array($varyData)) {
                    $page->setVaryData($varyData);
                    $this->pageRepository->save($page);
                }
            }
            $pageCollection->clear();
            $offset += $limit;
            $select->limit($limit, $offset);
        }
        if ($size) {
            $this->logger->info("Done");
        }
        $this->setup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '1.0.8';
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}
