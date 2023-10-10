<?php

/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 07/09/2019
 * Time: 15:24
 */


namespace Magenest\SagePay\Model;

use Magenest\SagePay\Api\ThreeDInfoInterface;

class ThreeDSecure implements ThreeDInfoInterface
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface
     */
    protected $orderStatusHistoryRepository;

    protected $json;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository
    ) {
        $this->json = $json;
        $this->_request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->orderRepository = $orderRepository;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
    }

    /**
     * @return mixed
     */
    public function get3DInfo()
    {
        try {
            $data = $this->get3DSecureResponseData();
        } catch (\Exception $e) {
            return $this->json->serialize(['error' => true, 'message' => __("Payment exception: " . $e->getMessage())]);
        }

        return $this->json->serialize($data);
    }

    /**
     * @return array|mixed
     */
    public function get3DSecureResponseData()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $payment = $order->getPayment();
        $data = $payment->getAdditionalInformation('3d_secure_response') ? $this->json->unserialize($payment->getAdditionalInformation('3d_secure_response')) : null;
        if ($data) {
            $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $order->setStatus('pending_payment');
            $comment = $order->addStatusHistoryComment(__('Update Order Status to [pending_payment]'));
            $this->orderStatusHistoryRepository->save($comment);
            $this->orderRepository->save($order);
            $data['is3dSecure'] = true;
            $data['success'] = true;
            $data['threeDSSessionData'] = str_replace(array("{", "}"), "", $data['VPSTxId'] ?? '');
            return $data;
        } else {
            return [
                'is3dSecure' => false,
                'success' => true
            ];
        }
    }
}
