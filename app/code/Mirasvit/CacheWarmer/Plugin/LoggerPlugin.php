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



namespace Mirasvit\CacheWarmer\Plugin;

use Mirasvit\CacheWarmer\Api\Data\JobInterface;
use Mirasvit\CacheWarmer\Api\Repository\JobRepositoryInterface;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\Core\Service\SerializeService;
use Monolog\Logger;

class LoggerPlugin
{
    const DELIMITER = '##';

    private $jobRepository;

    private $config;

    public function __construct(
        JobRepositoryInterface $jobRepository,
        Config $config
    ) {
        $this->jobRepository = $jobRepository;
	$this->config        = $config;
    }

    /**
     * @param Logger $subject
     * @param callable $proceed
     * @param int $level
     * @param string $message
     * @param array $context
     *
     * @return bool
     */
    public function aroundAddRecord(Logger $subject, $proceed, $level, $message, array $context = [])
    {
	$status = $subject->getStatus();

	if (!$this->config->isRequestLogEnabled() && $status === null) {
            return false;
        }
        if ($status === false) {
            return false;
	}

	if ($job = $subject->getJob()) {
            $trace = $job->getTrace();

            $traceKey     = (new \DateTime())->format('H:m:s.') . microtime(true);
            $traceMessage = $subject->getLevel($level)
                . self::DELIMITER . $message
                . self::DELIMITER . ($context ? SerializeService::encode($context) : '');

            $trace[$traceKey] = $traceMessage;

            $job->setTrace($trace);

            $this->jobRepository->save($job);

            $context['job'] = $job->getId();
        }

	return $proceed($level, $message, $context);
    }
}
