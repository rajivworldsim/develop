<?php

namespace Agtech\Elasticsearchmysqllegacy\Model\Indexer\Scope;

use Magento\Framework\Exception\LocalizedException;

/**
 * Exception which represents situation where temporary index table should be used somewhere,
 * but it does not exist in a database
 *
 * @api
 * @since 100.2.0
 * @deprecated
 * @see \Magento\ElasticSearch
 */
class IndexTableNotExistException extends LocalizedException
{
}
