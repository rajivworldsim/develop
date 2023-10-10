<?php
/*
 * @Author Hemant Singh
 * @Developer Hemant Singh
 * @Module Wishusucess_FilterProducts
 * @copyright Copyright (c) Wishusucess (http://www.wishusucess.com/)
 */
namespace Agtech\CmsBlocks\Model\ResourceModel\VirtualRates;

class VirtualRate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
 
    public function _construct(){
        $this->_init("worldsim_virtual_number_rates","entity_id");
    }
}
?>