<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Controller\Adminhtml\Ratesheetdatabundle;

use Worldsim\Databundle\Model\ResourceModel\Ratesheetdatabundle\CollectionFactory;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\File\Csv;
use Magento\Framework\View\Result\PageFactory;

class Csvsave extends Action
{
    protected $csv; 
    protected $collectionFactory;

    protected $resultPageFactory = false;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Csv $csv,
        CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->csv = $csv;
        $this->collectionFactory = $collectionFactory;
        $this->resultPageFactory = $resultPageFactory;
    }


    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();


        if (isset($_FILES['importcsv']) && isset($_FILES['importcsv']['tmp_name']) && ($_FILES['importcsv']['size'] > 0 ))
        {
            $collection = $this->collectionFactory->create();
            foreach($collection as $item){
                $item->delete();
            }
            $csvData = $this->csv->getData($_FILES['importcsv']['tmp_name']);
            $UploadedHeader = array_map("str_getcsv", file($_FILES['importcsv']['tmp_name'],FILE_SKIP_EMPTY_LINES));
            $UploadedHeaderKey = array_shift($UploadedHeader);
            $csvActualHeader = $UploadedHeaderKey;
            $csvActualHeaderResult = array_diff($UploadedHeaderKey, $csvActualHeader);

            $planPrice = [];
            foreach ($csvData as $row => $data) {    
                if ($row > 0) {
                    $rows = [];
                    $count = 0 ;

                    foreach($data as $key => $csvData){
                        switch ($csvActualHeader[$key]) {
                          case "1GB":
                            $rows["onegb"] = $csvData;
                            break;
                          case "3GB":
                            $rows["threegb"] = $csvData;
                            break;
                          case "5GB":
                            $rows["fivegb"] = $csvData;
                            break;
                          case "6GB":
                            $rows["sixgb"] = $csvData;
                            break;
                          case "10 GB ":
                            $rows["tengb"] = $csvData;
                            break;
                          case "20GB":
                            $rows["twenty"] = $csvData;
                            break;
                          case "1GB Code":
                            $rows["onegbcode"] = $csvData;
                            break;
                          case "3GB Code":
                            $rows["threegbcode"] = $csvData;
                            break;
                          case "5GB Code":
                            $rows["fivegbcode"] = $csvData;
                            break;
                          case "6GB Code":
                            $rows["sixgbcode"] = $csvData;
                            break;
                          case "10 GB Code":
                            $rows["tengbcode"] = $csvData;
                            break;
                          case "20 GB Code":
                            $rows["twentygbcode"] = $csvData;
                            break;
                          case "Unlimited GB Code":
                            $rows["unlimitedgbcode"] = $csvData;
                            break;
                          default:
                            $rows[$csvActualHeader[$key]] = $csvData;
                        }
                    }

                    $model = $this->_objectManager->create(\Worldsim\Databundle\Model\RateSheetDataBundle::class);
                    $model->setData($rows);

                    try {
                        $model->save();
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
                    }
                }
            }
        }
        else 
        {
            $this->messageManager->addError('Please insert csv file');
            return $resultRedirect->setPath('*/*/importcsv');
        }
        
        $this->messageManager->addSuccessMessage(__('You saved the Rate Sheet Data Bundle.'));
        return $resultRedirect->setPath('*/*/importcsv');
    }

}
