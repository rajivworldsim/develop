<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model;

use Magento\Framework\Model\AbstractModel;
use Magedia\StripeIntegration\Api\Data\StripeCustomerInterface;
use Magedia\StripeIntegration\Model\ResourceModel\StripeCustomer as StripeCustomerResourceModel;

class StripeCustomer extends AbstractModel implements StripeCustomerInterface
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StripeCustomerResourceModel::class);
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->getData(StripeCustomerInterface::CUSTOMER_EMAIL);
    }

    /**
     * @param string $customerEmail
     * @return StripeCustomerInterface
     */
    public function setCustomerEmail(string $customerEmail): StripeCustomerInterface
    {
        return $this->setData(StripeCustomerInterface::CUSTOMER_EMAIL);
    }

    /**
     * @return string
     */
    public function getStripeCustomerId(): string
    {
        return $this->getData(StripeCustomerInterface::STRIPE_CUSTOMER_ID);
    }

    /**
     * @param string $stripeCustomerId
     * @return StripeCustomerInterface
     */
    public function setStripeCustomerId(string $stripeCustomerId): StripeCustomerInterface
    {
        return $this->setData(StripeCustomerInterface::STRIPE_CUSTOMER_ID,$stripeCustomerId);
    }

    /**
     * @return string
     */
    public function getSession(): string
    {
        return $this->getData(StripeCustomerInterface::SESSION);
    }

    /**
     * @param string $session
     * @return StripeCustomerInterface
     */
    public function setSession(string $session): StripeCustomerInterface
    {
        return $this->setData(StripeCustomerInterface::SESSION,$session);
    }
}

