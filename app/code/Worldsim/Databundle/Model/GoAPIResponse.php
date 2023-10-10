<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model;

use Magento\Framework\Model\AbstractModel;
use Worldsim\Databundle\Api\Data\GoAPIResponseInterface;

class GoAPIResponse extends AbstractModel implements GoAPIResponseInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Worldsim\Databundle\Model\ResourceModel\GoAPIResponse::class);
    }

    /**
     * @inheritDoc
     */
    public function getGoApiResponseId()
    {
        return $this->getData(self::GO_API_RESPONSE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGoApiResponseId($goApiResponseId)
    {
        return $this->setData(self::GO_API_RESPONSE_ID, $goApiResponseId);
    }

    /**
     * @inheritDoc
     */
    public function getIccid()
    {
        return $this->getData(self::ICCID);
    }

    /**
     * @inheritDoc
     */
    public function setIccid($iccid)
    {
        return $this->setData(self::ICCID, $iccid);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getBundleCode()
    {
        return $this->getData(self::BUNDLE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setBundleCode($bundleCode)
    {
        return $this->setData(self::BUNDLE_CODE, $bundleCode);
    }

    /**
     * @inheritDoc
     */
    public function getIsNewSim()
    {
        return $this->getData(self::IS_NEW_SIM);
    }

    /**
     * @inheritDoc
     */
    public function setIsNewSim($isNewSim)
    {
        return $this->setData(self::IS_NEW_SIM, $isNewSim);
    }

    /**
     * @inheritDoc
     */
    public function getPin()
    {
        return $this->getData(self::PIN);
    }

    /**
     * @inheritDoc
     */
    public function setPin($pin)
    {
        return $this->setData(self::PIN, $pin);
    }

    /**
     * @inheritDoc
     */
    public function getPuk()
    {
        return $this->getData(self::PUK);
    }

    /**
     * @inheritDoc
     */
    public function setPuk($puk)
    {
        return $this->setData(self::PUK, $puk);
    }

    /**
     * @inheritDoc
     */
    public function getMatchingId()
    {
        return $this->getData(self::MATCHINGID);
    }

    /**
     * @inheritDoc
     */
    public function setMatchingId($matchingId)
    {
        return $this->setData(self::MATCHINGID, $matchingId);
    }

    /**
     * @inheritDoc
     */
    public function getSmdpAddress()
    {
        return $this->getData(self::SMDPADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setSmdpAddress($smdpAddress)
    {
        return $this->setData(self::SMDPADDRESS, $smdpAddress);
    }

    /**
     * @inheritDoc
     */
    public function getProfileStatus()
    {
        return $this->getData(self::PROFILESTATUS);
    }

    /**
     * @inheritDoc
     */
    public function setProfileStatus($profileStatus)
    {
        return $this->setData(self::PROFILESTATUS, $profileStatus);
    }

    /**
     * @inheritDoc
     */
    public function getFirstInstalledDateTime()
    {
        return $this->getData(self::FIRSTINSTALLEDDATETIME);
    }

    /**
     * @inheritDoc
     */
    public function setFirstInstalledDateTime($firstInstalledDateTime)
    {
        return $this->setData(self::FIRSTINSTALLEDDATETIME, $firstInstalledDateTime);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerRef()
    {
        return $this->getData(self::CUSTOMERREF);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerRef($customerRef)
    {
        return $this->setData(self::CUSTOMERREF, $customerRef);
    }
}

