<?php

namespace Magedia\StripeIntegration\Api\Data;

interface StripeProductInterface
{
    public const TABLE_NAME = 'magedia_stripe_products';
    public const ENTITY_ID = 'entity_id';
    public const STRIPE_PRODUCT_ID = 'stripe_product_id';
    public const PRICE = 'price';
    public const CURRENCY = 'currency';
    public const TRUE_STRIPE_PRODUCT_ID = "stripe_true_id";

    /**
     * @return string
     */
    public function getStripeProductId():string;

    /**
     * @param string $stripeProductId
     * @return StripeProductInterface
     */
    public function setStripeProductId(string $stripeProductId):StripeProductInterface;

    /**
     * @return float
     */
    public function getPrice():float;

    /**
     * @param float $price
     * @return StripeProductInterface
     */
    public function setPrice(float $price):StripeProductInterface;

    /**
     * @return string
     */
    public function getCurrency():string;

    /**
     * @param string $currency
     * @return StripeProductInterface
     */
    public function setCurrency(string $currency):StripeProductInterface;

    /**
     * @return string
     */
    public function getTrueStripeProductId():string;

    /**
     * @param string $trueStripeProductId
     * @return StripeProductInterface
     */
    public function setTrueStripeProductId(string $trueStripeProductId):StripeProductInterface;

}