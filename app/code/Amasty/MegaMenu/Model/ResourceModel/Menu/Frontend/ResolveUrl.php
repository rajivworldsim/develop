<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Frontend;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ResolveUrl
{
    /**
     * @var UrlKey
     */
    private $urlKeySource;

    /**
     * @var WrapColumns
     */
    private $wrapColumns;

    public function __construct(
        UrlKey $urlKeySource,
        WrapColumns $wrapColumns
    ) {
        $this->urlKeySource = $urlKeySource;
        $this->wrapColumns = $wrapColumns;
    }

    public function joinLink(AbstractCollection $collection): void
    {
        $coalesce = $this->wrapColumns->execute('main_table', [LinkInterface::LINK]);
        foreach ($this->urlKeySource->getTablesToJoin() as $type => $table) {
            $collection->getSelect()->joinLeft(
                [$table => $collection->getTable($table)],
                sprintf(
                    '%s.page_id = %s.page_id AND %s.link_type = \'%s\'',
                    'main_table',
                    $table,
                    'main_table',
                    $type
                ),
                ['identifier']
            );
            $coalesce[] = $table . '.identifier';
        }
        $coalesce[] = '\'\'';

        $collection->getSelect()->columns(sprintf(
            'COALESCE(%s) AS %s',
            implode(', ', $coalesce),
            'url'
        ));
    }
}
