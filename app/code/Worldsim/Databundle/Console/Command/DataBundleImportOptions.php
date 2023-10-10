<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\Exception\LocalizedException;
use Worldsim\Databundle\Model\RateSheetDataBundle;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Framework\App\State;


class DataBundleImportOptions extends Command
{

    private $state;
    private $productRepository;
    private $optionFactory;
    private $rateSheetDataBundle;

    public function __construct(
        State $state,
        ProductRepositoryInterface $productRepository,
        OptionFactory $optionFactory,
        RateSheetDataBundle $rateSheetDataBundle,

    ) {
        $this->state = $state;
        $this->productRepository = $productRepository;
        $this->optionFactory = $optionFactory;
        $this->rateSheetDataBundle = $rateSheetDataBundle;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('<info>Command started</info>');

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

        $customOptions = $this->getcustomOptions();

        $output->writeln('<info>Creating custom option for WorldSIM Bundle TopUp</info>');
        $this->customOptions('data-bundle-topup-new', $customOptions);

        $output->writeln('<info>Creating custom option for WorldSIM Bundle with NEW SIM</info>');
        $this->customOptions('data-sim-bundle-new', $customOptions);

        $output->writeln('<info>Creating custom option for Data Bundle with New eSIM</info>');
        $this->customOptions('data-esim-bundle-new', $customOptions);

        $output->writeln('<info>Command successfully completed</info>');
    }

    protected function configure()
    {
        $this->setName("worldsim:update:options");
        $this->setDescription("Update worldsim product cusomizable options");
        parent::configure();
    }

    protected function getcustomOptions()
    {
        $collections = $this->rateSheetDataBundle->getCollection()->getData();
        $customizableOptions = [];
        foreach ($collections as $collection) {

            if ($collection['onegbcode'] && $collection["onegb"] && $collection["onegb"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- 1 GB up to-' . $collection['days'] . 'days.',
                    'price' => $collection["onegb"],
                    'price_type' => 'fixed',
                    'sku' => $collection['onegbcode'],
                    'sort_order' => 1,
                ];
            }
            if ($collection['threegbcode'] && $collection["threegb"] && $collection["threegb"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- 3 GB up to-' . $collection['days'] . 'days.',
                    'price' => $collection["threegb"],
                    'price_type' => 'fixed',
                    'sku' => $collection['threegbcode'],
                    'sort_order' => 2,
                ];
            }
            if ($collection['fivegbcode'] && $collection["fivegb"] && $collection["fivegb"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- 5 GB up to-' . $collection['days'] . 'days.',
                    'price' => $collection["fivegb"],
                    'price_type' => 'fixed',
                    'sku' => $collection['fivegbcode'],
                    'sort_order' => 3,
                ];
            }
            if ($collection['sixgbcode'] && $collection["sixgb"] && $collection["sixgb"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- 6 GB up to-' . $collection['days'] . 'days.',
                    'price' => $collection["sixgb"],
                    'price_type' => 'fixed',
                    'sku' => $collection['sixgbcode'],
                    'sort_order' => 4,
                ];
            }
            if ($collection['tengbcode'] && $collection["tengb"] && $collection["tengb"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- 10 GB up to-' . $collection['days'] . 'days.',
                    'price' => $collection["tengb"],
                    'price_type' => 'fixed',
                    'sku' => $collection['tengbcode'],
                    'sort_order' => 5,
                ];
            }
            if ($collection['twentygbcode'] && $collection["twenty"] && $collection["twenty"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- 20 GB up to-' . $collection['days'] . 'days.',
                    'price' => $collection["twenty"],
                    'price_type' => 'fixed',
                    'sku' => $collection['twentygbcode'],
                    'sort_order' => 6,
                ];
            }
            if ($collection['unlimitedgbcode'] && $collection["unlimited"] && $collection["unlimited"] != 'N/A') {
                $customizableOptions[] = [
                    'title' => $collection['country'] . '- Unlimmited up to-' . $collection['days'] . 'days.',
                    'price' => $collection["unlimited"],
                    'price_type' => 'fixed',
                    'sku' => $collection['unlimitedgbcode'] ,
                    'sort_order' => 7,
                ];
            }
        }

        return $customizableOptions;

    }


    protected function getCountryCode($countryName)
    {
        $countryCodes = [
            'Afghanistan' => 'AF',
            'Aaland Islands' => 'AX',
            'Albania' => 'AL',
            'Algeria' => 'DZ',
            'American Samoa' => 'AS',
            'Andorra' => 'AD',
            'Angola' => 'AO',
            'Anguilla' => 'AI',
            'Antarctica' => 'AQ',
            'Antigua And Barbuda' => 'AG',
            'Argentina' => 'AR',
            'Armenia' => 'AM',
            'Aruba' => 'AW',
            'Australia' => 'AU',
            'Austria' => 'AT',
            'Azerbaijan' => 'AZ',
            'Bahamas' => 'BS',
            'Bahrain' => 'BH',
            'Bangladesh' => 'BD',
            'Barbados' => 'BB',
            'Belarus' => 'BY',
            'Belgium' => 'BE',
            'Belize' => 'BZ',
            'Benin' => 'BJ',
            'Bermuda' => 'BM',
            'Bhutan' => 'BT',
            'Bolivia' => 'BO',
            'Bosnia And Herzegovina' => 'BA',
            'Botswana' => 'BW',
            'Bouvet Island' => 'BV',
            'Brazil' => 'BR',
            'British Indian Ocean Territory' => 'IO',
            'Brunei Darussalam' => 'BN',
            'Bulgaria' => 'BG',
            'Burkina Faso' => 'BF',
            'Burundi' => 'BI',
            'Cambodia' => 'KH',
            'Cameroon' => 'CM',
            'Canada' => 'CA',
            'Cape Verde' => 'CV',
            'Cayman Islands' => 'KY',
            'Central African Republic' => 'CF',
            'Chad' => 'TD',
            'Chile' => 'CL',
            'China' => 'CN',
            'Christmas Island' => 'CX',
            'Cocos (Keeling) Islands' => 'CC',
            'Colombia' => 'CO',
            'Comoros' => 'KM',
            'Congo' => 'CG',
            'Congo, Democratic Republic' => 'CD',
            'Cook Islands' => 'CK',
            'Costa Rica' => 'CR',
            'Cote D\'Ivoire' => 'CI',
            'Croatia' => 'HR',
            'Cuba' => 'CU',
            'Cyprus' => 'CY',
            'Czech Republic' => 'CZ',
            'Denmark' => 'DK',
            'Djibouti' => 'DJ',
            'Dominica' => 'DM',
            'Dominican Republic' => 'DO',
            'Ecuador' => 'EC',
            'Egypt' => 'EG',
            'El Salvador' => 'SV',
            'Equatorial Guinea' => 'GQ',
            'Eritrea' => 'ER',
            'Estonia' => 'EE',
            'Ethiopia' => 'ET',
            'Falkland Islands (Malvinas)' => 'FK',
            'Faroe Islands' => 'FO',
            'Fiji' => 'FJ',
            'Finland' => 'FI',
            'France' => 'FR',
            'French Guiana' => 'GF',
            'French Polynesia' => 'PF',
            'French Southern Territories' => 'TF',
            'Gabon' => 'GA',
            'Gambia' => 'GM',
            'Georgia' => 'GE',
            'Germany' => 'DE',
            'Ghana' => 'GH',
            'Gibraltar' => 'GI',
            'Greece' => 'GR',
            'Greenland' => 'GL',
            'Grenada' => 'GD',
            'Guadeloupe' => 'GP',
            'Guam' => 'GU',
            'Guatemala' => 'GT',
            'Guernsey' => 'GG',
            'Guinea' => 'GN',
            'Guinea-Bissau' => 'GW',
            'Guyana' => 'GY',
            'Haiti' => 'HT',
            'Heard Island & Mcdonald Islands' => 'HM',
            'Holy See (Vatican City State)' => 'VA',
            'Honduras' => 'HN',
            'Hong Kong' => 'HK',
            'Hungary' => 'HU',
            'Iceland' => 'IS',
            'India' => 'IN',
            'Indonesia' => 'ID',
            'Iran, Islamic Republic Of' => 'IR',
            'Iraq' => 'IQ',
            'Ireland' => 'IE',
            'Isle Of Man' => 'IM',
            'Israel' => 'IL',
            'Italy' => 'IT',
            'Jamaica' => 'JM',
            'Japan' => 'JP',
            'Jersey' => 'JE',
            'Jordan' => 'JO',
            'Kazakhstan' => 'KZ',
            'Kenya' => 'KE',
            'Kiribati' => 'KI',
            'Korea' => 'KR',
            'Kuwait' => 'KW',
            'Kyrgyzstan' => 'KG',
            'Lao People\'s Democratic Republic' => 'LA',
            'Latvia' => 'LV',
            'Lebanon' => 'LB',
            'Lesotho' => 'LS',
            'Liberia' => 'LR',
            'Libyan Arab Jamahiriya' => 'LY',
            'Liechtenstein' => 'LI',
            'Lithuania' => 'LT',
            'Luxembourg' => 'LU',
            'Macao' => 'MO',
            'Macedonia' => 'MK',
            'Madagascar' => 'MG',
            'Malawi' => 'MW',
            'Malaysia' => 'MY',
            'Maldives' => 'MV',
            'Mali' => 'ML',
            'Malta' => 'MT',
            'Marshall Islands' => 'MH',
            'Martinique' => 'MQ',
            'Mauritania' => 'MR',
            'Mauritius' => 'MU',
            'Mayotte' => 'YT',
            'Mexico' => 'MX',
            'Micronesia, Federated States Of' => 'FM',
            'Moldova' => 'MD',
            'Monaco' => 'MC',
            'Mongolia' => 'MN',
            'Montenegro' => 'ME',
            'Montserrat' => 'MS',
            'Morocco' => 'MA',
            'Mozambique' => 'MZ',
            'Myanmar' => 'MM',
            'Namibia' => 'NA',
            'Nauru' => 'NR',
            'Nepal' => 'NP',
            'Netherlands' => 'NL',
            'New Caledonia' => 'NC',
            'New Zealand' => 'NZ',
            'Nicaragua' => 'NI',
            'Niger' => 'NE',
            'Nigeria' => 'NG',
            'Niue' => 'NU',
            'Norfolk Island' => 'NF',
            'Northern Mariana Islands' => 'MP',
            'Norway' => 'NO',
            'Oman' => 'OM',
            'Pakistan' => 'PK',
            'Palau' => 'PW',
            'Palestinian Territory, Occupied' => 'PS',
            'Panama' => 'PA',
            'Papua New Guinea' => 'PG',
            'Paraguay' => 'PY',
            'Peru' => 'PE',
            'Philippines' => 'PH',
            'Pitcairn' => 'PN',
            'Poland' => 'PL',
            'Portugal' => 'PT',
            'Puerto Rico' => 'PR',
            'Qatar' => 'QA',
            'Reunion' => 'RE',
            'Romania' => 'RO',
            'Russian Federation' => 'RU',
            'Rwanda' => 'RW',
            'Saint Barthelemy' => 'BL',
            'Saint Helena' => 'SH',
            'Saint Kitts And Nevis' => 'KN',
            'Saint Lucia' => 'LC',
            'Saint Martin' => 'MF',
            'Saint Pierre And Miquelon' => 'PM',
            'Saint Vincent And Grenadines' => 'VC',
            'Samoa' => 'WS',
            'San Marino' => 'SM',
            'Sao Tome And Principe' => 'ST',
            'Saudi Arabia' => 'SA',
            'Senegal' => 'SN',
            'Serbia' => 'RS',
            'Seychelles' => 'SC',
            'Sierra Leone' => 'SL',
            'Singapore' => 'SG',
            'Slovakia' => 'SK',
            'Slovenia' => 'SI',
            'Solomon Islands' => 'SB',
            'Somalia' => 'SO',
            'South Africa' => 'ZA',
            'South Georgia And Sandwich Isl.' => 'GS',
            'Spain' => 'ES',
            'Sri Lanka' => 'LK',
            'Sudan' => 'SD',
            'Suriname' => 'SR',
            'Svalbard And Jan Mayen' => 'SJ',
            'Swaziland' => 'SZ',
            'Sweden' => 'SE',
            'Switzerland' => 'CH',
            'Syrian Arab Republic' => 'SY',
            'Taiwan' => 'TW',
            'Tajikistan' => 'TJ',
            'Tanzania' => 'TZ',
            'Thailand' => 'TH',
            'Timor-Leste' => 'TL',
            'Togo' => 'TG',
            'Tokelau' => 'TK',
            'Tonga' => 'TO',
            'Trinidad And Tobago' => 'TT',
            'Tunisia' => 'TN',
            'Turkey' => 'TR',
            'Turkmenistan' => 'TM',
            'Turks And Caicos Islands' => 'TC',
            'Tuvalu' => 'TV',
            'Uganda' => 'UG',
            'Ukraine' => 'UA',
            'United Arab Emirates' => 'AE',
            'United Kingdom' => 'GB',
            'United States' => 'US',
            'United States Outlying Islands' => 'UM',
            'Uruguay' => 'UY',
            'Uzbekistan' => 'UZ',
            'Vanuatu' => 'VU',
            'Venezuela' => 'VE',
            'Viet Nam' => 'VN',
            'Virgin Islands, British' => 'VG',
            'Virgin Islands, U.S.' => 'VI',
            'Wallis And Futuna' => 'WF',
            'Western Sahara' => 'EH',
            'Yemen' => 'YE',
            'Zambia' => 'ZM',
            'Zimbabwe' => 'ZW'
        ];

        // Convert the country name to title case and remove leading/trailing spaces
        $countryName = trim(ucwords(strtolower($countryName)));

        // Check if the country name exists in the array
        if (isset($countryCodes[$countryName])) {
            return $countryCodes[$countryName];
        } else {
            // Return an empty string or null if the country name is not found
            return '';
        }
    }

    protected function customOptions($sku, $customOptions)
    {
        $product = $this->productRepository->get($sku, true);
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->prodOptionValue = $_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Option\Value');

        if (!empty($product->getOptions())) {
            foreach ($product->getOptions() as $option) {
                if ($option->getTitle() == 'Bundle') {

                    if ($option->getValues()) {
                        foreach ($option->getValues() as $value) {
                            $this->prodOptionValue->deleteValues($value->getOptionTypeId());
                        }
                    }
                    $option->setValues($customOptions);
                    $option->save();
                }
            }
        }
    }
}