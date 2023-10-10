<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.7.7
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Controller\Adminhtml\Page;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\SessionServiceInterface;
use Mirasvit\CacheWarmer\Controller\Adminhtml\AbstractPage;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\CurlService;
use Mirasvit\CacheWarmer\Service\WarmRuleService;

class Curl extends AbstractPage
{
    private $curlService;

    private $sessionService;
    
    private $warmRuleRepository;
    
    private $warmRuleService;

    public function __construct(
        WarmRuleRepositoryInterface $warmRuleRepository,
        WarmRuleService $warmRuleService,
        CurlService $curlService,
        PageRepositoryInterface $pageRepository,
        WarmerServiceInterface $warmerService,
        SessionServiceInterface $sessionService,
        Config $config,
        Filter $filter,
        Context $context
    ) {
        $this->curlService        = $curlService;
        $this->sessionService     = $sessionService;
        $this->warmRuleRepository = $warmRuleRepository;
        $this->warmRuleService    = $warmRuleService;

        parent::__construct($pageRepository, $warmerService, $config, $filter, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $page = $this->pageRepository->get($this->getRequest()->getParam(PageInterface::ID));

        $rule = null;

        if ($page && $page->getMainRuleId()) {
            $rule = $this->warmRuleRepository->get($page->getMainRuleId());
        }

        $page = $this->warmRuleService->modifyPage($page, $rule);

        $channel = $this->curlService->initChannel();

        $channel->setUrl($page->getUri());
        $channel->setUserAgent($page->getUserAgent());

        $cookies = $this->sessionService->getCookies($page);
        $channel->addCookies($cookies);

        $channel->setHeaders($page->getHeaders());

        $this->messageManager->addNoticeMessage(
            $channel->getCUrl()
        );

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
