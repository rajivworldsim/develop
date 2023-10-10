<?php
/**
 * Ascure AweberSubscription Cron
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com. All rights reserved.
 * @license     https://www.ascuretech.com/license.html
 */
declare(strict_types=1);

namespace Ascure\AweberSubscription\Cron;

use Ascure\AweberSubscription\Helper\Data;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class RefreshToken
{
    // Token URL for refreshing access token
    const TOKEN_URL = 'https://auth.aweber.com/oauth2/token';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    
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
     * @param Data $helper
     * @param Client $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        Client $httpClient,
        LoggerInterface $logger,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        $this->helper = $helper;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * Execute cron job
     *
     * @return void
     */
    public function execute()
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

            // Log success
            $this->logger->info('Token refresh successful.');
        } catch (\Exception $e) {
            // Log error and exception details
            $this->logger->error('Token refresh failed: ' . $e->getMessage());
        }
    }
}
