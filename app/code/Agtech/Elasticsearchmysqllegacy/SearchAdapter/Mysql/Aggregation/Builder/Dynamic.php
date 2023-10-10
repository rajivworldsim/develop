<?php

namespace Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\Aggregation\Builder;

use Magento\Framework\DB\Ddl\Table;
use Agtech\Elasticsearchmysqllegacy\SearchAdapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Framework\Search\Request\Aggregation\DynamicBucket;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

/**
 * MySQL search dynamic aggregation builder.
 *
 * @deprecated 102.0.0
 * @see \Magento\ElasticSearch
 */
class Dynamic implements BucketInterface
{
    /**
     * @var Repository
     */
    private $algorithmRepository;

    /**
     * @var EntityStorageFactory
     */
    private $entityStorageFactory;

    /**
     * @param Repository $algorithmRepository
     * @param EntityStorageFactory $entityStorageFactory
     */
    public function __construct(
        Repository $algorithmRepository,
        EntityStorageFactory $entityStorageFactory
    ) {
        $this->algorithmRepository = $algorithmRepository;
        $this->entityStorageFactory = $entityStorageFactory;
    }

    /**
     * @inheritdoc
     */
    public function build(
        DataProviderInterface $dataProvider,
        array $dimensions,
        RequestBucketInterface $bucket,
        Table $entityIdsTable
    ) {
        /** @var DynamicBucket $bucket */
        $algorithm = $this->algorithmRepository->get($bucket->getMethod());
        $data = $algorithm->getItems($bucket, $dimensions, $this->entityStorageFactory->create($entityIdsTable));

        $resultData = $this->prepareData($data);

        return $resultData;
    }

    /**
     * Prepare result data
     *
     * @param array $data
     * @return array
     */
    private function prepareData($data)
    {
        $resultData = [];
        foreach ($data as $value) {
            // https://github.com/magento/magento2/commit/256806aabbb802a545393c07bc8b8135dc7126e9
            // $from = is_numeric($value['from']) ? $value['from'] : '*';
            // $to = is_numeric($value['to']) ? $value['to'] : '*';
            // unset($value['from'], $value['to']);

            // $rangeName = "{$from}_{$to}";
            $rangeName = "{$value['from']}_{$value['to']}";
            $resultData[$rangeName] = array_merge(['value' => $rangeName], $value);
        }

        return $resultData;
    }
}
