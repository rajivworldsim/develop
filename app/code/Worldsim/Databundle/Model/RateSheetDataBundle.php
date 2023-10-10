<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model;

use Magento\Framework\Model\AbstractModel;
use Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface;

class RateSheetDataBundle extends AbstractModel implements RateSheetDataBundleInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle::class);
    }

    /**
     * @inheritDoc
     */
    public function getRateSheetDataBundleId()
    {
        return $this->getData(self::RATE_SHEET_DATA_BUNDLE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRateSheetDataBundleId($rateSheetDataBundleId)
    {
        return $this->setData(self::RATE_SHEET_DATA_BUNDLE_ID, $rateSheetDataBundleId);
    }

    /**
     * @inheritDoc
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @inheritDoc
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function getSupplier()
    {
        return $this->getData(self::SUPPLIER);
    }

    /**
     * @inheritDoc
     */
    public function setSupplier($supplier)
    {
        return $this->setData(self::SUPPLIER, $supplier);
    }

    /**
     * @inheritDoc
     */
    public function getSimtype()
    {
        return $this->getData(self::SIMTYPE);
    }

    /**
     * @inheritDoc
     */
    public function setSimtype($simtype)
    {
        return $this->setData(self::SIMTYPE, $simtype);
    }

    /**
     * @inheritDoc
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * @inheritDoc
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * @inheritDoc
     */
    public function getOnegb()
    {
        return $this->getData(self::ONEGB);
    }

    /**
     * @inheritDoc
     */
    public function setOnegb($onegb)
    {
        return $this->setData(self::ONEGB, $onegb);
    }

    /**
     * @inheritDoc
     */
    public function getThreegb()
    {
        return $this->getData(self::THREEGB);
    }

    /**
     * @inheritDoc
     */
    public function setThreegb($threegb)
    {
        return $this->setData(self::THREEGB, $threegb);
    }

    /**
     * @inheritDoc
     */
    public function getFivegb()
    {
        return $this->getData(self::FIVEGB);
    }

    /**
     * @inheritDoc
     */
    public function setFivegb($fivegb)
    {
        return $this->setData(self::FIVEGB, $fivegb);
    }

    /**
     * @inheritDoc
     */
    public function getSixgb()
    {
        return $this->getData(self::SIXGB);
    }

    /**
     * @inheritDoc
     */
    public function setSixgb($sixgb)
    {
        return $this->setData(self::SIXGB, $sixgb);
    }

    /**
     * @inheritDoc
     */
    public function getTengb()
    {
        return $this->getData(self::TENGB);
    }

    /**
     * @inheritDoc
     */
    public function setTengb($tengb)
    {
        return $this->setData(self::TENGB, $tengb);
    }

    /**
     * @inheritDoc
     */
    public function getTwenty()
    {
        return $this->getData(self::TWENTY);
    }

    /**
     * @inheritDoc
     */
    public function setTwenty($twenty)
    {
        return $this->setData(self::TWENTY, $twenty);
    }

    /**
     * @inheritDoc
     */
    public function getUnlimited()
    {
        return $this->getData(self::UNLIMITED);
    }

    /**
     * @inheritDoc
     */
    public function setUnlimited($unlimited)
    {
        return $this->setData(self::UNLIMITED, $unlimited);
    }

    /**
     * @inheritDoc
     */
    public function getTopupavail()
    {
        return $this->getData(self::TOPUPAVAIL);
    }

    /**
     * @inheritDoc
     */
    public function setTopupavail($topupavail)
    {
        return $this->setData(self::TOPUPAVAIL, $topupavail);
    }

    /**
     * @inheritDoc
     */
    public function getRoaminginc()
    {
        return $this->getData(self::ROAMINGINC);
    }

    /**
     * @inheritDoc
     */
    public function setRoaminginc($roaminginc)
    {
        return $this->setData(self::ROAMINGINC, $roaminginc);
    }

    /**
     * @inheritDoc
     */
    public function getRoamingcountries()
    {
        return $this->getData(self::ROAMINGCOUNTRIES);
    }

    /**
     * @inheritDoc
     */
    public function setRoamingcountries($roamingcountries)
    {
        return $this->setData(self::ROAMINGCOUNTRIES, $roamingcountries);
    }

    /**
     * @inheritDoc
     */
    public function getOnegbcode()
    {
        return $this->getData(self::ONEGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setOnegbcode($onegbcode)
    {
        return $this->setData(self::ONEGBCODE, $onegbcode);
    }

    /**
     * @inheritDoc
     */
    public function getThreegbcode()
    {
        return $this->getData(self::THREEGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setThreegbcode($threegbcode)
    {
        return $this->setData(self::THREEGBCODE, $threegbcode);
    }

    /**
     * @inheritDoc
     */
    public function getFivegbcode()
    {
        return $this->getData(self::FIVEGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setFivegbcode($fivegbcode)
    {
        return $this->setData(self::FIVEGBCODE, $fivegbcode);
    }

     /**
     * @inheritDoc
     */
    public function getSixgbcode()
    {
        return $this->getData(self::SIXGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setSixgbcode($sixgbcode)
    {
        return $this->setData(self::SIXGBCODE, $sixgbcode);
    }

    /**
     * @inheritDoc
     */
    public function getTengbcode()
    {
        return $this->getData(self::TENGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setTengbcode($tengbcode)
    {
        return $this->setData(self::TENGBCODE, $tengbcode);
    }

    /**
     * @inheritDoc
     */
    public function getTwentygbcode()
    {
        return $this->getData(self::TWENTYGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setTwentygbcode($twentygbcode)
    {
        return $this->setData(self::TWENTYGBCODE, $twentygbcode);
    }

    /**
     * @inheritDoc
     */
    public function getUnlimitedgbcode()
    {
        return $this->getData(self::UNLIMITEDGBCODE);
    }

    /**
     * @inheritDoc
     */
    public function setUnlimitedgbcode($unlimitedgbcode)
    {
        return $this->setData(self::UNLIMITEDGBCODE, $unlimitedgbcode);
    }

    /**
     * @inheritDoc
     */
    public function getWorldsimshortCode()
    {
        return $this->getData(self::WORLDSIMSHORTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setWorldsimshortCode($worldsimshortCode)
    {
        return $this->setData(self::WORLDSIMSHORTCODE, $worldsimshortCode);
    }

    /**
     * @inheritDoc
     */
    public function getWorldsimdistrubtor()
    {
        return $this->getData(self::WORLDSIMDISTRUBTOR);
    }

    /**
     * @inheritDoc
     */
    public function setWorldsimdistrubtor($worldsimdistrubtor)
    {
        return $this->setData(self::WORLDSIMDISTRUBTOR, $worldsimdistrubtor);
    }
}

