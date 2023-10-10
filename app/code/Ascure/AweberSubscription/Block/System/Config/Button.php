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
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ascure\AweberSubscription\Helper\Data;

class Button extends Field
{
    // Specify the template to be used for rendering this block
    protected $_template = 'Ascure_AweberSubscription::system/config/button.phtml';

    // Helper instance to access module-specific data and functionality
    private $helperData;

    /**
     * Button constructor.
     *
     * @param Context $context The context object for the block.
     * @param Data    $helperData The module's helper class.
     * @param array   $data Additional data for the block.
     */
    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Render the HTML content of the block.
     *
     * @param AbstractElement $element The form element.
     * @return string The rendered HTML.
     */
    public function render(AbstractElement $element)
    {
        // Remove unnecessary scope and website/default value settings for this element
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the HTML content for the button element.
     *
     * @param AbstractElement $element The form element.
     * @return string The HTML content of the button.
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Get the HTML content for the 'Generate' button.
     *
     * @return string The HTML content of the 'Generate' button.
     */
    public function getGenerateButtonHtml()
    {
        // Create a 'Generate' button block with specified attributes
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(['id' => 'generate_btn_id', 'label' => __('Generate')]);
        return $button->toHtml();
    }

    /**
     * Get the HTML content for the 'Refresh' button.
     *
     * @return string The HTML content of the 'Refresh' button.
     */
    public function getRefreshButtonHtml()
    {
        // Create a 'Refresh' button block with specified attributes
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(['id' => 'refresh_btn_id', 'label' => __('Refresh')]);
        return $button->toHtml();
    }

    /**
     * Get the URL for the Generate button's controller action.
     *
     * @return string The URL for the Generate button's controller action.
     */
    public function getGenerateControllerUrl()
    {
        return $this->getUrl('ascure_awebersubscription/aweber/authentication');
    }

    /**
     * Get the URL for the Refresh button's controller action.
     *
     * @return string The URL for the Refresh button's controller action.
     */
    public function getRefreshControllerUrl()
    {
        return $this->getUrl('ascure_awebersubscription/aweber/refresh');
    }

    /**
     * Check if the module is enabled.
     *
     * @return bool Returns true if the module is enabled, false otherwise.
     */
    public function isModuleEnabled()
    {
        return $this->helperData->isModuleEnabled();
    }
}
