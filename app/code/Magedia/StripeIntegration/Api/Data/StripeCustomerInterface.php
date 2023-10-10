<?php

namespace Magedia\StripeIntegration\Api\Data;

interface  StripeCustomerInterface
{
    public const TABLE_NAME = 'magedia_stripe_users';

    public const ENTITY_ID = 'entity_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const STRIPE_CUSTOMER_ID = 'stripe_customer_id';
    public const SESSION = 'session';

    /**
     * @return string
     */
    public function getCustomerEmail():string;

    /**
     * @param string $customerEmail
     * @return StripeCustomerInterface
     */
    public function setCustomerEmail(string $customerEmail):StripeCustomerInterface;

    /**
     * @return string
     */
    public function getStripeCustomerId():string;

    /**
     * @param string $stripeCustomerId
     * @return StripeCustomerInterface
     */
    public function setStripeCustomerId(string $stripeCustomerId):StripeCustomerInterface;

    /**
     * @return string
     */
    public function getSession():string;

    /**
     * @param string $session
     * @return StripeCustomerInterface
     */
    public function setSession(string $session):StripeCustomerInterface;
}