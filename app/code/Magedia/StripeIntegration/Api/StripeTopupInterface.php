<?php

namespace Magedia\StripeIntegration\Api;

interface StripeTopupInterface
{

    /**
     * @param string $orderId
     * @return string
     */
    public function checkTopup(string $orderId):string;

}
