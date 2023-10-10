<?php
namespace Agtech\Checkauto\Observer;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class OrderPlacebefore implements ObserverInterface
{
   public $objectCopyService;

    public function __construct(
        Copy $objectCopyService
    ) {
        $this->objectCopyService = $objectCopyService;
    }

    public function execute(Observer $observer)
    {
        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_quote',
            'to_order',
            $observer->getEvent()->getQuote(),
            $observer->getEvent()->getOrder()
        );

        return $this;
    }


    // protected $_inputParamsResolver;
    // protected $_quoteRepository;
 // protected $logger;
    // public function __construct(\Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver, 
        // \Magento\Quote\Model\QuoteRepository $quoteRepository, 
         // \Psr\Log\LoggerInterface $logger
		// ) 
    // {
        // $this->_inputParamsResolver = $inputParamsResolver;
        // $this->_quoteRepository = $quoteRepository;
          // $this->logger = $logger;
        // }
// public function execute(EventObserver $observer)
    // {
   
    /** @var \Magento\Quote\Model\Quote $quote */
    /* $quote = $this->_quoteRepository->get($order->getQuoteId());
	  $this->logger->info(print_r($quote,true)); */
    // $order->setSampleText( $quote->getSampleText() ); // Similarlly you can get other order data like this

    // Save this in Customer Session 
    
    // $myArray = array('value1','value2');
    // $setValue = $this->customerSession->setMyValue($myArray); //set value in customer session
    

    // To Save data in Checkout Session Follow this
       
    // $this->checkoutSession->setCustomerData($myArray);
    // $this->checkoutSession->setAnyVar("Hello There"); // You can get it the same way in success page - 
    


    // }
}