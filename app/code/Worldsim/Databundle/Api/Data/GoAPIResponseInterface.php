<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Api\Data;

interface GoAPIResponseInterface
{

    const EMAIL = 'email';
    const ORDER_ID = 'order_id';
    const BUNDLE_CODE = 'bundle_code';
    const MATCHINGID = 'matchingId';
    const GO_API_RESPONSE_ID = 'go_api_response_id';
    const PROFILESTATUS = 'profileStatus';
    const ICCID = 'iccid';
    const CUSTOMERREF = 'customerRef';
    const PIN = 'pin';
    const SMDPADDRESS = 'smdpAddress';
    const FIRSTINSTALLEDDATETIME = 'firstInstalledDateTime';
    const IS_NEW_SIM = 'is_new_sim';
    const PUK = 'puk';

    /**
     * Get go_api_response_id
     * @return string|null
     */
    public function getGoApiResponseId();

    /**
     * Set go_api_response_id
     * @param string $goApiResponseId
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setGoApiResponseId($goApiResponseId);

    /**
     * Get iccid
     * @return string|null
     */
    public function getIccid();

    /**
     * Set iccid
     * @param string $iccid
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setIccid($iccid);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setEmail($email);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setOrderId($orderId);

    /**
     * Get bundle_code
     * @return string|null
     */
    public function getBundleCode();

    /**
     * Set bundle_code
     * @param string $bundleCode
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setBundleCode($bundleCode);

    /**
     * Get is_new_sim
     * @return string|null
     */
    public function getIsNewSim();

    /**
     * Set is_new_sim
     * @param string $isNewSim
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setIsNewSim($isNewSim);

    /**
     * Get pin
     * @return string|null
     */
    public function getPin();

    /**
     * Set pin
     * @param string $pin
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setPin($pin);

    /**
     * Get puk
     * @return string|null
     */
    public function getPuk();

    /**
     * Set puk
     * @param string $puk
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setPuk($puk);

    /**
     * Get matchingId
     * @return string|null
     */
    public function getMatchingId();

    /**
     * Set matchingId
     * @param string $matchingId
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setMatchingId($matchingId);

    /**
     * Get smdpAddress
     * @return string|null
     */
    public function getSmdpAddress();

    /**
     * Set smdpAddress
     * @param string $smdpAddress
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setSmdpAddress($smdpAddress);

    /**
     * Get profileStatus
     * @return string|null
     */
    public function getProfileStatus();

    /**
     * Set profileStatus
     * @param string $profileStatus
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setProfileStatus($profileStatus);

    /**
     * Get firstInstalledDateTime
     * @return string|null
     */
    public function getFirstInstalledDateTime();

    /**
     * Set firstInstalledDateTime
     * @param string $firstInstalledDateTime
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setFirstInstalledDateTime($firstInstalledDateTime);

    /**
     * Get customerRef
     * @return string|null
     */
    public function getCustomerRef();

    /**
     * Set customerRef
     * @param string $customerRef
     * @return \Worldsim\Databundle\GoAPIResponse\Api\Data\GoAPIResponseInterface
     */
    public function setCustomerRef($customerRef);
}

