<?php
/**
 * Copyright © Etailors All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Agtech\OrderAPI\Block\Adminhtml\Form\Field;

class Logo extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function Logo()
    {
        //Your block code
        return __('Hello Developer! This how to get the storename: %1 and this is the way to build a url: %2', $this->_storeManager->getStore()->getName(), $this->getUrl('contacts'));
    }
}

