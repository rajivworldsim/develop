<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollector;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollectorInterface;
use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;

class UseDefaults implements DataCollectorInterface
{
    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    public function __construct(
        FieldsByStore $fieldsByStore
    ) {
        $this->fieldsByStore = $fieldsByStore;
    }

    public function execute(array $data): array
    {
        if (!$data[ItemInterface::STORE_ID]) {
            return $data;
        }

        $useDefaults = $data[ItemInterface::USE_DEFAULT] ?? [];
        foreach ($this->fieldsByStore->getCustomFields() as $fieldSet) {
            foreach ($fieldSet as $field) {
                if (!empty($useDefaults[$field])) {
                    $data[$field] = null;
                } else {
                    $data[$field] = $data[$field] ?? null;
                }
            }
        }

        $useDefaults = array_keys(array_filter($useDefaults));
        $useDefaults = implode(ItemInterface::SEPARATOR, $useDefaults);
        $data[ItemInterface::USE_DEFAULT] = $useDefaults;

        return $data;
    }
}
