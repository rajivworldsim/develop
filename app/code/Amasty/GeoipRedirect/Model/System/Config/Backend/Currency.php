<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GeoIP Redirect for Magento 2
*/

namespace Amasty\GeoipRedirect\Model\System\Config\Backend;

class Currency extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Math\Random $mathRandom,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $result = [];
        foreach ($value as $data) {
            if (!$data) {
                continue;
            }
            if (!is_array($data)) {
                continue;
            }
            if (count($data) < 2) {
                continue;
            }
            $countriesArr = $data['country_currency'];
            $countries = implode(',', $countriesArr);
            $result[$countries] = $data['currency'];
        }
        $this->setValue($this->serializer->serialize($result));
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->serializer->unserialize($value);
        if (is_array($value)) {
            $value = $this->encodeArrayFieldValue($value);
            $this->setValue($value);
        }
        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $country => $currency) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['country_currency' => $country, 'currency' => $currency];
        }
        return $result;
    }
}
