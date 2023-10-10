<?php

declare(strict_types=1);

namespace Agtech\Elasticsearchmysqllegacy\Model\Client;

use Magento\AdvancedSearch\Model\Client\ClientInterface;

/**
 * AgtechMysql client
 */
class AgtechMysql implements ClientInterface
{
    /**
     * @var bool
     */
    private $pingResult;


    /**
     * Ping
     *
     * @return bool
     */
    public function ping() : bool
    {
        if ($this->pingResult === null) {
            $this->pingResult = true;
        }

        return $this->pingResult;
    }

    /**
     * Validate connection
     *
     * @return bool
     */
    public function testConnection() : bool
    {
        return $this->ping();
    }
}
