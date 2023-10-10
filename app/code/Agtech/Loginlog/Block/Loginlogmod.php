<?php
namespace Agtech\Loginlog\Block;

class Loginlogmod extends \Magento\Framework\View\Element\Template
{
    /**
     * Logging instance
     * @var \Agtech\Loginlog\Logger\Logger
     */
    protected $_logger;

    /**
     * Constructor
     * @param \Agtech\Loginlog\Logger\Logger $logger
     */
    public function __construct(
        \Agtech\Loginlog\Logger\Logger $logger
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

