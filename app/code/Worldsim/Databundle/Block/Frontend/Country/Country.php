<?php

namespace Worldsim\Databundle\Block\Frontend\Country;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Worldsim\Databundle\Model\RateSheetDataBundle;
/**
 * Test View block
 */
class Country extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        Context $context,
        RateSheetDataBundle $rateSheetDataBundle
    ) {
        $this->_rateSheetDataBundle = $rateSheetDataBundle;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Simple Custom Module View Page'));
        
        return parent::_prepareLayout();
    }

    public function getCountryData()
{
    $collection = $this->_rateSheetDataBundle->getCollection()->addFieldToSelect(['country', 'region']);

    $singleCountryArray = [];
    $multipleCountryArray = [];

    foreach ($collection as $item) {
        $country = $item->getCountry();
        $region = $item->getRegion();

        if ($region == 'Single Country') {
            $singleCountryArray[] = [
                'country' => $country,
            ];
        } else if ($region == 'Multiple Country') {
            $multipleCountryArray[] = [
                'country' => $country,
            ];
        }
    }

    return [
        'singleCountryArray' => $singleCountryArray,
        'multipleCountryArray' => $multipleCountryArray
    ];
}

public function getSingleCountryDataWorldsim()
{
    $collection = $this->_rateSheetDataBundle->getCollection()->addFieldToSelect(['country', 'region', 'supplier']);

    $singleCountryArray = [];
    $multipleCountryArray = [];

    foreach ($collection as $item) {
        $country = $item->getCountry();
        $region = $item->getRegion();
        $supplier = $item->getSupplier();

        if ($region == 'Single Country' && $supplier == 'WorldSIM') {
            $singleCountryArray[] = [
                'country' => $country,
            ];
        }
    }

    return [
        'singleCountryArray' => $singleCountryArray
    ];
}

public function getCountryDataEsimConnect()
{
    $collection = $this->_rateSheetDataBundle->getCollection()->addFieldToSelect(['country', 'region', 'supplier']);

    $singleCountryArray = [];
    $multipleCountryArray = [];

    foreach ($collection as $item) {
        $country = $item->getCountry();
        $region = $item->getRegion();
        $supplier = $item->getSupplier();
        if ($region == 'Single Country' && $supplier == 'Go') {
            $singleCountryArray[] = [
                'country' => $country,
            ];
        } else if ($region == 'Multiple Country' && $supplier == 'Go') {
            $multipleCountryArray[] = [
                'country' => $country,
            ];
        }
    }

    return [
        'singleCountryArray' => $singleCountryArray,
        'multipleCountryArray' => $multipleCountryArray
    ];
}

    public function getPlanData()
    {
        $collection = $this->_rateSheetDataBundle->getCollection();

        return $collection;
    }
    public function getAjaxUrl()
    {
        return $this->getUrl('databundle/index/showplan/');
    }

    public function getRomingAjaxUrl()
    {
        return $this->getUrl('databundle/index/roming/');
    }
}