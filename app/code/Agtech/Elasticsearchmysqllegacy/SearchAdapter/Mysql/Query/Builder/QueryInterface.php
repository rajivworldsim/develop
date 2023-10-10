<?php

namespace Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\Query\Builder;

use Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\ScoreBuilder;

/**
 * MySQL search query builder.
 *
 * @deprecated 102.0.0
 * @see \Magento\ElasticSearch
 */
interface QueryInterface
{
    /**
     * Build query.
     *
     * @param \Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\ScoreBuilder $scoreBuilder
     * @param \Magento\Framework\DB\Select $select
     * @param \Magento\Framework\Search\Request\QueryInterface $query
     * @param string $conditionType
     * @return \Magento\Framework\DB\Select
     */
    public function build(
        ScoreBuilder $scoreBuilder,
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\Search\Request\QueryInterface $query,
        $conditionType
    );
}
