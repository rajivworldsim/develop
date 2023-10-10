<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Blog\Controller\Adminhtml\Import;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Blog\Helper\Data as BlogHelper;
use RuntimeException;

/**
 * Class Validate
 * @package Mageplaza\Blog\Controller\Adminhtml\Import
 */
class Validate extends Action
{
    /**
     * @var BlogHelper
     */
    public $blogHelper;

    /**
     * Validate constructor.
     *
     * @param Context $context
     * @param BlogHelper $blogHelper
     */
    public function __construct(
        Context $context,
        BlogHelper $blogHelper
    ) {
        $this->blogHelper = $blogHelper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        $data = $this->getRequest()->getParams();

        try {
            $connect    = mysqli_connect("localhost", "m1w0rkds3m_usr", "yF1ICF0!pc4j", "m1w0rkds3m_wdsim1");
            $importName = "aheadworksm1";
			$data["import_name"] = "aheadworksm1";

            /** @var Session */
            $this->_getSession()->setData('mageplaza_blog_import_data', $data);
            $result = ['import_name' => $importName, 'status' => 'ok'];

            mysqli_close($connect);

            return $this->getResponse()->representJson(BlogHelper::jsonEncode($result));
        } catch (RuntimeException $e) {
            $result = ['import_name' => "aheadworksm1", 'status' => 'false'];

            return $this->getResponse()->representJson(BlogHelper::jsonEncode($result));
        } catch (Exception $e) {
            $result = ['import_name' => "aheadworksm1", 'status' => 'false'];

            return $this->getResponse()->representJson(BlogHelper::jsonEncode($result));
        }
    }
}
