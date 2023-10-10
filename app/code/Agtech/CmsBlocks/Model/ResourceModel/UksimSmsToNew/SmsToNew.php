<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Model\ResourceModel\UksimSmsToNew;

class SmsToNew extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
 
    public function _construct(){
        $this->_init("worldsim_uksim_sms_to_new","entity_id");
    }
}
?>