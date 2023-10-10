<?php
/**
 * Ascure AweberSubscription Controller
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com. All rights reserved.
 * @license     https://www.ascuretech.com/license.html
 */
namespace Ascure\AweberSubscription\Controller\Aweber;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ascure\AweberSubscription\Helper\Data;
use League\OAuth2\Client\Provider\GenericProvider;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class GetCredentials extends Action
{
    // OAuth URLs
    const OAUTH_URL = 'https://auth.aweber.com/oauth2/authorize';
    const TOKEN_URL = 'https://auth.aweber.com/oauth2/token';
    const ACCOUNT_URL = 'https://api.aweber.com/1.0/accounts';

    /**
     * @var PageFactory
     */ 
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Pool
     */
    protected $cacheFrontendPool;

    /**
     * GetCredentials constructor.
     *
     * @param Context           $context The context object for the controller.
     * @param PageFactory       $resultPageFactory The factory to create result page.
     * @param Data              $helper The module's helper class.
     * @param TypeListInterface $cacheTypeList
     * @param Pool              $cacheFrontendPool
     */
    public function __construct(
        Context $context, 
        PageFactory $resultPageFactory,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool, 
        Data $helper
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->helper = $helper;
    }

    /**
     * Execute the controller action.
     *
     * @return \Magento\Framework\View\Result\Page The result page.
     */
    public function execute()
    {
        // Get OAuth credentials from helper
        $clientId = $this->helper->getClientId();
        $clientSecret = $this->helper->getClientSecret();
        $redirectUrl = $this->helper->getRedirectUrl();
        
        // Scopes for OAuth authorization
        $scopes = [
            'account.read',
            'list.read',
            'list.write',
            'subscriber.read',
            'subscriber.write',
            'email.read',
            'email.write',
            'subscriber.read-extended',
            'landing-page.read'
        ];

        // Combine scopes into a space-separated string
        $scopeStrings = implode(' ', $scopes);
        
        // Create an OAuth provider instance
        $provider = new GenericProvider([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUrl,
            'scopes' => $scopeStrings,
            'urlAuthorize' => self::OAUTH_URL,
            'urlAccessToken' => self::TOKEN_URL,
            'urlResourceOwnerDetails' => self::ACCOUNT_URL
        ]);

        // Get authorization code and state from the request
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        
        // Exchange authorization code for access token
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        // Store OAuth token and related data in helper
        $this->helper->setAccessToken($token->getToken());
        $this->helper->setRefreshToken($token->getRefreshToken());


        $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            
        // Create and configure the result page
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Thank You so Much, Aweber Authentication is Successful!..'));
        return $resultPage;
    }
}