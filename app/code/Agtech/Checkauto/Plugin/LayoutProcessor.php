<?php
namespace Agtech\Checkauto\Plugin;

use Magento\Customer\Model\Session;

class LayoutProcessor
{

	/**
     * @var Session $session
     */
    private $session;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    private $checkoutSession;

	/**
     * @param Session $session
     * @param \Magento\Checkout\Model\Session $checkoutSession
    */
    public function __construct(Session $session, \Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->session = $session;
        $this->_checkoutSession = $checkoutSession;
    }


    public function afterProcess(
	\Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
		){
    $getproductType = $this->_checkoutSession->getQuote();
    $attributeCode = 'createaccountcust';
        $fieldConfiguration = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.extension_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/checkbox',
                    'options' => [],
                ],
                'dataScope' => 'shippingAddress.extension_attributes.createaccountcust',
                'label' => 'Create account to view your call records, activate bundles and more...',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'checked' => true,
                'validation' => [],
                'sortOrder' => 250,
            ];

        $jsLayout['components']['checkout']['children']
        ['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['customer-email']['children']['additional-login-form-fields']['children'][$attributeCode] = $fieldConfiguration;


	$password = 'password';
	$passField = [
        'component' => 'Magento_Ui/js/form/element/abstract',
        'config' => [
            'customScope' => 'shippingAddress.extension_attributes',
            'customEntry' => null,
            'template' => 'ui/form/field',
            'elementTmpl' => 'ui/form/element/password',

        ],
        'dataScope' => 'shippingAddress.extension_attributes.' . $password,
        'label' => 'Password',
        'required' => true,
        'provider' => 'checkoutProvider',
        'sortOrder' => 251,
        'options' => [],
        'filterBy' => null,
        'customEntry' => null,
        'visible' => true
    ];

    $jsLayout['components']['checkout']['children']
        ['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['customer-email']['children']['additional-login-form-fields']['children'][$password] = $passField;

	$Confpassword = 'confpassword';

    $ConfpassField = [
        'component' => 'Magento_Ui/js/form/element/abstract',
        'config' => [
            'customScope' => 'shippingAddress.extension_attributes',
            'customEntry' => null,
            'template' => 'ui/form/field',
            'elementTmpl' => 'ui/form/element/password',

        ],
        'dataScope' => 'shippingAddress.extension_attributes.' . $Confpassword,
        'label' => 'Confirm Password ',
        'required' => true,
        'provider' => 'checkoutProvider',
        'sortOrder' => 252,
        'options' => [],
        'filterBy' => null,
        'customEntry' => null,
        'visible' => true
    ];

    $jsLayout['components']['checkout']['children']
        ['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['customer-email']['children']['additional-login-form-fields']['children'][$Confpassword] = $ConfpassField;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['value'] = $this->session->getCrmcusFirstName();

		$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['value'] =  $this->session->getCrmcusLastName( );



        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['value'] = $this->session->getCrmcusCity();

		$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['value'] = $this->session->getCrmcusPostCode();

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['value'] = $this->session->getCrmcusPhone();


        // billing address
        if($getproductType->getIsVirtual()){
        $configuration = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
            foreach ($configuration as $paymentGroup => $groupConfig) {
                if (isset($groupConfig['component'])) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['customer-email']['children']['before-login-form']['children'][$attributeCode] = [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'config' => [
                            'customScope' => 'billingAddress.extension_attributes',
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/checkbox',
                            'options' => [],
                        ],
                        'dataScope' => 'billingAddress.extension_attributes.'.$attributeCode,
                        'label' => 'Create account to view your call records, activate bundles and more...',
                        'provider' => 'checkoutProvider',
                        'visible' => true,
                        'checked' => true,
                        'validation' => [],
                        'sortOrder' => 250,
                    ];
                }
            }
             foreach ($configuration as $paymentGroup => $groupConfig) {
                if (isset($groupConfig['component'])) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['customer-email']['children']['before-login-form']['children'][$password] = [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'config' => [
                            'customScope' => 'billingAddress.extension_attributes',
                            'customEntry' => null,
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/password',
                        ],
                        'dataScope' => 'billingAddress.extension_attributes.'.$password,
                        'label' => 'Password',
                        'required' => true,
                        'provider' => 'checkoutProvider',
                        'sortOrder' => 251,
                        'options' => [],
                        'filterBy' => null,
                        'customEntry' => null,
                        'visible' => true
                    ];
                }
            }
             foreach ($configuration as $paymentGroup => $groupConfig) {
                if (isset($groupConfig['component'])) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['customer-email']['children']['before-login-form']['children'][$Confpassword] = [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'config' => [
                            'customScope' => 'billingAddress.extension_attributes',
                            'customEntry' => null,
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/password',
                        ],
                        'dataScope' => 'billingAddress.extension_attributes.'.$Confpassword,
                        'label' => 'Confirm Password ',
                        'required' => true,
                        'provider' => 'checkoutProvider',
                        'sortOrder' => 252,
                        'options' => [],
                        'filterBy' => null,
                        'customEntry' => null,
                        'visible' => true
                    ];
                }
            }
        }

        return $jsLayout;
	}

}
