<?php
namespace Agtech\Loginlog\Block;

class Topuploggermod extends \Magento\Framework\View\Element\Template
{
    /**
     * Logging instance
     * @var \Agtech\Loginlog\Logger\Topuplogger
     */
    protected $_logger;

    /**
     * Constructor
     * @param \Agtech\Loginlog\Logger\Topuplogger $logger
     */
    public function __construct(
        \Agtech\Loginlog\Logger\Topuplogger $logger
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

