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
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use GuzzleHttp\Client;

class Refresh extends Action
{
    // Token URL for refreshing access token
    const TOKEN_URL = 'https://auth.aweber.com/oauth2/token';

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Pool
     */
    protected $cacheFrontendPool;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param Data $helper
     * @param Client $httpClient
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Data $helper,
        Client $httpClient,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
        $this->httpClient = $httpClient;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
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

            // Make a POST request to refresh the access token
            $response = $this->httpClient->post(
                self::TOKEN_URL,
                [
                    'auth' => [
                        $clientId,
                        $clientSecret
                    ],
                    'json' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $this->helper->getRefreshToken()
                    ],
                ]
            );

            // Parse the response body
            $body = $response->getBody()->__toString();
            $newCreds = json_decode($body, true);

            // Update the access and refresh tokens
            $this->helper->setAccessToken($newCreds['access_token']);
            $this->helper->setRefreshToken($newCreds['refresh_token']);


            $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }

            $content = [
                'success' => true,
                'message' => 'Refresh successful',
            ];
        } catch (\Exception $e) {
            // Handle exceptions
            $content = [
                'success' => false,
                'message' => 'Refresh failed: ' . $e->getMessage()
            ];
        }

        // Create a JSON response
        $result = $this->jsonFactory->create();
        $result->setData($content);

        return $result;
    }
}
