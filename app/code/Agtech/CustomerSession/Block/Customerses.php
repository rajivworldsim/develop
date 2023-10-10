<?php 

namespace Agtech\CustomerSession\Block;

class Customerses extends \Magento\Framework\View\Element\Template
{
protected $customerSession;
public function __construct(
    \Magento\Customer\Model\Session $customerSession
){
    $this->custsession = $customerSession;
    
}	
public function CustomerSessionRe()
{
    return $this->custsession;
}
}