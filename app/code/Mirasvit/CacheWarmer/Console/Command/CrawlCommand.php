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



namespace Mirasvit\CacheWarmer\Console\Command;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\StateFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterfaceFactory;
use Mirasvit\CacheWarmer\Api\Data\SourceInterface;
use Mirasvit\CacheWarmer\Api\Repository\SourceRepositoryInterface;
use Mirasvit\CacheWarmer\Service\CrawlServiceFactory;
use Mirasvit\CacheWarmer\Service\SessionServiceFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlCommand extends \Symfony\Component\Console\Command\Command
{
    const CYCLE_LIMIT = 100;

    /**
     * Use in preg_match
     * @var array
     */
    private $ignorePatternPool
        = [
            '\\/customer',
            '\\/checkout',
            '\\/catalogsearch',
            '\\/wishlist',
            '\\/sendfriend',
            '\\/downloadable',
        ];

    /**
     * @var array
     */
    private $pool = [];

    /**
     * @var array
     */
    private $hostPool = [];

    private $appStateFactory;

    private $objectManager;

    private $storeManagerFactory;

    private $crawlServiceFactory;

    private $sessionServiceFactory;

    private $sourceRepository;
    
    private $customerCollectionFactory;

    public function __construct(
        StateFactory $appStateFactory,
        ObjectManagerInterface $objectManager,
        StoreManagerInterfaceFactory $storeManagerFactory,
        CrawlServiceFactory $crawlServiceFactory,
        SessionServiceFactory $sessionServiceFactory,
        SourceRepositoryInterface $sourceRepository,
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->appStateFactory           = $appStateFactory;
        $this->objectManager             = $objectManager;
        $this->storeManagerFactory       = $storeManagerFactory;
        $this->crawlServiceFactory       = $crawlServiceFactory;
        $this->sessionServiceFactory     = $sessionServiceFactory;
        $this->sourceRepository          = $sourceRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:cache-warmer:crawl')
            ->setDescription('Crawl all pages');

        $this->addOption('base-url', null, InputOption::VALUE_REQUIRED, 'Set base url');
        $this->addOption('store-id', null, InputOption::VALUE_REQUIRED, 'Set store id');
        $this->addOption('customer-group-id', null, InputOption::VALUE_REQUIRED, 'Set customer group id');

        $this->addOption('ignore-query', null, InputOption::VALUE_NONE, 'Ignore links with query params (?)');
        $this->addOption('ignore-http', null, InputOption::VALUE_NONE, 'Ignore links with http');
        $this->addOption('ignore-https', null, InputOption::VALUE_NONE, 'Ignore links with https');

        $this->addOption(
            'cycle-limit',
            null,
            InputOption::VALUE_REQUIRED,
            'The number of cycles (default value ' . self::CYCLE_LIMIT . ')'
        );

        $this->addOption('unlock', null, InputOption::VALUE_NONE, 'Unlock');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('unlock')) {
            $this->unlock();
        }

        if ($this->isLocked()) {
            $output->writeln('<comment>Current process is running or was finished incorrectly.</comment>');
            $output->writeln('To unlock run with option "--unlock"');

            return 0;
        }

        $this->lock();

        try {
            $this->appStateFactory->create()->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        /** @var SourceInterface $crawlerSource */
        $crawlerSource = $this->sourceRepository->getCrawlerSource();

        /** @var SourceInterface $defaultSource */
        $defaultSource = $this->sourceRepository->getDefaultSource();

        if (
            !$crawlerSource 
            && (!$defaultSource || !$defaultSource->getIsActive())
        ) {
            $output->writeln('<comment>No active sources are present for crawling URLs.</comment>');
            $output->writeln('<comment>The Default Source (Visitors\' actions) or a Crawler Source should be active.</comment>');
            $output->writeln('<error>Crawling aborted.</error>');

            $this->unlock();
            return 0;
        }

        if ($crawlerSource && $crawlerSource->getId()) { // unset last_sync_at for crawler source when crawler running
            $crawlerSource->setLastSyncronizedAt(null);
            $this->sourceRepository->save($crawlerSource);
        }

        $cycleLimit = $input->getOption('cycle-limit') ? : self::CYCLE_LIMIT;
        $output->writeln("Current 'cycle_limit' is $cycleLimit");

        $customerGroupId   = $input->getOption('customer-group-id') ? : 0;
        $sessionDataCookie = false;
        
        if ($customerGroupId) {
            $customersCollection = $this->customerCollectionFactory->create();
            $customersCollection->addFieldToFilter('group_id', $customerGroupId);
            
            if (!$customersCollection->count()) {
                $output->writeln('<comment>No such customer group with ID: ' . $customerGroupId . ' or no customers present in this customer group.</comment>');
                $output->writeln('<error>Crawling aborted.</error>');

                $this->unlock();
                return 0;
            }
            
            $sessionData = [
                'customer_group' => $customerGroupId,
                'customer_logged_in' => 1,
            ];
            $sessionDataCookie = $this->sessionServiceFactory->create()->getSessionCookie($sessionData, 0, 0);
        }

        $this->initializePool($input);

        $storeId   = $input->getOption('store-id');
        $storeCode = false;

        if ($storeId) {
            $store     = $this->storeManagerFactory->create()->getStore($storeId);
            $storeCode = $store->getCode();
        }

        $idx   = 0;
        $cycle = 0;

        $crawlService = $this->crawlServiceFactory->create();
        while (true) {
            $cycle++;

            if (count($this->pool) == $idx) {
                $output->writeln('<info>All URLs were crawled!</info>');
                break;
            }

            if ($cycle > $cycleLimit) {
                $output->writeln('<comment>Done</comment>');
                break;
            } else {
                $output->writeln("<comment>Cycle $cycle</comment>");
            }

            foreach ($this->pool as $url => $level) {
                if ($level === 0) {
                    continue;
                }

                //ignore store switch urls to avoid reseting store cookie
                if (strpos($url, 'stores/store/redirect') !== false) {
                    continue;
                }

                $idx++;

                $memoryUsage = round(memory_get_usage() / 1048576, 2);

                $output->writeln(sprintf(
                    '%s/%s %s %s (memory usage: %s Mb)',
                    $idx,
                    count($this->pool),
                    $level,
                    $url,
                    $memoryUsage
                ));

                $urls = $crawlService->getUrls($url, $sessionDataCookie, $output, false, $storeCode);
                foreach ($urls as $newUrl) {
                    $this->addUrlToPool($input, $newUrl, $level + 1);
                }

                $this->pool[$url] = 0;
            }
        }

        $this->unlock();
        
        return 0;
    }

    /**
     * @return void
     */
    private function unlock()
    {
        $lockFile = $this->getLockFile();
        if (is_file($lockFile)) {
            unlink($lockFile);
        }
    }

    /**
     * @return string
     */
    private function getLockFile()
    {
        $tmpPath  = $this->objectManager
            ->get(\Mirasvit\CacheWarmer\Model\Config::class)
            ->getTmpPath();
        $lockFile = $tmpPath . '/cache-warmer.cli.crawl.lock';

        return $lockFile;
    }

    /**
     * @return bool
     */
    private function isLocked()
    {
        $lockFile = $this->getLockFile();
        if (file_exists($lockFile)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function lock()
    {
        $lockFile = $this->getLockFile();

        $lockPointer = fopen($lockFile, "w");
        fwrite($lockPointer, date('c'));
        fclose($lockPointer);

        return true;
    }

    /**
     * @param InputInterface $input
     * @return $this
     */
    private function initializePool(InputInterface $input)
    {
        $storeId = $input->getOption('store-id');
        $baseUrl = $input->getOption('base-url');
        $storeManager = $this->storeManagerFactory->create();
        $baseUrls = [];
        if ($storeId) {
            /** @var \Magento\Store\Model\Store $store */
            $store      = $storeManager->getStore($storeId);
            $baseUrls[] = $store->getBaseUrl();
        } elseif ($baseUrl) {
            $baseUrls[] = $baseUrl;
        } else {
            /** @var \Magento\Store\Model\Store $store */
            foreach ($storeManager->getStores() as $store) {
                $baseUrls[] = $store->getBaseUrl();
            }
        }

        foreach ($baseUrls as $url) {
            $this->hostPool[] = parse_url($url, PHP_URL_HOST);
            $this->addUrlToPool($input, $url, 1);
        }

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param string         $url
     * @param int            $level
     * @return bool
     */
    private function addUrlToPool(InputInterface $input, $url, $level)
    {
        if (isset($this->pool[$url])) {
            return false;
        }

        if (in_array(pathinfo($url, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'pdf', 'zip', 'rar'])) {
            return false;
        }

        $schema = parse_url($url);

        if (!isset($schema['host']) || isset($schema['fragment'])) {
            return false;
        }

        if ($input->getOption('ignore-query') && isset($schema['query'])) {
            return false;
        }

        if ($input->getOption('ignore-http') && $schema['scheme'] === 'http') {
            return false;
        }

        if ($input->getOption('ignore-https') && $schema['scheme'] === 'https') {
            return false;
        }

        if (!in_array($schema['host'], $this->hostPool)) {
            return false;
        }

        $pattern = implode('|', $this->ignorePatternPool);
        if (preg_match('/' . $pattern . '/ims', $url)) {
            return false;
        }

        $this->pool[$url] = $level;

        return true;
    }
}
