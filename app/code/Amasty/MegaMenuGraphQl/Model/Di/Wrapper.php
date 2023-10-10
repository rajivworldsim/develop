<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

namespace Amasty\MegaMenuGraphQl\Model\Di;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerMetaProvider;

/**
 * Wrapper for compatibility with new Magento versions
 */
class Wrapper
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManagerInterface;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $getShared;

    /**
     * @var bool
     */
    private $isProxy;

    /**
     * @var object
     */
    private $subject;

    /**
     * @var ObjectManagerMetaProvider
     */
    private $diMetaProvider;

    public function __construct(
        ObjectManagerInterface $objectManagerInterface,
        ObjectManagerMetaProvider $diMetaProvider,
        $name = '',
        $getShared = false,
        $isProxy = false
    ) {
        $this->objectManagerInterface = $objectManagerInterface;
        $this->diMetaProvider = $diMetaProvider;
        $this->name = $name;
        $this->getShared = $getShared;
        $this->isProxy = $isProxy;
    }

    // @codingStandardsIgnoreStart

    /**
     * @param string $name
     * @param array arguments
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        $result = false;

        if ($this->canCreateObject()) {
            $object = $this->getSubject();
            $result = call_user_func_array([$object, $name], $arguments);
        }

        return $result;
    }

    /**
     * @return object
     */
    public function getSubject()
    {
        if ($this->isProxy && $this->subject) {
            return $this->subject;
        }

        if ($this->getShared) {
            $object = $this->objectManagerInterface->get($this->name);
        } else {
            $object = $this->objectManagerInterface->create($this->name);
        }

        if ($this->isProxy) {
            $this->subject = $object;
        }

        return $object;
    }

    // @codingStandardsIgnoreEnd

    private function isVirtualType(string $class): bool
    {
        $type = $this->diMetaProvider->getInstanceType($class);

        return $type !== $class;
    }

    private function canCreateObject(): bool
    {
        $canAutoload = (class_exists($this->name) || interface_exists($this->name));
        $canGetObjectByDI = $this->isVirtualType($this->name);

        return $this->name && ($canAutoload || $canGetObjectByDI);
    }
}
