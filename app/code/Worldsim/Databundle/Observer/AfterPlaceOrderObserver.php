<?php
namespace Worldsim\Databundle\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Worldsim\Databundle\Model\ResourceModel\GoAPIResponse\CollectionFactory;

class SaveApiResponse implements ObserverInterface
{
    protected $request;
    protected $goAPIResponseCollectionFactory;

    public function __construct(
        RequestInterface $request,
        CollectionFactory $goAPIResponseCollectionFactory
    ) {
        $this->request = $request;
        $this->goAPIResponseCollectionFactory = $goAPIResponseCollectionFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderEmail = $order->getCustomerEmail();
        $cookieData = json_decode($this->request->getCookie('GoSimAPIResponse'), true);
        $isNewSim = 0;
        // Save the data to the grid
        if ($cookieData) {
            $apiResponse = $this->goAPIResponseCollectionFactory->create();
            
            $apiResponse->setOrderId($order->getId());
            $apiResponse->setIccid($cookieData['iccid']);
            $apiResponse->setEmail($orderEmail);
            $apiResponse->setBundleCode($cookieData['bundle_code']);
            $apiResponse->setIsNewSim($isNewSim);
            $apiResponse->setPin($cookieData['pin']);
            $apiResponse->setPuk($cookieData['puk']);
            $apiResponse->setMatchingId($cookieData['matchingId']);
            $apiResponse->setSmdpAddress($cookieData['smdpAddress']);
            $apiResponse->setProfileStatus($cookieData['profileStatus']);
            $apiResponse->setFirstinstalldate($cookieData['firstInstalledDateTime']);
            $apiResponse->setCustomerRef($cookieData['customerRef']);

            $apiResponse->save($apiResponse);
        }
    }
}
