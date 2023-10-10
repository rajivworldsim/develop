<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Model\ResourceModel\GoogleReviews;

class GoogleReview extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
 
    public function _construct(){
        $this->_init("worldsim_google_reviews","entity_id");
    }
}
?>