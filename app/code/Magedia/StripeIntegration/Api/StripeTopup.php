<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Api;

use Magedia\StripeIntegration\Api\StripeTopupInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Magedia\StripeIntegration\Api\Data\TopupInterface;
use Magedia\StripeIntegration\Model\ResourceModel\Topup;
use StripeIntegration\Payments\Model\Config;
use Magento\Framework\App\RequestInterface;
use Magedia\StripeIntegration\Model\TopupFactory as TopUpModel;
use Magento\Framework\App\Rss\UrlBuilderInterface;
use Magento\Framework\Url;

class StripeTopup implements StripeTopupInterface
{
    /**
     * @var StripeClient
     */
    private StripeClient $stripeClient;

    /**
     * @var Url
     */
    private Url $urlBuilder;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var TopUpModel
     */
    private TopUpModel $topup;

    /**
     * @var Topup
     */
    private Topup $topupResourceModel;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Url $urlBuilder
     * @param RequestInterface $request
     * @param Config $config
     * @param StripeClient $stripeClient
     * @param TopUpModel $topup
     * @param Topup $topupResourceModel
     */
    public function __construct(
        UrL $urlBuilder,
        RequestInterface $request,
        Config $config,
        StripeClient $stripeClient,
        TopUpModel $topup,
        Topup $topupResourceModel
    ){
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->config = $config;
        $this->stripeClient = $stripeClient;
        $this->topup = $topup;
        $this->topupResourceModel = $topupResourceModel;
    }

    /**
     * @param string $orderId
     * @return string
     */
    public function checkTopup(string $orderId):string{
        try {
            $stripeAdmin = new StripeClient($this->config->getSecretKey());
            $topUpInterface = $this->topup->create();

            $this->topupResourceModel->load($topUpInterface,$orderId,'order_increment_id');
            $usuallyPayment = $topUpInterface->getUsuallyPayment();
            $pi = $stripeAdmin->paymentIntents->create([
                'amount' => $usuallyPayment * 100,
                'currency' => strtolower($topUpInterface->getCurrency()),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'customer'=> $topUpInterface->getStripeCustomerId()
            ]);

            $payment = $stripeAdmin->paymentIntents->confirm($pi->id,['return_url'=>$this->urlBuilder->getUrl(),'payment_method' => $topUpInterface->getStripePaymentMethod()]);
            $result = ['status' => true,
                "client_secret" => $payment->client_secret];

            return json_encode($result);
        }
        catch (\Exception $exception){
            $result = ['status' => false,
                "message" => $exception->getMessage()];

            return json_encode($result);
        }
    }

}
