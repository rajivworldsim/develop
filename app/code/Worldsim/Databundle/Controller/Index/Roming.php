<?php
declare(strict_types=1);

namespace Worldsim\Databundle\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\ResourceConnection;

class Roming extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Roming constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Execute AJAX request and fetch 'romingcountries' based on 'planid'
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $planid = (int)$this->getRequest()->getParam('planid');

        if ($planid) {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('worldsim_databundle_rate_sheet_data_bundle');

            $select = $connection->select()
                ->from($tableName, 'roamingcountries')
                ->where('id = ?', $planid);

            $romingcountries = $connection->fetchOne($select);

            $pattern = '/[,;]/'; // Regular expression pattern to match comma or semicolon

            $romingcountriesArray = array_unique(preg_split($pattern, $romingcountries));


            $optionsHtml = '';
            foreach ($romingcountriesArray as $country) {
                $optionsHtml .= "<option value=\"$country\">$country</option>";
            }

            $result = $this->resultJsonFactory->create();
            $result->setData(['success' => true, 'optionsHtml' => $optionsHtml]);
            return $result;
        } else {
            $result = $this->resultJsonFactory->create();
            $result->setData(['success' => false, 'message' => 'Missing planid parameter']);
            return $result;
        }
    }
}
