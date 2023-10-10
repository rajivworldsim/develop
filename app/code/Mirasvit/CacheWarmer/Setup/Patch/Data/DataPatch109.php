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
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;

class DataPatch109 implements DataPatchInterface, PatchVersionInterface
{
    private $setup;
    private $warmRuleRepository;

    public function __construct(
        ModuleDataSetupInterface $setup,
        WarmRuleRepositoryInterface $warmRuleRepository
    ) {
        $this->setup = $setup;
        $this->warmRuleRepository = $warmRuleRepository;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->setup->getConnection()->startSetup();
        $installer = $this->setup;

        $rule = $this->warmRuleRepository->create();

        $rule->setName('Default Rule')
            ->setIsActive(true)
            ->setPriority(1);

        $this->warmRuleRepository->save($rule);

        $this->setup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '1.0.9';
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [DataPatch108::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}
