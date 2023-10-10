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



namespace Mirasvit\CacheWarmer\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\DesignExceptions;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;
use Mirasvit\Core\Service\SerializeService;

class HttpContextPlugin
{
    private $extendedConfig;

    private $request;

    private $designExceptions;

    public function __construct(
        ExtendedConfig $extendedConfig,
        RequestInterface $request,
        DesignExceptions $designExceptions
    ) {
        $this->extendedConfig   = $extendedConfig;
        $this->request          = $request;
        $this->designExceptions = $designExceptions;
    }

    /**
     * @param mixed $subject
     * @param array $data
     * @return mixed
     */
    public function afterGetData($subject, $data)
    {
        if (isset($data['store'])
            && $this->extendedConfig->isUseSameCacheForNewVisitor()) {
            unset($data['store']);
        }

        if ($this->extendedConfig->isUseDesignSettings()) {
            $designCustom = $this->designExceptions->getThemeByRequest($this->request) ?: 'default';

            $data['mst_design'] = 'design_' . $designCustom;
        }

        return $data;
    }
}
