<?php


namespace Agtech\OrderAPI\Ui\Component\Columns;

use Agtech\OrderAPI\Model\OrderHistoryFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class OrderGrid extends Column
{
    /**
     * @var PriceCurrencyInterface|OrderHistory
     */
    protected $order_history;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderHistory $orderHistory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderHistoryFactory $orderHistory,
        array $components = [],
        array $data = []
    )
    {
        $this->order_history = $orderHistory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['status'] === 'cancel')
                    $item[$this->getData('name')] =
                        $this->order_history->create()->getGridColumn($item['entity_id'], 'cancel');
                else {
                    $item[$this->getData('name')] =
                        $this->order_history->create()->getGridColumn($item['entity_id'], 'order_save');
                }
            }
        }

        return $dataSource;
    }
}