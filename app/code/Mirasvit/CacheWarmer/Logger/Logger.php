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



namespace Mirasvit\CacheWarmer\Logger;

use Mirasvit\CacheWarmer\Api\Data\JobInterface;

class Logger extends \Monolog\Logger
{
    const DELIMITER = '##';

    /**
     * @var JobInterface
     */
    private static $job;

    /**
     * @var null
     */
    private $status = null;

    /**
     * @param JobInterface $job
     * @return $this
     */
    public function setJob(JobInterface $job)
    {
        self::$job = $job;

        return $this;
    }

    /**
     * @return JobInterface
     */
    public function getJob()
    {
        return self::$job;
    }

    /**
     * Force logger enable/disable
     *
     * @param  bool|null $status
     */
    public function forceEnable($status)
    {
        $this->status = $status;
    }

    /**
     * @param int $level
     *
     * @return string
     */
    public function getLevel($level)
    {
	return isset(self::$levels[$level]) ? self::$levels[$level] : '';
    }

    public function getStatus()
    {
	return $this->status;
    }
}
