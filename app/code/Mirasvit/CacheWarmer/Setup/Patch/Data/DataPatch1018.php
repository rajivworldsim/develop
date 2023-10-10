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
use Mirasvit\CacheWarmer\Api\Repository\SourceRepositoryInterface;
use Mirasvit\CacheWarmer\Model\Config\Source\CustomerGroups;
use Mirasvit\CacheWarmer\Api\Data\SourceInterface;

class DataPatch1018 implements DataPatchInterface, PatchVersionInterface
{
    private $setup;
    private $sourceRepository;
    private $customerGroups;

    public function __construct(
        ModuleDataSetupInterface $setup,
        SourceRepositoryInterface $sourceRepository,
        CustomerGroups $customerGroups
    ) {
        $this->setup = $setup;
        $this->sourceRepository = $sourceRepository;
        $this->customerGroups   = $customerGroups;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->setup->getConnection()->startSetup();
        $setup = $this->setup;

        $source = $this->sourceRepository->create();

        $customerGroupIds = $this->customerGroups->getCustomerGroupIds();

        $source->setSourceName('Default source')
            ->setSourceType(SourceInterface::TYPE_VISITOR)
            ->setCustomerGroups($customerGroupIds)
            ->setIsActive(true)
            ->setLastSyncronizedAt(date("Y-m-d H:i:s"));

        $this->sourceRepository->save($source);

        $this->setup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '1.0.16';
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [DataPatch1016::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}
