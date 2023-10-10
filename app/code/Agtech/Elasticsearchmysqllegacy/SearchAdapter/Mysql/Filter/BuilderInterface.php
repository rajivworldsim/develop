<?php

namespace Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\Filter;

use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

/**
 * MySQL search filter builder.
 *
 * @deprecated 102.0.0
 * @see \Magento\ElasticSearch
 */
interface BuilderInterface
{
    /**
     * Buil filter.
     *
     * @param RequestFilterInterface $filter
     * @param string $conditionType
     * @return string
     */
    public function build(RequestFilterInterface $filter, $conditionType);
}
