<?php
/**
 * Ascure AweberSubscription Block
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com. All rights reserved.
 * @license     https://www.ascuretech.com/license.html
 */

namespace Ascure\AweberSubscription\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DisabledField extends Field
{
    /**
     * Disable the input field for the Partially Blocked and Completely Blocked fields.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // Disable the input field
        $element->setDisabled('disabled');

        return parent::_getElementHtml($element);
    }

    /**
     * Get the element HTML code
     *
     * @param AbstractElement $element
     * @return string
     */
    public function getElementHtml(AbstractElement $element)
    {
        // Use the preferred method for disabling the input field
        $element->setDisabled('disabled');

        return parent::getElementHtml($element);
    }
}
