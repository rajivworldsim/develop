<?php

namespace Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\Filter;

use Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\ConditionManager;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * @inheritdoc
 */
class Preprocessor implements PreprocessorInterface
{
    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @param ConditionManager $conditionManager
     */
    public function __construct(ConditionManager $conditionManager)
    {
        $this->conditionManager = $conditionManager;
    }

    /**
     * @inheritdoc
     */
    public function process(FilterInterface $filter, $isNegation, $query)
    {
        return $query;
    }
}
