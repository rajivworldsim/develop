<?php

namespace Agtech\Elasticsearchmysqllegacy\Model\Search;

/**
 * @deprecated 101.0.0
 * @see \Magento\ElasticSearch
 */
class ReaderPlugin
{
    /**
     * @var \Agtech\Elasticsearchmysqllegacy\Model\Search\RequestGenerator
     */
    private $requestGenerator;

    /**
     * @param \Agtech\Elasticsearchmysqllegacy\Model\Search\RequestGenerator $requestGenerator
     */
    public function __construct(
        \Agtech\Elasticsearchmysqllegacy\Model\Search\RequestGenerator $requestGenerator
    ) {
        $this->requestGenerator = $requestGenerator;
    }

    /**
     * Merge reader's value with generated
     *
     * @param \Magento\Framework\Config\ReaderInterface $subject
     * @param array $result
     * @param string|null $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRead(
        \Magento\Framework\Config\ReaderInterface $subject,
        array $result,
        $scope = null
    ) {
        $result = array_merge_recursive($result, $this->requestGenerator->generate());
        return $result;
    }
}
