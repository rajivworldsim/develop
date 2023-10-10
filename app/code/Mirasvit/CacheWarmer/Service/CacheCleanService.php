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



namespace Mirasvit\CacheWarmer\Service;

use Mirasvit\CacheWarmer\Api\Data\TraceInterface;
use Mirasvit\CacheWarmer\Api\Repository\TraceRepositoryInterface;
use Mirasvit\CacheWarmer\Logger\FlushLogger;
use Mirasvit\CacheWarmer\Model\Config;

class CacheCleanService
{
    private $config;

    private $logger;

    private $traceRepository;

    public function __construct(
        Config $config,
        FlushLogger $logger,
        TraceRepositoryInterface $traceRepository
    ) {
        $this->config          = $config;
        $this->logger          = $logger;
        $this->traceRepository = $traceRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @param string $mode
     * @param array  $tags
     */
    public function logCacheClean($mode, array $tags)
    {
        $allowed = [
            "rma_order_status_history",
            "helpdesk_gateway",
            "rewards_transaction",
            "helpdesk_message",
            "helpdesk_ticket",
        ];
        if (count(array_intersect($allowed, $tags)) != 0) {
            return;
        }

        $isTagLogEnabled           = $this->config->isTagLogEnabled();
        $isBacktraceLogEnabled     = $this->config->isBacktraceLogEnabled();
        $isBacktraceLogFileEnabled = $this->config->isBacktraceLogFileEnabled();

        $backtrace = \Magento\Framework\Debug::backtrace(true, false, false);
        $reason    = $this->getCacheFlushReason($backtrace);

        if (strpos($backtrace, 'GenerateFixturesCommand') !== false) {
            return; // ignore cache flushes during performance fixtures generations
        }

        if ($isTagLogEnabled) {
            $this->logger->debug('Clean cache', [
                'mode'      => $mode,
                'tags'      => $tags,
                'reason'    => $reason,
                'backtrace' => $isBacktraceLogFileEnabled ? $backtrace : null,
            ]);
        }

        $url = $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

        $traceData = [
            'cli'       => php_sapi_name() == "cli" ? "Yes" : "No",
            'url'       => $url ? $url : "N/A",
            'mode'      => $mode,
            'tags'      => $tags,
            'reason'    => $reason,
            'backtrace' => $isBacktraceLogEnabled ? $backtrace : null,
        ];

        $trace = $this->traceRepository->create();
        $trace->setEntityType(TraceInterface::ENTITY_TYPE_CACHE)
            ->setEntityId(0)
            ->setTrace($traceData)
            ->setStartedAt(date('Y-m-d H:i:s'))
            ->setFinishedAt(date('Y-m-d H:i:s'));
        try {
            $this->traceRepository->save($trace);
        } catch (\Exception $e) {
            // migration can be not completed yet
        }
    }

    /**
     * @param string $trace
     * @return array
     */
    public function getCacheFlushReason($trace)
    {
        if (!$trace) {
            return [];
        }

        $knownMarkers = [
            'DiCompileCommand'                                            => 'setup:di:compile',
            'CacheFlushCommand'                                           => 'cache:flush',
            'CacheCleanCommand'                                           => 'cache:clean',
            'IndexerReindexCommand'                                       => 'indexer:reindex',
            'Magento\Catalog\Controller\Adminhtml\Product\Save'           => 'Product Saving',
            'Magento\Catalog\Controller\Adminhtml\Category\Save'          => 'Category Saving',
            'Adminhtml\Cache\MassRefresh'                                 => 'Cache Management: Refresh',
            'Adminhtml\Cache\FlushSystem'                                 => 'Cache Management: Flush',
            'Magento\Catalog\Controller\Adminhtml\Product\Attribute\Save' => 'Attribute Saving',
            'updateAttributes()'                                          => 'Mass Attribute Update (Products grid)',
            'MessageQueue\Console\StartConsumerCommand'                   => 'After Attribute Update',
            'Magento\Review\Controller\Adminhtml\Product\Save'            => 'Review Saving',
            'Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save'   => 'After Shipment Submited',
            'Magento\Indexer\Cron'                                        => 'Reindex by cron or triggered by some action',
            'Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog\Save' => 'Catalog Rule Saving',
            'Magento\Setup\Console\Command\UpgradeCommand'                => 'setup:upgrade',
            'Magento\Setup\Console\Command\ModuleDisableCommand'          => 'module:disable',
            'Magento\Setup\Console\Command\ModuleEnableCommand'           => 'module:enable',
            'Magento\CatalogInventory\Model\ResourceModel\Stock\Item'     => 'Stock updating',
            'Magento\Sales\Controller\Adminhtml\Order\Cancel'             => 'Canceling order',
        ];

        $nativeMarkers = [
            'Magento',
            'Mirasvit\CacheWarmer',
            'call_user_func'
        ];

        $presentMarkers = [];

        foreach ($knownMarkers as $marker => $msg) {
            if (strpos($trace, $marker) !== false) {
                $presentMarkers[] = $msg;
            }
        }

        $traceArr = explode(PHP_EOL, $trace);

        foreach ($traceArr as $row) {
            $isNative = false;

            foreach ($nativeMarkers as $nativeMarker) {
                if (strpos($row, $nativeMarker) === 0) {
                    $isNative = true;
                    break;
                }
            }

            if (!$isNative && preg_match('/\w+\\\w+/', $row, $match)) {
                $message = '3rd party: ' . $match[0];
                $presentMarkers = array_merge([$message], $presentMarkers);
            }
        }

        return array_unique($presentMarkers);
    }
}
