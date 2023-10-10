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



namespace Mirasvit\CacheWarmer\Ui\Page\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;

class ActionColumn extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ActionColumn constructor.
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'warm'   => [
                        'href'  => $this->urlBuilder->getUrl('cache_warmer/page/warm', [
                            PageInterface::ID => $item[PageInterface::ID],
                        ]),
                        'label' => __('Warm Cache'),
                    ],
                    'clean'  => [
                        'href'  => $this->urlBuilder->getUrl('cache_warmer/page/clean', [
                            PageInterface::ID => $item[PageInterface::ID],
                        ]),
                        'label' => __('Clean Cache'),
                    ],
                    'curl'   => [
                        'href'  => $this->urlBuilder->getUrl('cache_warmer/page/curl', [
                            PageInterface::ID => $item[PageInterface::ID],
                        ]),
                        'label' => __('Show Curl Request'),
                    ],
                    'delete' => [
                        'href'    => $this->urlBuilder->getUrl('cache_warmer/page/delete', [
                            PageInterface::ID => $item[PageInterface::ID],
                        ]),
                        'label'   => __('Delete'),
                        'confirm' => [
                            'title'   => __('Delete the record with ID "' . $item[PageInterface::ID] . '"'),
                            'message' => __('Are you sure you want to delete the record with ID "' . $item[PageInterface::ID] . '"?'),
                        ],
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
