<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Block\Adminhtml\Rate\Sheet\Data\Bundle\Import;


class Importdata extends \Magento\Framework\View\Element\Template
{

    protected $categoryFactory;

    public function __construct(
      \Magento\Framework\View\Element\Template\Context $context,
      \Magento\Catalog\Model\CategoryFactory $categoryFactory,
      array $data = []
    ){
      $this->categoryFactory = $categoryFactory;
      parent::__construct($context, $data);
    }

    public function getHeaderText()
    {
        return __('Import Location Data');
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }


    public function getFormActionUrl()
    {
        // if ($this->hasFormActionUrl()) {
        //     return $this->getData('form_action_url');
        // }
        return $this->getUrl('worldsim_databundle/ratesheetdatabundle/csvsave');
    }
}


