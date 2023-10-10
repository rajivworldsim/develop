<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Worldsim\Databundle\Controller\Adminhtml\Ratesheetdatabundle;

use Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $RatesheetdatabundleFactory;
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        CollectionFactory $RatesheetdatabundleFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->RatesheetdatabundleFactory = $RatesheetdatabundleFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }
    public function execute()
    {
        $collection = $this->filter->getCollection($this->RatesheetdatabundleFactory->create());

        $count = 0;
        foreach ($collection as $child) {
            $child->delete();
            $count++;
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}