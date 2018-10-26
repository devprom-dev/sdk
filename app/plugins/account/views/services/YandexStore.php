<?php
define('SHOP_ID', 71283);
define('SCENE_ID', 66484);
// define('SCENE_ID', 540656); // demo scene
define('SHOP_PASSWORD', 'G3jEs+EsTat8ethat8!');

class YandexStore
{
    private $language = 'ru';
    private $productionUrl = "https://money.yandex.ru/eshop.xml?";
    private $demoUrl = "https://demomoney.yandex.ru/eshop.xml?";

    public function __construct( $language = 'ru' ) {
        $this->language = $language;
    }

    public function getPaymentFormUrl( $orderId, $amount, $email, $options, $failUrl = '', $successUrl = '' )
    {
        if ( $this->language == 'ru' ) {
            $url = $this->productionUrl;
        }
        else {
            $url = $this->productionUrl;
        }
        $currency = $this->language == 'ru' ? 'RUB' : 'USD';

        $clientQuery = "shopId=".SHOP_ID.
            "&scid=".SCENE_ID.
            "&sum=".$amount.
            "&paymentType=AC".
            "&orderNumber=".$orderId.
            "&Currency=".$currency;
        $clientQuery .= "&customerNumber=".$email;
        $clientQuery .= "&cps_email=".$email;
        $clientQuery .= "&shopFailURL=".urlencode($failUrl);
        $clientQuery .= "&shopSuccessURL=".urlencode($successUrl);
        $clientQuery .= "&shopDefaultUrl=".urlencode($successUrl);
        $clientQuery .= "&OrderInfo=".urlencode(JsonWrapper::encode($options));

        return $url.$clientQuery;
    }

    public function validateOrder( $parms )
    {
        if ( $parms['md5'] == '' ) return true;
        $checkParameters = array();
        foreach( array('action','orderSumAmount','orderSumCurrencyPaycash','orderSumBankPaycash','shopId','invoiceId','customerNumber') as $parameter ) {
            $checkParameters[] = $parms[$parameter];
        }
        $checkParameters[] = SHOP_PASSWORD;
        return $parms['md5'] == strtoupper(md5(join(';', $checkParameters)));
    }

    public function replyOrderOk()
    {
        $dt = new DateTime();
        echo '<checkOrderResponse performedDatetime="'.$dt->format(DateTime::W3C).'" code="0" invoiceId="'.$_REQUEST['invoiceId'].'" shopId="'.SHOP_ID.'" orderSumAmount="'.$_REQUEST['orderSumAmount'].'" />';
        die();
    }

    public function replyOrderWrong()
    {
        $dt = new DateTime();
        echo '<checkOrderResponse performedDatetime="'.$dt->format(DateTime::W3C).'" code="100" invoiceId="'.$_REQUEST['invoiceId'].'" shopId="'.SHOP_ID.'"/>';
        die();
    }

    public function checkProcessingParms( $parms ) {
        return $parms['md5'] != '';
    }

    public function replyProcessingOk( $queryString )
    {
        $order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
        $url_parts = parse_url($order_info['Redirect']);

        $installLicenseUrl = $url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].'/module/accountclient/process?'.$queryString;
        Logger::getLogger('Commands')->info("Install license: ".$installLicenseUrl);

        file_get_contents($installLicenseUrl);
        $dt = new DateTime();
        echo '<paymentAvisoResponse performedDatetime="'.$dt->format(DateTime::W3C).'" code="0" invoiceId="'.$_REQUEST['invoiceId'].'" shopId="'.SHOP_ID.'" />';
        die();
    }

    public function replyLicenseInstalled( $queryString )
    {
        $order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
        $url_parts = parse_url($order_info['Redirect']);

        exit(header('Location: '.
            $url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].
            '/module/accountclient/process?'.$queryString
        ));
    }
}