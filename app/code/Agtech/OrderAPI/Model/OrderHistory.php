<?php
/**
 * Created by PhpStorm.
 * User: galillei
 * Date: 17.4.16
 * Time: 12.19
 */

namespace Agtech\OrderAPI\Model;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Ui\Component\Listing\Columns\Column;


class OrderHistory extends \Magento\Framework\Model\AbstractModel
{
	
	protected $_orderRepository;
    protected $urlInterface;
	
 	public function __construct(Context $context,
		Registry $Registry,
		OrderRepositoryInterface $orderRepository,
		\Magento\Framework\UrlInterface $urlInterface)
    {
        $this->_orderRepository = $orderRepository;
        $this->urlInterface = $urlInterface;
		$this->context = $context;
        parent::__construct($context, $Registry);
    }

    public function getGridColumn($id)
    {
		$order = $this->_orderRepository->get($id);
		$totalDue = $order->getBaseTotalDue();
		$doNotPushM1Order = 0;
		$orderCreatedDate = $order->getCreatedAt();
		$m2ShifftingDate = '2022-12-01 00:00:01';
		if($m2ShifftingDate > $orderCreatedDate){
			$doNotPushM1Order = 1;
		}
		if($order->getErpid() || $totalDue>0 || $doNotPushM1Order){
			return '<span onclick=\'location.href="'.$this->urlInterface->getUrl("agtech_orderapi/index/xmlview", ["id" => $id]).'"\' target="_blank" style="color: blue;cursor: pointer;">View XML</span>';
		}else{		
			return '<span onclick=\'location.href="'.$this->urlInterface->getUrl("agtech_orderapi/index/xmlview", ["id" => $id]).'"\' target="_blank" style="color: blue;cursor: pointer;">View XML</span><br /><span style="color: #000000;font-weight: bold;">or</span><br /><span onclick=\'location.href="'.$this->urlInterface->getUrl("agtech_orderapi/index/sendxml", ["id" => $id]).'"\' target="_blank" style="color: blue;cursor: pointer;">Resend XML</span>';
		}
    }


}