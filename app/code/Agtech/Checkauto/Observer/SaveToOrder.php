<?php 
namespace Agtech\Checkauto\Observer;

class SaveToOrder implements \Magento\Framework\Event\ObserverInterface
{   
     public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $event = $observer->getEvent();
        $quote = $event->getQuote();
        $order = $event->getOrder();
        if($quote->getCreateaccountcust())
        {
            $order->setData('createaccountcust', $quote->getCreateaccountcust());
        }
        if($quote->getPassword())
        {
            $order->setData('password', $quote->getPassword());
        }
        if($quote->getConfpassword())
        {
            $order->setData('confpassword', $quote->getConfpassword());
        }
    }
}