<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Model\ResourceModel\GoogleReviews;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	public function _construct(){
		$this->_init("Agtech\CmsBlocks\Model\GoogleReviews\GoogleReview","Agtech\CmsBlocks\Model\ResourceModel\GoogleReviews\GoogleReview");
	}
}
?>