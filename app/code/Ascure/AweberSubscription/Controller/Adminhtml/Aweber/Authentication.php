<?php
/**
 * Ascure AweberSubscription Admin Controller
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com. All rights reserved.
 * @license     https://www.ascuretech.com/license.html
 */
declare(strict_types=1);

namespace Ascure\AweberSubscription\Controller\Adminhtml\Aweber;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Ascure\AweberSubscription\Helper\Data;
use Magento\Framework\Controller\Result\Json;
use League\OAuth2\Client\Provider\GenericProvider;

class Authentication extends Action
{
    // OAuth URLs
    const OAUTH_URL = 'https://auth.aweber.com/oauth2/authorize';
    const TOKEN_URL = 'https://auth.aweber.com/oauth2/token';
    const ACCOUNT_URL = 'https://api.aweber.com/1.0/accounts';

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
    }

    /**
     * Execute view action
     *
     * @return Json
     */
    public function execute(): Json
    {
        try {
            // Get OAuth client credentials
            $clientId = $this->helper->getClientId();
            $clientSecret = $this->helper->getClientSecret();
            $redirectUrl = $this->helper->getRedirectUrl();

            // Define OAuth scopes
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

            // Convert scopes to a space-separated string
            $scopeStrings = implode(' ', $scopes);

            // Create a Generic OAuth provider instance
            $provider = new GenericProvider([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUrl,
                'scopes' => $scopeStrings,
                'urlAuthorize' => self::OAUTH_URL,
                'urlAccessToken' => self::TOKEN_URL,
                'urlResourceOwnerDetails' => self::ACCOUNT_URL
            ]);

            // Get the authorization URL
            $authorizationUrl = $provider->getAuthorizationUrl();

            $content = [
                'success' => true,
                'message' => 'Authentication successful',
                'authorizationUrl' => $authorizationUrl
            ];
        } catch (\Exception $e) {
            // Handle exceptions
            $content = [
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ];
        }

        // Create a JSON response
        $result = $this->jsonFactory->create();
        $result->setData($content);

        return $result;
    }
}
