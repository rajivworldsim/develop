<?php

namespace Magedia\StripeIntegration\Api\Data;

interface TopupInterface
{

    public const TABLE_NAME = 'magedia_stripe_subscriptions';

    public const ENTITY_ID = 'entity_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const ORDER_INCREMENT_ID = 'order_increment_id';
    public const COUNT = 'amount';
    public const CURRENCY = 'currency';
    public const STRIPE_PAYMENT_METHOD = 'stripe_payment_method';
    public const STRIPE_CUSTOMER_ID = 'stripe_customer_id';
    public const STRIPE_PAYMENT_INTENT = 'stripe_payment_intent';
    public const USUALLY_PAYMENT = 'usually_payment';

    /**
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * @param string $customerEmail
     * @return TopupInterface
     */
    public function setCustomerEmail(string $customerEmail):TopupInterface;

    /**
     * @return string
     */
    public function getStripePaymentMethod():string;

    /**
     * @param string $stripePaymentMethod
     * @return TopupInterface
     */
    public function setStripePaymentMethod(string $stripePaymentMethod):TopupInterface;

    /**
     * @param string $stripeCustomerId
     * @return TopupInterface
     */
    public function setStripeCustomerId(string $stripeCustomerId):TopupInterface;

    /**
     * @return string
     */
    public function getStripeCustomerId():string;

    /**
     * @param string $stripePaymentIntent
     * @return TopupInterface
     */
    public function setStripePaymentIntent(string $stripePaymentIntent):TopupInterface;

    /**
     * @return string
     */
    public function getStripePaymentIntent():string;

    /**
     * @return string
     */
    public function getOrderIncrementId():string;

    /**
     * @param string $orderIncrementId
     * @return TopupInterface
     */
    public function setOrderIncrementId(string $orderIncrementId): TopupInterface;

    /**
     * @return float
     */
    public function getCount():float;

    /**
     * @param float $count
     * @return TopupInterface
     */
    public function setCount(float $count):TopupInterface;

    /**
     * @return string
     */
    public function getCurrency():string;

    /**
     * @param string $currency
     * @return TopupInterface
     */
    public function setCurrency(string $currency): TopupInterface;

    /**
     * @return float
     */
    public function getUsuallyPayment():float;

    /**
     * @param float $usuallyPayment
     * @return TopupInterface
     */
    public function setUsuallyPayment(float $usuallyPayment): TopupInterface;

}
