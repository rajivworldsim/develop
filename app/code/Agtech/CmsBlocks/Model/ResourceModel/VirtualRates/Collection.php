<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Model\ResourceModel\VirtualRates;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	public function _construct(){
		$this->_init("Agtech\CmsBlocks\Model\VirtualRates\VirtualRate","Agtech\CmsBlocks\Model\ResourceModel\VirtualRates\VirtualRate");
	}
}
?>