<?php

include_once "ProcessOrder.php";

class ProcessOrder2 extends ProcessOrder
{
    public function getLicenseKey( $uid, $value )
	{
		openssl_sign($value.$uid, $signature, $this->getKey(), OPENSSL_ALGO_SHA512);
		return base64_encode($signature);
	}

    public function getLicenseValue( $order_info )
	{
        $value = array (
            'options' => $order_info['LicenseOptions']
        );

		$days = $order_info['LicenseValue'];
		if ( $days != '' ) {
            date_default_timezone_set('UTC');
            $date = new DateTime();
            $date->add(new DateInterval('P'.$days.'D'));

            $was_parms = $order_info['WasLicenseValue'];
            Logger::getLogger('Commands')->info('WASKEY: '.var_export($was_parms, true));

            $license_verified = openssl_verify(
                    json_encode($was_parms) . $order_info['InstallationUID'],
                    base64_decode(trim($order_info['WasLicenseKey'])),
                    file_get_contents(SERVER_ROOT_PATH . 'templates/config/license.pub'),
                    OPENSSL_ALGO_SHA512) == 1;

            if ( $license_verified && is_array($was_parms) && $was_parms['timestamp'] != '' )
            {
                $dt1 = new DateTime($was_parms['timestamp']);
                $dt2 = new DateTime();
                $left_days = $dt1->diff($dt2)->days;
                if ( $left_days > 0 ) {
                    $days += $left_days;
                    $date->add(new DateInterval('P'.$left_days.'D'));
                }
            }
            $value['timestamp'] = $date->format('Y-m-d');
            $value['days'] = $days;
        }
        if ( $order_info['LicenseUsers'] > 0 ) {
            $value['users'] = $order_info['LicenseUsers'];
        }
		return json_encode($value);
	}

	protected function getKey()
	{
		$key = '-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,1B7930333EA65EA7

KwU3y5aBtfBweADnPDe+CAcKL0a8iQjO8W3tOQXjE2YWHZQu7iaLTeH3uJQ/Uqb4
jYf39ymAuz+sMm633ErW6JQO9MptERMrIUZgGFwgSVyx4io+CDm+kqNi2hsBg2jV
PL1XNalrSJxyYCsBdmDa59Eu2ZWFJEVvrXIROMlIYAcwj2F/cMjCs6wK9thIie4x
k7pt+G6uvYn+OAvH2f1HiUfdqVhQCjZ4jPYq+HIFcjYrhxR3FwyMmNzxqgoP3srk
Il42NCkUpVh8Pb2xGVeD3gsGwdlVHAATR/GcxY3PBRn+gcvVjrEw2K67gOHZ6zr0
u8HQilV7cLChy4RMam0aIXxikQS2DB/mxVl4L07nwrPbDAdXgw1NQcWNe2G9EfHh
Gk+ZehvC+D+QCIvgYVIWoCmmq4nQ3Ondxp444mq7lQbbqXTHB3xdYAPbODj2t3L5
yy48MJkUU1Xk90iO+p1j/9HIl2ti1Rt5GL5mlYBNM2dfaJn50J6PE8dH3cvvaJFs
/GMHth0ya5VifOADJZ+S73Bf+Rn4bvNYLdLj/enbQ4t7J2tcRQ9eBL4qmCdDnHhI
1QbeMMZ7SxP2UCs4cTHPcl8dpwn2j7E0lHFwWUjyulqXNTfL6fYzibwyuYzae05j
g71UKcgv4pljdH98fVLQ3B9Ui2pgPr2BWcCn2zkcD7RRrLNRSpkDfUJEH9BqA3jF
3wIP4jNv0gqZIICqU46QswogZzzz6a6w+BL0AWZ7sU9K2U/3KESVE+8EQ+xfUH0j
cLOi8ercHtTmsC5/BTHjqqiSlsKmedZfE3QDdKOLpK2yW4aWyMFQsby2OiaDakr4
gmzc8kE1mRqriRLP4ow7JPpbSfppwpM3kKUz5WPQj2wjDwZ5E7S1Y1AI/TGEYHrY
sINXXnf3peLqvlneyP5HGydJsCUCl3eOJqeasRQ0cVP3xDqDvZUbQRaTh6U+m7AU
KdzV9KbCX+CyNXsFhLOZ6Ray+GoVvTVB4iE07r9JbN63r3wwm4Eo2guduVjqdnvW
gdmv0DdF2Av69oC2lRKuKYwZ5pEWVh9DNglp/zwi8PZ3DisCQSElHgfrl4ONJ/HO
nsyCFTvsSjwMjNEOhZB9tLIo2y135UFMBmaIgfmu7Xf4nOF6yPrB4LVsTl2j/gyS
PScVuzyPWLv9uLl84flJZqGuUo8WvNRTMRxyeS357MBT+VXHhDAO8/azU4jz+r8h
uDTM3lbTfvH6lIMYVjSJaoDiXuzpubDd3+GHoZGIeWHCZYOutFJxkImiOZOtYddP
+8dcwU1bB8fo7UdI3VdfbD9Rl4y+vKLF2owK2ovCLW0g4OU3OB5EgsmPzo5CxU5u
i6eD3jQ9s9dVsBoFT2k4Q5mb8Lmt46D88Bm37t3QkD7xILPjTjYdix9ZnZFWfRZy
+xcLf7qFzjO/Ibim3FpgD5JOEAgbYux9dBREI9JDx+wucR6kUGNVpdbhHVmNyXSX
5dseylFPCADI2E0OSuRVXrVp3s2eNOTESreGTUHODxWpTbP8psudi/Hc/wuV0zyU
Z4/3qtUVC4aVOe+dxVTF1png3yWzqFFxG8zKEEzGJcZ7pSY71UZsoQ==
-----END RSA PRIVATE KEY-----';
		return openssl_pkey_get_private($key,'vt;leyfhjlysq');
	}
}
