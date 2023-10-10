<?php
namespace Agtech\Checkauto\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;


class SetAdditionalOptions implements ObserverInterface
{
    protected $_request;    
    public function __construct(
        RequestInterface $request, 
        Json $serializer = null
        ) 
    {
        $this->_request = $request;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Check and set information according to your need
        $product = $observer->getProduct();                    
        if ($this->_request->getFullActionName() == 'checkout_cart_add') { //checking when product is adding to cart
            $params = $this->_request->getParams();

            if (isset($params['bonus']) && !empty($params['bonus'])) {
                $additionalOptions = [];
                $additionalOptions[] = array(
                    'label' => "Bonus", //Custom option label
                    'value' => $params['bonus'], //Custom option value
                );                        
                $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
            }
            $product = $observer->getProduct();
            
        }
    }
}