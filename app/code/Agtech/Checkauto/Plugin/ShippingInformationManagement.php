<?php

namespace Agtech\Checkauto\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;

class ShippingInformationManagement
{
    public $cartRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    public function beforeSaveAddressInformation($subject, $cartId, $addressInformation)
    {
        $quote = $this->cartRepository->getActive($cartId);
        $createaccountcust = $addressInformation->getShippingAddress()->getExtensionAttributes()->getCreateaccountcust();
        $password = $addressInformation->getShippingAddress()->getExtensionAttributes()->getPassword();
        $confpassword = $addressInformation->getShippingAddress()->getExtensionAttributes()->getConfpassword();
        $quote->setCreateaccountcust($createaccountcust);
        $quote->setPassword($password);
        $quote->setConfpassword($confpassword);
        $this->cartRepository->save($quote);
        return [$cartId, $addressInformation];
    }
}