<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Controller\Adminhtml\SmsToNew;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Agtech\CmsBlocks\Model\ResourceModel\UksimSmsToNew\CollectionFactory;

class MassDelete extends Action
{
    /**
     * @var \Agtech\CmsBlocks\Model\ResourceModel\UksimSmsToNew\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * @var \Agtech\CmsBlocks\Model\JsonDataFactory
     */
    protected $jsonDataFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter
     * @param \Agtech\CmsBlocks\Model\ResourceModel\UksimSmsToNew\CollectionFactory $collectionFactory
     * @param \Agtech\CmsBlocks\Model\UksimSmsToNew\SmsToNewFactory $callingToNewFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Agtech\CmsBlocks\Model\UksimSmsToNew\SmsToNewFactory $smsToNewFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->smsToNewFactory = $smsToNewFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $count = 0;
            foreach ($collection as $model) {
                $model = $this->smsToNewFactory->create()->load($model->getEntityId());
                $model->delete();
                $count++;
            }
            $this->messageManager->addSuccess(__('A total of %1 blog(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Agtech_CmsBlocks::uksim_sms_to_new');
    }
}
