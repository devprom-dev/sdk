<?php
namespace Devprom\ApplicationBundle\Service;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRCodeService
{
    public function getMobileAuthQRCode()
    {
        $authCookies = array();
        foreach( $_COOKIE['devprom'] as $cookieId => $cookieValue ) {
            $authCookies[] = array(
                'name' => "devprom[{$cookieId}]",
                'value' => $cookieValue
            );
        }
        $qrCodeData = array(
            'url' => \EnvironmentSettings::getServerUrl(),
            'cookies' => array_merge(
                array(
                    array(
                        'name' => 'devprom-app',
                        'value' => $_COOKIE['devprom-app']
                    )
                ),
                $authCookies
            )
        );

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile('Hello World!', 'qrcode.png');

        return base64_encode($writer->writeString(\JsonWrapper::encode($qrCodeData), 'UTF-8'));
    }
}