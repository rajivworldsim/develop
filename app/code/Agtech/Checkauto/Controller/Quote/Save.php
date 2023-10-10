<?php

namespace Agtech\Checkauto\Controller\Quote;
class Save extends \Magento\Framework\App\Action\Action
{
    protected $quoteRepository;
    private $_checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {

        $post = $this->getRequest()->getPostValue();
        if ($post) {
            $cartId       = $post['cartId'];
            $createaccountcust = $post['createaccountcust'];
            $password = $post['password'];
            $confpassword = $post['confpassword'];
            $loggin       = $post['is_customer'];
            $quote = $this->_checkoutSession->getQuote();
            if($quote && $quote->getId())
            {
                if (!$quote->getItemsCount()) {
                    throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
                }
                $quote->setCreateaccountcust($createaccountcust);
                $quote->setPassword($password);
                $quote->setConfpassword($confpassword);
                $this->quoteRepository->save($quote);
                
            }
        }
    }
}