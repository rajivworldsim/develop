<?php

namespace Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql;

/**
 * ScoreBuilder Factory
 *
 * @deprecated 102.0.0
 * @see \Magento\ElasticSearch
 */
class ScoreBuilderFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\ScoreBuilder::class
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\ScoreBuilder
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
