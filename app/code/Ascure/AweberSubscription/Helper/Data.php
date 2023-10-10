<?php
/**
 * Ascure AweberSubscription Helper.
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com. All rights reserved.
 * @license     https://www.ascuretech.com/license.html
 */
namespace Ascure\AweberSubscription\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface; 

/**
 * AweberSubscription module helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_AWEBER_ENABLED = 'ascure_awebersubscription/general/enabled';
    const XML_PATH_AWEBER_CLIENT_ID = 'ascure_awebersubscription/general/client_id';
    const XML_PATH_AWEBER_CLIENT_SECRET = 'ascure_awebersubscription/general/client_secret';
    const XML_PATH_AWEBER_REDIRECT_URL = 'ascure_awebersubscription/general/redirect_url';
    const XML_PATH_AWEBER_ACCESS_TOKEN = 'ascure_awebersubscription/credentials/access_token';
    const XML_PATH_AWEBER_REFRESH_TOKEN = 'ascure_awebersubscription/credentials/refresh_token';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     */
    public function __construct(Context $context, ScopeConfigInterface $scopeConfig, WriterInterface $configWriter)
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_AWEBER_ENABLED);
    }

    /**
     * Get Aweber Client ID
     *
     * @return string|null
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AWEBER_CLIENT_ID);
    }

    /**
     * Get Aweber Client Secret
     *
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AWEBER_CLIENT_SECRET);
    }

    /**
     * Get Aweber Redirect Url
     *
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AWEBER_REDIRECT_URL);
    }

    /**
     * Get Aweber Access Token
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AWEBER_ACCESS_TOKEN);
    }

    /**
     * Set Aweber Access Token
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->configWriter->save(self::XML_PATH_AWEBER_ACCESS_TOKEN, $accessToken);
    }

    /**
     * Get Aweber Refresh Token
     *
     * @return string|null
     */
    public function getRefreshToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AWEBER_REFRESH_TOKEN);
    }

    /**
     * Set Aweber Refresh Token
     *
     * @param string $refreshToken
     * @return void
     */
    public function setRefreshToken($refreshToken)
    {
        $this->configWriter->save(self::XML_PATH_AWEBER_REFRESH_TOKEN, $refreshToken);
    }

    /**
     * Get all of the entries for a collection
     *
     * @param Client $client HTTP Client used to make a GET request
     * @param string $accessToken Access token to pass in as an authorization header
     * @param string $url Full url to make the request
     * @return array Every entry in the collection
     */
    public function getCollection($client, $accessToken, $url)
    {
        $collection = array();
        while (isset($url)) {
            $request = $client->get(
                $url,
                ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
            );
            $body = $request->getBody()->__toString();
            $page = json_decode($body, true);
            $collection = array_merge($page['entries'], $collection);
            $url = isset($page['next_collection_link']) ? $page['next_collection_link'] : null;
        }
        return $collection;
    }

}