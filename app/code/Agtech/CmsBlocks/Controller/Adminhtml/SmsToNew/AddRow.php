<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Controller\Adminhtml\SmsToNew;

use Magento\Framework\Controller\ResultFactory;
class AddRow extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    /**
     * @var\Agtech\CmsBlocks\Model\UksimSmsToNew\smsToNewFactory $smsToNewFactory
     */
    private $smsToNewFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Agtech\CmsBlocks\Model\UksimSmsToNew\smsToNewFactory $smsToNewFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Agtech\CmsBlocks\Model\UksimSmsToNew\SmsToNewFactory $smsToNewFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->smsToNewFactory = $smsToNewFactory;
    }
    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
       
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->smsToNewFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
           $rowData = $rowData->load($rowId);
           $rowTitle = $rowData->getTitle();
           if (!$rowData->getEntityId()) {
               $this->messageManager->addError(__('Json data no longer exist.'));
               $this->_redirect('agtech_cmsblocks/smtonew/addrow');
               return;
           }
       }
       $this->coreRegistry->register('row_data', $rowData);
       $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
       $title = $rowId ? __('Edit Json ').$rowTitle : __('Add Json');
       $resultPage->getConfig()->getTitle()->prepend($title);
       return $resultPage;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Agtech_CmsBlocks::uksim_sms_to_new');
    }
}