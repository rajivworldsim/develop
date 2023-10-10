<?php

declare(strict_types=1);

namespace Magedia\StripeIntegration\Controller\Adminhtml\Topup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magedia\StripeIntegration\Model\ResourceModel\Collection\TopupFactory;
use Magedia\StripeIntegration\Model\ResourceModel\Topup as TopupResource;

class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @var TopupFactory
     */
    protected TopupFactory $collectionFactory;

    /**
     * @var TopupResource
     */
    protected TopupResource $topupResource;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @param Context $context
     * @param EventResource $eventResource
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        TopupResource $topupResource,
        TopupFactory $collectionFactory,
        LoggerInterface $logger,
        Filter $filter
    ) {
        $this->topupResource = $topupResource;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $topup) {
            try {
                $this->topupResource->delete($topup);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while deleting the TopUp ID %1', $topup->getEntityId())
                );
                $this->logger->critical($e);
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );

        return $this->resultRedirectFactory->create()
            ->setPath('*/*/index');
    }
}
