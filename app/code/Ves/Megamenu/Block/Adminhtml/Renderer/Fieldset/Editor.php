<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Block\Adminhtml\Renderer\Fieldset;
use Magento\Theme\Helper\Storage;

class Editor extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
\Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_fields;

    /**
     * Form element which re-rendering
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $_element;

    /**
     * @var string
     */
    protected $_template = 'editor.phtml';

    protected $_htmlId;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param   \Magento\Backend\Block\Template\Context $context
     * @param   \Magento\Framework\Registry $registry
     * @param   \Magento\Framework\ObjectManagerInterface $objectManager
     * @param   \Ves\Megamenu\Helper\Editor $editor
     * @param   \Ves\Megamenu\Helper\Data $vesData
     * @param   \Magento\Framework\Message\ManagerInterface $messageManager
     * @param   \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories
     * @param   \Ves\Megamenu\Model\Config\Source\Icons $icons
     * @param   \Ves\Megamenu\Model\Config\Source\BorderStyle $borderStyles
     * @param   \Ves\Megamenu\Model\Config\Source\Groupcustomgoogle $groupcustomgoogle
     * @param   \Ves\Megamenu\Model\Config\Source\GoogleFonts $googleFonts
     * @param   \Ves\Megamenu\Model\Config\Source\Subset $subset
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ves\Megamenu\Helper\Editor $editor,
        \Ves\Megamenu\Helper\Data $vesData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories,
        \Ves\Megamenu\Model\Config\Source\Icons $icons,
        \Ves\Megamenu\Model\Config\Source\BorderStyle $borderStyles,
        \Ves\Megamenu\Model\Config\Source\Groupcustomgoogle $groupcustomgoogle,
        \Ves\Megamenu\Model\Config\Source\GoogleFonts $googleFonts,
        \Ves\Megamenu\Model\Config\Source\Subset $subset,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->_coreRegistry  = $registry;
        $this->_fields        = $editor->getFields();
        $this->_vesData       = $vesData;
        $this->assetRepo      = $context->getAssetRepository();
        $this->storeCategories   = $storeCategories;
        $this->icons             = $icons;
        $this->borderStyles      = $borderStyles;
        $this->groupcustomgoogle = $groupcustomgoogle;
        $this->googleFonts       = $googleFonts;
        $this->subset            = $subset;
        $this->messageManager = $messageManager;
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        parent::_construct();
        $htmlId = 'ves_megamenu' . time();
        $this->setHmtlId($htmlId);
    }

    /**
     * get html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->_htmlId;
    }

    /**
     * set html id
     *
     * @param string $htmlId
     * @return $this
     */
    public function setHmtlId($htmlId)
    {
        $this->_htmlId = $htmlId;
        return $this;
    }

    /**
     * update widget img src
     *
     * @param string $str
     * @return string
     */
    public function updateWidgetImgSrc($str)
    {
        $widgetMedia = [
            'Magento_Catalog/images/product_widget_new.png'      => 'Magento_Catalog::images/product_widget_new.png',
            'Magento_Catalog/images/product_widget_link.png'     => 'Magento_Catalog::images/product_widget_link.png',
            'Magento_CatalogWidget/images/products_list.png'     => 'Magento_CatalogWidget::images/products_list.png',
            'Magento_Cms/images/widget_page_link.png'            => 'Magento_Cms::images/widget_page_link.png',
            'Magento_Cms/images/widget_block.png'                => 'Magento_Cms::images/widget_block.png',
            'Magento_Reports/images/product_widget_viewed.gif'   => 'Magento_Reports::images/product_widget_viewed.gif',
            'Magento_Reports/images/product_widget_compared.gif' => 'Magento_Reports::images/product_widget_compared.gif',
            'Magento_Widget/placeholder.gif'                     => 'Magento_Widget::placeholder.gif'
        ];
        $count = !empty($str) ? substr_count($str, "<img") : 0;
        //$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $firstPosition = 0;
        for ($i=0; $i < $count; $i++) {
            if($firstPosition==0) $tmp = $firstPosition;
            if($tmp > $count) continue;//If position greater than max length of string, temp will equal = string length
            if(empty($str) || $tmp > strlen($str)) continue;
            $firstPosition = strpos($str, "<img", $tmp);
            $nextPosition = strpos($str, "/>", $firstPosition);
            $tmp = $nextPosition;
            if(!strpos($str, "<img")) continue;
            $length = $nextPosition - $firstPosition;
            $img = substr($str, $firstPosition, $length+2);
            $newImg = $this->_vesData->filter($img);
            $f = @strpos($newImg, 'src="', 0)+5;
            $post_length = strlen($newImg);

            if ($post_length < ($f+5)) {
				$n = strpos($newImg, '"', $post_length-1);
			} else {
				$n = @strpos($newImg, '"', $f+5);
			}

            $src = @substr($newImg, $f, ($n-$f));
            foreach ($widgetMedia as $k1 => $v1) {
                if( @strpos($img, $k1)){
                    $src1 = $src;
                    foreach ($widgetMedia as $k => $v) {
                        if(preg_match('~(' . $k . ')~', $img)){
                            $src1 = $this->assetRepo->getUrl($v);
                            $newImg = @str_replace($src, $src1, $newImg);
                            $str = @str_replace($img, $newImg, $str);
                            break;
                        }
                    }
                }
            }

        }
        return $str;
    }

    /**
     * render menu item
     *
     * @param mixed|array $data
     * @param int $level
     * @param mixed|array $itemBuild
     * @return mixed|array
     */
    public function renderMenuItem($data = [] , $level = 0, $itemBuild = [])
    {
        //$_htmlId = $this->getHtmlId();
        $menu = $this->getMenu();
        $menuItems = $menu->getData('menuItems');
        $level++;
        $data_id = isset($data['id'])?$data['id']:0;
        $item = isset($menuItems[$data_id])?$menuItems[$data_id]:[];
        foreach ($item as $k => $v) {
            $item[$k] = $this->updateWidgetImgSrc($v);
        }
        $html = $this->_menuItems = json_encode($item) . ',';
        $itemBuild = $item;
        $newChildren = [];
        if (isset($data['children']) && count($data['children'])>0) {
            foreach ($data['children'] as $k => $v) {
                $newChildren[] = $this->renderMenuItem($v, $level, $itemBuild);
            }
        }
        $itemBuild['children'] = $newChildren;
        return $itemBuild;
    }

    /**
     * @param int $categoryId
     * @return string
     */
    public function getCategoryText($categoryId)
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category       = $_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);

        return $category && $category->getName() ? '(ID: ' . $category->getId() . ') ' . $category->getName() : __('(Unknown)');
    }

    /**
     * Render element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    /**
     * get current menu
     *
     * @return \Ves\Megamenu\Model\Menu
     */
    public function getMenu()
    {
        $model = $this->_coreRegistry->registry('megamenu_menu');
        return $model;
    }

    /**
     * Returns rows array
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Get media url
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }

    public function getIcons()
    {
        return $this->icons->toOptionArray();
    }

    public function getStoreCategories()
    {
        return $this->storeCategories->getCategoriesTree();
    }

    public function getAjaxSaveItemUrl()
    {
        return $this->getUrl('*/*/saveItem');
    }

    public function getAjaxSaveMenuUrl()
    {
        return $this->getUrl('*/*/savePreview');
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    public function getAjaxImportCategoryUrl()
    {
        return $this->getUrl('*/*/importCategory');
    }

    public function getBorderStyles()
    {
        return $this->borderStyles->toOptionArray();
    }

    public function getFontGroup()
    {
        return $this->groupcustomgoogle->toOptionArray();
    }

    public function getGoogleFonts()
    {
        return $this->googleFonts->toOptionArray();
    }

    public function getCharSubsets()
    {
        return $this->subset->toOptionArray();
    }
}
