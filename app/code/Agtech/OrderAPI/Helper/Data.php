<?php

namespace Agtech\OrderAPI\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Data extends AbstractHelper
{
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        UrlInterface $urlInterface
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->urlInterface = $urlInterface;
        parent::__construct($context);
    }

    public function sendEmail($orderId, $orderNumber, $errorMsg)
    {
        // this is an example and you can change template id,fromEmail,toEmail,etc as per your need.
        $templateId = 'order_needs_to_push_to_crm'; // template id
        $fromEmail = 'shahzad@worldsim.com';  // sender Email id
        $fromName = 'WorldSIM';             // sender Name
        $toEmail = 'shehzad.cs@gmail.com'; // receiver email id

        try {
            
            $resendXMLUrl =  $this->urlInterface->getUrl("agtech_orderapi/index/sendxml", ["id" => $orderId]);
            
            // template variables pass here
            $templateVars = [
                'orderNumber' => $orderNumber,
                'orderPushLink' => $resendXMLUrl,
                'errorMsg' => $errorMsg
            ];

            $storeId = $this->storeManager->getStore()->getId();

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }
}