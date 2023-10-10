<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Agtech\Recently\Block;

class Viewproduct extends \Magento\Framework\View\Element\Template
{
	
	protected $_collectionFactory;
	protected $_productsFactory;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
	public function __construct(
	\Magento\Backend\Block\Template\Context $context,
	\Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
	array $data = []
	) {

	$this->_productsFactory = $productsFactory;
	parent::__construct($context, $data);
	}

    /**
     * @return string
     */
    public function getMostViewedData()
    {
        
        $mostViewedCollection = $this->_productsFactory->create()->addViewsCount();  
    return $mostViewedCollection;
    }
}

