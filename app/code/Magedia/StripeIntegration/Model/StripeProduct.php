<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model;

use Magento\Framework\Model\AbstractModel;
use Magedia\StripeIntegration\Api\Data\StripeProductInterface;
use Magedia\StripeIntegration\Model\ResourceModel\StripeProduct as StripeProductResourceModel;

class StripeProduct extends AbstractModel implements StripeProductInterface
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StripeProductResourceModel::class);
    }

    /**
     * @return string
     */
    public function getStripeProductId():string {
        return $this->getData(StripeProductInterface::STRIPE_PRODUCT_ID);
    }

    /**
     * @param string $stripeProductId
     * @return StripeProductInterface
     */
    public function setStripeProductId(string $stripeProductId):StripeProductInterface {
        return $this->setData(StripeProductInterface::STRIPE_PRODUCT_ID,$stripeProductId);
    }

    /**
     * @return float
     */
    public function getPrice():float
    {
        return (float) $this->getData(StripeProductInterface::PRICE);
    }

    /**
     * @param float $price
     * @return StripeProductInterface
     */
    public function setPrice(float $price): StripeProductInterface
    {
        return $this->setData(StripeProductInterface::PRICE,$price);
    }

    /**
     * @return string
     */
    public function getCurrency():string {
        return $this->getData(StripeProductInterface::CURRENCY);
    }
    /**
     * @param string $currency
     * @return StripeProductInterface
     */
    public function setCurrency(string $currency):StripeProductInterface {
        return $this->setData(StripeProductInterface::CURRENCY,$currency);
    }

    /**
     * @return string
     */
    public function getTrueStripeProductId(): string
    {
        return $this->getData(StripeProductInterface::TRUE_STRIPE_PRODUCT_ID);
    }

    /**
     * @param string $trueStripeProductId
     * @return StripeProductInterface
     */
    public function setTrueStripeProductId(string $trueStripeProductId): StripeProductInterface
    {
        return $this->setData(StripeProductInterface::TRUE_STRIPE_PRODUCT_ID,$trueStripeProductId);
    }
}