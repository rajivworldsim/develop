<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Controller\Adminhtml\JsonData;

use Magento\Backend\App\Action;
use \Agtech\CmsBlocks\Model\JsonDataFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Agtech\CmsBlocks\Model\JsonDataFactory
     */
    protected $jsonDataFactory;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Agtech\CmsBlocks\Model\JsonDataFactory $jsonDataFactory
     * @param \Magento\Framework\File\Csv $csv
     */

    public function __construct(
        Action\Context $context,
        JsonDataFactory $jsonDataFactory,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Filesystem $filesystem,
    ) {
        parent::__construct($context);
        $this->jsonDataFactory = $jsonDataFactory;
        $this->csv = $csv;
        $this->_filesystem = $filesystem;

    }

   
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $jsonData = $this->jsonDataFactory->create();
        $jsonHeaders = array();
        $filePath = $data['importfile'][0]['url'];
        $filePath = substr($filePath, strpos($filePath, "/media") + 6);
        $mediaPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($filePath);
        $csvData = $this->csv->getData($mediaPath);
        foreach ($csvData as $data) {
        if($data[0]){
                if($data[0]=="id"){
                    $jsonHeaders = $data;
                }else{
                    $jsonArray[$data[0]] = array_combine($jsonHeaders, $data);
                }
            }
        }
        if(isset($jsonArray)){
            foreach ($jsonArray as $jsonArrayData) {
                $jsonData->setData($jsonArrayData)->save();
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            
            $this->messageManager->addSuccess(__('The data has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                if ($this->getRequest()->getParam('back') == 'add') {
                    return $resultRedirect->setPath('*/*/add');
                } else {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->jsonDataFactory->getEntityId(), '_current' => true]);
                }
            }

            return $resultRedirect->setPath('*/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
        }
        return $resultRedirect->setPath('*/*/');
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Agtech_CmsBlocks::jsondata');
    }
}

