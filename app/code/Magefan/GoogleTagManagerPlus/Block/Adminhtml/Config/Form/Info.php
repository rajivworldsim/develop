<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Block\Adminhtml\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Info extends \Magefan\Community\Block\Adminhtml\System\Config\Form\Info
{
    /**
     * Return info block html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $version = $this->getModuleVersion->execute($this->getModuleName());
        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            + Addon <strong>Google Tag Manager Plus v' . $this->_escaper->escapeHtml($version) . '</strong>
        </div>';

        return $html;
    }
}
