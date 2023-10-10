<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model;

use Magento\Framework\Model\AbstractModel;
use Magedia\StripeIntegration\Api\Data\TopupInterface;
use Magedia\StripeIntegration\Model\ResourceModel\Topup as TopupResourceModel;

class Topup extends AbstractModel implements TopupInterface
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(TopupResourceModel::class);
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @param string $customerEmail
     * @return TopupInterface
     */
    public function setCustomerEmail(string $customerEmail): TopupInterface
    {
        return $this->setData(self::CUSTOMER_EMAIL,$customerEmail);
    }

    /**
     * @return string
     */
    public function getOrderIncrementId(): string
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * @param string $orderIncrementId
     * @return TopupInterface
     */
    public function setOrderIncrementId(string $orderIncrementId): TopupInterface
    {
        return $this->setData(self::ORDER_INCREMENT_ID,$orderIncrementId);
    }

    /**
     * @return float
     */
    public function getCount(): float
    {
        return (float)$this->getData(self::COUNT);
    }

    /**
     * @param int $count
     * @return TopupInterface
     */
    public function setCount(float $count): TopupInterface
    {
        return $this->setData(self::COUNT, $count);
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @param string $currency
     * @return TopupInterface
     */
    public function setCurrency(string $currency): TopupInterface
    {
        return $this->setData(self::CURRENCY,$currency);
    }

    /**
     * @return string
     */
    public function getStripePaymentMethod(): string
    {
        return $this->getData(self::STRIPE_PAYMENT_METHOD);
    }

    /**
     * @param string $stripePaymentMethod
     * @return TopupInterface
     */
    public function setStripePaymentMethod(string $stripePaymentMethod): TopupInterface
    {
        return $this->setData(self::STRIPE_PAYMENT_METHOD,$stripePaymentMethod);
    }

    /**
     * @param string $stripeCustomerId
     * @return TopupInterface
     */
    public function setStripeCustomerId(string $stripeCustomerId): TopupInterface
    {
        return $this->setData(self::STRIPE_CUSTOMER_ID,$stripeCustomerId);
    }

    /**
     * @return string
     */
    public function getStripeCustomerId(): string
    {
        return $this->getData(self::STRIPE_CUSTOMER_ID);
    }

    /**
     * @param string $stripePaymentIntent
     * @return TopupInterface
     */
    public function setStripePaymentIntent(string $stripePaymentIntent): TopupInterface
    {
        return $this->setData(self::STRIPE_PAYMENT_INTENT,$stripePaymentIntent);
    }

    /**
     * @return string
     */
    public function getStripePaymentIntent(): string
    {
        return $this->getData(self::STRIPE_PAYMENT_INTENT);
    }

    /**
     * @param float $usuallyPayment
     * @return TopupInterface
     */
    public function setUsuallyPayment(float $usuallyPayment): TopupInterface
    {
        return  $this->setData(TopupInterface::USUALLY_PAYMENT,$usuallyPayment);
    }

    /**
     * @return float
     */
    public function getUsuallyPayment(): float
    {
        return (float)$this->getData(TopupInterface::USUALLY_PAYMENT);
    }
}