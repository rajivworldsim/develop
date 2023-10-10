<?php
namespace Agtech\Loginlog\Logger;


use Monolog\Logger;

class Handlerlog extends \Magento\Framework\Logger\Handler\Base
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
    protected $fileName = '/var/log/TopUpValidation_Error.log';
}