<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magedia\StripeIntegration\Model\TopupFactory;
use Magedia\StripeIntegration\Model\ResourceModel\Topup;
class TopupObserver implements ObserverInterface
{
    /**
     * @var TopupFactory
     */
    private TopupFactory $topupFactory;

    /**
     * @var Topup
     */
    private Topup $topup;
    public function __construct(
        TopupFactory $topupFactory,
        Topup $topup
    ){
        $this->topup = $topup;
        $this->topupFactory = $topupFactory;

    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        if (count($order->getItems())==1 && $order->getItemsCollection()->getFirstItem()->getProductId()==16 ){
            $topUpModel = $this->topupFactory->create();
            $topUpModel->setCount($order->getTotalInvoiced());
            $topUpModel->setCurrency($order->getOrderCurrency()->getCode());
            $topUpModel->setOrderIncrementId($order->getIncrementId());
            $topUpModel->setCustomerEmail($order->getAddresses()[0]->getEmail());
            $this->topup->save($topUpModel);




        }
    }
}
