<?php


namespace Agtech\Elasticsearchmysqllegacy\Model\Search;

use Magento\Framework\Data\Collection;

/**
 * Search collection provider.
 */
interface ItemCollectionProviderInterface
{
    /**
     * Get collection.
     *
     * @return Collection
     */
    public function getCollection() : Collection;
}
