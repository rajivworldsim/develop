<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Price;
use Stripe\Checkout\Session as StripeSession;
use Magento\Framework\App\Action\Action;
use StripeIntegration\Payments\Model\Config;
use Stripe\Product;
use Magedia\StripeIntegration\Model\StripeProductFactory;
use Magedia\StripeIntegration\Model\ResourceModel\StripeProduct;
use Magedia\StripeIntegration\Model\ResourceModel\Collection\StripeProductFactory as CollectionStripeProductFactory;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Filter\LocalizedToNormalized;
use Magento\Checkout\Model\Cart;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magedia\StripeIntegration\Api\Data\StripeProductInterface;
use StripeIntegration\Payments\Helper\Locale;
use Stripe\StripeClient;

class Session extends Action
{
    public const ALLOWED_COUNTRIES = [
        'AU', 'AT', 'BE', 'BG', 'CA', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HK', 'HU', 'IE', 'IT', 'JP', 'LV',
        'LT', 'LU', 'MT','NL','NZ','NO','PL','PT','RO','SG','SK','SI','ES','SE','CH', 'GB','US'];

    /**
     * @var LocalizedToNormalized
     */
    private LocalizedToNormalized $localizedToNormalized;

    /**
     * @var Locale
     */
    private Locale $localeHelper;

    /**
     * @var Cart
     */
    private Cart $cart;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Stripe
     */
    private Stripe $stripe;

    /**
     * @var Data
     */
    private Data $catalogHelper;

    /**
     * @var StripeProductFactory
     */
    private StripeProductFactory $stripeProductFactory;

    /**
     * @var StripeProduct
     */
    private StripeProduct $stripeProductResource;

    /**
     * @var CollectionStripeProductFactory
     */
    private CollectionStripeProductFactory $stripeProductCollection;

    /**
     * @var Price
     */
    private Price $price;

    /**
     * @var Product
     */
    private Product $stripeProduct;

    /**
     * @var StripeSession
     */
    private StripeSession $stripeSession;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Data $catalogHelper
     * @param StripeProduct $stripeProductResource
     * @param StripeProductFactory $stripeProductFactory
     * @param CollectionStripeProductFactory $stripeProductCollection
     * @param Product $stripeProduct
     * @param Stripe $stripe
     * @param Price $price
     * @param StripeSession $stripeSession
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        Locale $localeHelper,
        LocalizedToNormalized $localizedToNormalized,
        Cart $cart,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Data $catalogHelper,
        StripeProduct $stripeProductResource,
        StripeProductFactory $stripeProductFactory,
        CollectionStripeProductFactory $stripeProductCollection,
        Product $stripeProduct,
        Stripe $stripe,
        Price $price,
        StripeSession $stripeSession,
        Config $config,
        Context $context
    ){
        $this->localeHelper = $localeHelper;
        $this->localizedToNormalized = $localizedToNormalized;
        $this->cart = $cart;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->catalogHelper = $catalogHelper;
        $this->stripeProductResource = $stripeProductResource;
        $this->stripeProductFactory = $stripeProductFactory;
        $this->stripeProductCollection = $stripeProductCollection;
        $this->stripeProduct = $stripeProduct;
        $this->price = $price;
        $this->stripeSession = $stripeSession;
        $this->stripe = $stripe;
        $this->config = $config;
        parent::__construct($context);
    }

    public function execute()
    {
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result = [];
        $this->stripe::setApiKey($this->config->getSecretKey());
        try {
            $usuallyPay = $this->getRequest()->getParam('autoTopUpAmount');
            $addToCartParams = [];
            parse_str($this->getRequest()->getParam('productForm'), $addToCartParams);
            $this->addToCart($addToCartParams);
            $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $cartItem = $this->cart->getItems()->getLastItem();
            $stripeAdmin = new StripeClient($this->config->getSecretKey());

            $stripeProductModel = $this->findStripeItem($cartItem->getPrice(),$currency,$stripeAdmin,$usuallyPay);

            $checkoutSession = $this->stripeSession::create([
                'shipping_address_collection' => ['allowed_countries' => self::ALLOWED_COUNTRIES],
                'payment_intent_data' => ['setup_future_usage' => 'off_session'],
                'success_url' => $this->_url->getUrl('magedia_stripeintegration/order/place',['telephone'=>$addToCartParams["mob_cus_id_auto"],'usually_payment'=> $usuallyPay ]) .'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->_url->getUrl(),
                'mode' => 'payment',
                'line_items' => [[
                    'price' => $stripeProductModel->getStripeProductId(),
                    'quantity' => 1,
                ]],
            ]);

            $result['redirectUrl'] = $checkoutSession->url;
            $result['status'] = true;

        }
        catch (\Exception $e){
            $result['status'] = false;
            $result['message'] = $e->getMessage();
        }
        $jsonResult->setData($result);
        return $jsonResult;

    }

    public function addToCart($request){
        $params= $request;

        $productId = $params['product'];
        $related = $params['related_product'];

        if (isset($params['qty'])) {

            $this->localizedToNormalized->setOptions(['locale' => $this->localeHelper->getLocale()]);
            $params['qty'] = $this->localizedToNormalized->filter((string)$params['qty']);
        }

        $quote = $this->cart->getQuote();

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $product = $this->productRepository->getById($productId, false, $storeId);
            foreach ($quote->getAllItems() as $quoteItem){
                $this->cart->removeItem($quoteItem->getId());
                $quoteItem->delete();
            }

            $this->cart->save();

            $item = $this->cart->addProduct($product, $params);
            if ($item->getHasError()) {
                throw new LocalizedException(__($item->getMessage()));
            }

            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }


            $this->cart->save();

            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $quote->save();

        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        }


    }

    /**
     * @param $price
     * @param string $currency
     * @param StripeClient $stripeAdmin
     * @param string $usuallyPay
     * @return StripeProductInterface
     * @throws AlreadyExistsException
     * @throws ApiErrorException
     */
    public function findStripeItem($price, string $currency, StripeCLient $stripeAdmin, string $usuallyPay): StripeProductInterface {
        $price = $this->isZeroDecimal($currency,(int)$price);
        $collection = $this->stripeProductCollection->create();
        $collection->addFieldToFilter('currency',$currency)->addFieldToFilter('price',$price);

        if ($collection->count()==1){
            $firstItem =  $collection->getFirstItem();
            $stripeAdmin->products->update($firstItem->getTrueStripeProductId(),['name' =>'TopUp FirstPay ('.$currency.(int)$price.'), then '.$usuallyPay.$currency.' when my balance drops below.']);
            return $firstItem;

        } else {
            $stripeProduct = $stripeAdmin->products->create(['name' => 'TopUp FirstPay ('.$currency.(int)$price.'), then '.$usuallyPay.$currency.' when my balance drops below.','type' => 'service']);
            $stripePrice = $stripeAdmin->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => $price * 100,
                'currency' => strtolower($currency)
            ]);

            $magentoStripeProductModel = $this->stripeProductFactory->create();
            $magentoStripeProductModel->setStripeProductId($stripePrice->id);
            $magentoStripeProductModel->setCurrency($currency);
            $magentoStripeProductModel->setPrice((float)$price);
            $magentoStripeProductModel->setTrueStripeProductId($stripeProduct->id);
            $this->stripeProductResource->save($magentoStripeProductModel);

            return $magentoStripeProductModel;
        }
    }

    /**
     * @param string $currency
     * @param int $price
     * @return int
     */
    public function isZeroDecimal(string $currency,int $price ):int  {
        switch ($currency){
            case "GBP":
                break;
            case "EUR":
                $price = ceil($price / 0.833) * 100;
                if ($price==3100){
                    $price = 3000;
                }
                if ($price==15100){
                    $price = 15000;
                }
                $price = $price/100;
                break;
            case "INR":
                $price = ceil($price)  * 85;
                if ($price ==2975){
                    $price = 3000;
                }
                if ($price ==4930){
                    $price = 5000;
                }
                if ($price ==1955){
                    $price = 2000;
                }
                if ($price==935){
                    $price = 1000;
                }
                break;
            case "AUD":
                $price = ceil($price  * 1.85);
                if ($price ==74){
                    $price = 75 ;
                }
                if ($price ==19){
                    $price = 20;
                }
                break;
            case "ZAR":
                $price = ceil($price  * 18.5);
                if ($price ==740){
                    $price = 750 ;
                }
                if ($price == 241){
                    $price = 250;
                }
                if ($price ==2498){
                    $price = 2500;
                }
                if ($price == 1240){
                    $price = 1250;
                }
                break;
            case "USD":
                $price = ceil($price  * 1.351);
                break;

        }
        return  (int)$price;

    }
}
