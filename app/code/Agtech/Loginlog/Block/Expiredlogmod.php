<?php
namespace Agtech\Loginlog\Block;

class Expiredlogmod extends \Magento\Framework\View\Element\Template
{
    /**
     * Logging instance
     * @var \Agtech\Loginlog\Logger\Expiredlogger
     */
    protected $_logger;

    /**
     * Constructor
     * @param \Agtech\Loginlog\Logger\Expiredlogger $logger
     */
    public function __construct(
        \Agtech\Loginlog\Logger\Expiredlogger $logger
    ) {
        $this->_logger = $logger;
    }
	
	 /**
		@param string  $message
	*/
    public function messageLog($message)
    {
        $this->_logger->info($message);
    }
}

