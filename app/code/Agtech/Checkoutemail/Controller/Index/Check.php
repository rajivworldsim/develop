<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Agtech\Checkoutemail\Controller\Index;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\Quote;

class Check extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
private $checkoutSession;
	
	
    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct( \Magento\Framework\App\Action\Context $context,
	 \Magento\Framework\Serialize\Serializer\Json $json,
	 \Magento\Framework\Webapi\Soap\ClientFactory $soapClientFactory,
	  \Magento\Checkout\Model\SessionFactory $checkoutSession,
	  \Magento\Checkout\Model\Session $checkoutModelSession,
	  \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
	)
    {
        parent::__construct($context);
		$this->_json = $json;
		$this->soapClientFactory = $soapClientFactory;
		$this->checkoutSession = $checkoutSession;
		$this->checkoutModelSession = $checkoutModelSession;
        $this->cartRepository  = $cartRepository;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {

		 $query = $this->getRequest()->getContent();
		 $this->_json->unserialize($query);
		 $result = $this->_json->unserialize($query);


		$client = $this->soapClientFactory->create("https://accounts.worldsim.com/services/xmlservice.asmx?wsdl");
    
		$item = new \stdClass();
		$item->CustomerEmail = $result['customerEmail'];
		$crm_emailResult = $client->GetCustomerIDByEmail($item);
		
		$crm_email_result = $crm_emailResult->GetCustomerIDByEmailResult;	
		$getQuoteId = $this->checkoutSession->create()->getQuote();
		
		//MagePlaza Save Customer Email
		$quote = $this->checkoutModelSession->getQuote();
        $cartId = $quote->getId();
		if ($cartId){
			/** @var Quote $quote */
			$quote = $this->cartRepository->getActive($cartId);
			$quote->setCustomerEmail($result['customerEmail']);
			try {
				$this->cartRepository->save($quote);
			} catch (Exception $e) {
				return false;
			}
		}
		

		if($crm_email_result != 'Email-Not-Found'){
			echo "1";
		}
		else{
			if ($getQuoteId->hasProductId(10) || $getQuoteId->hasProductId(11) || $getQuoteId->hasProductId(12) ||$getQuoteId->hasProductId(13) || $getQuoteId->hasProductId(14) || $getQuoteId->hasProductId(15) || $getQuoteId->hasProductId(16) || $getQuoteId->hasProductId(17) || $getQuoteId->hasProductId(37) || $getQuoteId->hasProductId(38) || $getQuoteId->hasProductId(47) || $getQuoteId->hasProductId(332) ) {
					echo "0"; //set 2
			}else{
				echo "0";
			} 
		}
    }
}

