<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Controller\Adminhtml\SmsToNew;

use Magento\Framework\Controller\ResultFactory;

class CsvUploder extends \Magento\Framework\App\Action\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Agtech_CmsBlocks::uksim_calling_to_new';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Agtech\CmsBlocks\Model\csvUploder
     */
    protected $csvUploder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $baseTmpPath;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Agtech\CmsBlocks\Model\CsvUploder $csvUploder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Agtech\CmsBlocks\Model\CsvUploder $csvUploder,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultFactory = $context->getResultFactory();
        $this->csvUploder = $csvUploder;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $file=$this->getRequest()->getPostValue();
            $files=$file['param_name'];
            $result = $this->csvUploder->saveFileToTmpDir($files);
            $this->baseTmpPath = $this->csvUploder->getBaseTmpPath();
            $file_path = $this->baseTmpPath . '/' . $result['file'];
            } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
