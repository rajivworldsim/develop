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


use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\CacheWarmer\Api\Data\SourceInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\SourceRepositoryInterface;
use Mirasvit\CacheWarmer\Controller\Adminhtml\AbstractSource;
use Mirasvit\CacheWarmer\Helper\Serializer;

class Delete extends AbstractSource
{
    private $pageRepository;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SourceRepositoryInterface $sourceRepository,
        Registry $registry,
        Context $context,
        Serializer $serializer
    ) {
        $this->pageRepository = $pageRepository;

        parent::__construct($sourceRepository, $registry, $context, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(SourceInterface::ID);

        if ($id) {
            /** @var SourceInterface $defaultSource */
            $defaultSource = $this->sourceRepository->getDefaultSource();

            if ($id == $defaultSource->getId()) {
                $this->messageManager->addErrorMessage(__('The Default source can not be deleted'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            try {
                $model = $this->sourceRepository->get($id);

                $pageCollection = $this->pageRepository
                    ->getCollection()
                    ->addFieldToFilter('source_id', $model->getId());

                /** @var SourceInterface $defaultSource */
                $defaultSource = $this->sourceRepository->getDefaultSource();

                foreach ($pageCollection as $page) {
                    if ($page->getPopularity() > 0 && $defaultSource) {
                        $page->setSourceId($defaultSource->getId());
                        $this->pageRepository->save($page);
                    } else {
                        $this->pageRepository->delete($page);
                    }
                }

                $this->sourceRepository->delete($model);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->messageManager->addSuccessMessage(__('Source was removed'));
        } else {
            $this->messageManager->addErrorMessage(__('Please select any Source except the Default source'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
