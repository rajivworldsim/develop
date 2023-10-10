<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Plugin;

use StripeIntegration\Payments\Model\PaymentMethod;
use \Magento\Payment\Model\InfoInterface;

class BeforeCapture
{
    /**
     * @param PaymentMethod $subject
     * @param InfoInterface $payment
     * @return void
     */
    public function beforePayWithServerSideConfirmation(PaymentMethod $subject, InfoInterface $payment){
        $order = $payment->getOrder();
        foreach ($order->getItems() as $item){
            if ($item->getProductId()==16){
                $payment->setAdditionalInformation("is_recurring_subscription", true);
            }
        }

    }
}