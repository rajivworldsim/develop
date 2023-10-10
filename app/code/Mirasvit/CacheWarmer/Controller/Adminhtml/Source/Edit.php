<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.7.7
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */




namespace Mirasvit\CacheWarmer\Controller\Adminhtml\Source;


use Magento\Framework\Controller\ResultFactory;
use Mirasvit\CacheWarmer\Api\Data\SourceInterface;
use Mirasvit\CacheWarmer\Controller\Adminhtml\AbstractSource;

class Edit extends AbstractSource
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id    = $this->getRequest()->getParam(SourceInterface::ID);
        $model = $this->initModel();

        if ($id && !$model) {
            $this->messageManager->addErrorMessage(__('This Source no longer exists'));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $this->initPage($resultPage)->getConfig()->getTitle()
            ->prepend($id ? $model->getSourceName() : __('New Source'));

        return $resultPage;
    }
}
