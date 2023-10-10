<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Api\Data;

interface RateSheetDataBundleInterface
{

    const ID = 'id';
    const COUNTRY = 'country';
    const REGION = 'region';
    const SUPPLIER = 'supplier';
    const ONEGB = 'onegb';
    const TENGB = 'tengb';
    const FIVEGB = 'fivegb';
    const SIXGB = 'sixgb';
    const UNLIMITEDGBCODE = 'unlimitedgbcode';
    const THREEGBCODE = 'threegbcode';
    const ONEGBCODE = 'onegbcode';
    const DATE = 'date';
    const TOPUPAVAIL = 'topupavail';
    const TWENTY = 'twenty';
    const ROAMINGCOUNTRIES = 'roamingcountries';
    const TENGBCODE = 'tengbcode';
    const UNLIMITED = 'unlimited';
    const THREEGB = 'threegb';
    const WORLDSIMSHORTCODE = 'worldsimshortCode';
    const WORLDSIMDISTRUBTOR = 'worldsimdistrubtor';
    const FIVEGBCODE = 'fivegbcode';
    const SIXGBCODE = 'sixgbcode';
    const ROAMINGINC = 'roaminginc';
    const SIMTYPE = 'simtype';
    const TWENTYGBCODE = 'twentygbcode';

    /**
     * Get id
     * @return string|null
     */
    public function getRateSheetDataBundleId();

    /**
     * Set id
     * @param string $rateSheetDataBundleId
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setRateSheetDataBundleId($rateSheetDataBundleId);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setCountry($country);

    /**
     * Get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setRegion($region);

    /**
     * Get supplier
     * @return string|null
     */
    public function getSupplier();

    /**
     * Set supplier
     * @param string $supplier
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setSupplier($supplier);

    /**
     * Get simtype
     * @return string|null
     */
    public function getSimtype();

    /**
     * Set simtype
     * @param string $simtype
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setSimtype($simtype);

    /**
     * Get date
     * @return string|null
     */
    public function getDate();

    /**
     * Set date
     * @param string $date
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setDate($date);

    /**
     * Get onegb
     * @return string|null
     */
    public function getOnegb();

    /**
     * Set onegb
     * @param string $onegb
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setOnegb($onegb);

    /**
     * Get threegb
     * @return string|null
     */
    public function getThreegb();

    /**
     * Set threegb
     * @param string $threegb
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setThreegb($threegb);

    /**
     * Get fivegb
     * @return string|null
     */
    public function getFivegb();

    /**
     * Set fivegb
     * @param string $fivegb
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setFivegb($fivegb);

    /**
     * Get sixgb
     * @return string|null
     */
    public function getSixgb();

    /**
     * Set sixgb
     * @param string $sixgb
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setSixgb($sixgb);

    /**
     * Get tengb
     * @return string|null
     */
    public function getTengb();

    /**
     * Set tengb
     * @param string $tengb
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setTengb($tengb);

    /**
     * Get twenty
     * @return string|null
     */
    public function getTwenty();

    /**
     * Set twenty
     * @param string $twenty
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setTwenty($twenty);

    /**
     * Get unlimited
     * @return string|null
     */
    public function getUnlimited();

    /**
     * Set unlimited
     * @param string $unlimited
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setUnlimited($unlimited);

    /**
     * Get topupavail
     * @return string|null
     */
    public function getTopupavail();

    /**
     * Set topupavail
     * @param string $topupavail
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setTopupavail($topupavail);

    /**
     * Get roaminginc
     * @return string|null
     */
    public function getRoaminginc();

    /**
     * Set roaminginc
     * @param string $roaminginc
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setRoaminginc($roaminginc);

    /**
     * Get roamingcountries
     * @return string|null
     */
    public function getRoamingcountries();

    /**
     * Set roamingcountries
     * @param string $roamingcountries
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setRoamingcountries($roamingcountries);

    /**
     * Get onegbcode
     * @return string|null
     */
    public function getOnegbcode();

    /**
     * Set onegbcode
     * @param string $onegbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setOnegbcode($onegbcode);

    /**
     * Get threegbcode
     * @return string|null
     */
    public function getThreegbcode();

    /**
     * Set threegbcode
     * @param string $threegbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setThreegbcode($threegbcode);

    /**
     * Get fivegbcode
     * @return string|null
     */
    public function getFivegbcode();

    /**
     * Set fivegbcode
     * @param string $fivegbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setFivegbcode($fivegbcode);

    /**
     * Get sixgbcode
     * @return string|null
     */
    public function getSixgbcode();

    /**
     * Set sixgbcode
     * @param string $sixgbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setSixgbcode($sixgbcode);

    /**
     * Get tengbcode
     * @return string|null
     */
    public function getTengbcode();

    /**
     * Set tengbcode
     * @param string $tengbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setTengbcode($tengbcode);

    /**
     * Get twentygbcode
     * @return string|null
     */
    public function getTwentygbcode();

    /**
     * Set twentygbcode
     * @param string $twentygbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setTwentygbcode($twentygbcode);

    /**
     * Get unlimitedgbcode
     * @return string|null
     */
    public function getUnlimitedgbcode();

    /**
     * Set unlimitedgbcode
     * @param string $unlimitedgbcode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setUnlimitedgbcode($unlimitedgbcode);

    /**
     * Get worldsimshortCode
     * @return string|null
     */
    public function getWorldsimshortCode();

    /**
     * Set worldsimshortCode
     * @param string $worldsimshortCode
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setWorldsimshortCode($worldsimshortCode);

    /**
     * Get worldsimdistrubtor
     * @return string|null
     */
    public function getWorldsimdistrubtor();

    /**
     * Set worldsimdistrubtor
     * @param string $worldsimdistrubtor
     * @return \Worldsim\Databundle\RateSheetDataBundle\Api\Data\RateSheetDataBundleInterface
     */
    public function setWorldsimdistrubtor($worldsimdistrubtor);
}

