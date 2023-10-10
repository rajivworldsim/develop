<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Controller\Adminhtml\GoogleReviews;

use Magento\Framework\Controller\ResultFactory;
class AddRow extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    /**
     * @var \Agtech\CmsBlocks\Model\JsonDataFactory
     */
    private $callingToNewFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Agtech\CmsBlocks\Model\GoogleReviews\GoogleReviewFactory $googleReviewFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Agtech\CmsBlocks\Model\GoogleReviews\GoogleReviewFactory $googleReviewFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->googleReviewFactory = $googleReviewFactory;
    }
    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->googleReviewFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
           $rowData = $rowData->load($rowId);
           $rowTitle = $rowData->getTitle();
           if (!$rowData->getEntityId()) {
               $this->messageManager->addError(__('Json data no longer exist.'));
               $this->_redirect('agtech_cmsblocks/jsondata/addrow');
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
        return $this->_authorization->isAllowed('Agtech_CmsBlocks::google_reviews');
    }
}