<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Worldsim\Databundle\Controller\Adminhtml\GoAPIResponse;

use Worldsim\Databundle\Model\ResourceModel\GoAPIResponse\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $GoAPIResponseFactory;
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        CollectionFactory $GoAPIResponseFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->GoAPIResponseFactory = $GoAPIResponseFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }
    public function execute()
    {
        $collection = $this->filter->getCollection($this->GoAPIResponseFactory->create());

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