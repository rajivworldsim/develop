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




namespace Mirasvit\CacheWarmer\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class StrategySource implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            0 => __('Warm Rules (default)'),
            1 => __('Warm Rules + URL variations')
        ];
    }
}
