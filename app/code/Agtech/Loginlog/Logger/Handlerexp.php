<?php
namespace Agtech\Loginlog\Logger;


use Monolog\Logger;

class Handlerexp extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/Expired_Numbers_TopUp_Attempt.log';
}