<?php
//define ('MERCHANT_ID', 7742);
//define ('MERCHANT_KEY', 'efef1ce9-6c7b-401f-8430-1e96540636fc');
define ('MERCHANT_ID', 62021);
define ('MERCHANT_KEY', '30cfcab4-ce10-413f-bbfd-4a367823bc1c');

class PayonlineStore
{
    private $language = 'ru';

    public function __construct( $language = 'ru' ) {
        $this->language = $language;
    }

    public function getPaymentFormUrl( $orderId, $amount, $email, $options, $failUrl = '', $successUrl = '' )
    {
        if ( $this->language == 'ru' ) {
            $url = "https://secure.payonlinesystem.com/ru/payment/?";
        }
        else {
            $url = "https://secure.payonlinesystem.com/en/payment/?";
        }
        $currency = $this->language == 'ru' ? 'RUB' : 'USD';

        $baseQuery = "MerchantId=".MERCHANT_ID.
            "&OrderId=".$orderId.
            "&Amount=".$amount.
            "&Currency=".$currency;
        $queryWithSecurityKey = $baseQuery."&PrivateSecurityKey=".MERCHANT_KEY;
        $hash = md5($queryWithSecurityKey);

        $clientQuery = $baseQuery."&SecurityKey=".$hash;
        $clientQuery .= "&Email=".$email;
        $clientQuery .= "&FailUrl=".urlencode($failUrl);
        $clientQuery .= "&OrderInfo=".urlencode(JsonWrapper::encode($options));

        return $url.$clientQuery;
    }

    public function validateOrder( $parms )
    {
        $order_info = JsonWrapper::decode(urldecode($parms['OrderInfo']));
        $baseQuery = "DateTime=".$parms['DateTime'].
            "&TransactionID=".$parms['TransactionID'].
            "&OrderId=".$parms['OrderId'].
            "&Amount=".$order_info['Amount'].
            "&Currency=".$order_info['Currency'];

        $queryWithSecurityKey = $baseQuery."&PrivateSecurityKey=".MERCHANT_KEY;
        return $parms['SecurityKey'] == md5($queryWithSecurityKey);
    }

    public function replyOrderOk()
    {
    }

    public function replyOrderWrong()
    {
        $order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
        $url_parts = parse_url($order_info['Redirect']);

        exit(header('Location: '.
            $url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].
            '/module/accountclient/failed?ErrorCode='.intval($_REQUEST['ErrorCode'])
        ));
    }

    public function checkProcessingParms( $parms ) {
        return true;
    }

    public function replyProcessingOk( $queryString )
    {
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