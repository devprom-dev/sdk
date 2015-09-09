<?php

include_once "ExceptionHandlerListener.php";

class ExceptionHandlerListenerRaven extends ExceptionHandlerListener
{
    public function handle( $data, $code )
    {
        try
        {
            $client = $this->getClient();
            $client->captureMessage($data['error']['message'], $data);
            if ( $client->getLastError() != '' ) throw new \Exception($client->getLastError());
        }
        catch( Exception $e )
        {
            try {
                Logger::getLogger('System')->error($e->getMessage());
            }
            catch( Exception $e ) {
                error_log('Unable initialize logger: '.$e->getMessage());
            }
        }
    }

    public function captureException( $e )
    {
        try
        {
            $client = $this->getClient();
            $client->captureException($e);
            if ( $client->getLastError() != '' ) throw new \Exception($client->getLastError());
        }
        catch( Exception $e )
        {
            try {
                Logger::getLogger('System')->error($e->getMessage());
            }
            catch( Exception $e ) {
                error_log('Unable initialize logger: '.$e->getMessage());
            }
        }
    }

    protected function getClient()
    {
        $project = defined('DEVOPSKEY') ? DEVOPSKEY : 'af4078b6e4630da32f3c164d121ea2b1';
        return new Raven_Client('http://'.$project.':null@api.devopsboard.com/sentry/1',
            array (
                'release' => $_SERVER['APP_VERSION'],
                'name' => $_SERVER['SERVER_NAME'],
                'site' => $_SERVER['SERVER_ADDR'],
                'timeout' => '6'
            )
        );
    }
}